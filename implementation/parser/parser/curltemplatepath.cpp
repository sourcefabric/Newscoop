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
#include "curltemplatepath.h"
#include "data_types.h"
#include "util.h"


// CURLTemplatePath(): copy constructor
CURLTemplatePath::CURLTemplatePath(const CURLTemplatePath& p_rcoSrc)
	: CURL(p_rcoSrc)
{
	m_bValidURI = p_rcoSrc.m_bValidURI;
	m_coURI = p_rcoSrc.m_coURI;
	m_pDBConn = p_rcoSrc.m_pDBConn;
	m_coHTTPHost = p_rcoSrc.m_coHTTPHost;
}


// setURL(): sets the URL object value
void CURLTemplatePath::setURL(const CMsgURLRequest& p_rcoURLMessage)
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

	m_coTemplate = coPath;

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

// buildURI(): internal method; builds the URI string from object attributes
void CURLTemplatePath::buildURI() const
{
	if (m_bValidURI)
		return;

// 	CMYSQL_RES coRes;
// 	string coQuery = string("select FrontPage from Issues where Published = 'Y' and "
// 	               "IdPublication = ") + getValue(P_IDPUBL) + " order by Number desc";
// 	MYSQL_ROW qRow = QueryFetchRow(m_pDBConn, coQuery.c_str(), coRes);
// 	if (qRow == NULL)
// 		throw InvalidValue("publication identifier", getValue(P_IDPUBL));
// 	m_coURI = string("/") + qRow[0] + "/";

	m_coURI = m_coTemplate;

	string coQueryString = getQueryString();
	if (coQueryString != "")
		m_coURI += string("?") + coQueryString;

	m_bValidURI = true;
}
