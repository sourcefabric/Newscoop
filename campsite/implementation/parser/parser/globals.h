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
#include <functional>

using std::exception;
using std::string;
using std::binary_function;

typedef unsigned int UInt;
typedef unsigned long int ULInt;

// exception classes
class InvalidValue : public exception
{
public:
	virtual const char* what () const throw () { return "invalid value"; }
};

inline int case_comp(const string& p_rcoS1, const string& p_rcoS2)
{
	return strcasecmp(p_rcoS1.c_str(), p_rcoS2.c_str());
}

inline int case_comp(const string& p_rcoS1, const string& p_rcoS2, int len)
{
	return strcasecmp(p_rcoS1.substr(0, len).c_str(), p_rcoS2.substr(0, len).c_str());
}

struct str_case_less : public binary_function<string, string, bool>
{
	bool operator ()(const string& first, const string& second) const
	{ return case_comp(first, second) < 0; }
};

#endif
