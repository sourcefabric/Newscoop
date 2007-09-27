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
#include "curl.h"
#include "curltype.h"
#include "data_types.h"
#include "cmessage.h"


// CURLShortNames: class implementing the short names type of URL
// The URL format is: http://[alias]/[lang_code]/[issue_sn]/[sect_sn]/[art_sn]/?[other_params]
// where:
//    - alias is the publication alias
//    - lang_code: language code
//    - issue_sn: issue short name
//    - sect_sn: section short name
//    - art_sn: article short name
//    - other_params: other parameters
class CURLShortNames : public CURL
{
public:
	// CURLShortNames(): constructor: takes a URLRequest message and reads the URL
	// Needs a database connection pointer to read the publication parameters from
	// the database.
	CURLShortNames(const CMsgURLRequest& p_rcoURLMessage)
		: m_bValidTemplate(false), m_bLockTemplate(false)
		{ setURL(p_rcoURLMessage); }

	// CURLShortNames(): copy constructor
	CURLShortNames(const CURLShortNames& p_rcoSrc);

	// clone(): create a clone
	CURL* clone() const;

	virtual bool equalTo(const CURL* p_pcoURL) const;

	// setURL(): sets the URL object value
	virtual void setURL(const CMsgURLRequest& p_rcoURLMessage, bool p_bLockTemplate = false);

	virtual void lockTemplate() const;

	virtual void unlockTemplate() const;

	virtual string getHostName() const { return m_coHTTPHost; }

	// getURIPath(): returns the path component of the URI in string format
	virtual string getURIPath() const;

	// getURI(): returns the URI in string format
	virtual string getURI() const;

	// getURLType(): returns a name of the URL type
	virtual string getURLType() const;

	// getQueryString(): returns the query string
	virtual string getQueryString() const;

	virtual string getFormString() const;

	virtual string setTemplate(const string& p_rcoTemplate) throw (InvalidValue);

	virtual string setTemplate(id_type p_nTemplateId) throw (InvalidValue);

	virtual string getTemplate() const;

	virtual bool needTemplateParameter() const;

private:
	// BuildURI(): internal method; builds the URI string from object attributes
	void BuildURI() const;

	virtual void PreSetValue(const string& p_rcoParameter, const string& p_rcoValue);

private:
	mutable bool m_bValidURI;
	mutable string m_coURIPath;  // caches the path component of the URI
	mutable string m_coQueryString;  // caches the query string component of the URI
	mutable bool m_bValidTemplate;
	mutable string m_coTemplate;

private:
	string m_coHTTPHost;  // stores the HTTP host attribute
	mutable bool m_bLockTemplate;
};


class CURLShortNamesType : public CURLType
{
public:
	CURLShortNamesType() { registerURLType(); }

	static string typeName() { return string("short names"); }

	string getTypeName() const { return string("short names"); }

	CURL* getURL(const CMsgURLRequest& p_rcoMsg) const;
};


// CURLShortNames inline methods

inline void CURLShortNames::lockTemplate() const
{
	m_bLockTemplate = true;
}

inline void CURLShortNames::unlockTemplate() const
{
	m_bLockTemplate = false;
	m_bValidTemplate = false;
}

inline void CURLShortNames::PreSetValue(const string& p_rcoParameter,
										  const string& p_rcoValue)
{
	m_bValidURI = false;
	if (!m_bLockTemplate)
		m_bValidTemplate = false;
}

inline CURL* CURLShortNames::clone() const
{
	return new CURLShortNames(*this);
}

inline bool CURLShortNames::equalTo(const CURL* p_pcoURL) const
{
	return this->getURLType() == p_pcoURL->getURLType() && CURL::equalTo(p_pcoURL)
		&& m_coHTTPHost == ((const CURLShortNames*)p_pcoURL)->m_coHTTPHost;
}

inline string CURLShortNames::getURIPath() const
{
	if (!m_bValidURI)
		BuildURI();
	return m_coURIPath;
}

inline string CURLShortNames::getURI() const
{
	if (!m_bValidURI)
		BuildURI();
	if (m_coQueryString == "")
		return m_coURIPath;
	return m_coURIPath + "?" + m_coQueryString;
}

inline bool CURLShortNames::needTemplateParameter() const
{
	return true;
}

inline string CURLShortNames::getURLType() const
{
	return CURLShortNamesType::typeName();
}

inline CURL* CURLShortNamesType::getURL(const CMsgURLRequest& p_rcoMsg) const
{
	return new CURLShortNames(p_rcoMsg);
}
