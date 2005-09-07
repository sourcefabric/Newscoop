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

This file declares the Socket classes as needed
This is a blocking mode socket for use with TCP-IP

******************************************************************************/

#ifndef _CSOCKET_H
#define _CSOCKET_H

#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <netdb.h>
#include <unistd.h>
#include <iostream>

#include "globals.h"
#include "exceptions.h"
#ifdef _REENTRANT
#include "mutex.h"
#endif

#ifndef WIN32
typedef int SOCKET;
typedef int boolean;
#define TRUE 1
#define FALSE 0
#endif

typedef const char* IPAddr;

class CSocket
{
private:
	static bool s_bRegisterSockets;
#ifdef _REENTRANT
	// if threaded use a mutex to get access to the static member s_bRegisterSockets
	static CMutex m_coAccessMutex;
#endif

#ifdef _REENTRANT
	void RegisterSocket() EXCEPTION_DEF(throw (ExMutex));
	void UnregisterSocket() EXCEPTION_DEF(throw (ExMutex));
#else
	void RegisterSocket() EXCEPTION_DEF(throw ());
	void UnregisterSocket() EXCEPTION_DEF(throw ());
#endif

protected:
	SOCKET sock;

	static struct hostent* GetHostInfo(const char*)
				EXCEPTION_DEF(throw (HostNotFound, MalformedAddress));

public:
#ifdef _REENTRANT
	CSocket(int type, int domain, int protocol = 0) EXCEPTION_DEF(throw(SocketErrorException, ExMutex));
#else
	CSocket(int type, int domain, int protocol = 0) EXCEPTION_DEF(throw(SocketErrorException));
#endif
	CSocket(SOCKET s)
	{
		sock = s;
		RegisterSocket();
	}
	virtual ~CSocket();
	
	virtual void Close() const
	{
		close(sock);
	}
	void Shutdown(int how = SHUT_RDWR) const
	{
		shutdown(sock, how);
	}
	operator SOCKET() const
	{
		return sock;
	}
	operator SOCKET*()
	{
		return &sock;
	}
	const struct sockaddr_in* GetLocal() EXCEPTION_DEF(throw (SocketException));
	IPAddr LocalIP() EXCEPTION_DEF(throw (SocketException));
	const int LocalPort() EXCEPTION_DEF(throw (SocketException));

	// static member for resolving Names or IP-s to names
	static const char* HostName(const char*) EXCEPTION_DEF(throw (SocketException));
	static const char* LocalHostName() EXCEPTION_DEF(throw ());
	static const int AliasCount(const char*) EXCEPTION_DEF(throw (SocketException));
	static const char* Alias(const char*, const int = 1) EXCEPTION_DEF(throw (SocketException));
	static const int IPCount(const char*) EXCEPTION_DEF(throw (SocketException));
	static IPAddr IPAddress(const char*, const int = 1) EXCEPTION_DEF(throw (SocketException));
	static const int ServByName(const char*, const char* = "tcp") EXCEPTION_DEF(throw (ServNotFound));

	// setRegister: if parameter is true register all sockets that are created; closeAllSockets will
	//   not work for unregistered sockets
#ifdef _REENTRANT
	static void setRegister(bool) EXCEPTION_DEF(throw (ExMutex));
	static bool getRegister() EXCEPTION_DEF(throw (ExMutex));

	// closeAllSockets: needed for closing registered sockets in case a signal was received
	static void closeAllSockets() EXCEPTION_DEF(throw (ExMutex));
#else
	static void setRegister(bool) EXCEPTION_DEF(throw ());
	static bool getRegister() EXCEPTION_DEF(throw ());

	// closeAllSockets: needed for closing registered sockets in case a signal was received
	static void closeAllSockets() EXCEPTION_DEF(throw ());
#endif
};

// Class CConnectedSocket - connected type of sockets - either UDP or TCP
class CConnectedSocket : public CSocket
{
public:
	CConnectedSocket(int type, int domain, int protocol = 0)
	EXCEPTION_DEF(throw (SocketErrorException)) : CSocket(type, domain, protocol)
	{}
	CConnectedSocket(SOCKET s) : CSocket(s)
	{}

	virtual int IsConnected() EXCEPTION_DEF(throw (SocketErrorException));
	const struct sockaddr_in* GetRemote() EXCEPTION_DEF(throw (SocketErrorException));
	char* RemoteIP() EXCEPTION_DEF(throw (SocketErrorException));
	int RemotePort() EXCEPTION_DEF(throw (SocketErrorException));
	virtual int Connect(const char* remote_addr, int port) = 0;
	virtual int Send(const char* message, int len, int flags = 0)
	EXCEPTION_DEF(throw (SocketErrorException));
	virtual int Recv(char* buffer, int len, int flags = 0)
	EXCEPTION_DEF(throw (SocketErrorException));
	void dummy()
	{}
};

// Class CTCPSocket - tcp sockets
class CTCPSocket : public CConnectedSocket
{
public:
	CTCPSocket(char* local_ip = "0.0.0.0", int lport = 0, int backlog = 5)
	EXCEPTION_DEF(throw (SocketErrorException));
	CTCPSocket(SOCKET s) : CConnectedSocket(s)
	{}

	virtual int Connect(const char* remote, int port) EXCEPTION_DEF(throw (SocketErrorException));
	void dummy()
	{}
};

// Class CServerSocket - tcp sockets
class CServerSocket : public CTCPSocket
{
public:
	CServerSocket(char* local_ip = "0.0.0.0", int lport = 0, int backlog = 5)
	EXCEPTION_DEF(throw (SocketErrorException)) : CTCPSocket(local_ip, lport, backlog)
	{}

	virtual CTCPSocket* Accept() EXCEPTION_DEF(throw (SocketErrorException));
	void dummy()
	{}
};

// Class CUDPSocket - udp sockets
class CUDPSocket : public CSocket
{
public:
	CUDPSocket(const char* local_ip = "0.0.0.0", const int lport = 0, int backlog = 5)
	EXCEPTION_DEF(throw (SocketErrorException));
	virtual int SendTo(const char* message, int len, const char* host, unsigned int port,
	                   int flags = 0) EXCEPTION_DEF(throw (SocketErrorException));
	virtual int RecvFrom(char* buffer, int len, int flags = 0, char* fromIP = NULL,
	                     unsigned int* fromport = NULL) EXCEPTION_DEF(throw (SocketErrorException));
	void dummy()
	{}
};

// Class CTCPSocket - tcp sockets
class CUDPConnSocket : public CConnectedSocket
{
public:
	CUDPConnSocket(char* local_ip = "0.0.0.0", int lport = 0)
	EXCEPTION_DEF(throw (SocketErrorException));
	virtual int Connect(const char* remote_addr, int port) EXCEPTION_DEF(throw (SocketErrorException));
	//virtual int Reconnect(char *remote_addr,int port) EXCEPTION_DEF(throw (SocketErrorException));
};

#endif
