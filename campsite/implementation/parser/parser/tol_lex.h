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
 
Defines lexem identifiers, lexem names, TOLLexem an TOLLex classes
TOLLex performs syntactical analisys; it builds the lexems structure which is a
hash of statements (TOLStatment); on request it delivers the next lexem which
can be an atom, attribute or statement
 
******************************************************************************/

#ifndef _TOL_LEX_H
#define _TOL_LEX_H

#include <string.h>
#include <iostream.h>

#include "tol_types.h"
#include "tol_atoms.h"

// statement identifiers
#define TOL_ST_LANGUAGE 1
#define TOL_ST_INCLUDE 2
#define TOL_ST_PUBLICATION 3
#define TOL_ST_ISSUE 4
#define TOL_ST_SECTION 5
#define TOL_ST_ARTICLE 6
#define TOL_ST_ORDER 7
#define TOL_ST_LIST 8
#define TOL_ST_FOREMPTYLIST 9
#define TOL_ST_ENDLIST 10
#define TOL_ST_IMAGE 11
#define TOL_ST_URLPARAMETERS 12
#define TOL_ST_FORMPARAMETERS 13
#define TOL_ST_PRINT 14
#define TOL_ST_IF 15
#define TOL_ST_PREVIOUSITEMS 16
#define TOL_ST_NEXTITEMS 17
#define TOL_ST_ELSE 18
#define TOL_ST_ENDIF 19
#define TOL_ST_DATE 20
#define TOL_ST_LOCAL 21
#define TOL_ST_ENDLOCAL 22
#define TOL_ST_SUBSCRIPTION 23
#define TOL_ST_ENDSUBSCRIPTION 24
#define TOL_ST_ALLOWED 25
#define TOL_ST_EDIT 26
#define TOL_ST_SELECT 27
#define TOL_ST_USER 28
#define TOL_ST_ENDUSER 29
#define TOL_ST_LOGIN 30
#define TOL_ST_ENDLOGIN 31
#define TOL_ST_SEARCH 32
#define TOL_ST_ENDSEARCH 33
#define TOL_ST_SEARCHRESULT 34
#define TOL_ST_SUBTITLE 35
#define TOL_ST_PREVSUBTITLES 36
#define TOL_ST_NEXTSUBTITLES 37
#define TOL_ST_WITH 38
#define TOL_ST_ENDWITH 39
#define  TOL_ST_CURRENTSUBTITLE 40

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

// The lexem returned by lex class
class TOLLexem
{
private:
	TOLLexResult m_Res;			// result code
	TDataType m_DataType;		// lexem data type
	const TOLAtom* m_pcoAtom;	// pointer to atom structure
	cpChar m_pchTextStart;		// html text found (after ">" - end of statement - lexem)
	long int m_nTextLen;		// html text length

public:
	// constructor
	TOLLexem(TOLLexResult p_Res, TDataType p_DataType, const TOLAtom* p_pcoAtom = NULL,
	         cpChar p_pchText = NULL, long int p_nTextLen = 0)
			: m_Res(p_Res), m_DataType(p_DataType), m_pcoAtom(p_pcoAtom)
	{
		m_pchTextStart = p_pchText;
		m_nTextLen = p_nTextLen;
	}
	// copy constructor
	TOLLexem(const TOLLexem& p_rcoLexem)
	{
		*this = p_rcoLexem;
	}

	// assign operator
	const TOLLexem& operator =(const TOLLexem& p_rcoLexem)
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

	friend class TOLLex;
	friend class TOLParser;
};

// lex class; performs syntactic analysis
class TOLLex
{
private:
	static const char s_pchTOLTokenStart[];	// start token for campsite instruction
	static const char s_chTOLTokenEnd;		// end token
	static const int s_nTempBuffLen;		// length of the temporary buffer
	static TOLStatementHash s_coStatements;	// statements
	static pthread_once_t s_StatementsInit;	// control the statements initialisation
	pChar m_pchTempBuff;					// temporary buffer
	int m_nLine;							// current line
	int m_nColumn;							// current column
	int m_nPrevLine;						// previous line
	int m_nPrevColumn;						// previous column
	int m_nState;							// lex current state
	TOLAtom m_coAtom;						//
	TOLLexem m_coLexem;						// current lexem
	char m_chChar;							// current character read from buffer
	int m_nTempIndex;						// temporary buffer index of current character
	int m_nAtomIdIndex;						// atom identifier index of current character
	bool m_bLexemStarted;					// true if reading lexem
	bool m_bIsEOF;							// true if end of text buffer
	cpChar m_pchTextStart;					// html text start
	cpChar m_pchInBuf;						// input text buffer
	ULInt m_nBufLen;						// input text buffer length
	ULInt m_nIndex;							// index to current character of input text buffer

	// NextChar: return next character from text buffer
	char NextChar();
	// FlushTempBuff: flush temporary buffer
	void FlushTempBuff();
	// IdentifyAtom: identifies the current lexem
	const TOLLexem* IdentifyAtom();
	// AppentOnAtom: return true if not end of identifier buffer (can append character to atom
	// identifier)
	int AppendOnAtom();
	// InitStatements: initialise statements
	static void InitStatements();

public:
	// constructor
	TOLLex(cpChar = 0, ULInt = 0);
	// copy-constructor
	TOLLex(const TOLLex& p_rcoSrc)
			: m_coLexem(TOL_LEX_NONE, TOL_DT_NONE)
	{
		*this = p_rcoSrc;
	}
	// destructor
	~TOLLex()
	{
		delete m_pchTempBuff;
	}

	// Reset: reset lex
	void Reset(cpChar = NULL, ULInt = 0);
	// UpdateArticleTypes: update article types structure from database
	static bool UpdateArticleTypes();
	// assign operator
	const TOLLex& operator =(const TOLLex&);
	// GetLexem: return next lexem
	const TOLLexem* GetLexem();
	// PrintStatements: print known statements
	static void PrintStatements();
	// Line: return current line
	int Line() const
	{
		return m_nLine;
	}
	// Column: return current column
	int Column() const
	{
		return m_nColumn;
	}
	// PrevLine: return previous line
	int PrevLine() const
	{
		return m_nPrevLine;
	}
	// PrevColumn: return previous column
	int PrevColumn() const
	{
		if (m_coLexem.m_pcoAtom && strlen(m_coLexem.m_pcoAtom->Identifier()) > 0)
			return m_nPrevColumn - strlen(m_coLexem.m_pcoAtom->Identifier()) + 1;
		return m_nPrevColumn;
	}

	friend class TOLParser;
};

#endif
