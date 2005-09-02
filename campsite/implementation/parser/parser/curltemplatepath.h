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


// CURLTemplatePath: class implementing the template path type of URL
// The URL format is: http://[alias]/[template_path]/?[params]
// where:
//    - alias is the publication alias
//    - template_path is the template path relative to HTML directory
//    - params: parameters
class CURLTemplatePath : public CURL
{
public:
	// CURLTemplatePath(): constructor: takes a URLRequest message and reads the URL
	// Needs a database connection pointer to read the publication parameters from
	// the database.
	CURLTemplatePath(const CMsgURLRequest& p_rcoURLMessage, MYSQL* p_pDBConn)
		: m_pDBConn(p_pDBConn), m_bValidTemplate(false), m_bLockTemplate(false)
		{ setURL(p_rcoURLMessage); }

	// CURLTemplatePath(): copy constructor
	CURLTemplatePath(const CURLTemplatePath& p_rcoSrc);

	// clone(): create a clone
	CURL* clone() const;

	virtual bool equalTo(const CURL* p_pcoURL) const;

	// setURL(): sets the URL object value
	virtual void setURL(const CMsgURLRequest& p_rcoURLMessage, bool p_bLockTemplate = false);

	virtual void lockTemplate() const;

	virtual void unlockTemplate() const;

	// getURL(): returns the URL in string format
	virtual string getURL() const { return m_coHTTPHost + getURI(); }

	// getURI(): returns the path component of the URI in string format
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
	mutable MYSQL* m_pDBConn;  // caches the connection to the database
	mutable bool m_bValidTemplate;
	mutable string m_coTemplate;

private:
	string m_coHTTPHost;  // stores the HTTP host attribute
	mutable bool m_bLockTemplate;
};


class CURLTemplatePathType : public CURLType
{
public:
	CURLTemplatePathType() { registerURLType(); }

	static string typeName() { return string("template path"); }

	string getTypeName() const { return string("template path"); }

	CURL* getURL(const CMsgURLRequest& p_rcoMsg) const;
};


// CURLTemplatePath inline methods

inline void CURLTemplatePath::lockTemplate() const
{
	m_bLockTemplate = true;
}

inline void CURLTemplatePath::unlockTemplate() const
{
	m_bLockTemplate = false;
	m_bValidTemplate = false;
}

inline void CURLTemplatePath::PreSetValue(const string& p_rcoParameter,
										  const string& p_rcoValue)
{
	m_bValidURI = false;
	if (!m_bLockTemplate)
		m_bValidTemplate = false;
}

inline CURL* CURLTemplatePath::clone() const
{
	return new CURLTemplatePath(*this);
}

inline bool CURLTemplatePath::equalTo(const CURL* p_pcoURL) const
{
	return this->getURLType() == p_pcoURL->getURLType() && CURL::equalTo(p_pcoURL)
		&& m_coHTTPHost == ((const CURLTemplatePath*)p_pcoURL)->m_coHTTPHost;
}

inline string CURLTemplatePath::getURIPath() const
{
	if (!m_bValidURI)
		BuildURI();
	return m_coURIPath;
}

inline string CURLTemplatePath::getURI() const
{
	if (!m_bValidURI)
		BuildURI();
	if (m_coQueryString == "")
		return m_coURIPath;
	return m_coURIPath + "?" + m_coQueryString;
}

inline string CURLTemplatePath::getURLType() const
{
	return CURLTemplatePathType::typeName();
}

inline bool CURLTemplatePath::needTemplateParameter() const
{
	return false;
}

inline CURL* CURLTemplatePathType::getURL(const CMsgURLRequest& p_rcoMsg) const
{
	return new CURLTemplatePath(p_rcoMsg, MYSQLConnection());
}
