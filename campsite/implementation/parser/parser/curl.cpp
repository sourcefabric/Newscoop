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


#include "curl.h"


const string& CURL::getValue(const string& p_rcoParameter) const
{
	String2StringMMap::const_iterator coValIt;
	String2MMapIt::iterator coIt = m_coParamIterators.find(p_rcoParameter);
	if (coIt == m_coParamIterators.end())
	{
		coValIt = m_coParamMap.find(p_rcoParameter);
		if (coValIt == m_coParamMap.end())
			return String::emptyString;
		m_coParamIterators[p_rcoParameter] = coValIt;
		return (*coValIt).second;
	}
	if ((*coIt).second == m_coParamMap.end())
		return String::emptyString;
	return (*((*coIt).second)).second;
}

const string& CURL::getNextValue(const string& p_rcoParameter) const
{
	String2StringMMap::const_iterator coValIt;
	String2MMapIt::iterator coIt = m_coParamIterators.find(p_rcoParameter);
	if (coIt == m_coParamIterators.end())
	{
		coValIt = m_coParamMap.find(p_rcoParameter);
		if (coValIt == m_coParamMap.end())
			return String::emptyString;
		m_coParamIterators[p_rcoParameter] = coValIt;
		return (*coValIt).second;
	}
	if ((*coIt).second == m_coParamMap.end())
		return String::emptyString;
	++((*coIt).second);
	if ((*((*coIt).second)).first != p_rcoParameter)
	{
		m_coParamIterators[p_rcoParameter] = m_coParamMap.end();
		return String::emptyString;
	}
	return (*((*coIt).second)).second;
}

void CURL::resetParamValuesIndex(const string& p_rcoParameter) const
{
	if (p_rcoParameter != "")
	{
		m_coParamIterators.erase(p_rcoParameter);
		return;
	}
	String2MMapIt::iterator coIt = m_coParamIterators.begin();
	for (; coIt != m_coParamIterators.end(); ++coIt)
		m_coParamIterators.erase(coIt);
}

void CURL::replaceValue(const string& p_rcoParameter, const string& p_rcoValue)
{
	PreSetValue(p_rcoParameter, p_rcoValue);
	deleteParameter(p_rcoParameter);
	m_coParamMap.insert(pair<string, string>(p_rcoParameter, p_rcoValue));
	PostSetValue(p_rcoParameter, p_rcoValue);
}

// readQueryString(): static method; reads the parameters from the query string
String2StringMMap* CURL::readQueryString(const string& p_rcoQueryString,
                                         String2StringMMap* p_pcoParams)
{
	if (p_rcoQueryString == "")
		return p_pcoParams;

	if (p_pcoParams == NULL)
		p_pcoParams = new String2StringMMap;

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
		p_pcoParams->insert(pair<string, string>(coParam, coValue));

		// prepare for the next iteration
		nStart = nIndex + 1;
		if (nStart >= p_rcoQueryString.size())
			break;
	}
	return p_pcoParams;
}
