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

#include <iostream>
using std::cout;
using std::endl;


// setURL(): sets the URL object value
void CURLShortNames::setURL(const CMsgURLRequest& p_rcoURLMessage)
{
	m_coHTTPHost = p_rcoURLMessage.getHTTPHost();
	m_coURI = p_rcoURLMessage.getReqestURI();
	m_bValidURI = true;

	CMYSQL_RES coRes;
	string coQuery = string("select IdPublication from Aliases where Name = '")
	               + m_coHTTPHost + "'";
	MYSQL_ROW qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
	if (qRow == NULL)
		throw InvalidValue("site alias", m_coHTTPHost.c_str());
	long nPublication = Integer(qRow[0]);
	setPublication(nPublication);

	// prepare the path string
	string::size_type nQMark = m_coURI.find('?');
	string coPath = (nQMark != string::npos) ? m_coURI.substr(0, nQMark) : m_coURI;

	// declare variables to store publication parameters found as short names in URI
	string coLangCode, coIssue, coSection, coArticle;

	// indexes in the URI string
	string::size_type nCurrent = (coPath[0] == '/') ? 1 : 0;
	string::size_type nNext = coPath.find('/', nCurrent);
	nNext = nNext == string::npos ? coPath.size() + 1 : nNext;

	// read the language code
	coLangCode = coPath.substr(nCurrent, nNext - nCurrent);

	// query the database for the language code
	coQuery = string("select Id from Languages where Code = '") + coLangCode + "'";
	qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
	if (qRow == NULL)
		throw InvalidValue("language code", coLangCode.c_str());
	long nLanguage = Integer(qRow[0]);
	setLanguage(nLanguage);

	// read the issue short name
	nCurrent = nNext + 1;
	nNext = coPath.find('/', nCurrent);
	nNext = nNext == string::npos ? coPath.size() + 1 : nNext;
	coIssue = coPath.substr(nCurrent, nNext - nCurrent);

	long nIssue = -1;
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

	int sz = coPath.size();
	if (nNext < (coPath.size() - 1))
	{
		// read the section short name
		nCurrent = nNext + 1;
		nNext = coPath.find('/', nCurrent);
		nNext = nNext == string::npos ? coPath.size() + 1 : nNext;
		coSection = coPath.substr(nCurrent, nNext - nCurrent);
	}

	string coIssueCond = nIssue != -1 ? string(" and NrIssue = ") + getValue(P_NRISSUE) : "";
	long nSection = -1;
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

	if (nNext < (coPath.size() - 1))
	{
		// read the article short name
		nCurrent = nNext + 1;
		nNext = coPath.find('/', nCurrent);
		nNext = nNext == string::npos ? coPath.size() + 1 : nNext;
		coArticle = coPath.substr(nCurrent, nNext - nCurrent);
	}

	string coSectCond = nSection != -1 ? string(" and NrSection = ") + getValue(P_NRSECTION) : "";
	long nArticle = -1;
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

// buildURI(): internal method; builds the URI string from object attributes
void CURLShortNames::buildURI() const
{
	if (m_bValidURI)
		return;

	CMYSQL_RES coRes;
	string coQuery = string("select Code from Languages where Id = ") + getValue(P_IDLANG);
	MYSQL_ROW qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
	if (qRow == NULL)
		throw InvalidValue("language identifier", getValue(P_IDLANG));
	m_coURI = string("/") + qRow[0] + "/";

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
	m_coURI += coIssueSN + "/";
	if (coSectionSN != "" || coArticleSN != "")
		m_coURI += coSectionSN + "/";
	if (coArticleSN != "")
		m_coURI += coArticleSN + "/";

	string coQueryString = getQueryString();
	if (coQueryString != "")
		m_coURI += string("?") + coQueryString;

	m_bValidURI = true;
}

// readQueryString(): internal method; reads the parameters from the query string
void CURLShortNames::readQueryString(const string& p_rcoQueryString)
{
	if (p_rcoQueryString == "")
		return;

	string::size_type nStart = 0;
	while (true)
	{
		// read the parameter name
		string::size_type nIndex = p_rcoQueryString.find('=', nStart);
		if (nIndex == string::npos)
			break;
		string coParam = p_rcoQueryString.substr(nStart, nIndex - nStart);

		// read the parameter value
		nStart = nIndex + 1;
		nIndex = p_rcoQueryString.find("&", nStart);
		nIndex = nIndex == string::npos ? p_rcoQueryString.size() : nIndex;
		string coValue = p_rcoQueryString.substr(nStart, nIndex - nStart);

		// set the parameter value in the parameter map
		setValue(coParam, coValue);

		// prepare for the next iteration
		nStart = nIndex + 1;
		if (nStart >= p_rcoQueryString.size())
			break;
	}
}
