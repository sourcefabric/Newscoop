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

#include <pwd.h>
#include <grp.h>

#include "ccampsiteinstance.h"

using std::cout;
using std::endl;

pid_t CCampsiteInstance::run(InstanceFunction p_pInstanceFunction) throw (RunException)
{
}

void CCampsiteInstance::stop()
{
}

const ConfAttrValue& CCampsiteInstance::ReadConf() throw (ConfException)
{
	// read parser configuration
	string coParserConfFile = m_coConfDir + "/parser_conf.php";
	m_coAttributes.open(coParserConfFile);

	// read apache configuration
	string coApacheConfFile = m_coConfDir + "/apache_conf.php";
	m_coAttributes.open(coApacheConfFile);
	struct passwd* pPwEnt = getpwnam(m_coAttributes.valueOf("APACHE_USER").c_str());
	if (pPwEnt == NULL)
		throw ConfException("Invalid user name in conf file");
	struct group* pGrEnt = getgrnam(m_coAttributes.valueOf("APACHE_GROUP").c_str());
	if (pGrEnt == NULL)
		throw ConfException("Invalid group name in conf file");

	// read database configuration
	string coDatabaseConfFile = m_coConfDir + "/database_conf.php";
	m_coAttributes.open(coDatabaseConfFile);

	return m_coAttributes;
}
