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

#ifndef _CMS_TYPES
#define _CMS_TYPES

#include <string>
#include <map>
#include <set>
#include <functional>
#include <mysql/mysql.h>
#include <unistd.h>
#include <fstream>
#include <iostream>
#include <exception>
#if (__GNUC__ < 3)
#include <streambuf.h>
#else
#include <streambuf>
#endif

#include "threadkey.h"

using std::string;
using std::map;
using std::set;
using std::less;
using std::binary_function;

// TDataType: data types recognised by template parser
typedef enum {
	CMS_DT_NONE = 0,
	CMS_DT_INTEGER = 1,
	CMS_DT_STRING = 2,
	CMS_DT_SWITCH = 3,
	CMS_DT_DATE = 4,
	CMS_DT_TIME = 5,
	CMS_DT_DATETIME = 6,
	CMS_DT_ENUM = 7,
	CMS_DT_TOPIC = 8
} TDataType;

// result codes returned by lex (in lexem class) - see tol_lex.h
typedef enum {
	CMS_ERR_EOF = -3,
	CMS_ERR_LESS_IN_TOKEN = -2,
	CMS_ERR_END_QUOTE_MISSING = -1,
	CMS_LEX_NONE = 0,
	CMS_LEX_IDENTIFIER = 1,
	CMS_LEX_STATEMENT = 2,
	CMS_LEX_END_STATEMENT = 4,
	CMS_LEX_START_STATEMENT = 8
} TLexResult;

// class of attribute: normal and type (a special attribute used for dynamic attributes)
// see tol_atoms.h - CTypeAttributes
typedef enum {
	CMS_NORMAL_ATTR = 0,
	CMS_TYPE_ATTR = 1
} TAttrClass;

extern const int CMS_CT_DEFAULT;
extern const int CMS_CT_LIST;
extern const int CMS_CT_IF;
extern const int CMS_CT_PRINT;
extern const int CMS_CT_EDIT;
extern const int CMS_CT_SELECT;
extern const int CMS_CT_URLPARAMETERS;
extern const int CMS_CT_WITH;
extern const int CMS_CT_ISSUE;
extern const int CMS_CT_ARTICLE;
extern const int CMS_CT_ARTICLECOMMENT;
extern const int CMS_CT_SEARCHRESULT;

// TAction: actions identifiers
typedef enum _TAction {
	CMS_ACT_NONE = 0x0000,
	CMS_ACT_LANGUAGE = 0x0001,
	CMS_ACT_INCLUDE = 0x0002,
	CMS_ACT_PUBLICATION = 0x0003,
	CMS_ACT_ISSUE = 0x0004,
	CMS_ACT_SECTION = 0x0005,
	CMS_ACT_ARTICLE = 0x0006,
	CMS_ACT_LIST = 0x0007,
	CMS_ACT_URLPARAMETERS = 0x0008,
	CMS_ACT_FORMPARAMETERS = 0x0009,
	CMS_ACT_PRINT = 0x000a,
	CMS_ACT_IF = 0x000b,
	CMS_ACT_DATE = 0x000c,
	CMS_ACT_TEXT = 0x000d,
	CMS_ACT_LOCAL = 0x000e,
	CMS_ACT_SUBSCRIPTION = 0x000f,
	CMS_ACT_EDIT = 0x0010,
	CMS_ACT_SELECT = 0x0011,
	CMS_ACT_USER = 0x0012,
	CMS_ACT_LOGIN = 0x0013,
	CMS_ACT_SEARCH = 0x0014,
	CMS_ACT_WITH = 0x0015,
	CMS_ACT_TOPIC = 0x0016,
	CMS_ACT_URIPATH = 0x0017,
	CMS_ACT_URI = 0x0018,
	CMS_ACT_HTMLENCODING = 0x0019,
	CMS_ACT_ARTICLECOMMENTFORM = 0x0020,
	CMS_ACT_ARTICLECOMMENT = 0x0021,
	CMS_ACT_URL = 0x0022
} TAction;

// TPubLevel: identifies the levels in the publication structure
typedef enum _TPubLevel {
	CMS_PL_ROOT = 0x0000,
	CMS_PL_LANGUAGE = 0x0001,
	CMS_PL_PUBLICATION = 0x0002,
	CMS_PL_ISSUE = 0x0003,
	CMS_PL_SECTION = 0x0004,
	CMS_PL_ARTICLE = 0x0005,
	CMS_PL_SUBTITLE = 0x0006
} TPubLevel;

typedef enum _TSubsUnit {
	CMS_SU_NONE = 0,
	CMS_SU_DAY = 1,
	CMS_SU_WEEK = 2,
	CMS_SU_MONTH = 3,
	CMS_SU_YEAR = 4
} TSubsUnit;

class CAtom;
class CAttribute;
class CTypeAttributes;
class CStatementContext;
class CStatement;
class CParameter;
class CAction;
class CContext;
class CParser;
class CError;
class CGI;
class CCLexem;

typedef CThreadKey < MYSQL > TK_MYSQL;
template <> inline void TK_MYSQL::destroyData(void* p_pData) throw() {}

typedef CThreadKey < char > TK_char;
template <> inline void TK_char::destroyData(void* p_pData) throw()
{
	delete (char*)p_pData;
}

typedef CThreadKey < bool > TK_bool;
template <> inline void TK_bool::destroyData(void* p_pData) throw()
{
	delete (bool*)p_pData;
}

typedef CThreadKeyConst < string> TK_const_string;

class outbuf : public std::streambuf
{
public:
	outbuf(int p_nFileId = -1) : m_nFileId(p_nFileId) {}

protected:
	// central output function
	virtual int overflow (int c)
	{
		if (m_nFileId == -1)
			return EOF;
		if (c != EOF)
		{
			// write the character to the output file
			if (write(m_nFileId, &c, 1) != 1)
				return EOF;
		}
		return c;
	}

private:
	int m_nFileId;
};

typedef std::ostream sockstream;

class RunException : public std::exception
{
	private:
		string m_coMsg;

	public:
		RunException(const string& p_rcoMsg) : m_coMsg(p_rcoMsg) {}

		virtual ~RunException() throw() {}

		virtual const char* what() const throw() { return m_coMsg.c_str(); }
};

int case_comp(const string& p_rcoS1, const string& p_rcoS2);
inline int case_comp(const string& p_rcoS1, const string& p_rcoS2)
{
	return strcasecmp(p_rcoS1.c_str(), p_rcoS2.c_str());
}

int case_comp(const string& p_rcoS1, const string& p_rcoS2, int len);
inline int case_comp(const string& p_rcoS1, const string& p_rcoS2, int len)
{
	return strcasecmp(p_rcoS1.substr(0, len).c_str(), p_rcoS2.substr(0, len).c_str());
}

struct str_case_less : public binary_function<string, string, bool>
{
	bool operator ()(const string& first, const string& second) const
	{ return case_comp(first, second) < 0; }
};

typedef map <string, string, str_case_less> String2String;

typedef set <string, str_case_less> StringSet;

#endif
