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
#include "cgi.h"
#include "cms_types.h"

using std::bad_alloc;

#define PARAM_NR 36
#define ERR_NR 6

// CGIParams: structure containing some CGI environment variables
typedef struct CGIParams
{
	char* m_pchDocumentRoot;
	char* m_pchIP;
	char* m_pchPathTranslated;
	char* m_pchPathInfo;
	char* m_pchRequestMethod;
	char* m_pchQueryString;
	char* m_pchHttpCookie;

	CGIParams()
			: m_pchDocumentRoot(NULL), m_pchIP(NULL), m_pchPathTranslated(NULL),
			m_pchPathInfo(NULL), m_pchRequestMethod(NULL), m_pchQueryString(NULL),
			m_pchHttpCookie(NULL)
	{}

	~CGIParams()
	{
		delete []m_pchDocumentRoot;
		delete []m_pchIP;
		delete []m_pchPathTranslated;
		delete []m_pchPathInfo;
		delete []m_pchRequestMethod;
		delete []m_pchQueryString;
		delete []m_pchHttpCookie;
	}
} CGIParams;

class RunException : public exception
{
private:
	string m_coMsg;

public:
	RunException(const string& p_rcoMsg) : m_coMsg(p_rcoMsg) {}

	virtual ~RunException() throw() {}

	virtual const char* what() const throw() { return m_coMsg.c_str(); }
};

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
//		CGIParams* p_pParams - pointer to cgi environment structure
//		sockstream& p_rOs - output stream
int RunParser(MYSQL* p_pSQL, CGIParams* p_pParams, sockstream& p_rOs) throw(RunException, bad_alloc);

// WriteCharset: write http tag specifying the charset - according to current language
// Parameters:
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
//		sockstream& fs - output stream
int WriteCharset(CContext& c, MYSQL* pSql, sockstream& fs);

// Login: perform login action: log user in
// Parameters:
//		CGI& cgi - cgi environment
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
int Login(CGI& cgi, CContext& c, MYSQL* pSql);

// CheckUserInfo: read user informations from CGI parameters
// Parameters:
//		CGI& cgi - cgi environment
//		CContext& c - current context
//		const char* ppchParams[] - parameters to read from cgi environment
//		int param_nr - parameters number
int CheckUserInfo(CGI& cgi, CContext& c, const char* ppchParams[], int param_nr);

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
//		CGI& cgi - cgi environment
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
int DoSubscribe(CGI& cgi, CContext& c, MYSQL* pSql);

// getword: read the next word from string of characters
// Parameters:
//		char** word - resulted word (dinamically allocated); must be deallocated with
// free(*word)
//		const char** line - pointer to string of characters; incremented after reading word
//		char stop - stop character (word separator)
void getword(char** word, const char** line, char stop);

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
//		CGI& cgi - cgi environment
int Search(CContext& c, MYSQL* pSql, CGI& cgi);

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
