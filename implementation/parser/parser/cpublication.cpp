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


#include <sstream>
#include <iostream>


#include "cpublication.h"
#include "util.h"


using std::cout;
using std::endl;


void CPublication::BuildFromDB(long p_nId, MYSQL* p_DBConn) throw(InvalidValue)
{
	m_nId = p_nId;

	// read the publication default language and URL type
	stringstream coSql;
	coSql << "select IdDefaultLanguage, ut.Name from Publications as p, URLTypes as ut "
	         "where p.IdURLType = ut.Id and p.Id = " << p_nId;
	CMYSQL_RES coRes;
	MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
	if (qRow == NULL)
		throw InvalidValue("publication identifier", ((string)Integer(p_nId)).c_str());
	m_nIdLanguage = Integer(qRow[0]);
	m_coURLTypeName = qRow[1];

	// read publication aliases
	coSql.str("");
	coSql << "select Name from Aliases where IdPublication = " << p_nId;
	qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
	if (qRow == NULL)
		throw InvalidValue("publication identifier", ((string)Integer(p_nId)).c_str());
	while (qRow != NULL)
	{
		addAlias(qRow[0]);
		qRow = mysql_fetch_row(*coRes);
	}
}
