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

	const string& getPathTranslated() const { return m_coPathTranslated; }

	const string& getDocumentRoot() const { return m_coDocumentRoot; }

	void setId(long p_nId) { replaceValue("url_id", p_nId); }

	long getId() const throw(InvalidValue) { return getIntValue("url_id"); }

	void setLanguage(long p_nLanguage) { replaceValue(P_IDLANG, p_nLanguage); }

	long getLanguage() const throw(InvalidValue) { return getIntValue(P_IDLANG); }

	void setPublication(long p_nPublication) { replaceValue(P_IDPUBL, p_nPublication); }

	long getPublication() const throw(InvalidValue) { return getIntValue(P_IDPUBL); }

	void setIssue(long p_nIssue) { replaceValue(P_NRISSUE, p_nIssue); }

	long getIssue() const throw(InvalidValue) { return getIntValue(P_NRISSUE); }

	void setSection(long p_nSection) { replaceValue(P_NRSECTION, p_nSection); }

	long getSection() const throw(InvalidValue) { return getIntValue(P_NRSECTION); }

	void setArticle(long p_nArticle) { replaceValue(P_NRARTICLE, p_nArticle); }

	long getArticle() const throw(InvalidValue) { return getIntValue(P_NRARTICLE); }

	void setValue(const string& p_rcoParameter, long p_nValue);

	void setValue(const string& p_rcoParameter, const string& p_rcoValue);

	void replaceValue(const string& p_rcoParameter, long p_nValue);

	void replaceValue(const string& p_rcoParameter, const string& p_rcoValue);

	bool isSet(const string& p_rcoParameter) const;

	const string& getValue(const string& p_rcoParameter) const;

	const string& getNextValue(const string& p_rcoParameter) const;

	long getIntValue(const string& p_rcoParameter) const throw(InvalidValue);

	void resetParamValuesIndex(const string& p_rcoParameter = string("")) const;

	void deleteParameter(const string& p_rcoParameter);

	const String2StringMMap& getParameters() const;

	void setCookie(const string& p_rcoName, const string& p_rcoValue);

	string getCookie(const string& p_rcoName) const;

	const String2String& getCookies() const;

	void deleteCookie(const string& p_rcoName);

	virtual bool equalTo(const CURL* p_pcoURL) const;

	virtual void setURL(const CMsgURLRequest& p_rcoMsg) = 0;

	virtual string getURL() const = 0;

	virtual string getURIPath() const = 0;

	virtual string getURI() const = 0;

	virtual string getURLType() const = 0;

	virtual string getQueryString() const = 0;

	virtual string getFormString() const = 0;

	virtual string setTemplate(const string& p_rcoTemplate) throw (InvalidValue) = 0;

	virtual string getTemplate() const = 0;

	// readQueryString(): static method; reads the parameters from the query string
	static String2StringMMap* readQueryString(const string& p_rcoQueryString,
	                                          String2StringMMap* p_pcoParams = NULL);

protected:
	// ReadQueryString(): internal method; reads the parameters from the query string
	void ReadQueryString(const string& p_rcoQueryString);

	virtual void PreSetValue(const string& p_rcoParameter, const string& p_rcoValue) {}

	virtual void PostSetValue(const string& p_rcoParameter, const string& p_rcoValue) {}

protected:
	typedef map<string, String2StringMMap::const_iterator, less<string> > String2MMapIt;

protected:
	string m_coMethod;
	string m_coPathTranslated;
	string m_coDocumentRoot;
	String2StringMMap m_coParamMap;
	String2String m_coCookies;

private:
	mutable String2MMapIt m_coParamIterators;
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
	PreSetValue(p_rcoParameter, p_rcoValue);
	m_coParamMap.insert(pair<string, string>(p_rcoParameter, p_rcoValue));
	PostSetValue(p_rcoParameter, p_rcoValue);
}

inline void CURL::replaceValue(const string& p_rcoParameter, long p_nValue)
{
	replaceValue(p_rcoParameter, (string)Integer(p_nValue));
}

inline bool CURL::isSet(const string& p_rcoParameter) const
{
	return m_coParamMap.find(p_rcoParameter) != m_coParamMap.end();
}

inline bool CURL::equalTo(const CURL* p_pcoURL) const
{
	return this->getURLType() == p_pcoURL->getURLType()
		&& m_coMethod == p_pcoURL->m_coMethod
		&& m_coParamMap == p_pcoURL->m_coParamMap
		&& m_coCookies == p_pcoURL->m_coCookies;
}

inline long CURL::getIntValue(const string& p_rcoParameter) const throw(InvalidValue)
{
	string coValue = getValue(p_rcoParameter);
	return coValue != "" ? (long)Integer(coValue) : 0;
}

inline void CURL::deleteParameter(const string& p_rcoParameter)
{
	m_coParamIterators.erase(p_rcoParameter);
	m_coParamMap.erase(p_rcoParameter);
}

inline const String2StringMMap& CURL::getParameters() const
{
	return m_coParamMap; 
}

inline void CURL::setCookie(const string& p_rcoName, const string& p_rcoValue)
{
	m_coCookies[p_rcoName] = p_rcoValue;
}

inline string CURL::getCookie(const string& p_rcoName) const
{
	String2String::const_iterator coIt = m_coCookies.find(p_rcoName);
	if (coIt == m_coCookies.end())
		return string("");
	return (*coIt).second;
}

inline void CURL::deleteCookie(const string& p_rcoName)
{
	m_coCookies.erase(p_rcoName);
}

inline void CURL::ReadQueryString(const string& p_rcoQueryString)
{
	CURL::readQueryString(p_rcoQueryString, &m_coParamMap);
}

inline const String2String& CURL::getCookies() const
{
	return m_coCookies;
}

#endif // CURL_H
