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

Defines CCLexem, CCLex, CCParser classes. These make a smaller parser used
for articles content parsing and writing to output. The lexem (CCLexem) is
identified by one of CCLexResult values. CCLex returns a lexem at request.
The parser (CCParser) does not build an actions tree. It just parses the
article content and writes the output. It's main method is Parse, which receives
a context parameter (see tol_context.h). The context can be modified by this.
This parser fills in the article content subtitles.

******************************************************************************/

#ifndef _CMS_CPARSER
#define _CMS_CPARSER

#include <mysql/mysql.h>
#include <string.h>
#include <ctype.h>

#include "context.h"

#define CMS_CST_IMAGE 1
#define CMS_CST_KEYWORD 2

#define CST_IMAGE "Image"
#define CST_CLASS "Class"
#define CST_TITLE "Title"
#define CST_LINK "Link"
#define CST_ENDCLASS "EndClass"
#define CST_ENDTITLE "EndTitle"
#define CST_ENDLINK "EndLink"

// result codes returned by lex (in lexem class)
typedef enum _CCLexResult {
    CMS_CRES_EOF = -3,
    CMS_CERR_LESS_IN_TOKEN = -2,
    CMS_CERR_END_QUOTE_MISSING = -1,
    CMS_CLEX_NONE = 0,
    CMS_CLEX_IDENTIFIER = 1,
    CMS_CLEX_CST_IMAGE = 2,
    CMS_CLEX_CST_CLASS = 3,
    CMS_CLEX_CST_TITLE = 4,
    CMS_CLEX_CST_LINK = 5,
    CMS_CLEX_END_STATEMENT = 6,
    CMS_CLEX_START_STATEMENT = 7,
    CMS_CLEX_CST_ENDCLASS = 8,
    CMS_CLEX_CST_ENDTITLE = 9,
    CMS_CLEX_CST_ENDLINK = 10
} CCLexResult;

typedef enum _TCDataType {
    CMS_CDT_NONE = 0,
    CMS_CDT_NUMBER = 1,
    CMS_CDT_STRING = 2
} TCDataType;

#define ID_MAXLEN 1024

// The lexem returned by lex class
class CCLexem
{
private:
	CCLexResult res;					// result code
	char Identifier[ID_MAXLEN + 1];		// lexem identifier
	TCDataType atom_dt;					// atom data type
	const char* text_start;					// html text found (after ">" - end of statement - lexem)
	lint text_len;					// html text length

public:
	// constructor
	CCLexem(CCLexResult r, TCDataType a_dt, const char* t = 0, lint tl = 0)
			: res(r), atom_dt(a_dt)
	{
		text_start = t;
		text_len = tl;
	}

	// copy-constructor
	CCLexem(const CCLexem& p_rcoLexem) { *this = p_rcoLexem; }

	// destructor
	~CCLexem() { }

	// assign operator
	const CCLexem& operator =(const CCLexem& s)
	{
		res = s.res;
		atom_dt = s.atom_dt;
		text_start = s.text_start;
		text_len = s.text_len;
		return *this;
	}

	friend class CCLex;
	friend class CCParser;
};

// lex class; performs syntactic analysis
class CCLex
{
private:
	static const char CTokenStart[];		// start token for campsite instruction
	static const char CTokenEnd;			// end token
	int CurrLine;							// current line
	int CurrCol;							// current column
	int OldLine;							// previous line
	int OldCol;								// previous column
	int CurrState;							// lex current state
	char CurrChar;							// current char
	char* m_pchTempBuff;					// temporary buffer
	static const int TempBuffLen;			// temporary buffer length
	const char* in_buf;							// input text buffer
	const char* text_start;						// html text start
	int TempIndex;							// temporary buffer index of current character
	int LexemStarted;						// true if reading lexem
	int QuotedLexem;						// true if LexemStarted is true and the new lexem
											// is quoted
	int isEOF;								// true if end of text buffer
	lint buf_len;						// input text buffer length
	lint text_len;						// html text length
	lint IdIndex;						// atom identifier index of current character
	lint index;							// index to current character of input text buffer
	CCLexem CurrLexem;					// current lexem

	// NextChar: return next character from text buffer
	char NextChar();

	// FlushTempBuff: flush temporary buffer
	inline void FlushTempBuff();

	// IdentifyAtom: identifies the current lexem
	const CCLexem* IdentifyAtom();

	// AppentOnAtom: return true if not end of identifier buffer (can append character to atom
	// identifier)
	int AppendOnAtom();

public:
	// constructor
	CCLex(const char* = 0, lint = 0);

	// copy-constructor
	CCLex(const CCLex& s) : m_pchTempBuff(NULL), CurrLexem(CMS_CLEX_NONE, CMS_CDT_NONE)
	{ *this = s; }

	// destructor
	~CCLex() { delete m_pchTempBuff; }

	// reset: reset lex
	void reset(const char* = 0, lint = 0);

	// assign operator
	const CCLex& operator =(const CCLex&);

	// getLexem: return next lexem
	const CCLexem* getCLexem();
	
	// currentLine: return current line
	int currentLine() const { return CurrLine; }

	// currentColumn: return current column
	int currentColumn() const { return CurrCol; }

	// getOldLine: return previous line
	int getOldLine() const { return OldLine; }

	// getOldColumn: return previous column
	int getOldColumn() const;

	friend class CCParser;
};

// CCParser: the article content parser
class CCParser
{
private:
	CCLex clex;				// the lex
	bool debug;					// if true print debug information

	// DEBUGLexem: print debug information
	// Parameters:
	//	 	const char* p_pchContext - context information
	//		const CCLexem* p_pcoLexem - lexem
	void DEBUGLexem(const char* p_pchContext, const CCLexem* p_pcoLexem);
	
	// WaitForStatementStart: read from input file until it finds a start statement
	// Parameters:
	//		sockstream& p_rcoOut - stream to write output to
	//		bool p_bWrite - if true, write read lexems to output stream
	const CCLexem* WaitForStatementStart(sockstream& p_rcoOut, bool p_bWrite);
	
	// WaitForStatementEnd: read from input file until it finds an end statement
	const CCLexem* WaitForStatementEnd();

	// MakeImageLink: write image link
	// Parameters:
	//		CContext& p_rcoContext - context
	//		lint p_nImageNr - image number
	//		const char* p_pchAlign - html parameter (align)
	//		const char* p_pchAlt - html parameter (alt)
	//		const char* p_pchImgTitle - image subtitle
	//		sockstream& p_rcoOut - output stream
	void MakeImageLink(const CContext& p_rcoContext, lint p_rcoImageNr,
					   const char* p_pchAlign, const char* p_pchAlt, const char* p_pchImgTitle, sockstream& p_rcoOut);

	// MakeClassLink: write class popup link
	// Parameters:
	//		const CContext& p_rcoContext - context
	//		const char* p_pchClass - class name
	//		const char* p_pchKey - keyword
	//		lint p_nKeyLen - keyword length
	//		sockstream& p_rcoOut - output stream
	//		MYSQL* p_SQL - pointer to MySQL connection
	void MakeClassLink(const CContext& p_rcoContext, const char* p_pchClass,
					   const char* p_pchKey, lint p_nKeyLen, sockstream& p_rcoOut,
					   MYSQL* p_SQL);
	
	// HTMLClean: clean the input string of html code
	// Paramters:
	//		char*& p_pchCleanKey - cleaned string
	//		lint& p_nCleanKeyLen - cleaned string length
	//		const char* p_pchKey - string to clean
	//		lint p_nKeyLen - string to clean length
	void HTMLClean(char*& p_pchCleanKey, lint& p_nCleanKeyLen, const char* p_pchKey,
				   lint p_nKeyLen);
	// HTMLUnescape: unescape input html string
	//		char*& unesc - unescaped string
	//		lint& unesc_len - unescaped string length
	//		const char* str - string to unescape
	//		lint str_len - string to unescape length
	void HTMLUnescape(char*& unesc, lint& unesc_len, const char* str, lint str_len);
	
	// CGIEscape: escape input string for URL use
	// Parameters:
	//		char*& esc - escaped string
	//		lint& esc_len - escaped string length
	//		const char* str - string to escape
	//		lint str_len - string to escape length
	void CGIEscape(char*& esc, lint& esc_len, const char* str, lint str_len);
	
	// DoParse: parse the article content
	//		CContext& p_rcoContext - context
	//		sockstream& p_rcoOut - output stream
	//		MYSQL* p_SQL - pointer to MySQL connection
	//		bool do_append - if true append subtitle to subtitle list
	//		int& index - index of current subtitle
	//		int start_st = 0 - print subtitles starting from start_st subtitle number
	//		bool all = false - if true, print all subtitles
	//		bool p_bWrite = true - if true, write output to p_rcoOut; if false it does not
	//			write anything to output stream
	const CCLexem* DoParse(CContext& p_rcoContext, sockstream& p_rcoOut,
							 MYSQL* p_SQL, bool do_append, int& index,
							 int start_st = 0, bool all = false, bool p_bWrite = true);
public:
	// constructor
	CCParser(const char* b = 0, lint bl = 0, bool d = false) : clex(b, bl) { debug = d; }

	// copy-constructor
	CCParser(const CCParser& p) : clex(p.clex.in_buf, p.clex.buf_len) {}

	// destructor
	~CCParser()
	{
		try
		{
			reset();
		}
		catch (...)
		{
		}
	}

	// reset: reset article content parser
	void reset(const char* b = 0, lint bl = 0) { clex.reset(b, bl); }
	
	// setDebug: set debug member
	void setDebug(bool v) { debug = v; }
	
	// parse: start the parser
	// Parameters:
	//		CContext& p_rcoContext - context
	//		sockstream& p_rcoOut - output stream
	//		MYSQL* p_SQL - pointer to MySQL connection
	//		int start = 0 - print subtitles starting from start subtitle number
	//		bool all = false - if true, print all subtitles
	//		bool p_bWrite = true - if true, write output to p_rcoOut; if false it does not
	//			write anything to output stream
	int parse(CContext& p_rcoContext, sockstream& p_rcoOut, MYSQL* p_SQL, int start = 0,
			  bool all = false, bool p_bWrite = true);

	// assign operator
	const CCParser& operator =(const CCParser& s)
	{
		clex = s.clex;
		debug = s.debug;
		return *this;
	}
};

#endif
