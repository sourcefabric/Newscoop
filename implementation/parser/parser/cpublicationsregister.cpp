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


#include "cpublication.h"
#include "cpublicationsregister.h"


CPublicationsRegister g_coPublicationsRegister;


void CPublicationsRegister::insert(CPublication& p_rcoPublication)
{
#ifdef _REENTRANT
	CMutexHandler coLockHandler(&m_coMutex);
#endif
	erase(p_rcoPublication.getId());
	m_coPublications[p_rcoPublication.getId()] = &p_rcoPublication;
	const StringSet& rcoAliases = p_rcoPublication.getAliases();
	for (StringSet::const_iterator coIt = rcoAliases.begin(); coIt != rcoAliases.end(); ++coIt)
	{
		m_coAliases[*coIt] = p_rcoPublication.getId();
	}
}

inline void CPublicationsRegister::erase(id_type p_nPublicationId)
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
