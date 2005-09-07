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


#ifndef CURLTYPEREGISTER_H
#define CURLTYPEREGISTER_H


#include <string>
#include <map>


#include "curltype.h"


using std::string;
using std::map;
using std::less;


typedef map <string, CURLType*, less<string> > CURLTypeMap;


/**
  * class CURLTypeRegister
  * stores all CURLType objects; static object
  */

class CURLTypeRegister
{
public:
	CURLTypeRegister() {}

	static CURLTypeRegister& getInstance();

	void insert(CURLType& p_rcoURLType);

	void erase(const string& p_rcoURLTypeName);

	bool has(const string& p_rcoURLTypeName) const;

	const CURLType* getURLType(const string& p_rcoURLTypeName) const throw (InvalidValue);

private:
	CURLTypeMap m_coCURLTypes;

#ifdef _REENTRANT
	mutable CMutex m_coMutex;
#endif
};


// CURLTypeRegister inline methods

inline void CURLTypeRegister::insert(CURLType& p_rcoURLType)
{
#ifdef _REENTRANT
	CMutexHandler coLockHandler(&m_coMutex);
#endif
	m_coCURLTypes[p_rcoURLType.getTypeName()] = &p_rcoURLType;
}

inline void CURLTypeRegister::erase(const string& p_rcoURLTypeName)
{
#ifdef _REENTRANT
	CMutexHandler coLockHandler(&m_coMutex);
#endif
	CURLTypeMap::const_iterator coIt = m_coCURLTypes.find(p_rcoURLTypeName);
	if (coIt == m_coCURLTypes.end())
		return;
	CURLType* pcoURLType = (*coIt).second;
	m_coCURLTypes.erase(p_rcoURLTypeName);
	delete pcoURLType;
}

inline bool CURLTypeRegister::has(const string& p_rcoURLTypeName) const
{
#ifdef _REENTRANT
	CMutexHandler coLockHandler(&m_coMutex);
#endif
	return m_coCURLTypes.find(p_rcoURLTypeName) != m_coCURLTypes.end();
}

inline const CURLType* CURLTypeRegister::getURLType(const string& p_rcoURLTypeName) const
	throw (InvalidValue)
{
#ifdef _REENTRANT
	CMutexHandler coLockHandler(&m_coMutex);
#endif
	CURLTypeMap::const_iterator coIt = m_coCURLTypes.find(p_rcoURLTypeName);
	if (coIt == m_coCURLTypes.end())
		throw InvalidValue("URL type", p_rcoURLTypeName);
	return (*coIt).second;
}


#endif // CURLTYPEREGISTER_H
