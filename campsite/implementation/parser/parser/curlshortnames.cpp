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


#include "util.h"
#include "curlshortnames.h"
#include "data_types.h"
#include "util.h"
#include "cpublication.h"


// CURLShortNames(): copy constructor
CURLShortNames::CURLShortNames(const CURLShortNames& p_rcoSrc)
	: CURL(p_rcoSrc)
{
	m_bValidURI = p_rcoSrc.m_bValidURI;
	m_coURIPath = p_rcoSrc.m_coURIPath;
	m_coQueryString = p_rcoSrc.m_coQueryString;
	m_pDBConn = p_rcoSrc.m_pDBConn;
	m_coHTTPHost = p_rcoSrc.m_coHTTPHost;
	m_coTemplate = p_rcoSrc.m_coTemplate;
	m_bLockTemplate = p_rcoSrc.m_bLockTemplate;
	m_bValidTemplate = p_rcoSrc.m_bValidTemplate;
}


// setURL(): sets the URL object value
void CURLShortNames::setURL(const CMsgURLRequest& p_rcoURLMessage, bool p_bLockTemplate)
{
	m_coTemplate = "";
	m_bValidTemplate = false;
	m_bLockTemplate = p_bLockTemplate;
	m_coDocumentRoot = p_rcoURLMessage.getDocumentRoot();
	m_coPathTranslated = p_rcoURLMessage.getPathTranslated();
	m_coHTTPHost = p_rcoURLMessage.getHTTPHost();
	string coURI = p_rcoURLMessage.getReqestURI();
	m_bValidURI = true;

	CMYSQL_RES coRes;
	string coQuery = string("select IdPublication from Aliases where Name = '")
	               + m_coHTTPHost + "'";
	MYSQL_ROW qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
	if (qRow == NULL)
		throw InvalidValue("site alias", m_coHTTPHost.c_str());
	id_type nPublication = Integer(qRow[0]);
	setPublication(nPublication);

	// prepare the path string
	string::size_type nQMark = coURI.find('?');
	m_coURIPath = (nQMark != string::npos) ? coURI.substr(0, nQMark) : coURI;
	m_coQueryString = (nQMark != string::npos) ? coURI.substr(nQMark) : "";

	// declare variables to store publication parameters found as short names in URI
	string coLangCode, coIssue, coSection, coArticle;

	// indexes in the URI string
	string::size_type nCurrent = (m_coURIPath[0] == '/') ? 1 : 0;
	string::size_type nNext = m_coURIPath.find('/', nCurrent);
	nNext = nNext == string::npos ? m_coURIPath.size() + 1 : nNext;

	// read the language code
	coLangCode = m_coURIPath.substr(nCurrent, nNext - nCurrent);

	if ("" != coLangCode)
		// query the database for the language code
		coQuery = string("select Id from Languages where Code = '") + coLangCode + "'";
	else
		// read the default publication language
		coQuery = string("select IdDefaultLanguage from Publications where Id = ")
		        + getValue(P_IDPUBL);
	qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
	if (qRow == NULL)
		throw InvalidValue("language code", coLangCode.c_str());
	id_type nLanguage = Integer(qRow[0]);
	if ("" == m_coURIPath || "/" == m_coURIPath || "" != coLangCode)
		setLanguage(nLanguage);

	if (nNext < (m_coURIPath.size() - 1))
	{
		// read the issue short name
		nCurrent = nNext + 1;
		nNext = m_coURIPath.find('/', nCurrent);
		nNext = nNext == string::npos ? m_coURIPath.size() + 1 : nNext;
		coIssue = m_coURIPath.substr(nCurrent, nNext - nCurrent);
	}

	id_type nIssue = -1;
	// query the database for the issue
	if (coIssue != "")
	{
		coQuery = string("select Number from Issues where IdPublication = ")
		        + getValue(P_IDPUBL) + " and IdLanguage = " + getValue(P_IDLANG)
		        + " and ShortName = '" + coIssue + "'";
		qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
		if (qRow == NULL)
			throw InvalidValue("issue short name", coIssue.c_str());
		nIssue = Integer(qRow[0]);
		setIssue(nIssue);
	}
	else if ("" == m_coURIPath || "/" == m_coURIPath)
	{
		coQuery = string("select max(Number) from Issues where IdPublication = ")
		        + (string)Integer(nPublication) + " and IdLanguage = "
		        + (string)Integer(nLanguage) + " and Published = 'Y'";
		qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
		if (qRow != NULL)
		{
			nIssue = Integer(qRow[0]);
			setIssue(nIssue);
		}
	}

	if (nNext < (m_coURIPath.size() - 1))
	{
		// read the section short name
		nCurrent = nNext + 1;
		nNext = m_coURIPath.find('/', nCurrent);
		nNext = nNext == string::npos ? m_coURIPath.size() + 1 : nNext;
		coSection = m_coURIPath.substr(nCurrent, nNext - nCurrent);
	}

	string coIssueCond = nIssue != -1 ? string(" and NrIssue = ") + getValue(P_NRISSUE) : "";
	id_type nSection = -1;
	// query the database for the section
	if (coSection != "")
	{
		coQuery = string("select Number from Sections where IdPublication = ")
		        + getValue(P_IDPUBL) + coIssueCond + " and IdLanguage = " + getValue(P_IDLANG)
		        + " and ShortName = '" + coSection + "'";
		qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
		if (qRow == NULL)
			throw InvalidValue("section short name", coSection.c_str());
		nSection = Integer(qRow[0]);
		setSection(nSection);
	}

	if (nNext < (m_coURIPath.size() - 1))
	{
		// read the article short name
		nCurrent = nNext + 1;
		nNext = m_coURIPath.find('/', nCurrent);
		nNext = nNext == string::npos ? m_coURIPath.size() + 1 : nNext;
		coArticle = m_coURIPath.substr(nCurrent, nNext - nCurrent);
	}

	string coSectCond = nSection != -1 ? string(" and NrSection = ") + getValue(P_NRSECTION) : "";
	id_type nArticle = -1;
	// query the database for the section
	if (coArticle != "")
	{
		coQuery = string("select Number from Articles where IdPublication = ")
		        + getValue(P_IDPUBL) + coIssueCond + coSectCond
		        + " and IdLanguage = " + getValue(P_IDLANG)
		        + " and ShortName = '" + coArticle + "'";
		qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
		if (qRow == NULL)
			throw InvalidValue("article short name", coArticle.c_str());
		nArticle = Integer(qRow[0]);
		setArticle(nArticle);
	}

	// read parameters
	const String2Value& coParams = p_rcoURLMessage.getParameters().getMap();
	String2Value::const_iterator coIt = coParams.begin();
	for (; coIt != coParams.end(); ++coIt)
		setValue((*coIt).first, (*coIt).second->asString());

	// read cookies
	const String2String& coCookies = p_rcoURLMessage.getCookies();
	String2String::const_iterator coIt2 = coCookies.begin();
	for (; coIt2 != coCookies.end(); ++coIt2)
		setCookie((*coIt2).first, (*coIt2).second);
}

// getQueryString(): returns the query string
string CURLShortNames::getQueryString() const
{
	string coQueryString;
	String2String::const_iterator coIt = m_coParamMap.begin();
	for (; coIt != m_coParamMap.end(); ++coIt)
	{
		string coParam = (*coIt).first;
		if (coParam != P_IDLANG && coParam != P_IDPUBL && coParam != P_NRISSUE
			&& coParam != P_NRSECTION && coParam != P_NRARTICLE)
		{
			const char* pchValue = EscapeURL((*coIt).second.c_str());
			if (coIt != m_coParamMap.begin())
				coQueryString += "&";
			coQueryString += coParam + "=" + pchValue;
			delete []pchValue;
		}
	}
	return coQueryString;
}

string CURLShortNames::getFormString() const
{
	string coFormString;
	String2String::const_iterator coIt = m_coParamMap.begin();
	for (; coIt != m_coParamMap.end(); ++coIt)
	{
		string coParam = (*coIt).first;
		if (coParam != P_IDLANG && coParam != P_IDPUBL && coParam != P_NRISSUE
			&& coParam != P_NRSECTION && coParam != P_NRARTICLE)
		{
			const char* pchValue = EscapeHTML((*coIt).second.c_str());
			coFormString += string("<input type=\"hidden\" name=\"") + coParam + "\" value=\""
			             + pchValue + "\">\n";
			delete []pchValue;
		}
	}
	return coFormString;
}

string CURLShortNames::setTemplate(const string& p_rcoTemplate) throw (InvalidValue)
{
	if (p_rcoTemplate == "")
	{
		m_bValidTemplate = false;
		m_bLockTemplate = false;
		return getTemplate();
	}

	bool bRelativePath = p_rcoTemplate[0] != '/';
	string coTemplate = p_rcoTemplate.substr(1);
	if (bRelativePath)
	{
		getTemplate();
		ulint nSlashPos = m_coTemplate.rfind('/');
		if (nSlashPos != string::npos)
			coTemplate = m_coTemplate.substr(0, nSlashPos) + "/" + p_rcoTemplate;
	}
	string coSql = string("select Id from Templates where Name = '") + coTemplate + "'";
	CMYSQL_RES coRes;
	MYSQL_ROW qRow = QueryFetchRow(m_pDBConn, coSql.c_str(), coRes);
	if (qRow == NULL)
		throw InvalidValue("template name", p_rcoTemplate.c_str());
	m_coTemplate = coTemplate;
	m_bValidTemplate = true;
	m_bLockTemplate = true;
	return m_coTemplate;
}

string CURLShortNames::setTemplate(id_type p_nTemplateId) throw (InvalidValue)
{
	string coSql = string("select Name from Templates where Id = ")
	             + (string)Integer(p_nTemplateId);
	CMYSQL_RES coRes;
	MYSQL_ROW qRow = QueryFetchRow(m_pDBConn, coSql.c_str(), coRes);
	if (qRow == NULL)
		throw InvalidValue("template identifier", (string)Integer(p_nTemplateId));
	m_coTemplate = qRow[0];
	m_bValidTemplate = true;
	m_bLockTemplate = true;
	return m_coTemplate;
}

string CURLShortNames::getTemplate() const
{
	if (m_bValidTemplate)
		return m_coTemplate;
	if (getValue(P_TEMPLATE_ID) != "")
	{
		string coSql = string("select Name from Templates where Id = ") + getValue(P_TEMPLATE_ID);
		CMYSQL_RES coRes;
		MYSQL_ROW qRow = QueryFetchRow(m_pDBConn, coSql.c_str(), coRes);
		m_coTemplate = qRow == NULL ? "" : qRow[0];
	}
	else
	{
		m_coTemplate = CPublication::getTemplate(getLanguage(), getPublication(), getIssue(),
		                                         getSection(), getArticle(), m_pDBConn, true);
	}
	m_bValidTemplate = true;
	return m_coTemplate;
}

// BuildURI(): internal method; builds the URI string from object attributes
void CURLShortNames::BuildURI() const
{
	if (m_bValidURI)
		return;

	if ("" == getValue(P_IDLANG))
	{
		m_coURIPath = "";
		m_coQueryString = getQueryString();
		m_bValidURI = true;
		return;
	}

	CMYSQL_RES coRes;
	string coQuery = string("select Code from Languages where Id = ") + getValue(P_IDLANG);
	MYSQL_ROW qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
	if (qRow == NULL)
		throw InvalidValue("language identifier", getValue(P_IDLANG));
	m_coURIPath = string("/") + qRow[0] + "/";

	if ("" == getValue(P_NRISSUE))
	{
		m_coQueryString = getQueryString();
		m_bValidURI = true;
		return;
	}

	string coIssueSN, coSectionSN, coArticleSN, coLang = getValue(P_IDLANG);
	string coPubCond, coIssueCond, coSectionCond, coArticleCond;
	if ("" != getValue(P_IDPUBL))
		coPubCond = string(" and IdPublication = ") + getValue(P_IDPUBL);
	if ("" != getValue(P_NRISSUE))
		coIssueCond = string(" and NrIssue = ") + getValue(P_NRISSUE);
	if ("" != getValue(P_NRSECTION))
		coSectionCond = string(" and NrSection = ") + getValue(P_NRSECTION);
	if ("" != getValue(P_NRARTICLE))
		coArticleCond = string(" and Number = ") + getValue(P_NRARTICLE);

	if ("" != getValue(P_NRISSUE))
	{
		coQuery = string("select ShortName from Issues where IdLanguage = ") + coLang
		        + coPubCond + " and Number = " + getValue(P_NRISSUE);
		qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
		if (qRow == NULL)
			throw InvalidValue("issue number", getValue(P_NRISSUE));
		coIssueSN = qRow[0];
	}

	if ("" != getValue(P_NRSECTION))
	{
		coQuery = string("select ShortName from Sections where IdLanguage = ") + coLang 
		        + coPubCond + coIssueCond + " and Number = " + getValue(P_NRSECTION);
		qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
		if (qRow == NULL)
			throw InvalidValue("section number", getValue(P_NRSECTION));
		coSectionSN = qRow[0];
	}

	if ("" != getValue(P_NRARTICLE))
	{
		coQuery = string("select ShortName from Articles where IdLanguage = ") + coLang 
		        + coPubCond + coIssueCond + coSectionCond + coArticleCond;
		qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
		if (qRow == NULL)
			throw InvalidValue("article number", getValue(P_NRARTICLE));
		coArticleSN = qRow[0];
	}
	m_coURIPath += coIssueSN + "/";
	if (coSectionSN != "" || coArticleSN != "")
		m_coURIPath += coSectionSN + "/";
	if (coArticleSN != "")
		m_coURIPath += coArticleSN + "/";

	m_coQueryString = getQueryString();
	m_bValidURI = true;
}
