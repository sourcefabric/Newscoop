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


#ifndef CPUBLICATION_H
#define CPUBLICATION_H


#include <string>
#include <set>


#include "curltyperegister.h"
#include "cpublicationsregister.h"


using std::string;
using std::set;


typedef set <string, str_case_less> StringSet;


/**
  * class CPublication
  * publication metadata class; static object
  */
class CPublication
{
public:
	CPublication(long p_nId, MYSQL* p_DBConn);

	long getId() const { return m_nId; }

	long getLanguageId() const { return m_nIdLanguage; }

	void addAlias(const string& p_rcoAlias) { m_coAliases.insert(p_rcoAlias); }

	bool hasAlias(const string& p_rcoAlias) const
		{ return m_coAliases.find(p_rcoAlias) != m_coAliases.end(); }

	void deleteAlias(const string& p_rcoAlias) { m_coAliases.erase(p_rcoAlias); }

	const StringSet& getAliases() const { return m_coAliases; }

	void setURLType(const string& p_rcoURLTypeName);

	const CURLType* getURLType() const;

	const string& getURLTypeName() const { return m_coURLTypeName; }

private:
	long m_nId;
	long m_nIdLanguage;
	StringSet m_coAliases;
	string m_coURLTypeName;

private:
	void BuildFromDB(long p_nId, MYSQL* p_DBConn) throw(InvalidValue);
};


// CPublication inline methods

inline const CURLType* CPublication::getURLType() const
{
	return CURLTypeRegister::getInstance().getURLType(m_coURLTypeName);
}

inline CPublication::CPublication(long p_nId, MYSQL* p_DBConn)
{
	BuildFromDB(p_nId, p_DBConn); 
	CPublicationsRegister::getInstance().insert(*this);
}

inline void CPublication::setURLType(const string& p_rcoURLTypeName)
{
	if (!CURLTypeRegister::getInstance().has(p_rcoURLTypeName))
		throw InvalidValue();
	m_coURLTypeName = p_rcoURLTypeName;
}


#endif // CPUBLICATION_H
