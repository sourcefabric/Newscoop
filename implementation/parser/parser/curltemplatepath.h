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
		: m_coURI(""), m_pDBConn(p_pDBConn)
		{ setURL(p_rcoURLMessage); }

	// CURLTemplatePath(): copy constructor
	CURLTemplatePath(const CURLTemplatePath& p_rcoSrc);

	// clone(): create a clone
	CURL* clone() const;

	virtual void setValue(const string& p_rcoParameter, long p_nValue);

	virtual void setValue(const string& p_rcoParameter, const string& p_rcoValue);

	virtual bool equalTo(const CURL* p_pcoURL) const;

	// setURL(): sets the URL object value
	virtual void setURL(const CMsgURLRequest& p_rcoURLMessage);

	// getURL(): returns the URL in string format
	virtual string getURL() const { return m_coHTTPHost + getURI(); }

	// getURI(): returns the URI in string format
	virtual string getURI() const;

	// getURLType(): returns a name of the URL type
	virtual string getURLType() const;

	// getQueryString(): returns the query string
	virtual string getQueryString() const;

private:
	// buildURI(): internal method; builds the URI string from object attributes
	void buildURI() const;

private:
	mutable bool m_bValidURI;
	mutable string m_coURI;  // caches the URI string
	mutable MYSQL* m_pDBConn;  // caches the connection to the database

private:
	string m_coHTTPHost;  // stores the HTTP host attribute
	string m_coTemplate;
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

inline CURL* CURLTemplatePath::clone() const
{
	return new CURLTemplatePath(*this);
}

inline void CURLTemplatePath::setValue(const string& p_rcoParameter, long p_nValue)
{
	CURL::setValue(p_rcoParameter, p_nValue);
	m_bValidURI = false;
}

inline void CURLTemplatePath::setValue(const string& p_rcoParameter, const string& p_rcoValue)
{
	CURL::setValue(p_rcoParameter, p_rcoValue);
	m_bValidURI = false;
}

inline bool CURLTemplatePath::equalTo(const CURL* p_pcoURL) const
{
	return this->getURLType() == p_pcoURL->getURLType() && CURL::equalTo(p_pcoURL)
		&& m_coHTTPHost == ((const CURLTemplatePath*)p_pcoURL)->m_coHTTPHost;
}

inline string CURLTemplatePath::getURI() const
{
	if (!m_bValidURI)
		buildURI();
	return m_coURI;
}

inline string CURLTemplatePath::getURLType() const
{
	return CURLTemplatePathType::typeName();
}

inline CURL* CURLTemplatePathType::getURL(const CMsgURLRequest& p_rcoMsg) const
{
	return new CURLTemplatePath(p_rcoMsg, MYSQLConnection());
}
