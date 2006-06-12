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

Define error codes, macros and classes used for dealing parse and write errors

******************************************************************************/

#ifndef _CMS_ERROR
#define _CMS_ERROR

#include <string>
#include <fstream>

#include "cms_types.h"
#include "globals.h"

using std::fstream;

// ERROR CODES
#define RES_OK 0
#define ERR_NOTYPE 1
#define ERR_NOMEM 2
#define ERR_SQLCONNECT 3
#define ERR_QUERY 4
#define ERR_NODATA 5
#define ERR_NOHASH 6
#define ERR_NOHASHENT 7
#define ERR_NOOP 8
#define ERR_NOPARAM 9
#define ERR_NOLISTINDEX 10
#define ERR_NOACCESS 11
#define ERR_INVALID_FIELD 12
#define ERR_NOKEY 13
#define ERR_LOCKHASH 14

#define PERR_ATOM_MISSING 100
#define PERR_STATEMENT_MISSING 101
#define PERR_ATOM_NOT_STATEMENT 102
#define PERR_IDENTIFIER_MISSING 103
#define PERR_WRONG_STATEMENT 104
#define PERR_NO_ATOM_IN_LEXEM 105
#define PERR_END_STATEMENT_MISSING 106
#define PERR_NOT_VALUE 107
#define PERR_INVALID_ATTRIBUTE 108
#define PERR_INVALID_STATEMENT 109
#define PERR_INVALID_OPERATOR 110
#define PERR_EOS_MISSING 111
#define PERR_DATA_TYPE 112
#define PERR_ATTRIBUTE_REDEF 113
#define PERR_INV_TYPE_VAL 114
#define PERR_UNEXPECTED_EOF 115
#define PERR_INVALID_VALUE 116
#define PERR_INCLUDE_CICLE 117
#define PERR_INVALID_DATE_FORM 118
#define PERR_INVALID_TEMPLATE 119
#define PERR_INTERNAL 500

#define EMAP_STAT 1000
#define EMAP_NOTREGFILE 1001
#define EMAP_EOPENFILE 1002
#define EMAP_FAILED 1003

#define SERR_INTERNAL 2000
#define SERR_USER_NOT_SPECIFIED 2001
#define SERR_USER_NOT_READER 2002
#define SERR_PUBL_NOT_SPECIFIED 2003
#define SERR_SUBS_NOT_PAID 2004
#define SERR_UNIT_NOT_SPECIFIED 2005
#define SERR_TYPE_NOT_SPECIFIED 2006

#define UERR_INTERNAL 3000
#define UERR_USER_EXISTS 3001
#define UERR_NO_NAME 3002
#define UERR_NO_UNAME 3003
#define UERR_NO_PASSWORD 3004
#define UERR_NO_EMAIL 3005
#define UERR_DUPLICATE_EMAIL 3006
#define UERR_INVALID_USER 3007
#define UERR_NO_COUNTRY 3008
#define UERR_NO_PASSWORD_AGAIN 3009
#define UERR_PASSWORDS_DONT_MATCH 3010
#define UERR_PASSWORD_TOO_SIMPLE 3011

#define LERR_INTERNAL 4000
#define LERR_NO_UNAME 4001
#define LERR_INVALID_UNAME 4002
#define LERR_NO_PASSWORD 4003
#define LERR_INVALID_PASSWORD 4004

#define SRERR_INTERNAL 5000
#define SRERR_NO_KEYWORDS 5001
#define SRERR_INVALID_LEVEL 5002

#define ACERR_INTERNAL 5000
#define ACERR_USER_NOT_DEFINED 5001
#define ACERR_EMPTY_CONTENT 5002
#define ACERR_ARTICLE_NOT_DEFINED 5003
#define ACERR_COMMENTS_NOT_ALLOWED 5004
#define ACERR_USER_BANNED 5005
#define ACERR_REJECTED 5006

#define MODE_PARSE 0
#define MODE_WRITE 1

// MACROS
#define SetError(el, id, mod)\
el.insert(el.end(), CError(id, mod));

#define FatalError(el, id, mod)\
{\
el.insert(el.end(), new CError(id, mod));\
return 1;\
}

#define SetPError(el, id, mod, req, l, c)\
el.insert(el.end(), new CError(id, mod, req, l, c));

#define FatalPError(el, id, mod, req, l, c)\
{\
el.insert(el.end(), new CError(id, mod, req, l, c));\
return 1;\
}

#include <map>

using std::map;
using std::string;

typedef map <int, const char*> Int2String;

// CError: class containing information about an error occured when parsing or writing
// output
class CError
{
private:
	int code;			// error code
	int mode;			// mode: parsing or writing output
	string required;	// when parsing: required statements, attributes, operators or values
	lint row;			// when parsing: the row containing the error
	lint column;		// when parsing: the column where the error occured
	static const string req_word, line_word, column_word, when_word, error_word;
	static Int2String messages;		// error messages
	static Int2String modes;		// modes: parsing or writing output
	static pthread_once_t m_InitControl;	// control the initialisation

	static void init();		// init messages and modes

public:
	// Constructor
	// Parameters:
	//	int p_nCode - error code
	//	int p_nMode - mode (parsing/writing output)
	//	string p_coRequired - on parsing, required tokens
	//	lint p_nRow - on parsing, the row where the error occured
	//	lint p_nColumng - on parsing, the column where the error occured
	CError(int p_nCode, int p_nMode, string p_coRequired = string(""),
	       lint p_nRow = 0, lint p_nColumn = 0);

	// Print: print the error
	// Parameters:
	//	sockstream& fs - the stream to print the error to
	//	bool p_bPrintContext - if true print mode (parsing/writing output)
	sockstream& Print(sockstream& fs, bool p_bPrintContext = false);
};

#endif
