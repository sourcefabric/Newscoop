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

Define socket exception classes

******************************************************************************/

#ifndef _EXCEPTIONS_H
#define _EXCEPTIONS_H

#include <errno.h>

#define EXCEPTION_DEF(arg)
#define THROW_EX(arg)
#define TRY
#define CATCH(arg)

#define _EXCEPTIONS_
#ifdef _EXCEPTIONS_

#undef EXCEPTION_DEF
#define EXCEPTION_DEF(arg) arg

#define OPEN_TRY try {
#define CLOSE_TRY }

#undef THROW_EX
#define THROW_EX(arg) arg

#undef CATCH
#define CATCH(arg) catch(arg) {

#define END_CATCH }

#endif

/////////////////////////////////////////////////////////////////////////////////////
class SocketException
{
protected:
	const char *msg;
public:
	SocketException(const char* message = ""): msg(message)
	{}
	virtual ~SocketException()
	{}

	virtual const char* Message() const
	{
		return msg;
	}
};

//////////////////////////////////////////////////////////////////////////////////////////
class SocketErrorException : public SocketException
{
protected:
	const int m_errno;
public:
	SocketErrorException(const char* message = "", const int err = 0)
			: SocketException(message), m_errno(err)
	{}

	const int ErrorCode() const
	{
		return m_errno;
	}
};

///////////////////////////////////////////////////////////////////////////////////////////
class ConnectException : public SocketErrorException
{
protected:
	const char* address;
	const int port;

public:
	ConnectException(const char* message, const char* addr, const int mport, const int err)
			: SocketErrorException(message, err), address(addr), port(mport)
	{}

	virtual const char* Host() const
	{
		return address;
	}
	virtual const int Port() const
	{
		return port;
	}
};

///////////////////////////////////////////////////////////////////////////////////////////
class AddressAlreadyInUse : public ConnectException
{
public:
	AddressAlreadyInUse(const char* message, const char* addr, const int mport)
			: ConnectException(message, addr, mport, EADDRINUSE)
	{}
};

///////////////////////////////////////////////////////////////////////////////////////////
class ConnectRefused : public ConnectException
{
public:
	ConnectRefused(const char* message, const char* addr, const int mport)
			: ConnectException(message, addr, mport, ECONNREFUSED)
	{}
};

///////////////////////////////////////////////////////////////////////////////////////////
class NetworkUnreachable : public ConnectException
{
public:
	NetworkUnreachable(const char* message, const char* addr)
			: ConnectException(message, addr, 0, ENETUNREACH)
	{}
};

///////////////////////////////////////////////////////////////////////////////////////////
class ConnectTimeout : public ConnectException
{
public:
	ConnectTimeout(const char* message, const char* addr, const int mport)
			: ConnectException(message, addr, mport, ETIMEDOUT)
	{}
};

///////////////////////////////////////////////////////////////////////////////////////////
class AlreadyConnected : public ConnectException
{
public:
	AlreadyConnected(const char* message, const char* addr, const int mport)
			: ConnectException(message, addr, mport, EISCONN)
	{}
};

///////////////////////////////////////////////////////////////////////////////////////////
class NotConnected : public SocketErrorException
{
public:
	NotConnected(const char* message = "") : SocketErrorException(message, ENOTCONN)
	{}
};

///////////////////////////////////////////////////////////////////////////////////////////
class MalformedAddress: public SocketException
{
protected:
	const char* host;

public:
	MalformedAddress(const char* message, const char* m_host)
			: SocketException(message), host(m_host)
	{}

	const char* Host() const
	{
		return host;
	}
};

///////////////////////////////////////////////////////////////////////////////////////////

class HostNotFound: public SocketErrorException
{
protected:
	const char* host;

public:
	HostNotFound(const char* message, const char* m_host, int err)
			: SocketErrorException(message, err), host(m_host)
	{}

	const char* Host() const
	{
		return host;
	}
};

///////////////////////////////////////////////////////////////////////////////////////////
class AddressRange : public SocketException
{
protected:
	const int index;

public:
	AddressRange(const char* message, const int mindex) : SocketException(message), index(mindex)
	{}

	virtual const int Index() const
	{
		return index;
	}
};

/////////////////////////////////////////////////////////////////////////////////////////////////

class ServNotFound : public SocketException
{
protected:
	const char* service;
	const char* protocol;

public:
	ServNotFound(const char* message, const char* serv, const char* prot)
			: SocketException(message), service(serv), protocol(prot)
	{}

	const char* Service() const
	{
		return service;
	}
	const char* Protocol() const
	{
		return protocol;
	}
};

#endif
