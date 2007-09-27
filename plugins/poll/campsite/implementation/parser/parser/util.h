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

/******************************************************************************

General purpose functions

******************************************************************************/

#ifndef _CMS_UTIL_H
#define _CMS_UTIL_H

#include <mysql/mysql.h>
#include <string>
#include <new>
#include <iostream>

#include "globals.h"

using std::cout;
using std::endl;
using std::ostream;
using std::string;
using std::bad_alloc;

extern string SQL_SERVER;
extern string SQL_USER;
extern string SQL_PASSWORD;
extern string SQL_DATABASE;
extern int SQL_SRV_PORT;

// SQLConnection: initialise connection to MySQL server
// Parameters: none
// Returns: pointer to MYSQL structure; NULL if error
MYSQL* MYSQLConnection(bool p_bForceNew = false);

// UpdateTopics: update topics values from campsite database
// Parameters: bool& p_rbUpdated - out parameter: set true if values changed
// Returns: 0 - on success or error code
int UpdateTopics(bool& p_rbUpdated);

class CTypeAttributesMap;

// GetTypeAttributes: initialise the CTypeAttributesHash container;
//		this will contain all article specific attributes
// Parameters: CTypeAttributesHash** ahash - pointer to pointer to
//		attributes hash; ahash and *ahash must not be NULL
// Returns: RES_OK (0) on success
int GetArticleTypeAttributes(CTypeAttributesMap**) throw(bad_alloc);

// IsValidType: return 0 if the string is the name of a valid article type
// Parameters: const char* type - pointer to const char; article type name; must not be NULL
// Returns: 0 if type is valid; error code otherwise
int IsValidType(const char*, MYSQL*);

// EscapeURL: return the given character string escaped for URL
// Parameters: const char* src - pointer to const char; string to escape; must not be
//		NULL
// Returns: pointer to escaped string; this is dynamically allocated using new
//		operator; after use this must be deallocated using delete operator
const char* const EscapeURL(const char* src);

// UnescapeURL: return the given character string unescaped from URL format
// Parameters: const char* src - pointer to const char; string to unescape; must not be
//		NULL
// Returns: pointer to escaped string; this is dynamically allocated using new
//		operator; after use this must be deallocated using delete operator
const char* const UnescapeURL(const char* src);

// EscapeHTML: return the given character string escaped for HTML
// Parameters: const char* src - pointer to const char; string to escape; must not be
//		NULL
// Returns: pointer to escaped string; this is dynamically allocated using new
//		operator; after use this must be deallocated using delete operator
const char* const EscapeHTML(const char* src);

struct HTMLEncoder {
	ostream& (*m_pEncodeMethod)(ostream&, const char*, bool);
	const char* m_pchString;
	bool m_bEncode;
	
	HTMLEncoder(ostream& (*p_pEncodeMethod)(ostream&, const char* p_pchString, bool),
				const char* p_pchString, bool p_bEncode)
	: m_pEncodeMethod(p_pEncodeMethod), m_pchString(p_pchString), m_bEncode(p_bEncode) {}
};

ostream& operator << (ostream& p_rOutStream, HTMLEncoder p_rEncoder);

ostream& outEncodeHTML(ostream& p_rcoOutStream, const char* p_rchString, bool p_bEncode);

inline HTMLEncoder encodeHTML(const string& p_rcoString, bool p_bEncode = true)
{
	return HTMLEncoder(outEncodeHTML, p_rcoString.c_str(), p_bEncode);
}

inline HTMLEncoder encodeHTML(const char* p_pchString, bool p_bEncode = true)
{
	return HTMLEncoder(outEncodeHTML, p_pchString, p_bEncode);
}

// CMYSQL_RES: wrapper class around MYSQL_RES structure; it takes care of result
//		deallocation
class CMYSQL_RES
{
public:
	// Constructor; initialises the pointer to MYSQL_RES
	CMYSQL_RES(MYSQL_RES* p_pRes = NULL) : m_pRes(p_pRes) { }

	// Destructor; deallocates the MYSQL_RES structure if necessary
	~CMYSQL_RES()
	{
		if (m_pRes != NULL)
			mysql_free_result(m_pRes);
		m_pRes = NULL;
	}

	// operator *; returns pointer to MYSQL_RES structure
	MYSQL_RES* operator * () const
	{
		return m_pRes;
	}

	// operator =
	const CMYSQL_RES& operator = (MYSQL_RES* p_pRes)
	{
		if (m_pRes != NULL)
			mysql_free_result(m_pRes);
		m_pRes = p_pRes;
		return *this;
	}

	// NumRows: return the number of result rows
	my_ulonglong NumRows() const
	{
		return m_pRes != NULL ? mysql_num_rows(m_pRes) : 0;
	}

	// NumRows: return the number of result fields
	unsigned int NumFields() const
	{
		return m_pRes != NULL ? mysql_num_fields(m_pRes) : 0;
	}

	// FetchRow: return the next row
	MYSQL_ROW FetchRow()
	{
		return m_pRes != NULL ? mysql_fetch_row(m_pRes) : 0;
	}

private:
	MYSQL_RES* m_pRes;
};

// SQLEscapeString: escape given string for sql query; returns escaped string
// The returned string must be deallocated by the user using delete operator.
// Parameters:
//      const char* src - source string
//      ulint p_nLength - string length
char* SQLEscapeString(const char* src, ulint p_nLength);

MYSQL_ROW QueryFetchRow(MYSQL* p_pDBConn, const string& p_rcoQuery, CMYSQL_RES& p_rcoQRes);

// Macros used on database operations

#define SQLQuery(sql, buf)\
if (sql == NULL)\
return ERR_SQLCONNECT;\
if (mysql_query(sql, buf))\
return ERR_QUERY;

#define SQLRealQuery(sql, buf, len)\
if (mysql_real_query(sql, buf, len))\
return ERR_QUERY;

#define StoreResult(sql, res)\
CMYSQL_RES res = mysql_store_result(sql);\
if (*res == NULL)\
return ERR_NOMEM;

#define CheckForRows(res, num)\
if (mysql_num_rows(res) < num) {\
return ERR_NODATA;\
}

#define FetchRow(res, row)\
MYSQL_ROW row = mysql_fetch_row(res);\
if (row == NULL)\
return ERR_NOMEM;

#endif
