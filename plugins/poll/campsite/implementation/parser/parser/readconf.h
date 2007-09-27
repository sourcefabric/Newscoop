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

#ifndef _READCONF_H
#define _READCONF_H

#include <map>
#include <string>

#include "cms_types.h"
#include "globals.h"

using std::map;
using std::string;

class ConfException : public exception
{
public:
	ConfException(const string& p_rcoMsg) : m_coMsg(p_rcoMsg) {}

	virtual ~ConfException() throw() {}

	virtual const char* what() const throw() { return m_coMsg.c_str(); }

private:
	string m_coMsg;
};

class ConfAttrValue
{
public:
	ConfAttrValue(const string& p_rcoConfFileName) throw (ConfException);
	void open(const string& p_rcoConfFileName) throw (ConfException);
	const string& valueOf(const string& p_rcoAttribute) const throw (ConfException);

private:
	static string ReadWord(string& p_rcoLine, int& p_rnIndex);
	static bool IsDel(char p_chChar);

	map <string, string, str_case_less> m_coAttrMap;
};

inline ConfAttrValue::ConfAttrValue(const string& p_rcoConfFileName) throw (ConfException)
{
	if (p_rcoConfFileName != "")
		open(p_rcoConfFileName);
}

#endif
