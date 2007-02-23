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

Global types

******************************************************************************/

#ifndef _CMS_GLOBALS
#define _CMS_GLOBALS

#include <stdexcept>

#undef _REENTRANT

using std::ostream;
using std::exception;
using std::string;

typedef unsigned int uint;
typedef unsigned long int ulint;
typedef long int lint;
typedef lint id_type;

// exception classes
class InvalidValue : public exception
{
public:
	InvalidValue(const char* p_pchName = NULL, const char* p_pchValue = NULL);

	InvalidValue(const string& p_rcoName, const string& p_rcoValue);

	~InvalidValue() throw () {}

	virtual const char* what () const throw () { return m_coMsg.c_str(); }

private:
	string m_coMsg;
};

inline InvalidValue::InvalidValue(const char* p_pchName, const char* p_pchValue)
{
	m_coMsg = string("Invalid value");
	if (p_pchValue != NULL)
		m_coMsg += string(" \"") + p_pchValue + "\""; 
	if (p_pchName != NULL)
		m_coMsg += string(" of \"") + p_pchName + "\""; 
}

inline InvalidValue::InvalidValue(const string& p_rcoName, const string& p_rcoValue)
{
	m_coMsg = string("Invalid value \"") + p_rcoName + "\" of \"" + p_rcoValue + "\"";
}


// other useful functions
string int2string(int p_nValue);
string long2string(lint p_nValue);
string uint2string(int p_nValue);
string ulong2string(ulint p_nValue);

#include <sstream>

using std::stringstream;

// other useful functions
inline string int2string(int p_nValue)
{
	stringstream coStr("");
	coStr << p_nValue;
	return coStr.str();
}

inline string long2string(lint p_nValue)
{
	stringstream coStr("");
	coStr << p_nValue;
	return coStr.str();
}

inline string uint2string(int p_nValue)
{
	stringstream coStr("");
	coStr << p_nValue;
	return coStr.str();
}

inline string ulong2string(ulint p_nValue)
{
	stringstream coStr("");
	coStr << p_nValue;
	return coStr.str();
}

extern ostream g_coDebug;
extern ostream g_coNoDebug;

struct DebugHeaderContent {
	ostream& (*m_pDebugMethod)(ostream&, const char*);
	const char* m_pchString;
	
	DebugHeaderContent(ostream& (*p_pDebugMethod)(ostream&, const char* p_pchString),
				const char* p_pchString)
	: m_pDebugMethod(p_pDebugMethod), m_pchString(p_pchString) {}
};

inline ostream& operator << (ostream& p_rOutStream,
							 DebugHeaderContent p_rDebugHeaderContent)
{
	return p_rDebugHeaderContent.m_pDebugMethod(p_rOutStream,
			p_rDebugHeaderContent.m_pchString);
}

inline ostream& outDebugHeader(ostream& p_rcoOutStream, const char* p_pchString)
{
	if (p_pchString != "")
	{
		p_rcoOutStream << p_pchString << " ";
	}
#ifdef _REENTRANT
	p_rcoOutStream << "(th: " << pthread_self() << ") ";
#endif
	return p_rcoOutStream;
}

inline DebugHeaderContent debugHeader(const string& p_rcoString)
{
	return DebugHeaderContent(outDebugHeader, p_rcoString.c_str());
}

inline DebugHeaderContent debugHeader(const char* p_pchString)
{
	return DebugHeaderContent(outDebugHeader, p_pchString);
}

#endif
