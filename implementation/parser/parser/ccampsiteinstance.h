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

#ifndef _CMS_CAMPSITEINSTANCE
#define _CMS_CAMPSITEINSTANCE

#include <sys/types.h>

#include "readconf.h"

typedef int (*InstanceFunction) (const ConfAttrValue&);

class CCampsiteInstance
{
public:
	CCampsiteInstance(const string& p_rcoConfDir, InstanceFunction p_pInstFunc = NULL)
			throw (ConfException)
	: m_coConfDir(p_rcoConfDir), m_pInstanceFunction(p_pInstFunc), m_coAttributes("")
	{
		m_coAttributes = ReadConf();
	}

	~CCampsiteInstance() { stop(); }

	pid_t run(InstanceFunction p_pInstanceFunction) throw (RunException);

	void stop();

private:
	const ConfAttrValue& ReadConf() throw (ConfException);

private:
	pid_t m_nChildPID;
	string m_coConfDir;
	InstanceFunction m_pInstanceFunction;
	ConfAttrValue m_coAttributes;
};

#endif
