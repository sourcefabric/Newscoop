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
 
Global used types and macros
 
******************************************************************************/

#ifndef __global__
#define __global__

typedef unsigned int UInt;
typedef unsigned long ULong;
typedef unsigned long int ULInt;

typedef void* pVoid;
typedef char* pChar;
typedef const char* cpChar;

#define EXCEPTION_DEF(arg)
#define THROW_EX(arg)
#define TRY
#define CATCH(arg)

#define _EXCEPTIONS_
#ifdef _EXCEPTIONS_
#undef EXCEPTION_DEF
#define EXCEPTION_DEF(arg) arg
#undef THROW_EX
#define THROW_EX(arg) arg
#undef TRY
#define TRY try
#undef CATCH
#define CATCH(arg) catch(arg)
#endif

// Exception class; general exception thrown by functions
class Exception
{
public:
	Exception(const char* p_pchMsg) : m_pchMsg(p_pchMsg)
	{}
	virtual ~Exception()
	{}

	const char* Message() const
	{
		return m_pchMsg;
	}

private:
	const char* m_pchMsg;
};

#endif
