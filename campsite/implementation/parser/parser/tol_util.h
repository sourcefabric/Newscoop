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

#ifndef _TOL_UTIL_H
#define _TOL_UTIL_H

#include <mysql/mysql.h>
#include <string>

#include "tol_atoms.h"

extern string SQL_SERVER;
extern string SQL_USER;
extern string SQL_PASSWORD;
extern string SQL_DATABASE;
extern int SQL_SRV_PORT;

// SQLConnection: initialise connection to MySQL server
// Parameters: none
// Returns: pointer to MYSQL structure; NULL if error
MYSQL* MYSQLConnection();

// GetTypeAttributes: initialise the TOLTypeAttributesHash container;
//		this will contain all article specific attributes
// Parameters: TOLTypeAttributesHash** ahash - pointer to pointer to
//		attributes hash; ahash and *ahash must not be NULL
// Returns: RES_OK (0) on success
int GetArticleTypeAttributes(TOLTypeAttributesHash**);

// IsValidType: return 0 if the string is the name of a valid article type
// Parameters: cpChar type - pointer to const char; article type name; must not be NULL
// Returns: 0 if type is valid; error code otherwise
int IsValidType(cpChar, MYSQL*);

// EscapeURL: return the given character string escaped for URL
// Parameters: cpChar src - pointer to const char; string to escape; must not be
//		NULL
// Returns: pointer to escaped string; this is dynamically allocated using new
//		operator; after use this must be deallocated using delete operator
cpChar const EscapeURL(const char* src);

// EscapeURL: return the given character string escaped for HTML
// Parameters: cpChar src - pointer to const char; string to escape; must not be
//		NULL
// Returns: pointer to escaped string; this is dynamically allocated using new
//		operator; after use this must be deallocated using delete operator
cpChar const EscapeHTML(const char* src);

// CMYSQL_RES: wrapper class around MYSQL_RES structure; it takes care of result
//		deallocation
class CMYSQL_RES
{
public:
	// Constructor; initialises the pointer to MYSQL_RES
	CMYSQL_RES(MYSQL_RES* p_pRes = NULL) : m_pRes(p_pRes)
	{}
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
