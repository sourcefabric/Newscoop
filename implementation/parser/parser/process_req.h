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

Functions for processing client requests

******************************************************************************/

#ifndef _PROCESS_REQ_H
#define _PROCESS_REQ_H

#include <mysql/mysql.h>

#include "globals.h"
#include "context.h"
#include "cms_types.h"

using std::bad_alloc;

#define PARAM_NR 36
#define ERR_NR 6

// RunParser:
//   - prepare the context: read cgi environment into context, read user subscriptions
//     into context
//   - perform requested actions: log user in, add user to database, add subscriptions,
//     modify user informations, search keywords against database
//   - launch parser with current context: search for a parser instance of the requested
//     template; if not found, create parser instance for requested template and add it
//     to parsers hash; call Parse and WriteOutput methods of parser instance; eventually
//     call PrintParseErrors and PrintWriteErrors for admin users
// Return RES_OK if no errors occured, error code otherwise
// Parameters:
//		MYSQL* p_pSql - pointer to MySQL connection
//		CURL* p_pcoURL - pointer to the URL object
//		const char* p_pchRemoteIP - pointer to string containing the client IP address
//		sockstream& p_rOs - output stream
int RunParser(MYSQL* p_pSQL, CURL* p_pcoURL, const char* p_pchRemoteIP, sockstream& p_rOs)
	throw(RunException, bad_alloc);

// WriteCharset: write http tag specifying the charset - according to current language
// Parameters:
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
//		sockstream& fs - output stream
int WriteCharset(CContext& c, MYSQL* pSql, sockstream& fs);

// Login: perform login action: log user in
// Parameters:
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
int Login(CContext& c, MYSQL* pSql);

// CheckUserInfo: read user informations from CGI parameters
// Parameters:
//		CContext& c - current context
//		const char* ppchParams[] - parameters to read from cgi environment
//		int param_nr - parameters number
int CheckUserInfo(CContext& c, const char* ppchParams[], int param_nr);

// AddUser: perform add user action (add user to database); return error code
// Parameters:
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
//		const char* ppchParams[] - parameters to read from context (user information)
//		int param_nr - parameters number
//		const int errs[] - error list (errors codes)
//		int err_nr - errors number
int AddUser(CContext& c, MYSQL* pSql, const char* ppchParams[], int param_nr,
			const int errs[], int err_nr);

// ModifyUser: perform modify user action (modify user information in the database)
// Return error code.
// Parameters:
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
//		const char* ppchParams[] - parameters to read from context (user information)
//		int param_nr - parameters number
//		const int errs[] - error list (errors codes)
//		int err_nr - errors number
int ModifyUser(CContext& c, MYSQL* pSql, const char* ppchParams[], int param_nr,
               const int errs[], int err_nr);

// DoSubscribe: perform subscribe action (subscribe user to a certain publication)
// Parameters:
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
int DoSubscribe(CContext& c, MYSQL* pSql);

// SetReaderAccess: update current context: set reader access to publication sections
// according to user subscriptions.
// Parameters:
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
void SetReaderAccess(CContext& c, MYSQL* pSql);

// Search: perform search action; search against the database for keywords retrieved from
// cgi environment
// Parameters:
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
int Search(CContext& c, MYSQL* pSql);

// ParseKeywords: read keywords from a string of keywords and add them to current context
// Parameters:
//		const char* s - string of keywords
//		CContext& c - current context
void ParseKeywords(const char* s, CContext& c);

// IsSeparator: return true if c character is separator
// Parameters:
//		char c - character to test
bool IsSeparator(char c);

#endif
