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
 
Define global types.
 
******************************************************************************/

#ifndef _TOL_TYPES_H
#define _TOL_TYPES_H

#include <string>
#include <map>
#include <stack>
#include <list>
#include <hashtable.h>
#include <mysql/mysql.h>

#include "global.h"
#include "cgi.h"
#include "threadkey.h"

// TDataType: data types recognised by template parser
typedef enum _TDataType {
    TOL_DT_NONE = 0,
    TOL_DT_NUMBER = 1,
    TOL_DT_STRING = 2,
    TOL_DT_NUMBER_PARITY = 3,
    TOL_DT_ORDER = 4,
    TOL_DT_ON_OFF = 5,
    TOL_DT_ART_POS = 6,
    TOL_DT_DATE = 7
} TDataType;

// result codes returned by lex (in lexem class) - see tol_lex.h
typedef enum _TOLLexResult {
    TOL_ERR_EOF = -3,
    TOL_ERR_LESS_IN_TOKEN = -2,
    TOL_ERR_END_QUOTE_MISSING = -1,
    TOL_LEX_NONE = 0,
    TOL_LEX_IDENTIFIER = 1,
    TOL_LEX_STATEMENT = 2,
    TOL_LEX_END_STATEMENT = 4,
    TOL_LEX_START_STATEMENT = 8
} TOLLexResult;

// class of attribute: normal and type (a special attribute used for dynamic attributes)
// see tol_atoms.h - TOLTypeAttributes
typedef enum _TOLAttrClass {
    TOL_NORMAL_ATTR = 0,
    TOL_TYPE_ATTR = 1
} TOLAttrClass;

// TContext: context in which certain statements can be used
typedef enum _TContext {
    TOL_CT_DEFAULT = 0,
    TOL_CT_LIST = 1,
    TOL_CT_IF = 2,
    TOL_CT_PRINT = 3,
    TOL_CT_EDIT = 4,
    TOL_CT_SELECT = 5,
    TOL_CT_URLPARAMETERS = 6,
    TOL_CT_WITH = 7
} TContext;

// TOperator: operators recognised by template parser
typedef enum _TOperator {
    TOL_NO_OP = 0,
    TOL_OP_IS = 1,
    TOL_OP_IS_NOT = 2,
    TOL_OP_GREATER = 4,
    TOL_OP_SMALLER = 8
} TOperator;

// TAction: actions identifiers
typedef enum _TAction {
    TOL_ACT_NONE = 0x0000,
    TOL_ACT_LANGUAGE = 0x0001,
    TOL_ACT_INCLUDE = 0x0002,
    TOL_ACT_PUBLICATION = 0x0003,
    TOL_ACT_ISSUE = 0x0004,
    TOL_ACT_SECTION = 0x0005,
    TOL_ACT_ARTICLE = 0x0006,
    TOL_ACT_LIST = 0x0007,
    TOL_ACT_URLPARAMETERS = 0x0008,
    TOL_ACT_FORMPARAMETERS = 0x0009,
    TOL_ACT_PRINT = 0x000a,
    TOL_ACT_IF = 0x000b,
    TOL_ACT_DATE = 0x000c,
    TOL_ACT_TEXT = 0x000d,
    TOL_ACT_LOCAL = 0x000e,
    TOL_ACT_SUBSCRIPTION = 0x00f,
    TOL_ACT_EDIT = 0x0010,
    TOL_ACT_SELECT = 0x0011,
    TOL_ACT_USER = 0x0012,
    TOL_ACT_LOGIN = 0x0013,
    TOL_ACT_SEARCH = 0x0014,
    TOL_ACT_WITH = 0x0015
} TAction;

typedef enum _TListModifier {
    TOL_LMOD_ISSUE = 0x0201,
    TOL_LMOD_SECTION = 0x0202,
    TOL_LMOD_ARTICLE = 0x0203,
    TOL_LMOD_SEARCHRESULT = 0x0204,
    TOL_LMOD_SUBTITLE = 0x0205
} TListModifier;

typedef enum _TIfModifier {
    TOL_IMOD_ISSUE = 0x0301,
    TOL_IMOD_SECTION = 0x0302,
    TOL_IMOD_ARTICLE = 0x0303,
    TOL_IMOD_LIST = 0x0304,
    TOL_IMOD_PREVIOUSITEMS = 0x0305,
    TOL_IMOD_NEXTITEMS = 0x0306,
    TOL_IMOD_ALLOWED = 0x0307,
    TOL_IMOD_SUBSCRIPTION = 0x0308,
    TOL_IMOD_USER = 0x0309,
    TOL_IMOD_LOGIN = 0x030a,
    TOL_IMOD_PUBLICATION = 0x030b,
    TOL_IMOD_SEARCH = 0x030c,
    TOL_IMOD_PREVSUBTITLES = 0x030d,
    TOL_IMOD_NEXTSUBTITLES = 0x030e,
    TOL_IMOD_SUBTITLE = 0x030f,
    TOL_IMOD_CURRENTSUBTITLE = 0x0310,
    TOL_IMOD_IMAGE = 0x311,
    TOL_IMOD_LANGUAGE = 0x312
} TIfModifier;

typedef enum _TPrintModifier {
    TOL_PMOD_IMAGE = 0x0401,
    TOL_PMOD_PUBLICATION = 0x0402,
    TOL_PMOD_ISSUE = 0x0403,
    TOL_PMOD_SECTION = 0x0404,
    TOL_PMOD_ARTICLE = 0x0405,
    TOL_PMOD_LIST = 0x0406,
    TOL_PMOD_LANGUAGE = 0x0407,
    TOL_PMOD_SUBSCRIPTION = 0x0408,
    TOL_PMOD_USER = 0x0409,
    TOL_PMOD_LOGIN = 0x040a,
    TOL_PMOD_SEARCH = 0x040b,
    TOL_PMOD_SUBTITLE = 0x040c
} TPrintModifier;

typedef enum _TEditModifier {
    TOL_EMOD_SUBSCRIPTION = 0x0501,
    TOL_EMOD_USER = 0x0502,
    TOL_EMOD_LOGIN = 0x0503,
    TOL_EMOD_SEARCH = 0x0504
} TEditModifier;

typedef enum _TSelectModifier {
    TOL_SMOD_SUBSCRIPTION = 0x0601,
    TOL_SMOD_USER = 0x0602,
    TOL_SMOD_SEARCH = 0x0603
} TSelectModifier;

typedef enum _TSubsUnit {
    TOL_SU_NONE = 0,
    TOL_SU_DAY = 1,
    TOL_SU_WEEK = 2,
    TOL_SU_MONTH = 3,
    TOL_SU_YEAR = 4
} TSubsUnit;

#define ID_MAXLEN 80
#define MAX_BUF_LEN 1000

class TOLAtom;
class TOLAttribute;
class TOLTypeAttributes;
class TOLStatementContext;
class TOLStatement;
class TOLParameter;
class TOLAction;
class TOLContext;
class TOLParser;
class TOLError;
class CGI;
class TOLCLexem;

typedef bool (*cpChar_EQUAL) (cpChar, cpChar);
inline bool cpCharEqual(cpChar a1, cpChar a2)
{
	return !strcasecmp(a1, a2);
}

typedef unsigned int (*cpChar_HASH) (cpChar);
inline unsigned int cpCharHashFn(cpChar a)
{
	return (unsigned int)strlen(a) % 4;
}

typedef cpChar (*TOLAttribute_VALUE) (const TOLAttribute&);
typedef hashtable < TOLAttribute, cpChar, cpChar_HASH, TOLAttribute_VALUE, cpChar_EQUAL >
	TOLAttributeHash;

typedef cpChar (*TOLTypeAttributes_VALUE) (const TOLTypeAttributes&);
typedef hashtable < TOLTypeAttributes, cpChar, cpChar_HASH, TOLTypeAttributes_VALUE,
					cpChar_EQUAL >
	TOLTypeAttributesHash;

typedef bool (*TContext_EQUAL) (TContext, TContext);
typedef TContext (*TOLStatementContext_VALUE) (const TOLStatementContext&);
typedef unsigned int (*TContext_HASH) (TContext);

typedef hashtable < TOLStatementContext, TContext, TContext_HASH, TOLStatementContext_VALUE,
					TContext_EQUAL >
	TOLStatementContextHash;

typedef cpChar (*TOLStatement_VALUE) (const TOLStatement&);
typedef hashtable < TOLStatement, cpChar, cpChar_HASH, TOLStatement_VALUE, cpChar_EQUAL >
	TOLStatementHash;

typedef list < TOLParameter > TOLParameterList;

typedef list < TOLAction* > TOLPActionList;

typedef map < int, cpChar, less < int > > Int2String;

typedef map < string, int, less < string > > String2Int;

typedef map < string, bool, less < string > > String2Bool;

typedef map < string, long int, less < string > > String2LInt;

typedef stack < TOLContext > TOLContextStack;

typedef cpChar (*TOLParser_VALUE) (const TOLParser*);
typedef hashtable < TOLParser*, cpChar, cpChar_HASH, TOLParser_VALUE, cpChar_EQUAL >
	TOLParserHash;

typedef list < TOLError* > TOLErrorList;

typedef int (*TintEqual) (int, int);
inline int intEqual (int i1, int i2)
{
	return i1 == i2;
}

typedef int (*TintValue) (int);
inline int intValue(int i)
{
	return i;
}

typedef int (*TintHash) (int);
inline int intHashFn(int i)
{
	return i % 4;
}

typedef hashtable < int, int, TintHash, TintValue, TintEqual > intHash;

typedef int (*TlintEqual) (long int, long int);
inline int lintEqual (long int i1, long int i2)
{
	return i1 == i2;
}

typedef long int (*TlintValue) (long int);
inline long int lintValue(long int i)
{
	return i;
}

typedef int (*TlintHash) (long int);
inline int lintHashFn(long int i)
{
	return i % 4;
}

typedef hashtable < long int, long int, TlintHash, TlintValue, TlintEqual > lintHash;

typedef map < long int, lintHash, less < long int > > lint2lintHash;

typedef map < string, string, less < string > > string2string;

typedef list < string > StringList;

typedef map < string, StringList, less < string > > String2StringList;

typedef map < string, StringList::iterator, less < string > > String2StringListIt;

typedef cpChar (*TstringValue)(const string&);
inline cpChar stringValue(const string& s)
{
	return s.c_str();
}

typedef hashtable < string, cpChar, cpChar_HASH, TstringValue, cpChar_EQUAL > StringHash;

typedef CThreadKey < MYSQL > TK_MYSQL;
inline void TK_MYSQL::destroyData(void* p_pData) throw()
{}

typedef CThreadKey < char > TK_char;
inline void TK_char::destroyData(void* p_pData) throw()
{
	delete (pChar)p_pData;
}

typedef CThreadKey < bool > TK_bool;
inline void TK_bool::destroyData(void* p_pData) throw()
{
	delete (bool*)p_pData;
}

typedef CThreadKeyConst < string> TK_const_string;

#endif
