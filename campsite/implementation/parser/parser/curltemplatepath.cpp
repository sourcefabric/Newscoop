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
using std::cout;
using std::endl;


#include "util.h"
#include "curltemplatepath.h"
#include "data_types.h"
#include "util.h"
#include "cpublication.h"


// CURLTemplatePath(): copy constructor
CURLTemplatePath::CURLTemplatePath(const CURLTemplatePath& p_rcoSrc)
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
void CURLTemplatePath::setURL(const CMsgURLRequest& p_rcoURLMessage, bool p_bLockTemplate)
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
#ifdef _DEBUG
	cout << "CURLTemplatePath alias query: " << coQuery << endl;
#endif
	if (qRow == NULL)
	{
		cout << "CURLTemplatePath query: result is NULL" << endl;
		throw InvalidValue("site alias", m_coHTTPHost.c_str());
	}
	id_type nPublication = Integer(qRow[0]);
	setPublication(nPublication);

	// prepare the path string
	string::size_type nQMark = coURI.find('?');
	m_coURIPath = (nQMark != string::npos) ? coURI.substr(0, nQMark) : coURI;
	m_coQueryString = (nQMark != string::npos) ? coURI.substr(nQMark) : "";

	// read parameters
	const String2Value& coParams = p_rcoURLMessage.getParameters().getMap();
	String2Value::const_iterator coIt = coParams.begin();
	for (; coIt != coParams.end(); ++coIt)
		if ((*coIt).first != P_IDPUBL)
			setValue((*coIt).first, (*coIt).second->asString());

	if ("" == m_coURIPath || "/" == m_coURIPath)
	{
		if (0 == getLanguage())
		{
			coQuery = string("select IdDefaultLanguage from Publications where Id = ")
					+ getValue(P_IDPUBL);
			qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
#ifdef _DEBUG
			cout << "CURLTemplatePath def lang query: " << coQuery << endl;
#endif
			if (qRow == NULL)
				throw InvalidValue("publication identifier", getValue(P_IDPUBL));
			setValue(P_IDLANG, qRow[0]);
		}
		if (0 == getIssue())
		{
			coQuery = string("select max(Number) from Issues where IdPublication = ")
					+ getValue(P_IDPUBL) + " and IdLanguage = " + getValue(P_IDLANG)
					+ " and Published = 'Y'";
			qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
			if (qRow != NULL)
				setValue(P_NRISSUE, qRow[0]); 
		}
		m_coTemplate = CPublication::getIssueTemplate(getIntValue(P_IDLANG), getIntValue(P_IDPUBL),
				getIntValue(P_NRISSUE), m_pDBConn);
	}
	else
	{
		if (strncmp(m_coURIPath.c_str(), "/look/", 6) != 0)
			throw InvalidValue("template name", m_coURIPath);
		m_coTemplate = m_coURIPath.substr(6);
		CPublication::getTemplateId(m_coTemplate, m_pDBConn);
	}
	m_bValidTemplate = true;

	// read cookies
	const String2String& coCookies = p_rcoURLMessage.getCookies();
	String2String::const_iterator coIt2 = coCookies.begin();
	for (; coIt2 != coCookies.end(); ++coIt2)
		setCookie((*coIt2).first, (*coIt2).second);
}

// getQueryString(): returns the query string
string CURLTemplatePath::getQueryString() const
{
	string coQueryString;
	String2String::const_iterator coIt = m_coParamMap.begin();
	for (; coIt != m_coParamMap.end(); ++coIt)
	{
		string coParam = (*coIt).first;
		const char* pchValue = EscapeURL((*coIt).second.c_str());
		if (coIt != m_coParamMap.begin())
			coQueryString += "&";
		coQueryString += coParam + "=" + pchValue;
		delete []pchValue;
	}
	return coQueryString;
}

string CURLTemplatePath::getFormString() const
{
	string coFormString;
	String2String::const_iterator coIt = m_coParamMap.begin();
	for (; coIt != m_coParamMap.end(); ++coIt)
	{
		string coParam = (*coIt).first;
		const char* pchValue = EscapeHTML((*coIt).second.c_str());
		coFormString += string("<input type=\"hidden\" name=\"") + coParam + "\" value=\""
		             + pchValue + "\">";
		delete []pchValue;
	}
	return coFormString;
}

string CURLTemplatePath::setTemplate(const string& p_rcoTemplate) throw (InvalidValue)
{
	m_bValidURI = false;
	if (p_rcoTemplate == "")
	{
		m_bValidTemplate = false;
		m_bLockTemplate = false;
		return getTemplate();
	}

	bool bRelativePath = p_rcoTemplate[0] != '/';
	string coTemplate;
	if (bRelativePath)
	{
		getTemplate();
		ulint nSlashPos = m_coTemplate.rfind('/');
		if (nSlashPos != string::npos)
		{
			coTemplate = m_coTemplate.substr(0, nSlashPos) + "/";
		}
		coTemplate += p_rcoTemplate;
	}
	else
	{
		coTemplate = p_rcoTemplate.substr(1);
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

string CURLTemplatePath::setTemplate(id_type p_nTemplateId) throw (InvalidValue)
{
	m_bValidURI = false;
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

string CURLTemplatePath::getTemplate() const
{
	if (m_bValidTemplate)
		return m_coTemplate;
	m_coTemplate = CPublication::getTemplate(getLanguage(), getPublication(), getIssue(),
	                                         getSection(), getArticle(), m_pDBConn, true);
	return m_coTemplate;
}

// BuildURI(): internal method; builds the URI string from object attributes
void CURLTemplatePath::BuildURI() const
{
	if (m_bValidURI)
		return;

	if (!m_bValidTemplate)
		getTemplate();
	m_coURIPath = string("/look/") + m_coTemplate;
	m_coQueryString = getQueryString();
	m_bValidURI = true;
}
