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

Defines lexem identifiers, lexem names, CLexem an CLex classes
CLex performs syntactical analisys; it builds the lexems structure which is a
hash of statements (CStatment); on request it delivers the next lexem which
can be an atom, attribute or statement

******************************************************************************/

#ifndef _CMS_LEX
#define _CMS_LEX

#include <string.h>
#include <iostream>
#include <map>

#include "cms_types.h"
#include "atoms.h"

using std::map;

// statement identifiers
#define CMS_ST_LANGUAGE 1
#define CMS_ST_INCLUDE 2
#define CMS_ST_PUBLICATION 3
#define CMS_ST_ISSUE 4
#define CMS_ST_SECTION 5
#define CMS_ST_ARTICLE 6
#define CMS_ST_ORDER 7
#define CMS_ST_LIST 8
#define CMS_ST_FOREMPTYLIST 9
#define CMS_ST_ENDLIST 10
#define CMS_ST_IMAGE 11
#define CMS_ST_URLPARAMETERS 12
#define CMS_ST_FORMPARAMETERS 13
#define CMS_ST_PRINT 14
#define CMS_ST_IF 15
#define CMS_ST_PREVIOUSITEMS 16
#define CMS_ST_NEXTITEMS 17
#define CMS_ST_ELSE 18
#define CMS_ST_ENDIF 19
#define CMS_ST_DATE 20
#define CMS_ST_LOCAL 21
#define CMS_ST_ENDLOCAL 22
#define CMS_ST_SUBSCRIPTION 23
#define CMS_ST_ENDSUBSCRIPTION 24
#define CMS_ST_ALLOWED 25
#define CMS_ST_EDIT 26
#define CMS_ST_SELECT 27
#define CMS_ST_USER 28
#define CMS_ST_ENDUSER 29
#define CMS_ST_LOGIN 30
#define CMS_ST_ENDLOGIN 31
#define CMS_ST_SEARCH 32
#define CMS_ST_ENDSEARCH 33
#define CMS_ST_SEARCHRESULT 34
#define CMS_ST_SUBTITLE 35
#define CMS_ST_PREVSUBTITLES 36
#define CMS_ST_NEXTSUBTITLES 37
#define CMS_ST_WITH 38
#define CMS_ST_ENDWITH 39
#define CMS_ST_CURRENTSUBTITLE 40
#define CMS_ST_TOPIC 41
#define CMS_ST_URIPATH 42
#define CMS_ST_URI 43
#define CMS_ST_ARTICLETOPIC 44
#define CMS_ST_SUBTOPIC 45

// statement names
#define ST_LANGUAGE "Language"
#define ST_INCLUDE "Include"
#define ST_PUBLICATION "Publication"
#define ST_ISSUE "Issue"
#define ST_SECTION "Section"
#define ST_ARTICLE "Article"
#define ST_ORDER "Order"
#define ST_LIST "List"
#define ST_FOREMPTYLIST "ForEmptyList"
#define ST_ENDLIST "EndList"
#define ST_IMAGE "Image"
#define ST_URLPARAMETERS "URLParameters"
#define ST_FORMPARAMETERS "FormParameters"
#define ST_PRINT "Print"
#define ST_IF "If"
#define ST_PREVIOUSITEMS "PreviousItems"
#define ST_NEXTITEMS "NextItems"
#define ST_ELSE "Else"
#define ST_ENDIF "EndIf"
#define ST_DATE "Date"
#define ST_LOCAL "Local"
#define ST_ENDLOCAL "EndLocal"
#define ST_SUBSCRIPTION "Subscription"
#define ST_ENDSUBSCRIPTION "EndSubscription"
#define ST_ALLOWED "Allowed"
#define ST_EDIT "Edit"
#define ST_SELECT "Select"
#define ST_USER "User"
#define ST_ENDUSER "EndUser"
#define ST_LOGIN "Login"
#define ST_ENDLOGIN "EndLogin"
#define ST_SEARCH "Search"
#define ST_ENDSEARCH "EndSearch"
#define ST_SEARCHRESULT "SearchResult"
#define ST_SUBTITLE "Subtitle"
#define ST_PREVSUBTITLES "PrevSubtitles"
#define ST_NEXTSUBTITLES "NextSubtitles"
#define ST_WITH "With"
#define ST_ENDWITH "EndWith"
#define ST_CURRENTSUBTITLE "CurrentSubtitle"
#define ST_TOPIC "Topic"
#define ST_URIPATH "URIPath"
#define ST_URI "URI"
#define ST_ARTICLETOPIC "ArticleTopic"
#define ST_SUBTOPIC "Subtopic"

// The lexem returned by lex class
class CLexem
{
private:
	TLexResult m_Res;			// result code
	TDataType m_DataType;		// lexem data type
	const CAtom* m_pcoAtom;		// pointer to atom structure
	const char* m_pchTextStart;	// html text found (after ">" - end of statement - lexem)
	lint m_nTextLen;		// html text length

public:
	// constructor
	CLexem(TLexResult p_Res, TDataType p_DataType, const CAtom* p_pcoAtom = NULL,
	         const char* p_pchText = NULL, lint p_nTextLen = 0)
			: m_Res(p_Res), m_DataType(p_DataType), m_pcoAtom(p_pcoAtom)
	{
		m_pchTextStart = p_pchText;
		m_nTextLen = p_nTextLen;
	}

	// copy constructor
	CLexem(const CLexem& p_rcoLexem) { *this = p_rcoLexem; }

	// assign operator
	const CLexem& operator =(const CLexem& p_rcoLexem)
	{
		if (this == &p_rcoLexem)
			return * this;
		m_Res = p_rcoLexem.m_Res;
		m_pcoAtom = p_rcoLexem.m_pcoAtom;
		m_DataType = p_rcoLexem.m_DataType;
		m_pchTextStart = p_rcoLexem.m_pchTextStart;
		m_nTextLen = p_rcoLexem.m_nTextLen;
		return *this;
	}

	// return current atom
	const CAtom* atom() const { return m_pcoAtom; }

	// set atom
	void setAtom(const CAtom* p_pcoAtom) { m_pcoAtom = p_pcoAtom; }

	TLexResult res() const { return m_Res; }
	void setRes(TLexResult p_Res) { m_Res = p_Res; }

	TDataType dataType() const { return m_DataType; }
	void setDataType(TDataType p_DataType) { m_DataType = p_DataType; }

	const char* textStart() const { return m_pchTextStart; }
	void setTextStart(const char* p_pchTxtStart) { m_pchTextStart = p_pchTxtStart; }

	lint textLen() const { return m_nTextLen; }
	void setTextLen(lint p_nTextLen) { m_nTextLen = p_nTextLen; }
};


class CStatementMap : public map<string, CStatement*, str_case_less>
{
public:
	CStatementMap();

	CStatementMap(const CStatementMap& o) { *this = o; }

	~CStatementMap() { clear(); }

	const CStatementMap& operator =(const CStatementMap&);

	bool operator ==(const CStatementMap&) const;

	bool operator !=(const CStatementMap& o) const { return ! (*this == o); }

	void insert(CStatement* p_pcoSt) { (*this)[p_pcoSt->identifier()] = p_pcoSt; }

	void clear();

private:
	int InitStatements();
};

// lex class; performs syntactic analysis
class CLex
{
private:
	static const char s_pchCTokenStart[];	// start token for campsite instruction
	static const char s_chCTokenEnd;		// end token
	static CStatementMap s_coStatements;	// statements
	int m_nLine;							// current line
	int m_nColumn;							// current column
	int m_nPrevLine;						// previous line
	int m_nPrevColumn;						// previous column
	int m_nState;							// lex current state
	CAtom m_coAtom;							//
	CLexem m_coLexem;						// current lexem
	char m_chChar;							// current character read from buffer
	int m_nTempIndex;						// temporary buffer index of current character
	bool m_bLexemStarted;					// true if reading lexem
	bool m_bQuotedLexem; 					// true if m_bLexemStarted is true and the new
											// lexem is quoted
	bool m_bIsEOF;							// true if end of text buffer
	int m_nHtmlCodeLevel;
	const char* m_pchTextStart;				// html text start
	const char* m_pchInBuf;					// input text buffer
	ulint m_nBufLen;						// input text buffer length
	ulint m_nIndex;							// index to current character of input text buffer

private:
	// NextChar: return next character from text buffer
	char NextChar();

	// IdentifyAtom: identifies the current lexem
	const CLexem* IdentifyAtom();

	// AppentOnAtom: return true if not end of identifier buffer (can append character to atom
	// identifier)
	int AppendOnAtom();

	// InitStatements: initialise statements
//	static void InitStatements();

public:
	// constructor
	CLex(const char* = 0, ulint = 0);

	// copy-constructor
	CLex(const CLex& p_rcoSrc)
		: m_coAtom(""), m_coLexem(CMS_LEX_NONE, CMS_DT_NONE) { *this = p_rcoSrc; }

	// destructor
	~CLex() { }

	// reset: reset lex
	void reset(const char* = NULL, ulint = 0) throw();

	// updateArticleTypes: update article types structure from database
	static bool updateArticleTypes();

	// assign operator
	const CLex& operator =(const CLex&);

	// getLexem: return next lexem
	const CLexem* getLexem();

	// return pointer to statement identified by name
	static const CStatement* findSt(const string&);

	// return lex end token
	static string endToken() { return string(&s_chCTokenEnd, 1); }

	// line: return current line
	int line() const { return m_nLine; }

	// column: return current column
	int column() const { return m_nColumn; }

	// prevLine: return previous line
	int prevLine() const { return m_nPrevLine; }

	// prevColumn: return previous column
	int prevColumn() const
	{
		if (m_coLexem.atom() && m_coLexem.atom()->identifier().length() > 0)
			return m_nPrevColumn - m_coLexem.atom()->identifier().length() + 1;
		return m_nPrevColumn;
	}

	// printStatements: print known statements
	static void printStatements();
};

#endif
