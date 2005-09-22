/******************************************************************************

CAMPSITE is a Unicode-enabled multilingual web content
management system for news publications.
CAMPFIRE is a Unicode-enabled java-based near WYSIWYG text editor.
Copyright (C)2000,2001  Media Development Loan Fund
contact: contact@campware.org - http://www.campware.org
Campware encourages further development. Please let us know.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

******************************************************************************/

/******************************************************************************

Implementation of the classes defined in csocket.h

******************************************************************************/

#include "csocket.h"
#include <errno.h>
#include <ctype.h>
#include <string.h>
#include <pthread.h>
#include <map>
#include <functional>

#include "globals.h"

#ifdef SOLARIS
extern "C" int gethostname(char *name, int len);
#endif

/* platforms that don't have gethost*_r or getserv*_r, or that have them but
   with a different interface, can use these functions. note these are thread
   safe via a mutex lock, and are not truely threaded
*/
#if (!HAVE_GETHOSTBYADDR_R) || (!HAVE_GETHOSTBYNAME_R) || (!HAVE_GETSERVBYNAME_R)
static pthread_mutex_t threadsafe_gethost_mutex = PTHREAD_MUTEX_INITIALIZER;

#define ptrsize (sizeof(void*))
#define ROUND_TO_PTR_BOUNDARY( buff, roundup, buflen ) 						\
	if( (roundup = ptrsize - ((ulint)buff % ptrsize)) != ptrsize ){	\
		buff = (char*)(ulint)buff + roundup;						\
		if( (buflen -= roundup) < 0 )										\
			return -1;														\
	}
#endif

#if (!HAVE_GETHOSTBYADDR_R) || (!HAVE_GETHOSTBYNAME_R)
int construct_hostent( struct hostent* result, struct hostent* host, char* buff, int buflen )
{
	int i,arraysize;
	int strtotal=0;
	char* pdata;
	int roundup;

	result->h_addrtype = host->h_addrtype;
	result->h_length = host->h_length;

	/* copy h_name if enough room */
	if( (buflen -= (strtotal = strlen( host->h_name)+1 )) < 0 )
		return -1;
	strcpy( buff, host->h_name );
	result->h_name = buff;
	buff+=strtotal;

	/* count aliases */
	strtotal = 0;
	for( arraysize=0; host->h_aliases[ arraysize ]; ++arraysize ){
		strtotal += strlen( host->h_aliases[ arraysize ] )+1;
	}
	++arraysize; /* copy null too */

	ROUND_TO_PTR_BOUNDARY( buff, roundup, buflen );

	buflen -= strtotal + sizeof(char*) * arraysize;
	if( buflen < 0 ){
		return -1;
	}

	pdata = buff + (sizeof(char*) * (arraysize+1));

	result->h_aliases = (char**)buff;
	for( i=0; i < arraysize; ++i ){
		if( host->h_aliases[i] != NULL ){
			result->h_aliases[i] = pdata; 
			strcpy( pdata, host->h_aliases[i] );
			pdata+=strlen( host->h_aliases[i] ) + 1;
		}else{
			result->h_aliases[i] = NULL;
		}
	}
	buff = pdata;
	
	/* count addresses */
	for( arraysize=0; host->h_addr_list[ arraysize ]; ++arraysize );
	++arraysize;	/* copy the null part too */

	ROUND_TO_PTR_BOUNDARY( buff, roundup, buflen );

	buflen -= ((host->h_length * arraysize) + (sizeof(char*) * arraysize));
	if( buflen < 0 ){
		return -1;
	}

	pdata = buff + (sizeof(char*) * arraysize);

	result->h_addr_list = (char**)buff;
	for( i=0; i < arraysize; ++i, pdata+=host->h_length ){
		if( host->h_addr_list[i] != NULL ){
			result->h_addr_list[i] = pdata; 
			memcpy( pdata, host->h_addr_list[i], host->h_length );
		}else{
			result->h_addr_list[i] = NULL;
		}
	}
	return 0;
}
#endif

#if (!HAVE_GETSERVBYNAME_R)
int construct_servent( struct servent* result, struct servent* host, char* buff, int buflen )
{
	int i,arraysize;
	int strtotal=0;
	char* pdata;
	int roundup;

	result->s_port = host->s_port;

	result->s_name = buff;
	buflen -= strlen( host->s_name )+1;
	if( buflen < 0 )
		return -1;
	strcpy( buff, host->s_name );
	buff += strlen( host->s_name )+1;

	result->s_proto = buff;
	buflen -= strlen( host->s_proto )+1;
	if( buflen < 0 )
		return -1;
	strcpy( buff, host->s_proto );
	buff += strlen( host->s_proto )+1;

	/* count aliases */
	for( arraysize=0; host->s_aliases[ arraysize ]; ++arraysize ){
		strtotal += strlen( host->s_aliases[ arraysize ] )+1;
	}

	++arraysize; /* copy null too */
	ROUND_TO_PTR_BOUNDARY( buff, roundup, buflen );

	buflen -= strtotal + sizeof(char*) * arraysize;
	if( buflen < 0 ){
		return -1;
	}

	pdata = buff + (sizeof(char*) * (arraysize+1));

	result->s_aliases = (char**)buff;
	for( i=0; i < arraysize; ++i ){
		if( host->s_aliases[i] != NULL ){
			result->s_aliases[i] = pdata; 
			strcpy( pdata, host->s_aliases[i] );
			pdata+=strlen( host->s_aliases[i] ) + 1;
		}else{
			result->s_aliases[i] = NULL;
		}
	}
	buff = pdata;
	return 0;
}
#endif

#if (!HAVE_GETHOSTBYADDR_R)
struct hostent*
gethostbyaddr_r( const char *addr, int length, int type, struct hostent *result, char *buffer, int buflen, int bufstart, int *h_errnop )
{
	struct hostent *host;
	*h_errnop = 0;

	if( bufstart>=buflen )
		return NULL;

	pthread_mutex_lock( &threadsafe_gethost_mutex );

	buffer+=bufstart;
	buflen-=bufstart;

	if( (host = gethostbyaddr(addr, length, type)) == 0 ){
		*h_errnop = h_errno;
		result = NULL;
	}else{
		if( construct_hostent( result, host, buffer, buflen ) == -1 ){
			result = NULL;
		}
	}

	pthread_mutex_unlock( &threadsafe_gethost_mutex );
	return result;
}
#endif

#if (!HAVE_GETHOSTBYNAME_R)
struct hostent *
gethostbyname_r (const char *name, struct hostent *result, char *buffer, int buflen, int bufstart, int *h_errnop)
{
	struct hostent *host;
	*h_errnop = 0;

	if( bufstart>=buflen )
		return NULL;

	pthread_mutex_lock( &threadsafe_gethost_mutex );

	buffer+=bufstart;
	buflen-=bufstart;

	if( (host = gethostbyname(name)) == 0 ){
		*h_errnop = h_errno;
		result = NULL;
	}else{
		if( construct_hostent( result, host, buffer, buflen ) == -1 ){
			result = NULL;
		}
	}

	pthread_mutex_unlock( &threadsafe_gethost_mutex );
	return result;
}
#endif

#if (!HAVE_GETSERVBYNAME_R)
struct servent *
getservbyname_r (const char *name, const char *proto, struct servent *result, char *buffer, int buflen, int bufstart )
{
	struct servent *serv;

	if( bufstart>=buflen )
		return NULL;

	pthread_mutex_lock( &threadsafe_gethost_mutex );

	buffer+=bufstart;
	buflen-=bufstart;

	if( (serv = getservbyname( name, proto ) ) == 0 ){
		result = NULL;
	}else{
		if( construct_servent( result, serv, buffer, buflen ) == -1 ){
			result = NULL;
		}
	}

	pthread_mutex_unlock( &threadsafe_gethost_mutex );
	return result;
}
#endif

// CSocket implementatation

#ifdef _REENTRANT
#include "mutex.h"
#endif

using std::map;
using std::less;

// class CSocketMap: used to store all existing sockets; this way they can be closed all at once in
//   case a signal was received
class CSocketMap
{
public:
	// default constructor (empty)
	CSocketMap() {}
	~CSocketMap();

	// insert socket into map
	void insert(CSocket*);

	// delete socket from map
	void erase(CSocket*);

	// find socket with given identifier; returns pointer to CSocket class
	CSocket* find(SOCKET) const;

	// returns true if socket is in map
	bool has(const CSocket*) const;

	// returns map size
	int size() const;

	// returns true if map is empty
	bool isEmpty() const;

	// clears the map; if the parameter is true closes all sockets
	void clear(bool = true);

private:
	map<SOCKET, CSocket*, less<SOCKET> > m_coSocketMap;
#ifdef _REENTRANT
	// if threaded use a mutex to get access to the map
	mutable CMutex m_coAccessMutex;
#endif
};

// destructor
inline CSocketMap::~CSocketMap()
{
	clear();
}

// insert socket into map
inline void CSocketMap::insert(CSocket* p_pcoSocket)
{
#ifdef _REENTRANT
	CMutexHandler coAccessHandler(&m_coAccessMutex);
#endif
	m_coSocketMap[(SOCKET)*p_pcoSocket] = p_pcoSocket;
}

// delete socket from map
inline void CSocketMap::erase(CSocket* p_pcoSocket)
{
#ifdef _REENTRANT
	CMutexHandler coAccessHandler(&m_coAccessMutex);
#endif
	p_pcoSocket->Close();
	m_coSocketMap.erase((SOCKET)*p_pcoSocket);
}

// find socket with given identifier; returns pointer to CSocket class
inline CSocket* CSocketMap::find(SOCKET p_nSOCKET) const
{
#ifdef _REENTRANT
	CMutexHandler coAccessHandler(&m_coAccessMutex);
#endif
	map<SOCKET, CSocket*, less<SOCKET> >::const_iterator coIt = m_coSocketMap.find(p_nSOCKET);
	if (coIt == m_coSocketMap.end())
		return NULL;
	return (*coIt).second;
}

// returns true if socket is in map
inline bool CSocketMap::has(const CSocket* p_pcoSocket) const
{
#ifdef _REENTRANT
	CMutexHandler coAccessHandler(&m_coAccessMutex);
#endif
	return m_coSocketMap.find((SOCKET)*p_pcoSocket) != m_coSocketMap.end();
}

// returns map size
inline int CSocketMap::size() const
{
#ifdef _REENTRANT
	CMutexHandler coAccessHandler(&m_coAccessMutex);
#endif
	return m_coSocketMap.size();
}

// returns true if map is empty
inline bool CSocketMap::isEmpty() const
{
#ifdef _REENTRANT
	CMutexHandler coAccessHandler(&m_coAccessMutex);
#endif
	return m_coSocketMap.empty();
}

// clears the map; if the parameter is true closes all sockets
void CSocketMap::clear(bool p_bCloseSockets)
{
#ifdef _REENTRANT
	CMutexHandler coAccessHandler(&m_coAccessMutex);
#endif
	if (p_bCloseSockets) {
		map<SOCKET, CSocket*, less<SOCKET> >::iterator coIt;
		for (coIt = m_coSocketMap.begin(); coIt != m_coSocketMap.end(); ++coIt)
		{
			(*coIt).second->Close();
		}
	}
	m_coSocketMap.clear();
}

// declare map to store the sockets
static CSocketMap g_coSocketmap;

bool CSocket::s_bRegisterSockets = true;
#ifdef _REENTRANT
// if threaded use a mutex to get access to the static member s_bRegisterSockets
CMutex CSocket::m_coAccessMutex;
#endif

// CSocket constructor
// if exceptions enabled throws SocketErrorException on failure to create socket
CSocket::CSocket(int type, int domain, int protocol)
#ifdef _REENTRANT
	EXCEPTION_DEF(throw(SocketErrorException, ExMutex))
#else
	EXCEPTION_DEF(throw(SocketErrorException))
#endif
{
	if ((sock = socket(domain, type, protocol)) == -1)
	{
		switch (errno)
		{
		case EACCES:
			THROW_EX(throw SocketErrorException("Permission denied to create socket", EACCES));
		case EMFILE:
			THROW_EX(throw SocketErrorException("Descriptor table full", EMFILE));
		case ENOMEM:
			THROW_EX(throw SocketErrorException("Not enough memory", ENOMEM));
		}
	}
	RegisterSocket();
}

CSocket::~CSocket()
{
	Close();
#ifdef _REENTRANT
	try
	{
#endif
		UnregisterSocket();
#ifdef _REENTRANT
	}
	catch (ExMutex& coEx)
	{
	}
#endif
}

void CSocket::RegisterSocket()
#ifdef _REENTRANT
	EXCEPTION_DEF(throw (ExMutex))
#else
	EXCEPTION_DEF(throw ())
#endif
{
#ifdef _REENTRANT
	CMutexHandler coAccessHandler(&m_coAccessMutex);
#endif
	if (s_bRegisterSockets)
		g_coSocketmap.insert(this);
}

void CSocket::UnregisterSocket()
#ifdef _REENTRANT
	EXCEPTION_DEF(throw (ExMutex))
#else
	EXCEPTION_DEF(throw ())
#endif
{
#ifdef _REENTRANT
	CMutexHandler coAccessHandler(&m_coAccessMutex);
#endif
	if (s_bRegisterSockets)
		g_coSocketmap.erase(this);
}

// setRegister: if parameter is true register all sockets that are created; closeAllSockets will
//   not work for unregistered sockets
void CSocket::setRegister(bool p_bRegisterSockets)
#ifdef _REENTRANT
	EXCEPTION_DEF(throw (ExMutex))
#else
	EXCEPTION_DEF(throw ())
#endif
{
#ifdef _REENTRANT
	CMutexHandler coAccessHandler(&m_coAccessMutex);
#endif
	s_bRegisterSockets = p_bRegisterSockets;
}

bool CSocket::getRegister()
#ifdef _REENTRANT
	EXCEPTION_DEF(throw (ExMutex))
#else
	EXCEPTION_DEF(throw ())
#endif
{
#ifdef _REENTRANT
	CMutexHandler coAccessHandler(&m_coAccessMutex);
#endif
	bool bRegisterSockets = s_bRegisterSockets;
	return bRegisterSockets;
}

// closeAllSockets: needed for closing registered sockets in case a signal was received
void CSocket::closeAllSockets()
#ifdef _REENTRANT
	EXCEPTION_DEF(throw (ExMutex))
#else
	EXCEPTION_DEF(throw ())
#endif
{
#ifdef _REENTRANT
	CMutexHandler coAccessHandler(&m_coAccessMutex);
#endif
	if (s_bRegisterSockets)
		g_coSocketmap.clear(true);
}

// GetLocal()
// returns a pointer to struct sockaddr_in filled with the local end point of an socket
// returns NULL if cannot complete the operation
// if exceptions enabled throws SocketException on error
const struct sockaddr_in* CSocket::GetLocal() EXCEPTION_DEF(throw(SocketException))
{
	static struct sockaddr_in s;
	unsigned int len = sizeof(s);
	if (getsockname(sock, (sockaddr*) &s, &len) == -1)
	{
		THROW_EX(throw SocketException("Cannot complete operation"));
		return NULL;
	}
	return &s;
}

// LocalIP()
// returns a string containing the Local IP address of the scoket
// returns NULL  if cannot complete
// Throws SocketException if exceptions enabled and cannot complete
const char* CSocket::LocalIP() EXCEPTION_DEF(throw(SocketException))
{
	const struct sockaddr_in* l;
	l = GetLocal();
#ifndef _EXCEPTIONS_
	if (l == NULL)
		return NULL;
#endif
	return inet_ntoa(l->sin_addr);
}

// LocalPort()
// returns the Local Port of the socket
// returns (-errno) if it cannot complete
// Throws SocketException if exceptions enabled and cannot complete
const int CSocket::LocalPort() EXCEPTION_DEF(throw(SocketException))
{
	const struct sockaddr_in* l;
	l = GetLocal();
#ifndef _EXCEPTIONS_
	if (l == NULL)
		return -errno;
#endif
	return ntohs(l->sin_port);
}

// CSocket static member GetHostInfo
// Returns a pointer to a  hostent structure for the argument or NULL for error
// throws HostNotFound if cannot resolve name or address if exceptions enabled
// throws MalformedAddress if invalid address
struct hostent* CSocket::GetHostInfo(const char* name_or_addr)
			EXCEPTION_DEF(throw(HostNotFound, MalformedAddress))
{
	struct hostent* h;
	struct hostent* ph = 0;
	ulint addr;
	int nErrNo;
#ifdef _REENTRANT
	char pchErrMsg[100];
	struct hostent res;
#endif
	if (isdigit(name_or_addr[0]))
	{
		if ((addr = inet_addr(name_or_addr)) < 0)
		{
			THROW_EX(throw MalformedAddress("Malformed address", name_or_addr));
			return NULL;
		}
#ifndef _REENTRANT
		ph = gethostbyaddr((char*) & addr, sizeof(addr), AF_INET);
		nErrNo = h_errno;
#else
		ph = gethostbyaddr_r((char*) & addr, sizeof(addr), AF_INET, &res, pchErrMsg, 100, 0,
		                     &nErrNo) == 0 ? &res : 0;
#endif
	}
	else
	{
#ifndef _REENTRANT
		ph = gethostbyname(name_or_addr);
		nErrNo = h_errno;
#else
		ph = gethostbyname_r(name_or_addr, &res, pchErrMsg, 100, 0, &nErrNo) == 0 ? &res : 0;
#endif
	}
	if (ph == NULL)
	{
		THROW_EX(throw HostNotFound("Host Not found", name_or_addr, nErrNo));
		return NULL;
	}
	else
	{
		h = (struct hostent*) new (struct hostent);
		memcpy(h, ph, sizeof(struct hostent));
		return h;
	}
}

// static member HostName
// Returns a string with the host name for the IP address or name parameter
// throws HostNotFound if cannot resolve name or address
// throws MalformedAddress if invalid address
const char* CSocket::HostName(const char* name_or_addr) EXCEPTION_DEF(throw(SocketException))
{
	struct hostent* h;
	char* name;
	h = GetHostInfo(name_or_addr);
#ifndef _EXCEPTIONS_
	if (h == NULL)
		return NULL;
#endif
	name = new char[strlen(h->h_name) + 1];
	strcpy(name, h->h_name);
	delete h;
	return (const char*) name;
}

// static member IPAddress
// Returns the index'th IP address for the host <name>=IP or hostname
// throws HostNotFound if cannot resolve name or address
// throws MalformedAddress if invalid address
// throws AddressRange if the index is out of range
IPAddr CSocket::IPAddress(const char* name, const int index) EXCEPTION_DEF(throw(SocketException))
{
	struct in_addr in;
	IPAddr ip;
	struct hostent* h = GetHostInfo(name);
#ifndef _EXCEPTIONS_
	if (h == NULL)
		return NULL;
#endif
	if (index <= IPCount(name))
	{
		memcpy(&in.s_addr, h->h_addr_list[index - 1], sizeof(struct in_addr));
		ip = (IPAddr) inet_ntoa(in);
		delete h;
		return ip;
	}
	delete h;
	THROW_EX(throw AddressRange("Address index out of range", index));
	return NULL;
}

// static member IPCount
// Returns the number of IP addresses for the host <name>=IP or hostname
// throws HostNotFound if cannot resolve name or address
// throws MalformedAddress if invalid address
const int CSocket::IPCount(const char* name_or_addr) EXCEPTION_DEF(throw(SocketException))
{
	struct hostent* h = GetHostInfo(name_or_addr);
	int count = 0;
#ifndef _EXCEPTIONS_
	if (h == NULL)
		return NULL;
#endif
	for (char** p = h->h_addr_list; *p != 0; p++)
		count++;
	delete h;
	return (count);
}

// static member AliasCount
// Returns the number of Aliases for the host <name>=IP or hostname
// throws HostNotFound if cannot resolve name or address
// throws MalformedAddress if invalid address
const int CSocket::AliasCount(const char* name_or_addr) EXCEPTION_DEF(throw(SocketException))
{
	struct hostent* h = GetHostInfo(name_or_addr);
	int count = 0;
#ifndef _EXCEPTIONS_
	if (h == NULL)
		return NULL;
#endif
	for (char** p = h->h_aliases; *p != 0; p++)
		count++;
	delete h;
	return count;
}

// static member Alias -
// Returns the index'th Alias for the host <name>=IP or hostname
// throws HostNotFound if cannot resolve name or address
// throws MalformedAddress if invalid address
// throws AddressRange if the index is out of range
const char* CSocket::Alias(const char* name, const int index) EXCEPTION_DEF(throw(SocketException))
{
	char* alias;
	struct hostent* h = GetHostInfo(name);
#ifndef _EXCEPTIONS_
	if (h == NULL)
		return NULL;
#endif
	if (index <= AliasCount(name))
	{
		alias = new char[strlen(h->h_aliases[index - 1]) + 1];
		strcpy(alias, h->h_aliases[index - 1]);
		delete h;
		return alias;
	}
	delete h;
	THROW_EX(throw AddressRange("Address index out of range", index));
	return NULL;
}

// static member ServByName -
// returns the integer identifying the port assigned to the service
// returns -1 on error (not found)
// throws ServNotFound if cannot find the requested service
const int CSocket::ServByName(const char* service, const char *protocol)
EXCEPTION_DEF(throw(ServNotFound))
{
	struct servent* sp;
#ifndef _REENTRANT
	sp = getservbyname(service, protocol);
#else
	struct servent res;
	char pchErrMsg[100];
	sp = getservbyname_r(service, protocol, &res, pchErrMsg, 100, 0) == 0 ? &res : 0;
#endif
	if (sp == NULL)
	{
		THROW_EX (throw ServNotFound("Service not found", service, protocol));
		return -1;
	}
	return sp->s_port;
}

// static member LocalHostName() - returns the Name of the local host
// throws no exceptions
const char* CSocket::LocalHostName() EXCEPTION_DEF(throw())
{
	static char name[100];
#ifndef _REENTRANT
	gethostname((char*) name, sizeof(name));
#else
	gethostname((char*) name, sizeof(name));
#endif
	return (const char*) name;
}

// CConnectedSocket Implementatation

// GetRemote() - method
// returns a pointer to a struct sockaddr_in of the remote end of the socket
// returns NULL if socket is not connected or cannot complete operation
// throws SocketErrorException if not enough resources to complete operation
// throws NotConnected if not connected
const struct sockaddr_in* CConnectedSocket::GetRemote() EXCEPTION_DEF(throw(SocketErrorException))
{
	static struct sockaddr_in remote;
	struct sockaddr_in* result = NULL;
	unsigned int len = sizeof(remote);

	if (
#ifndef _REENTRANT
	    getpeername(sock, (struct sockaddr*) &remote, &len)
#else
	    getpeername(sock, (struct sockaddr*) &remote, &len)
#endif
	    == -1)
	{
		switch (errno)
		{
		case ENOTCONN:
			THROW_EX(throw NotConnected("Socket not connected"));
		default:
			THROW_EX(throw SocketErrorException("Not enough resources to complete operation", errno));
			return result;
		}
	}
	else
		result = &remote;
	return result;
}

// RemoteIP() - method
// returns a string containing the remote end IP address connected to the socket
// returns NULL if socket is not connected or cannot complete operation
// throws SocketErrorException if not enough resources to complete operation
// throws NotConnected if not connected
char* CConnectedSocket::RemoteIP() EXCEPTION_DEF(throw(SocketErrorException))
{
	const struct sockaddr_in* l = GetRemote();
#ifndef _EXCEPTIONS_
	if (l == NULL)
		return NULL;
#endif
	return inet_ntoa(l->sin_addr);
}

// RemotePort() - method        !!!! (errno set on exit)
// returns the remote port of the peer
// returns (-errno) if socket is not connected or cannot complete operation
// throws SocketErrorException if not enough resources to complete operation
// throws NotConnected if not connected
int CConnectedSocket::RemotePort() EXCEPTION_DEF(throw(SocketErrorException))
{
	const struct sockaddr_in* l = GetRemote();
#ifndef _EXCEPTIONS_
	if (l == NULL)
		return ( -errno);
#endif
	return ntohs(l->sin_port);
}

// IsConnected() - tests if connected to a host
// returns TRUE (>0) if connected or 0 if cannot complete or not connected
// throws SocketErrorException if cannot complete operation (test)
int CConnectedSocket::IsConnected() EXCEPTION_DEF(throw(SocketErrorException))
{
	const struct sockaddr_in* l;
#ifdef _EXCEPTIONS_
	try
	{
#endif
		l = GetRemote();
#ifdef _EXCEPTIONS_
	}
	catch (NotConnected& ex)
	{
		return 0;
	}
#endif
	return (l != NULL);
}

// Send() - Send data to the remote end         (sets errno)
// returns the number of bytes written or -1 if error
// throws SocketErrorException on error
int CConnectedSocket::Send(const char* message, int len, int flags)
EXCEPTION_DEF(throw(SocketErrorException))
{
	int n = send(sock, message, len, flags);
#ifdef _EXCEPTIONS_
	if (n == -1)
		throw SocketErrorException("Send error", errno);
#endif
	return n;
}

// Recv() - read data from the remote end       (sets errno)
// returns the number of bytes read or -1 if error
// throws SocketErrorException on error
int CConnectedSocket::Recv(char* message, int len, int flags) EXCEPTION_DEF(throw(SocketErrorException))
{
	int n = recv(sock, message, len, flags);
#ifdef _EXCEPTIONS_
	if (n == -1)
		throw SocketErrorException("Recv error", errno);
#endif
	return n;
}

// CTCPSocket Implementation

// CTCPSocket constructor
// if exceptions enabled throws SocketErrorException on failure to create socket
// or if cannot bind to SPECIFIED (if any) local address
CTCPSocket::CTCPSocket(char* local_ip, int lport, int backlog, bool reuse) EXCEPTION_DEF(throw(SocketErrorException))
		: CConnectedSocket(SOCK_STREAM, PF_INET)
{

	ulint addr;
	if ((addr = inet_addr(local_ip)) != INADDR_ANY || lport != 0)
	{
		sock = socket(AF_INET, SOCK_STREAM, 0);
		int on = reuse ? 1 : 0;
		setsockopt(sock, SOL_SOCKET, SO_REUSEADDR, &on, sizeof(on));
		struct sockaddr_in s;
		s.sin_family = AF_INET;
		s.sin_port = htons(lport);
		s.sin_addr.s_addr = addr;
		if (bind(sock, (sockaddr*) &s, sizeof(s)) == -1)
		{
			switch (errno)
			{
			case EACCES:
				THROW_EX(throw SocketErrorException
				         ("Bind: Permission denied.Reason: Address protected", EACCES));
			case EADDRINUSE:
				THROW_EX(throw AddressAlreadyInUse
				         ("Bind: The local address is already in use", local_ip, lport));
			case EADDRNOTAVAIL:
				THROW_EX(throw SocketErrorException
				         ("Bind: Local address is not available on the local machine",
				          EADDRNOTAVAIL));
			}
		}
		if (listen(sock, backlog) == -1)
		{
			THROW_EX(throw SocketErrorException("Listen error", errno));
		}
	}
}

// Connect() - connects the socket to the remote host and port (errno set on exit)
// returns 1 if success or 0 in failure
// throws SocketErrorException on failure to connect if network Unreachable or
// if interrupted by a signal.
// throws ConnectException if other cases of failure to connect !
int CTCPSocket::Connect(const char* remote, int port) EXCEPTION_DEF(throw(SocketErrorException))
{
	struct sockaddr_in server;
	struct hostent* h;
	int result;
	unsigned int size;

	server.sin_family = AF_INET;
	server.sin_port = htons(port);
	if (!isdigit(remote[0]))
	{
		h = GetHostInfo(remote);
#ifndef _EXCEPTIONS_
		if (h == NULL)
			return 0;
#endif
		memcpy(&server.sin_addr.s_addr, h->h_addr, sizeof(struct in_addr));
	}
	else
		server.sin_addr.s_addr = inet_addr(remote);
	if ((result = connect(sock, (struct sockaddr*) & server, sizeof(server))) == -1)
	{
		switch (errno)
		{
		case EADDRINUSE:
			THROW_EX(throw AddressAlreadyInUse("Connect: Address already in use", remote, port));
		case EADDRNOTAVAIL:
			THROW_EX(throw
			         ConnectException("Connect: Address is not available on", remote, port, errno));
		case ECONNREFUSED:
			THROW_EX(throw ConnectRefused("Connect: connection refused by", remote, port));
		case EINTR:
			THROW_EX(throw SocketErrorException("Connect: interrupted by a signal", errno));
		case ENETUNREACH:
			THROW_EX(throw NetworkUnreachable("Network unreacheable", remote));
		case ETIMEDOUT:
			THROW_EX(throw ConnectTimeout("Connect:timeout", remote, port));
			return 0;
			// In case compiler does not support exceptions or compiled  without exceptions
		case EISCONN:
			getsockname(sock, (struct sockaddr *) &server, &size);
			THROW_EX(throw AlreadyConnected("Connect:already connected to:",
			                                inet_ntoa(server.sin_addr), server.sin_port));
			return 0;
			// In case compiler does not support exceptions or compiled  without exceptions
		}

	}
	return 1;
}

// CTCPSocket Implementation

// Accept() - listens for connections on the socket (sets errno)
// returns a pointer to a CTCPSocket used for further communications
// throws SocketErrorException on any error (with the errno code)
CTCPSocket* CServerSocket::Accept() EXCEPTION_DEF(throw(SocketErrorException))
{
	SOCKET cs;
	if ((cs = accept(sock, NULL, 0)) == -1)
	{
		THROW_EX(throw SocketErrorException("Accept error", errno));
		return NULL;
	}
	CTCPSocket* ps = new CTCPSocket(cs);
	return ps;
}

// CUDPPSocket Implementation

CUDPSocket::CUDPSocket(const char* local_ip, const int lport, int backlog)
EXCEPTION_DEF(throw(SocketErrorException)): CSocket(SOCK_DGRAM, PF_INET)
{
	ulint addr;
	if ((addr = inet_addr(local_ip)) != INADDR_ANY || lport != 0)
	{
		struct sockaddr_in s;
		s.sin_family = AF_INET;
		s.sin_port = htons(lport);
		s.sin_addr.s_addr = inet_addr(local_ip);
		if (bind(sock, (sockaddr*) &s, sizeof(s)) == -1)
		{
			switch (errno)
			{
			case EACCES:
				THROW_EX(throw SocketErrorException
				         ("Bind: Permission denied.Reason: Address protected", EACCES));
			case EADDRINUSE:
				THROW_EX(throw AddressAlreadyInUse
				         ("Bind: The local address is already in use", local_ip, lport));
			case EADDRNOTAVAIL:
				THROW_EX(throw SocketErrorException
				         ("Bind: Local address is not available on the local machine", EADDRNOTAVAIL));
			}
			if (listen(sock, backlog) == -1)
			{
				THROW_EX(throw SocketErrorException("Listen error", errno));
			}
		}
	}
}

// SendTO() - Send data to the remote end       (sets errno)
// returns the number of bytes written or -1 if error
// throws SocketErrorException on error
int CUDPSocket::SendTo(const char* message, int len, const char* host, unsigned int port, int flags)
EXCEPTION_DEF(throw(SocketErrorException))
{
	struct sockaddr_in remote;
	remote.sin_family = AF_INET;
	remote.sin_port = htons(port);
	remote.sin_addr.s_addr = inet_addr(CSocket::IPAddress(host));

	int n = sendto(sock, message, len, flags, (sockaddr*) & remote, sizeof(remote));
#ifdef _EXCEPTIONS_
	if (n == -1)
		throw SocketErrorException("SendTo error", errno);
#endif
	return n;
}

// Recvfrom() - read data from the remote end   (sets errno)
// returns the number of bytes read or -1 if error
// throws SocketErrorException on error
int CUDPSocket::RecvFrom(char* message, int len, int flags, char* FromIP, unsigned int* fromport)
EXCEPTION_DEF(throw(SocketErrorException))
{
	struct sockaddr_in s;
	int struct_len = sizeof(s);
	int n = recvfrom(sock, message, (u_int) len, flags, (sockaddr*) & s, (u_int*) & struct_len);
#ifdef _EXCEPTIONS_
	if (n == -1)
		throw SocketErrorException("Recv error", errno);
#endif
	if (FromIP != NULL)
		strcpy(FromIP, inet_ntoa(s.sin_addr));
	if (fromport != NULL)
		*fromport = ntohs(s.sin_port);
	return n;
}

// CUDPConnSocket Implementation

// CUDPConnSocket constructor
// if exceptions enabled throws SocketErrorException on failure to create socket
// or if cannot bind to SPECIFIED (if any) local address
CUDPConnSocket::CUDPConnSocket(char* local_ip, int lport)
EXCEPTION_DEF(throw(SocketErrorException)): CConnectedSocket(SOCK_DGRAM, PF_INET)
{
	ulint addr;
	if ((addr = inet_addr(local_ip)) != INADDR_ANY || lport != 0)
	{
		struct sockaddr_in s;
		s.sin_family = AF_INET;
		s.sin_port = htons(lport);
		s.sin_addr.s_addr = addr;
		if (bind(sock, (sockaddr*) &s, sizeof(s)) == -1)
			switch (errno)
			{
			case EACCES:
				THROW_EX(throw SocketErrorException
				         ("Bind: Permission denied.Reason: Address protected", EACCES));
			case EADDRINUSE:
				THROW_EX(throw AddressAlreadyInUse("Bind: The local address is already in use",
				                                   local_ip, lport));
			case EADDRNOTAVAIL:
				THROW_EX(throw SocketErrorException
				         ("Bind: Local address is not available on the local machine",
				          EADDRNOTAVAIL));
			}
	}
}

// Connect() - connects the socket to the remote host and port (errno set on exit)
// returns 1 if success or 0 in failure
// throws SocketErrorException on failure to connect if network Unreachable or
// if interrupted by a signal.
int CUDPConnSocket::Connect(const char* remote_addr, int port) EXCEPTION_DEF(throw(SocketErrorException))
{
	struct sockaddr_in server;
	struct hostent* h;
	int result;
	server.sin_family = AF_INET;
	server.sin_port = htons(port);
	if (!isdigit(remote_addr[0]))
	{
		h = GetHostInfo(remote_addr);
#ifndef _EXCEPTIONS_
		if (h == NULL)
			return 0;
#endif
		memcpy(&server.sin_addr.s_addr, h->h_addr, sizeof(struct in_addr));
	}
	else
		server.sin_addr.s_addr = inet_addr(remote_addr);
	if ((result = connect(sock, (struct sockaddr*) & server, sizeof(server))) == -1)
	{
		THROW_EX(throw SocketErrorException("Error connecting the socket", errno));
		return errno;
	}
	return 1;
}
