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
	CURLShortNames(const CMsgURLRequest& p_rcoURLMessage, MYSQL* p_pDBConn)
		: m_coURI(""), m_pDBConn(p_pDBConn)
		{ setURL(p_rcoURLMessage); }

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
	
	// readQueryString(): internal method; reads the parameters from the query string
	void readQueryString(const string& p_rcoQueryString);

private:
	mutable string m_coURI;  // caches the URI string
	mutable MYSQL* m_pDBConn;  // caches the connection to the database

private:
	string m_coHTTPHost;  // stores the HTTP host attribute
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

inline string CURLShortNames::getURI() const
{
	if (!m_bValidURI)
		buildURI();
	return m_coURI;
}

inline string CURLShortNames::getURLType() const
{
	return CURLShortNamesType::typeName();
}

inline CURL* CURLShortNamesType::getURL(const CMsgURLRequest& p_rcoMsg) const
{
	return new CURLShortNames(p_rcoMsg, MYSQLConnection());
}
