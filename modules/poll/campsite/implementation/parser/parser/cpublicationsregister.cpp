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


#include <iostream>
using std::endl;


#include "cpublication.h"
#include "cpublicationsregister.h"
#include "globals.h"


CPublicationsRegister g_coPublicationsRegister;

ostream *g_pcoAliasDebug;

CPublicationsRegister::CPublicationsRegister()
{
#ifdef _DEBUG_ALIAS
	m_pcoAliasDebug = &g_coDebug;
#else
	m_pcoAliasDebug = &g_coNoDebug;
#endif
}


const CPublication* CPublicationsRegister::getPublication(const string& p_rcoAlias) const
		throw (out_of_range, ExMutex)
{
#ifdef _REENTRANT
	*m_pcoAliasDebug << debugHeader("CPublicationsRegister::getPublication")
			<< "begin; mutex: " << &m_coMutex << endl;
#ifdef _DEBUG_ALIAS
	CMutexHandler coLockHandler(&m_coMutex, true);
#else
	CMutexHandler coLockHandler(&m_coMutex, false);
#endif
	*m_pcoAliasDebug << debugHeader("CPublicationsRegister::getPublication")
			<< "mutex acquired" << endl;
#endif

#ifdef _DEBUG_ALIAS
	CPublicationsAliases::const_iterator coIt1 = m_coAliases.begin();
	for (; coIt1 != m_coAliases.end(); ++coIt1)
	{
		*m_pcoAliasDebug << debugHeader("CPublicationsRegister::getPublication")
				<< "alias: " << (*coIt1).first << endl;
	}
#endif

	CPublicationsAliases::const_iterator coIt = m_coAliases.find(p_rcoAlias);
	if (coIt == m_coAliases.end())
	{
		*m_pcoAliasDebug << debugHeader("CPublicationsRegister::getPublication")
				<< "alias not found" << endl;
		throw out_of_range(string("invalid publication alias ") + p_rcoAlias);
	}
	CPublicationsMap::const_iterator coIt2 = m_coPublications.find((*coIt).second);
	if (coIt2 == m_coPublications.end())
	{
		*m_pcoAliasDebug << debugHeader("CPublicationsRegister::getPublication")
				<< "internal error; alias " << (*coIt).first
				<< " found but the pub id " << (*coIt).second << " is missing" << endl;
		throw out_of_range(string("internal error: publication missing for alias ")
				+ p_rcoAlias);
	}
	*m_pcoAliasDebug << debugHeader("CPublicationsRegister::getPublication")
			<< "found alias " << p_rcoAlias << " with pub id "
			<< (*coIt2).second->getId() << endl;
	return (*coIt2).second;
}


void CPublicationsRegister::insert(CPublication& p_rcoPublication)
{
#ifdef _REENTRANT
	*m_pcoAliasDebug << debugHeader("CPublicationsRegister::insert")
			<< "begin; mutex: " << &m_coMutex << endl;
#ifdef _DEBUG_ALIAS
	CMutexHandler coLockHandler(&m_coMutex, true);
#else
	CMutexHandler coLockHandler(&m_coMutex, false);
#endif
	*m_pcoAliasDebug << debugHeader("CPublicationsRegister::insert")
			<< "mutex handler" << endl;
#endif
	*m_pcoAliasDebug << debugHeader("CPublicationsRegister::insert")
			<< "erasing pub id " << p_rcoPublication.getId() << endl;
	erase(p_rcoPublication.getId());
	*m_pcoAliasDebug << debugHeader("CPublicationsRegister::insert")
			<< "insert pub id " << p_rcoPublication.getId() << endl;
	m_coPublications[p_rcoPublication.getId()] = &p_rcoPublication;
	const StringSet& rcoAliases = p_rcoPublication.getAliases();
	for (StringSet::const_iterator coIt = rcoAliases.begin(); coIt != rcoAliases.end(); ++coIt)
	{
		m_coAliases[*coIt] = p_rcoPublication.getId();
		*m_pcoAliasDebug << debugHeader("CPublicationsRegister::insert") << "set alias "
				<< *coIt << " to pub id " << p_rcoPublication.getId() << endl;
	}
	return;
}

void CPublicationsRegister::erase(id_type p_nPublicationId)
{
#ifdef _REENTRANT
	CMutexHandler coLockHandler(&m_coMutex);
#endif
	CPublicationsMap::const_iterator coIt2 = m_coPublications.find(p_nPublicationId);
	if (coIt2 == m_coPublications.end())
		return;
	const CPublication* pcoPublication = (*coIt2).second;
	if (pcoPublication == NULL)
		return;
	const StringSet& rcoAliases = pcoPublication->getAliases();
	for (StringSet::const_iterator coIt = rcoAliases.begin(); coIt != rcoAliases.end(); ++coIt)
	{
		m_coAliases.erase(*coIt);
	}
	m_coPublications.erase(p_nPublicationId);
	delete pcoPublication;
}
