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
 
Defines TOLCLexem, TOLCLex, TOLCParser classes. These make a smaller parser used
for articles content parsing and writing to output. The lexem (TOLCLexem) is
identified by one of TOLCLexResult values. TOLCLex returns a lexem at request.
The parser (TOLCParser) does not build an actions tree. It just parses the
article content and writes the output. It's main method is Parse, which receives
a context parameter (see tol_context.h). The context can be modified by this.
This parser fills in the article content subtitles.
 
******************************************************************************/

#ifndef _TOL_CPARSER_H
#define _TOL_CPARSER_H

#include <mysql/mysql.h>
#include <fstream.h>
#include <string.h>
#include <ctype.h>

#include "tol_context.h"

#define TOL_CST_IMAGE 1
#define TOL_CST_KEYWORD 2

#define CST_IMAGE "Image"
#define CST_CLASS "Class"
#define CST_TITLE "Title"
#define CST_LINK "Link"
#define CST_ENDCLASS "EndClass"
#define CST_ENDTITLE "EndTitle"
#define CST_ENDLINK "EndLink"

// result codes returned by lex (in lexem class)
typedef enum _TOLCLexResult {
    TOL_CRES_EOF = -3,
    TOL_CERR_LESS_IN_TOKEN = -2,
    TOL_CERR_END_QUOTE_MISSING = -1,
    TOL_CLEX_NONE = 0,
    TOL_CLEX_IDENTIFIER = 1,
    TOL_CLEX_CST_IMAGE = 2,
    TOL_CLEX_CST_CLASS = 3,
    TOL_CLEX_CST_TITLE = 4,
    TOL_CLEX_CST_LINK = 5,
    TOL_CLEX_END_STATEMENT = 6,
    TOL_CLEX_START_STATEMENT = 7,
    TOL_CLEX_CST_ENDCLASS = 8,
    TOL_CLEX_CST_ENDTITLE = 9,
    TOL_CLEX_CST_ENDLINK = 10
} TOLCLexResult;

typedef enum _TCDataType {
    TOL_CDT_NONE = 0,
    TOL_CDT_NUMBER = 1,
    TOL_CDT_STRING = 2
} TCDataType;

// The lexem returned by lex class
class TOLCLexem
{
private:
	TOLCLexResult res;					// result code
	char Identifier[ID_MAXLEN + 1];		// lexem identifier
	TCDataType atom_dt;					// atom data type
	cpChar text_start;					// html text found (after ">" - end of statement - lexem)
	long int text_len;					// html text length

public:
	// constructor
	TOLCLexem(TOLCLexResult r, TCDataType a_dt, cpChar t = 0, long int tl = 0)
			: res(r), atom_dt(a_dt)
	{
		text_start = t;
		text_len = tl;
	}
	// copy-constructor
	TOLCLexem(const TOLCLexem& p_rcoLexem)
	{
		*this = p_rcoLexem;
	}
	// destructor
	~TOLCLexem()
	{ }

	// assign operator
	const TOLCLexem& operator =(const TOLCLexem& s)
	{
		res = s.res;
		atom_dt = s.atom_dt;
		text_start = s.text_start;
		text_len = s.text_len;
		return *this;
	}

	friend class TOLCLex;
	friend class TOLCParser;
};

// lex class; performs syntactic analysis
class TOLCLex
{
private:
	static const char TOLTokenStart[];		// start token for campsite instruction
	static const char TOLTokenEnd;			// end token
	int CurrLine;							// current line
	int CurrCol;							// current column
	int OldLine;							// previous line
	int OldCol;								// previous column
	int CurrState;							// lex current state
	char CurrChar;							// current char
	pChar m_pchTempBuff;					// temporary buffer
	static const int TempBuffLen;			// temporary buffer length
	cpChar in_buf;							// input text buffer
	cpChar text_start;						// html text start
	int TempIndex;							// temporary buffer index of current character
	int LexemStarted;						// true if reading lexem
	int isEOF;								// true if end of text buffer
	long int buf_len;						// input text buffer length
	long int text_len;						// html text length
	long int IdIndex;						// atom identifier index of current character
	long int index;							// index to current character of input text buffer
	TOLCLexem CurrLexem;					// current lexem

	// NextChar: return next character from text buffer
	char NextChar();
	// FlushTempBuff: flush temporary buffer
	inline void FlushTempBuff();
	// IdentifyAtom: identifies the current lexem
	const TOLCLexem* IdentifyAtom();
	// AppentOnAtom: return true if not end of identifier buffer (can append character to atom
	// identifier)
	int AppendOnAtom();

public:
	// constructor
	TOLCLex(cpChar = 0, long int = 0);
	// copy-constructor
	TOLCLex(const TOLCLex& s)
			: CurrLexem(TOL_CLEX_NONE, TOL_CDT_NONE)
	{
		*this = s;
	}
	// destructor
	~TOLCLex()
	{
		delete m_pchTempBuff;
	}
	
	// Reset: reset lex
	void Reset(cpChar = 0, long int = 0);

	// assign operator
	const TOLCLex& operator =(const TOLCLex&);
	
	// GetLexem: return next lexem
	const TOLCLexem* GetCLexem();
	
	// CurrentLine: return current line
	int CurrentLine() const
	{
		return CurrLine;
	}
	// CurrentColumn: return current column
	int CurrentColumn() const
	{
		return CurrCol;
	}
	// GetOldLine: return previous line
	int GetOldLine() const
	{
		return OldLine;
	}
	// GetOldColumn: return previous column
	int GetOldColumn() const;

	friend class TOLCParser;
};

// TOLCParser: the article content parser
class TOLCParser
{
private:
	TOLCLex clex;				// the lex
	bool debug;					// if true print debug information

	// DEBUGLexem: print debug information
	// Parameters:
	//	 	cpChar p_pchContext - context information
	//		const TOLCLexem* p_pcoLexem - lexem
	void DEBUGLexem(cpChar p_pchContext, const TOLCLexem* p_pcoLexem);
	
	// WaitForStatementStart: read from input file until it finds a start statement
	// Parameters:
	//		fstream& p_rcoOut - stream to write output to
	//		bool p_bWrite - if true, write read lexems to output stream
	const TOLCLexem* WaitForStatementStart(fstream& p_rcoOut, bool p_bWrite);
	
	// WaitForStatementEnd: read from input file until it finds an end statement
	const TOLCLexem* WaitForStatementEnd();

	// MakeImageLink: write image link
	// Parameters:
	//		TOLContext& p_rcoContext - context
	//		long int p_nImageNr - image number
	//		cpChar p_pchAlign - html parameter (align)
	//		cpChar p_pchAlt - html parameter (alt)
	//		fstream& p_rcoOut - output stream
	void MakeImageLink(const TOLContext& p_rcoContext, long int p_rcoImageNr,
					   cpChar p_pchAlign, cpChar p_pchAlt, fstream& p_rcoOut);

	// MakeClassLink: write class popup link
	// Parameters:
	//		const TOLContext& p_rcoContext - context
	//		cpChar p_pchClass - class name
	//		cpChar p_pchKey - keyword
	//		long int p_nKeyLen - keyword length
	//		fstream& p_rcoOut - output stream
	//		MYSQL* p_SQL - pointer to MySQL connection
	void MakeClassLink(const TOLContext& p_rcoContext, cpChar p_pchClass,
					   cpChar p_pchKey, long int p_nKeyLen, fstream& p_rcoOut,
					   MYSQL* p_SQL);
	
	// HTMLClean: clean the input string of html code
	// Paramters:
	//		pChar& p_pchCleanKey - cleaned string
	//		long int& p_nCleanKeyLen - cleaned string length
	//		cpChar p_pchKey - string to clean
	//		long int p_nKeyLen - string to clean length
	void HTMLClean(pChar& p_pchCleanKey, long int& p_nCleanKeyLen, cpChar p_pchKey,
				   long int p_nKeyLen);
	// HTMLUnescape: unescape input html string
	//		pChar& unesc - unescaped string
	//		long int& unesc_len - unescaped string length
	//		cpChar str - string to unescape
	//		long int str_len - string to unescape length
	void HTMLUnescape(pChar& unesc, long int& unesc_len, cpChar str, long int str_len);
	
	// CGIEscape: escape input string for URL use
	// Parameters:
	//		pChar& esc - escaped string
	//		long int& esc_len - escaped string length
	//		cpChar str - string to escape
	//		long int str_len - string to escape length
	void CGIEscape(pChar& esc, long int& esc_len, cpChar str, long int str_len);
	
	// DoParse: parse the article content
	//		TOLContext& p_rcoContext - context
	//		fstream& p_rcoOut - output stream
	//		MYSQL* p_SQL - pointer to MySQL connection
	//		bool do_append - if true append subtitle to subtitle list
	//		int& index - index of current subtitle
	//		int start_st = 0 - print subtitles starting from start_st subtitle number
	//		bool all = false - if true, print all subtitles
	//		bool p_bWrite = true - if true, write output to p_rcoOut; if false it does not
	//			write anything to output stream
	const TOLCLexem* DoParse(TOLContext& p_rcoContext, fstream& p_rcoOut,
							 MYSQL* p_SQL, bool do_append, int& index,
							 int start_st = 0, bool all = false, bool p_bWrite = true);
public:
	// constructor
	TOLCParser(cpChar b = 0, long int bl = 0, bool d = false)
			: clex(b, bl)
	{
		debug = d;
	}
	// copy-constructor
	TOLCParser(const TOLCParser& p)
			: clex(p.clex.in_buf, p.clex.buf_len)
	{}
	// destructor
	~TOLCParser()
	{
		try
		{
			Reset();
		}
		catch (...)
		{
		}
	}

	// Reset: reset article content parser
	void Reset(cpChar b = 0, long int bl = 0)
	{
		clex.Reset(b, bl);
	}
	
	// SetDebug: set debug member
	void SetDebug(bool v)
	{
		debug = v;
	}
	
	// Parse: start the parser
	// Parameters:
	//		TOLContext& p_rcoContext - context
	//		fstream& p_rcoOut - output stream
	//		MYSQL* p_SQL - pointer to MySQL connection
	//		int start = 0 - print subtitles starting from start subtitle number
	//		bool all = false - if true, print all subtitles
	//		bool p_bWrite = true - if true, write output to p_rcoOut; if false it does not
	//			write anything to output stream
	int Parse(TOLContext& p_rcoContext, fstream& p_rcoOut, MYSQL* p_SQL, int start = 0,
			  bool all = false, bool p_bWrite = true);
	
	// assign operator
	const TOLCParser& operator =(const TOLCParser& s)
	{
		clex = s.clex;
		debug = s.debug;
		return *this;
	}
};

#endif
