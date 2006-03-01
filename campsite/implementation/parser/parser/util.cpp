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

Implementation of general purpose functions

******************************************************************************/

#include <sys/types.h>
#include <fcntl.h>
#include <stdio.h>
#include <sys/stat.h>
#include <unistd.h>
#include <sys/mman.h>
#include <string>
#include <mysql/mysql.h>

#include "util.h"
#include "atoms_impl.h"
#include "cms_types.h"
#include "attributes.h"
#include "error.h"
#include "auto_ptr.h"

string SQL_SERVER;
string SQL_USER;
string SQL_PASSWORD;
string SQL_DATABASE;
int SQL_SRV_PORT = 0;

// SQLConnection: initialise connection to MySQL server
// Parameters: none
// Returns: pointer to MYSQL structure; NULL if error
MYSQL* MYSQLConnection()
{
	// Each thread should use at most on database connection; this is accomplished
	// using a static key variable; this is initialised to NULL at program start
	static TK_MYSQL coMySql(NULL);
	// connect to mysql server (if not already connected)
	if (&coMySql != NULL)
		return &coMySql; 					// already connected
	MYSQL* pSQL = NULL;
	if ((pSQL = mysql_init(pSQL)) == NULL)	// initialise connection to MySQL server
		return NULL;
	// connecto to MySQL server
	pSQL = mysql_real_connect(pSQL, SQL_SERVER.c_str(), SQL_USER.c_str(), SQL_PASSWORD.c_str(),
	                          SQL_DATABASE.c_str(), SQL_SRV_PORT, 0, 0);
	if (pSQL == NULL)						// unable to connect
		return NULL;
	coMySql = pSQL;							// set key variable value to MySQL connection
	return pSQL;
}

// UpdateTopics: update topics values from campsite database
// Parameters: bool& p_rbUpdated - out parameter: set true if values changed
// Returns: 0 - on success or error code
int UpdateTopics(bool& p_rbUpdated)
{
	// we need a connection to database server
	MYSQL* sql = MYSQLConnection();
	if (sql == NULL)
		return ERR_SQLCONNECT;

	// query for all table names in the database
	string q;
	int q_res;
	q = "select Topics.Id, Topics.Name, Languages.Code, Topics.ParentId from Topics, Languages "
	    "where Topics.LanguageId = Languages.Id order by Topics.Id asc";
	q_res = mysql_query(sql, q.c_str());
	if (q_res)
		return ERR_QUERY;
	StoreResult(sql, res);
	id_type nLastId = -1;
	CStringMap coValues;
	Topic::setUpdated(false);
	MYSQL_ROW row;
	while ((row = mysql_fetch_row(*res)))
	{
		id_type nTopicId = atol(row[0]);
		const char* pchTopicName = row[1];
		const char* pchLangCode = row[2];
		id_type nParentId = atol(row[3]);
		if (nParentId == 0)
			nParentId = -1;
		if (nTopicId != nLastId && !Topic::isValid(nTopicId))
			Topic::setTopic(pchTopicName, pchLangCode, nTopicId, nParentId);
		if (nLastId == -1)
			nLastId = nTopicId;
		if (nTopicId != nLastId)
		{
			Topic::setNames(coValues, nLastId);
			nLastId = nTopicId;
			coValues.clear();
		}
		coValues[pchLangCode] = pchTopicName;
	}
	if (nLastId != -1)
		Topic::setNames(coValues, nLastId);
	Topic::clearInvalid();
	p_rbUpdated = Topic::valuesChanged();
	return 0;
}

// GetTypeAttributes: initialise the CTypeAttributesMap container;
//		this will contain all article specific attributes
// Parameters: CTypeAttributesMap** ahash - pointer to pointer to
//		attributes hash; ahash and *ahash must not be NULL
// Returns: RES_OK (0) on success
int GetArticleTypeAttributes(CTypeAttributesMap** ta_h) throw(bad_alloc)
{
	// we need a connection to database server
	MYSQL* sql = MYSQLConnection();
	if (sql == NULL)
		return ERR_SQLCONNECT;

	// type-attributes map
	SafeAutoPtr<CTypeAttributesMap> pcoTypes(new CTypeAttributesMap);

	// query for tables with names that start with X (contain article types data)
	if (mysql_query(sql, "SHOW TABLES LIKE 'X%'"))
		return ERR_QUERY;
	StoreResult(sql, res);
	CheckForRows(*res, 1);
	MYSQL_ROW row;
	while ((row = mysql_fetch_row(*res)) != NULL)
	{
		// these tables contain the fields of every type of article
		string q = string("SHOW COLUMNS FROM ") + row[0] + " LIKE 'F%'";
		if (mysql_query(sql, q.c_str()) != 0)
			continue;
		CMYSQL_RES tres = mysql_store_result(sql);
		if (*tres == NULL)
			continue;

		CTypeAttributes* pcoType = new CTypeAttributes(row[0] + 1);	// new article type
		// create the contexts
		CStatementContext* pcoCtxList = new CStatementContext(CMS_CT_LIST);
		CStatementContext* pcoCtxPrint = new CStatementContext(CMS_CT_PRINT);
		CStatementContext* pcoCtxWith = new CStatementContext(CMS_CT_WITH);
		CStatementContext* pcoCtxIf = new CStatementContext(CMS_CT_IF);

		MYSQL_ROW trow;
		while ((trow = mysql_fetch_row(*tres)) != NULL)
		{
			CAttribute* pcoAttr = NULL;	// pointer to new attribute
			if (strncasecmp(trow[1], "tinyint", 7) == 0)	// field type is switch
			{
				pcoAttr = new CSwitchAttr(trow[0] + 3, trow[0]);
			}
			else if (strncasecmp(trow[1], "int", 3) == 0)	// field type is integer or topic
			{
				q = string("SELECT RootTopicId FROM TopicFields WHERE ArticleType = '")
						+ (row[0] + 1) + "' AND FieldName = '" + (trow[0] + 1) + "'";
				if (mysql_query(sql, q.c_str()) != 0)
					continue;
				CMYSQL_RES fres = mysql_store_result(sql);
				if (*fres == NULL)
					continue;
				MYSQL_ROW frow;
				if ((frow = mysql_fetch_row(*fres)) != NULL)
				{
					pcoAttr = new CTopicAttr(trow[0] + 1, trow[0]);
				}
				else
				{
					pcoAttr = new CIntegerAttr(trow[0] + 1, trow[0]);
				}
			}
			else if (strncasecmp(trow[1], "datetime", 8) == 0)	// field type is datetime
			{
				pcoAttr = new CDateTimeAttr(trow[0] + 1, trow[0]);
			}
			else if (strncasecmp(trow[1], "date", 4) == 0)	// field type is date
			{
				pcoAttr = new CDateAttr(trow[0] + 1, trow[0]);
			}
			else if (strncasecmp(trow[1], "time", 4) == 0)	// field type is time
			{
				pcoAttr = new CTimeAttr(trow[0] + 1, trow[0]);
			}
			else	// others are considered string
			{
				pcoAttr = new CStringAttr(trow[0] + 1, trow[0]);
			}

			pcoCtxList->insertAttr(pcoAttr);
			pcoCtxPrint->insertAttr((CAttribute*)pcoAttr->clone());
			pcoCtxWith->insertAttr((CAttribute*)pcoAttr->clone());
			pcoCtxIf->insertAttr((CAttribute*)pcoAttr->clone());
		}
		pcoType->insertCtx(pcoCtxList);
		pcoType->insertCtx(pcoCtxPrint);
		pcoType->insertCtx(pcoCtxWith);
		pcoType->insertCtx(pcoCtxIf);
		// insert a new type into type-attributes map
		// the type name is the table name having the X removed
		pcoTypes->operator [](pcoType->name()) = pcoType;
	}
	*ta_h = pcoTypes.release();
	return RES_OK;
}

// IsValidType: return 0 if the string is the name of a valid article type
// Parameters: const char* type - pointer to const char; article type name; must not be NULL
// Returns: 0 if type is valid; error code otherwise
int IsValidType(const char* t, MYSQL* sql)
{
	SQLQuery(sql, "show tables");
	StoreResult(sql, res);
	CheckForRows(*res, 1);
	MYSQL_ROW row;
	for (row = mysql_fetch_row(*res); row != NULL; row = mysql_fetch_row(*res))
		if (row[0][0] == 'X' && strcmp(t, row[0] + 1) == 0)
		{
			return 0;
		}
	return 1;
}

static char url_ok_chars[] = "*-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz";
static char hex_digits[17] = "0123456789ABCDEF";

// EscapeURL: return the given character string escaped for URL
// Parameters: const char* src - pointer to const char; string to escape; must not be
//		NULL
// Returns: pointer to escaped string; this is dynamically allocated using new
//		operator; after use this must be deallocated using delete operator
const char* const EscapeURL(const char* src)
{
	// translates all the charactes that ar not in url_ok_chars string into %xx string
	// %xx specifies the character ascii code in hexadecimal
	if (src == NULL)
		return NULL;
	char* dst = (char*)new char[strlen(src) * 3 + 1];
	if (dst == NULL)
		return NULL;
	int srcI;
	int dstI;
	for (srcI = 0, dstI = 0; src[srcI] != 0; srcI = srcI + 1, dstI = dstI + 1)
		if (src[srcI] == ' ')
			dst[dstI] = '+';
		else if (strchr(url_ok_chars, src[srcI]))
			dst[dstI] = src[srcI];
		else
		{
			dst[(dstI)++] = '%';
			dst[(dstI)++] = hex_digits[((unsigned char) src[srcI]) >> 4];
			dst[dstI] = hex_digits[src[srcI] & 0x0F];
		}
	dst[dstI] = 0;
	return dst;
}

// UnescapeURL: return the given character string unescaped from URL format
// Parameters: const char* src - pointer to const char; string to unescape; must not be
//		NULL
// Returns: pointer to escaped string; this is dynamically allocated using new
//		operator; after use this must be deallocated using delete operator
const char* const UnescapeURL(const char* src)
{
	// translates all sequences of form %xx corresponding characters
	// %xx specifies the character ascii code in hexadecimal
	if (src == NULL)
		return NULL;
	char* dst = (char*)new char[strlen(src) + 1];
	if (dst == NULL)
		return NULL;
	int srcI;
	int dstI;
	char pchHex[3];
	for (srcI = 0, dstI = 0; src[srcI] != 0; srcI = srcI + 1, dstI = dstI + 1)
		switch (src[srcI])
		{
			case '+':
				dst[dstI] = ' ';
				break;
			case '%':
				if (src[++srcI] == '%')
				{
					dst[dstI] = '%';
					break;
				}
				strncpy(pchHex, src + srcI++, 2);
				pchHex[2] = 0;
				dst[dstI] = (char)strtol(pchHex, NULL, 16);
				break;
			default:
				dst[dstI] = src[srcI];
		}
	dst[dstI] = 0;
	return dst;
}

// EscapeHTML: return the given character string escaped for HTML
// Parameters: const char* src - pointer to const char; string to escape; must not be
//		NULL
// Returns: pointer to escaped string; this is dynamically allocated using new
//		operator; after use this must be deallocated using delete operator
const char* const EscapeHTML(const char *src)
{
	// translates <, >, ", &, end of line and line feed characters into their HTML escapes
	if (src == NULL)
		return NULL;
	char* dst = (char*)new char[strlen(src) * 6 + 1];
	if (dst == NULL)
		return NULL;
	int srcI;
	int dstI;
	for (srcI = 0, dstI = 0; src[srcI] != 0; srcI = srcI + 1, dstI = dstI + 1)
		switch (src[srcI])
		{
		case '<':
			strcpy((dst + dstI), "&lt;");
			dstI = dstI + 3;
			break;
		case '>':
			strcpy((dst + dstI), "&gt;");
			dstI = dstI + 3;
			break;
		case '"':
			strcpy((dst + dstI), "&quot;");
			dstI = dstI + 5;
			break;
		case '&':
			strcpy((dst + dstI), "&amp;");
			dstI = dstI + 4;
			break;
		case '\n':
			if (dstI > 0 && (dst)[dstI - 1] == '\r')
			{
				break;
			}
		case '\r':
			strcpy((dst + dstI), "<BR>\r");
			dstI = dstI + 4;
			break;
		default:
			dst[dstI] = src[srcI];
		}
	dst[dstI] = 0;
	return dst;
}

ostream& operator << (ostream& p_rOutStream, HTMLEncoder p_rEncoder)
{
	return p_rEncoder.m_pEncodeMethod(p_rOutStream, p_rEncoder.m_pchString, p_rEncoder.m_bEncode);
}

ostream& outEncodeHTML(ostream& p_rcoOutStream, const char* p_pchString, bool p_bEncode)
{
	if (p_bEncode)
	{
		const char* pchEncodedStream = EscapeHTML(p_pchString);
		ostream& rcoOutStream = p_rcoOutStream << pchEncodedStream;
		delete [] pchEncodedStream;
		return rcoOutStream;
	}
	return p_rcoOutStream << p_pchString;
}

MYSQL_ROW QueryFetchRow(MYSQL* p_pDBConn, const string& p_rcoQuery, CMYSQL_RES& p_rcoQRes)
{
	if (p_pDBConn == NULL)
		p_pDBConn = MYSQLConnection();
	for (int nAttempt = 0; nAttempt < 10; nAttempt++)
	{
		if (mysql_query(p_pDBConn, p_rcoQuery.c_str()) != 0)
		{
#ifdef _DEBUG
			cout << "QueryFetchRow: error querying the database server" << endl;
#endif
			if (nAttempt >= 9)
				return NULL;
		}
		else
		{
			break;
		}
		usleep(100000);
		p_pDBConn = MYSQLConnection();
	}
	p_rcoQRes = mysql_store_result(p_pDBConn);
	return mysql_fetch_row(*p_rcoQRes);
}
