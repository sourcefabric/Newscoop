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

Defines the classes used for implementation

******************************************************************************/

#ifndef _CMS_ATOMS_IMPL
#define _CMS_ATOMS_IMPL

#include <map>

using std::map;
using std::string;

#include "atoms.h"

class CAttributeMap : public map <string, CAttribute*, str_case_less>
{
public:
	CAttributeMap() {}

	CAttributeMap(const CAttributeMap& o) { *this = o; }

	~CAttributeMap() { clear(); }

	const CAttributeMap& operator =(const CAttributeMap&);

	bool operator ==(const CAttributeMap&) const;

	bool operator !=(const CAttributeMap& o) const { return ! (*this == o); }

	void clear();
};

class CStatementContextMap : public map<int, CStatementContext*>
{
public:
	CStatementContextMap() {}

	~CStatementContextMap() { clear(); }

	CStatementContextMap(const CStatementContextMap& o) { *this = o; }

	const CStatementContextMap& operator =(const CStatementContextMap&);

	bool operator ==(const CStatementContextMap&) const;

	bool operator !=(const CStatementContextMap& o) const { return ! (*this == o); }

	void clear();
};

class CTypeAttributesMap : public map<string, CTypeAttributes*, str_case_less>
{
public:
	CTypeAttributesMap() {}

	~CTypeAttributesMap() { clear(); }

	CTypeAttributesMap(const CTypeAttributesMap& o) { *this = o; }

	const CTypeAttributesMap& operator =(const CTypeAttributesMap&);

	bool operator ==(const CTypeAttributesMap&) const;

	bool operator !=(const CTypeAttributesMap& o) const { return ! (*this == o); }

	void clear();
};

#endif
