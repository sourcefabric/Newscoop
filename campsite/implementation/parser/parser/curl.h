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


#ifndef CURL_H
#define CURL_H


#include <string>
#include <map>


using std::string;
using std::map;
using std::multimap;


#include "cgiparams.h"
#include "globals.h"
#include "cms_types.h"
#include "data_types.h"
#include "cmessage.h"


typedef multimap <string, string, str_case_less> String2StringMMap;

/**
  * class CURL
  * store the URL parameters; return URL string; the derived classes will implement
  * the setURL, getURL methods; CURL(string) just calls setURL
  */

class CURL
{
public:
	CURL() { }

	CURL(const CURL& p_rcoSrc);

	virtual ~CURL() {}

	virtual CURL* clone() const = 0;

	void setMethod(const string& p_rcoMethod) { m_coMethod = p_rcoMethod; }

	const string& getMethod() const { return m_coMethod; }

	void setPathTranslated(const string& p_coPathTranslated)
		{ m_coPathTranslated = p_coPathTranslated; }

	const string& getPathTranslated() const { return m_coPathTranslated; }

	const string& getDocumentRoot() const { return m_coDocumentRoot; }

	void setDocumentRoot(const string& p_coDocumentRoot)
		{ m_coDocumentRoot = p_coDocumentRoot; }

	void setId(long p_nId) { setValue("url_id", p_nId); }

	long getId() const throw(InvalidValue) { return getIntValue("url_id"); }

	void setLanguage(long p_nLanguage) { setValue(P_IDLANG, p_nLanguage); }

	long getLanguage() const throw(InvalidValue) { return getIntValue(P_IDLANG); }

	void setPublication(long p_nPublication) { setValue(P_IDPUBL, p_nPublication); }

	long getPublication() const throw(InvalidValue) { return getIntValue(P_IDPUBL); }

	void setIssue(long p_nIssue) { setValue(P_NRISSUE, p_nIssue); }

	long getIssue() const throw(InvalidValue) { return getIntValue(P_NRISSUE); }

	void setSection(long p_nSection) { setValue(P_NRSECTION, p_nSection); }

	long getSection() const throw(InvalidValue) { return getIntValue(P_NRSECTION); }

	void setArticle(long p_nArticle) { setValue(P_NRARTICLE, p_nArticle); }

	long getArticle() const throw(InvalidValue) { return getIntValue(P_NRARTICLE); }

	virtual void setValue(const string& p_rcoParameter, long p_nValue);

	virtual void setValue(const string& p_rcoParameter, const string& p_rcoValue);

	string getValue(string p_rcoParameter) const;

	long getIntValue(string p_rcoParameter) const throw(InvalidValue);

	void setCookie(const string& p_rcoName, const string& p_rcoValue);

	string getCookie(string p_rcoName) const;

	const String2String& getCookies() const;

	virtual bool equalTo(const CURL* p_pcoURL) const;

	virtual void setURL(const CMsgURLRequest& p_rcoMsg) = 0;

	virtual string getURL() const = 0;

	virtual string getURI() const = 0;

	virtual string getURLType() const = 0;

	virtual string getQueryString() const = 0;

	// readQueryString(): static method; reads the parameters from the query string
	static String2StringMMap* readQueryString(const string& p_rcoQueryString,
	                                          String2StringMMap* p_pcoParams = NULL);

protected:
	// ReadQueryString(): internal method; reads the parameters from the query string
	void ReadQueryString(const string& p_rcoQueryString);

protected:
	string m_coMethod;
	string m_coPathTranslated;
	string m_coDocumentRoot;
	String2StringMMap m_coParamMap;
	String2String m_coCookies;
};


// CURL inline methods

inline CURL::CURL(const CURL& p_rcoSrc)
{
	m_coMethod = p_rcoSrc.m_coMethod;
	m_coParamMap = p_rcoSrc.m_coParamMap;
	m_coCookies = p_rcoSrc.m_coCookies;
}

inline void CURL::setValue(const string& p_rcoParameter, long p_nValue)
{
	setValue(p_rcoParameter, (string)Integer(p_nValue));
}

inline void CURL::setValue(const string& p_rcoParameter, const string& p_rcoValue)
{
	m_coParamMap.insert(pair<string, string>(p_rcoParameter, p_rcoValue));
}

inline bool CURL::equalTo(const CURL* p_pcoURL) const
{
	return this->getURLType() == p_pcoURL->getURLType()
		&& m_coMethod == p_pcoURL->m_coMethod
		&& m_coParamMap == p_pcoURL->m_coParamMap
		&& m_coCookies == p_pcoURL->m_coCookies;
}

inline string CURL::getValue(string p_rcoParameter) const
{
	String2StringMMap::const_iterator coIt = m_coParamMap.find(p_rcoParameter);
	if (coIt == m_coParamMap.end())
		return string("");
	return (*coIt).second;
}

inline long CURL::getIntValue(string p_rcoParameter) const throw(InvalidValue)
{
	return Integer(getValue(p_rcoParameter));
}

inline void CURL::setCookie(const string& p_rcoName, const string& p_rcoValue)
{
	m_coCookies[p_rcoName] = p_rcoValue;
}

inline string CURL::getCookie(string p_rcoName) const
{
	String2String::const_iterator coIt = m_coCookies.find(p_rcoName);
	if (coIt == m_coCookies.end())
		return string("");
	return (*coIt).second;
}

inline const String2String& CURL::getCookies() const
{
	return m_coCookies;
}

inline void CURL::ReadQueryString(const string& p_rcoQueryString)
{
	CURL::readQueryString(p_rcoQueryString, &m_coParamMap);
}

#endif // CURL_H
