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


#include "cpublication.h"
#include "util.h"


string CPublication::getTemplate(id_type p_nLanguage, id_type p_nPublication, id_type p_nIssue,
                                 id_type p_nSection, id_type p_nArticle, MYSQL* p_DBConn,
                                 bool p_bIsPublished)
{
	if (p_nLanguage <= 0)
	{
		stringstream coSql;
		coSql << "select IdDefaultLanguage from Publications where Id = " << p_nPublication;
		CMYSQL_RES coRes;
		MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
		if (qRow == NULL)
			throw InvalidValue("publication number", ((string)Integer(p_nPublication)).c_str());
		p_nLanguage = Integer(qRow[0]);
	}
	if (p_nArticle > 0)
	{
		if (p_nIssue <= 0 || p_nSection <= 0)
		{
			stringstream coSql;
			coSql << "select NrIssue, NrSection from Articles where Number = "
			      << p_nArticle << " and IdLanguage = " << p_nLanguage;
			if (p_bIsPublished)
			{
				coSql << " and Published = 'Y'";
			}
			CMYSQL_RES coRes;
			MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
			if (qRow == NULL)
			{
				throw InvalidValue("article number", ((string)Integer(p_nArticle)).c_str());
			}
			p_nIssue = (p_nIssue > 0) ? p_nIssue : (id_type)Integer(string(qRow[0]));
			p_nSection = (p_nSection > 0) ? p_nSection : (id_type)Integer(string(qRow[1]));
		}
		return getArticleTemplate(p_nLanguage, p_nPublication, p_nIssue, p_nSection, p_DBConn);
	}
	if (p_nSection > 0)
	{
		if (p_nIssue <= 0)
		{
			stringstream coSql;
			coSql << "select max(i.Number) from Sections as s, Issues as i where "
			      << "s.IdPublication = i.IdPublication and s.IdLanguage = i.IdLanguage "
			      << "and s.IdPublication = " << p_nPublication << " and s.IdLanguage = "
			      << p_nLanguage;
			if (p_bIsPublished)
			{
				coSql << " and i.Published = 'Y'";
			}
			CMYSQL_RES coRes;
			MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
			if (qRow == NULL || qRow[0] == NULL)
			{
				throw InvalidValue("section number", ((string)Integer(p_nSection)).c_str());
			}
			p_nIssue = (id_type)Integer(string(qRow[0]));
		}
		return getSectionTemplate(p_nLanguage, p_nPublication, p_nIssue, p_nSection, p_DBConn);
	}
	if (p_nIssue <= 0)
	{
		stringstream coSql;
		coSql << "select max(Number) from Issues where IdPublication = " << p_nPublication
		      << " and IdLanguage = " << p_nLanguage;
		if (p_bIsPublished)
		{
			coSql << " and Published = 'Y'";
		}
		CMYSQL_RES coRes;
		MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
		if (qRow == NULL || qRow[0] == NULL)
		{
			throw RunException("There are no published issues.");
		}
		p_nIssue = (id_type)Integer(string(qRow[0]));
	}
	return getIssueTemplate(p_nLanguage, p_nPublication, p_nIssue, p_DBConn);
}


string CPublication::getIssueTemplate(id_type p_nLanguage, id_type p_nPublication,
									  id_type p_nIssue, MYSQL* p_DBConn)
{
	stringstream coSql;
	coSql << "select t.Name from Issues as i, Templates as t where i.IssueTplId = t.Id "
	      << "and i.IdPublication = " << p_nPublication << " and Number = " << p_nIssue
	      << " and IdLanguage = " << p_nLanguage;
	CMYSQL_RES coRes;
	MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
	if (qRow == NULL)
	{
		throw InvalidValue("issue template; set the issue template!", "(unset)");
	}
	return string(qRow[0]);
}

string CPublication::getSectionTemplate(id_type p_nLanguage, id_type p_nPublication,
										id_type p_nIssue, id_type p_nSection, MYSQL* p_DBConn)
{
	stringstream coSql;
	CMYSQL_RES coRes;
	if (p_nSection > 0)
	{
		coSql << "select t.Name from Sections as s, Templates as t where s.SectionTplId = t.Id "
		      << "and s.IdPublication = " << p_nPublication << " and NrIssue = " << p_nIssue
		      << " and IdLanguage = " << p_nLanguage << " and Number = " << p_nSection;
		MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
		if (qRow != NULL)
		{
			return string(qRow[0]);
		}
		coSql.str("");
	}
	coSql << "select t.Name from Issues as i, Templates as t where i.SectionTplId = t.Id "
	      << "and i.IdPublication = " << p_nPublication << " and Number = " << p_nIssue
	      << " and IdLanguage = " << p_nLanguage;
	MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
	if (qRow == NULL)
	{
		throw InvalidValue("section template; set the section template!", "(unset)");
	}
	return string(qRow[0]);
}

string CPublication::getArticleTemplate(id_type p_nLanguage, id_type p_nPublication,
										id_type p_nIssue, id_type p_nSection, MYSQL* p_DBConn)
{
	stringstream coSql;
	CMYSQL_RES coRes;
	if (p_nSection > 0)
	{
		coSql << "select t.Name from Sections as s, Templates as t where s.ArticleTplId = t.Id "
		      << "and s.IdPublication = " << p_nPublication << " and NrIssue = " << p_nIssue
		      << " and IdLanguage = " << p_nLanguage << " and Number = " << p_nSection;
		MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
		if (qRow != NULL)
		{
			return string(qRow[0]);
		}
		coSql.str("");
	}
	coSql << "select t.Name from Issues as i, Templates as t where i.ArticleTplId = t.Id "
	      << "and i.IdPublication = " << p_nPublication << " and Number = " << p_nIssue
	      << " and IdLanguage = " << p_nLanguage;
	MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
	if (qRow == NULL)
	{
		throw InvalidValue("article template; set the article template!", "(unset)");
	}
	return string(qRow[0]);
}


id_type CPublication::getTemplateId(const string& p_rcoTemplate, MYSQL* p_DBConn)
		throw(InvalidValue)
{
	stringstream coSql;
	coSql << "select Id from Templates where Name = '" << p_rcoTemplate << "'";
	CMYSQL_RES coRes;
	MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
	if (qRow == NULL)
	{
		throw InvalidValue("template name", p_rcoTemplate);
	}
	return Integer(qRow[0]);
}


void CPublication::BuildFromDB(id_type p_nId, MYSQL* p_DBConn) throw(InvalidValue)
{
	m_nId = p_nId;

	// read the publication default language and URL type
	stringstream coSql;
	coSql << "select p.IdDefaultLanguage, ut.Name, p.IdDefaultAlias "
			"from Publications as p, URLTypes as ut "
			"where p.IdURLType = ut.Id and p.Id = " << p_nId;
	CMYSQL_RES coRes;
	MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
	if (qRow == NULL)
	{
		throw InvalidValue("publication identifier", ((string)Integer(p_nId)).c_str());
	}
	m_nIdLanguage = Integer(qRow[0]);
	m_coURLTypeName = qRow[1];
	lint nDefaultAliasId = strtol(qRow[2], 0, 10);

	// read publication aliases
	coSql.str("");
	coSql << "select Name, Id from Aliases where IdPublication = " << p_nId;
	qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
	if (qRow == NULL)
	{
		throw InvalidValue("publication identifier", ((string)Integer(p_nId)).c_str());
	}
	while (qRow != NULL)
	{
		addAlias(qRow[0]);
		if (strtol(qRow[1], 0, 10) == nDefaultAliasId)
		{
			m_coDefaultAlias = qRow[0];
		}
		qRow = mysql_fetch_row(*coRes);
	}
}

bool CPublication::isValidIssue(id_type p_nLanguage, id_type p_nPublication, id_type p_nIssue,
								MYSQL* p_DBConn)
{
	stringstream coSql;
	coSql << "select Number from Issues where IdPublication = " << p_nPublication
			<< " and Number = " << p_nIssue << " and IdLanguage = " << p_nLanguage;
	CMYSQL_RES coRes;
	MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
	return (qRow != NULL);
}

bool CPublication::isValidSection(id_type p_nLanguage, id_type p_nPublication, id_type p_nIssue,
								  id_type p_nSection, MYSQL* p_DBConn)
{
	stringstream coSql;
	coSql << "select Number from Sections where IdPublication = " << p_nPublication
			<< " and NrIssue = " << p_nIssue << " and IdLanguage = " << p_nLanguage
			<< " and Number = " << p_nSection;
	CMYSQL_RES coRes;
	MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
	return (qRow != NULL);
}

bool CPublication::isValidArticle(id_type p_nLanguage, id_type p_nPublication, id_type p_nIssue,
								  id_type p_nSection, id_type p_nArticle, MYSQL* p_DBConn)
{
	stringstream coSql;
	coSql << "select Number from Articles where IdPublication = " << p_nPublication
			<< " and NrIssue = " << p_nIssue << " and NrSection = " << p_nSection
			<< " and Number = " << p_nArticle << " and IdLanguage = " << p_nLanguage;
	CMYSQL_RES coRes;
	MYSQL_ROW qRow = QueryFetchRow(p_DBConn, coSql.str().c_str(), coRes);
	return (qRow != NULL);
}
