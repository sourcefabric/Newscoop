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

Implementation of CCLexem, CCLex, CCParser methods.

******************************************************************************/

#include <stdio.h>
#include <iostream>

#include "cgiparams.h"
#include "cparser.h"
#include "util.h"
#include "error.h"
#include "curl.h"


using std::cout;


// macros
#define CheckForEOF(l)\
{\
if (l->res == CMS_CRES_EOF) {\
return l;\
}\
}

#define CheckForEndSt(l)\
{\
if (l->res == CMS_CLEX_END_STATEMENT) {\
continue;\
}\
}

#define RequireAtom(l)\
{\
l = clex.getCLexem();\
DEBUGLexem("req atom", l);\
CheckForEOF(l);\
CheckForEndSt(l);\
}


const char CCLex::CTokenStart[] = "<!**";
const char CCLex::CTokenEnd = '>';
const int CCLex::TempBuffLen = 1000 + strlen(CTokenStart);


// NextChar: return next character from text buffer
char CCLex::NextChar()
{
	if (in_buf == 0)
		return CurrChar = EOF;
	CurrChar = index >= buf_len ? EOF : in_buf[index++];
	if (CurrChar == 0)
		NextChar();
	if (CurrState < 3 || CurrState == 4)
	{
		if (TempIndex >= TempBuffLen)
			FlushTempBuff();
		m_pchTempBuff[TempIndex++] = CurrChar;
	}
	return CurrChar;
}

// FlushTempBuff: flush temporary buffer
inline void CCLex::FlushTempBuff()
{
	TempIndex = 0;
}

// IdentifyAtom: identifies the current lexem
const CCLexem* CCLex::IdentifyAtom()
{
	CurrLexem.Identifier[IdIndex] = 0;
	if (strcmp(CurrLexem.Identifier, CST_IMAGE) == 0)
		CurrLexem.res = CMS_CLEX_CST_IMAGE;
	else if (strcmp(CurrLexem.Identifier, CST_CLASS) == 0)
		CurrLexem.res = CMS_CLEX_CST_CLASS;
	else if (strcmp(CurrLexem.Identifier, CST_TITLE) == 0)
		CurrLexem.res = CMS_CLEX_CST_TITLE;
	else if (strcmp(CurrLexem.Identifier, CST_LINK) == 0)
		CurrLexem.res = CMS_CLEX_CST_LINK;
	else if (strcmp(CurrLexem.Identifier, CST_ENDCLASS) == 0)
		CurrLexem.res = CMS_CLEX_CST_ENDCLASS;
	else if (strcmp(CurrLexem.Identifier, CST_ENDTITLE) == 0)
		CurrLexem.res = CMS_CLEX_CST_ENDTITLE;
	else if (strcmp(CurrLexem.Identifier, CST_ENDLINK) == 0)
		CurrLexem.res = CMS_CLEX_CST_ENDLINK;
	else
		CurrLexem.res = CMS_CLEX_IDENTIFIER;
	return &CurrLexem;
}

// AppentOnAtom: return true if not end of identifier buffer (can append character to atom
// identifier)
int CCLex::AppendOnAtom()
{
	if (IdIndex >= ID_MAXLEN)
	{
		CurrLexem.Identifier[IdIndex] = 0;
		return 0;
	}
	CurrLexem.Identifier[IdIndex++] = CurrChar;
	if (!isdigit(CurrChar))
		CurrLexem.atom_dt = CMS_CDT_STRING;
	return 1;
}

// CCLex constructor
CCLex::CCLex(const char* i, lint bl)
		: CurrLexem(CMS_CLEX_NONE, CMS_CDT_NONE)
{
	text_start = in_buf = i;
	buf_len = bl;
	index = 0;
	CurrLine = 1;
	CurrCol = 0;
	CurrState = 1;
	CurrChar = 0;
	m_pchTempBuff = new char[TempBuffLen];
	m_pchTempBuff[0] = (char)0;
	TempIndex = 0;
	LexemStarted = 0;
	isEOF = 0;
}

// CCLex assign operator
const CCLex& CCLex::operator =(const CCLex& s)
{
	text_start = in_buf = s.in_buf;
	buf_len = s.buf_len;
	index = 0;
	CurrLine = 1;
	CurrCol = 0;
	CurrState = 1;
	CurrChar = 0;
	if (m_pchTempBuff == NULL)
		m_pchTempBuff = new char[TempBuffLen];
	m_pchTempBuff[0] = (char)0;
	TempIndex = 0;
	LexemStarted = 0;
	isEOF = 0;
	QuotedLexem = 0;
	return *this;
}

// reset: reset lex
void CCLex::reset(const char* b, lint bl)
{
	if (b != NULL)
	{
		in_buf = b;
		buf_len = bl;
	}
	text_start = in_buf;
	index = 0;
	CurrLine = 1;
	CurrCol = 0;
	CurrState = 1;
	CurrChar = 0;
	if (m_pchTempBuff == NULL)
		m_pchTempBuff = new char[TempBuffLen];
	m_pchTempBuff[0] = (char)0;
	TempIndex = 0;
	LexemStarted = 0;
	QuotedLexem = 0;
	isEOF = 0;
}

// getLexem: return next lexem
const CCLexem* CCLex::getCLexem()
{
	int FoundLexem = 0;
	CurrLexem.atom_dt = CMS_CDT_NUMBER;
	CurrLexem.text_start = 0;
	CurrLexem.text_len = 0;
	IdIndex = 0;
	if (isEOF)
	{
		CurrLexem.res = CMS_CRES_EOF;
		return &CurrLexem;
	}
	while (!FoundLexem && !isEOF)
	{
		NextChar();
		if (CurrChar == EOF)	// end of text buffer
		{
			isEOF = 1;
			if (LexemStarted)
			{
				LexemStarted = 0;
				return IdentifyAtom();
			}
			CurrLexem.text_start = text_start;
			CurrLexem.text_len = index - (ulint)(text_start - in_buf);
			CurrLexem.res = CMS_CLEX_NONE;
			return &CurrLexem;
		}
		OldLine = CurrLine;
		OldCol = CurrCol;
		if (CurrChar == '\n')	// increment line and set column to 0 on new line character
		{
			CurrLine ++;
			CurrCol = 0;
		}
		else
			CurrCol += CurrChar == '\t' ? 8 : 1; // increment column (by 8 if character is tab)

		switch (CurrState)
		{
		case 1:	// start state; read html text
			if (CurrChar == CTokenStart[0])
			{
				TempIndex --;
				FlushTempBuff();
				m_pchTempBuff[TempIndex++] = CurrChar;
			}
			if (strncmp(m_pchTempBuff, CTokenStart, TempIndex) == 0)
				CurrState = 2;
			break;
		case 2:	// found some characters matching start token
			if (CurrChar != CTokenStart[TempIndex - 1])
			{
				CurrState = 1;
				break;
			}
			if (TempIndex == (int)strlen(CTokenStart))	// found start token
			{
				TempIndex = 0;
				CurrState = 3;
				LexemStarted = 0;
				CurrLexem.Identifier[0] = 0;
				if (text_start)
				{
					CurrLexem.text_len = index - (ulint)(text_start - in_buf)
					                     - strlen(CTokenStart);
					CurrLexem.text_start = text_start;
				}
				CurrLexem.res = CMS_CLEX_START_STATEMENT;
				return &CurrLexem;
			}
			break;
		case 3:	// after a start token; read campsite instruction
			if (!LexemStarted)		// didn't find an atom yet
			{
				if (CurrChar >= 0 && CurrChar <= ' ')		// separator
				{
					;
				}
				else if (CurrChar == CTokenEnd)	// end token
				{
					CurrState = 1;
					text_start = in_buf + index;
					CurrLexem.res = CMS_CLEX_END_STATEMENT;
					return &CurrLexem;
				}
				else if (CurrChar == '<')		// invalid token inside campsite instruction
				{
					m_pchTempBuff[TempIndex++] = CurrChar;
					CurrState = 1;
					text_start = 0;
					CurrLexem.res = CMS_CERR_LESS_IN_TOKEN;
					return &CurrLexem;
				}
				else							// atom found
				{
					LexemStarted = 1;
					QuotedLexem = (CurrChar == '\"') ? 1 : 0;
					if (!QuotedLexem)
						AppendOnAtom();
				}
			}
			else if (QuotedLexem)			// lexem (atom) is delimited by quotes
			{
				if ((CurrChar >= 0 && CurrChar < ' ') || CurrChar == CTokenEnd)
				{
					LexemStarted = 0;
					if (CurrChar == CTokenEnd)
						CurrState = 4;
					CurrLexem.res = CMS_CERR_END_QUOTE_MISSING;
					return &CurrLexem;
				}
				else if (CurrChar == '\"')
				{
					FoundLexem = 1;
					LexemStarted = 0;
				}
				else
				{
					if (!AppendOnAtom())
					{
						LexemStarted = 0;
						return IdentifyAtom();
					}
				}
			}
			else							// lexem is not delimited by quotes
			{
				if ((CurrChar >= 0 && CurrChar <= ' ') || CurrChar == CTokenEnd)  // separator or end token
				{
					FoundLexem = 1;
					LexemStarted = 0;
					if (CurrChar == CTokenEnd)
					{
						CurrState = 4;
						return IdentifyAtom();
					}
				}
				else if (CurrChar == '\"')	// found another lexem delimited by quotes
				{
					FoundLexem = 1;
					LexemStarted = 1;
					QuotedLexem = 1;
					return IdentifyAtom();
				}
				else						// append character to atom identifier
				{
					if (!AppendOnAtom())
					{
						LexemStarted = 0;
						return IdentifyAtom();
					}
				}
			}
			break;
		case 4:	// found end token; set the text start pointer
			CurrState = 1;
			text_start = in_buf + index - 1;
			CurrLexem.res = CMS_CLEX_END_STATEMENT;
			return &CurrLexem;
			break;
		}
	}
	return IdentifyAtom();
}

// DEBUGLexem: print debug information
// Parameters:
//	 	const char* p_pchContext - context information
//		const CCLexem* l - lexem
void CCParser::DEBUGLexem(const char* p_pchContext, const CCLexem* p_pcoLexem)
{
	if (debug == true)
	{
		cout << "<!-- @CLEXEM " << p_pchContext << ": " << (int)p_pcoLexem->res;
		if (*(p_pcoLexem->Identifier))
			cout << " identifier: " << p_pcoLexem->Identifier;
		if (p_pcoLexem->text_start)
		{
			cout << " text %";
			cout.write(p_pcoLexem->text_start, p_pcoLexem->text_len);
			cout << "% len: " << p_pcoLexem->text_len;
		}
		cout << " -->\n";
	}
}

// WaitForStatementStart: read from input file until it finds a start statement
// Parameters:
//		sockstream& p_rcoOut - stream to write output to
//		bool p_bWrite - if true, write read lexems to output stream
const CCLexem* CCParser::WaitForStatementStart(sockstream& p_rcoOut, bool p_bWrite)
{
	const CCLexem* c_lexem = clex.getCLexem();
	if (c_lexem->text_start && c_lexem->text_len && p_bWrite)
		p_rcoOut.write(c_lexem->text_start, c_lexem->text_len);
	DEBUGLexem("wf start 1", c_lexem);
	while (c_lexem->res != CMS_CLEX_START_STATEMENT && c_lexem->res != CMS_CRES_EOF)
	{
		if (c_lexem->res < 0)
			return c_lexem;
		c_lexem = clex.getCLexem();
		if (c_lexem->text_start && c_lexem->text_len && p_bWrite)
			p_rcoOut.write(c_lexem->text_start, c_lexem->text_len);
		DEBUGLexem("wf start 2", c_lexem);
	}
	return c_lexem;
}

// WaitForStatementEnd: read from input file until it finds a start statement
const CCLexem* CCParser::WaitForStatementEnd()
{
	const CCLexem* c_lexem = clex.getCLexem();
	DEBUGLexem("wf end 1", c_lexem);
	while (c_lexem->res != CMS_CLEX_END_STATEMENT
	        && c_lexem->res != CMS_CLEX_START_STATEMENT
	        && c_lexem->res != CMS_CRES_EOF)
	{
		c_lexem = clex.getCLexem();
		DEBUGLexem("wf end 2", c_lexem);
	}
	return c_lexem;
}

// MakeImageLink: write image link
// Parameters:
//		const CContext& p_rcoContext - context
//		lint p_nImageNr - image number
//		const char* p_pchAlign - html parameter (align)
//		const char* p_pchAlt - html parameter (alt)
//		const char* p_pchImgTitle - image subtitle
//		sockstream& p_rcoOut - output stream
void CCParser::MakeImageLink(const CContext& p_rcoContext, lint p_rcoImageNr,
							 const char* p_pchAlign, const char* p_pchAlt,
							 const char* p_pchImgTitle, sockstream& p_rcoOut)
{
	p_rcoOut << "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"cs_img\" "
			<< p_pchAlign << ">\n"
			<< "<tr><td align=\"center\"><img src=\"/cgi-bin/get_img?"
			<< P_NRARTICLE << "=" << p_rcoContext.Article() << "&" << P_NRIMAGE
			<< "=" << p_rcoImageNr << "\" " << p_pchAlt
			<< " border=\"0\" hspace=\"5\" vspace=\"5\"></td></tr>\n";
	if (strlen(p_pchImgTitle) > 0)
		p_rcoOut << "<tr><td align=\"center\" class=\"caption\">" << p_pchImgTitle
		         << "</td></tr>\n";
	p_rcoOut << "</table>\n";
}

// MakeClassLink: write class popup link
// Parameters:
//		const CContext& p_rcoContext - context
//		const char* p_pchClass - class name
//		const char* p_pchKey - keyword
//		lint p_nKeyLen - keyword length
//		sockstream& p_rcoOut - output stream
//		MYSQL* p_SQL - pointer to MySQL connection
void CCParser::MakeClassLink(const CContext& p_rcoContext, const char* p_pchClass,
							   const char* p_pchKey, lint p_nKeyLen, sockstream& p_rcoOut,
							   MYSQL* p_SQL)
{
	if (p_SQL == NULL)
		return ;
	char* pchCleanKey;
	char* pchUnescKey;
	lint nCleanKeyLen;
	lint nUnescKeyLen;
	HTMLClean(pchCleanKey, nCleanKeyLen, p_pchKey, p_nKeyLen);
	HTMLUnescape(pchUnescKey, nUnescKeyLen, pchCleanKey, nCleanKeyLen);
	char* pchTmpBuf = new char [350 + 2 * strlen(p_pchClass) + 2 * strlen(pchUnescKey)];
	char* pchSQLEscClass = new char[2 * strlen(p_pchClass) + 1];
	char* pchSQLEscKey = new char[2 * strlen(pchUnescKey) + 1];
	mysql_escape_string(pchSQLEscClass, p_pchClass, strlen(p_pchClass));
	mysql_escape_string(pchSQLEscKey, pchUnescKey, strlen(pchUnescKey));
	sprintf(pchTmpBuf, "select Classes.Id, Dictionary.Id from Classes, Dictionary, "
			"KeywordClasses where Dictionary.Id = KeywordClasses.IdDictionary and "
			"Classes.Id = KeywordClasses.IdClasses and Dictionary.IdLanguage = %ld "
			"and Classes.IdLanguage = %ld and KeywordClasses.IdLanguage = %ld and "
			"Dictionary.Keyword = \"%s\" and Classes.Name = \"%s\"", p_rcoContext.Language(),
			p_rcoContext.Language(), p_rcoContext.Language(), pchSQLEscKey, pchSQLEscClass);
	delete pchSQLEscClass;
	delete pchSQLEscKey;
	if (mysql_query(p_SQL, pchTmpBuf) != 0)
	{
		p_rcoOut << pchCleanKey;
		delete pchTmpBuf;
		delete pchCleanKey;
		delete pchUnescKey;
		return ;
	}
	delete pchTmpBuf;
	CMYSQL_RES res = mysql_store_result(p_SQL);
	if (*res == NULL)
	{
		p_rcoOut << pchCleanKey;
		delete pchCleanKey;
		delete pchUnescKey;
		return ;
	}
	MYSQL_ROW row = mysql_fetch_row(*res);
	if (row == NULL)
	{
		p_rcoOut << pchCleanKey;
		delete pchCleanKey;
		delete pchUnescKey;
		return ;
	}
	p_rcoOut << "<a href=\"\" onclick=\"window.open(\'/dictionary.php?"
	<< P_IDLANG << "=" << p_rcoContext.Language() << "&" << P_CLASS << "=" << row[0]
	<< "&" << P_KEYWORD << "=" << row[1] << "\', \'Dictionary\', \'width=300"
	<< ", heigth=300\');return false\">" << pchCleanKey << "</a>";
	delete pchCleanKey;
	delete pchUnescKey;
}

// HTMLClean: clean the input string of html code
// Paramters:
//		char*& p_pchCleanKey - cleaned string
//		lint& p_nCleanKeyLen - cleaned string length
//		const char* p_pchKey - string to clean
//		lint p_nKeyLen - string to clean length
void CCParser::HTMLClean(char*& p_pchCleanKey, lint& p_nCleanKeyLen, const char* p_pchKey,
						   lint p_nKeyLen)
{
	int html = 0;
	int s_index = 0;
	int d_index = 0;
	bool start = true;
	p_pchCleanKey = (char*)new char[p_nKeyLen + 1];
	for (; s_index < p_nKeyLen; s_index++)
	{
		if (d_index > 0)
			start = false;
		if (start && p_pchKey[s_index] <= ' ')
			;
		else if (p_pchKey[s_index] == '<')
			html++;
		else if (p_pchKey[d_index] == '>')
			html--;
		else if (html <= 0)
			p_pchCleanKey[d_index++] = p_pchKey[s_index];
	}
	p_pchCleanKey[d_index] = 0;
	for (; d_index > 0 && p_pchCleanKey[d_index] <= ' '; d_index--);
	p_pchCleanKey[++d_index] = 0;
	p_nCleanKeyLen = d_index + 1;
}

// HTMLUnescape: unescape input html string
//		char*& unesc - unescaped string
//		lint& unesc_len - unescaped string length
//		const char* str - string to unescape
//		lint str_len - string to unescape length
void CCParser::HTMLUnescape(char*& unesc, lint& unesc_len, const char* str, lint str_len)
{
	lint s_index = 0;
	lint d_index = 0;
	unesc = (char*)new char[str_len + 1];
	for (; s_index < str_len; s_index++)
		if (str[s_index] == '&')
		{
			int len = str_len - s_index - 1;
			if (len <= 0)
				break;
			if (len >= 4 && strncmp("amp;", str + s_index + 1, 4) == 0)
			{
				unesc[d_index++] = '&';
				s_index += 3;
			}
			else if (len >= 3 && strncmp("gt;", str + s_index + 1, 3) == 0)
			{
				unesc[d_index++] = '>';
				s_index += 2;
			}
			else if (len >= 3 && strncmp("lt;", str + s_index + 1, 3) == 0)
			{
				unesc[d_index++] = '<';
				s_index += 2;
			}
			else if (len >= 5 && strncmp("quot;", str + s_index + 1, 5) == 0)
			{
				unesc[d_index++] = '\"';
				s_index += 4;
			}
		}
		else
			unesc[d_index++] = str[s_index];
	unesc[d_index] = 0;
	unesc_len = d_index + 1;
}

// CGIEscape: escape input string for URL use
// Parameters:
//		char*& esc - escaped string
//		lint& esc_len - escaped string length
//		const char* str - string to escape
//		lint str_len - string to escape length
void CCParser::CGIEscape(char*& esc, lint& esc_len, const char* str, lint str_len)
{
	lint s_index = 0;
	lint d_index = 0;
	esc = (char*)new char[3 * str_len + 1];
	for (; s_index < str_len; s_index++)
		if (str[s_index] == ' ')
			esc[d_index++] = '+';
		else if (!isalnum(str[s_index]))
		{
			esc[d_index++] = '%';
			sprintf(esc + d_index, "%.2X", (int)str[s_index]);
			d_index += 2;
		}
		else
			esc[d_index++] = str[s_index];
	esc[d_index] = 0;
	esc_len = d_index + 1;
}

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
const CCLexem* CCParser::DoParse(CContext& p_rcoContext, sockstream& p_rcoOut,
									 MYSQL* p_SQL, bool do_append, int& index,
                                     int start_st, bool all, bool p_bWrite)
{
	const CCLexem* l;
	while (1)
	{
		// local_write: if true print article content read from database
		bool local_write = p_bWrite && ((index == start_st && !all)
										|| (index >= start_st && all));
		l = WaitForStatementStart(p_rcoOut, local_write);
		if (l->res == CMS_CRES_EOF)
			return l;
		RequireAtom(l);
		if (l->res == CMS_CLEX_CST_ENDLINK && local_write)
			return l;
		if (l->res == CMS_CLEX_CST_IMAGE && local_write)	// found Image statement
		{							// take action only if local_write is true
			RequireAtom(l);
			if (l->res != CMS_CLEX_IDENTIFIER || l->atom_dt != CMS_CDT_NUMBER)
				continue;
			lint img_nr = atol(l->Identifier);
			l = clex.getCLexem();
			DEBUGLexem("parse 3", l);
			string align;
			string alt;
			string img_title;
			if (l->res == CMS_CLEX_IDENTIFIER
			        && strncasecmp(l->Identifier, "Align=", strlen("Align=")) == 0)
			{
				align = string(l->Identifier);
				l = clex.getCLexem();
				DEBUGLexem("parse 8", l);
			}
			if (l->res == CMS_CLEX_IDENTIFIER
			        && strncasecmp(l->Identifier, "Alt=", strlen("Alt=")) == 0)
			{
				alt = string(l->Identifier);
				if (strcasecmp(alt.c_str(), "Alt=") == 0)
				{
					l = clex.getCLexem();
					DEBUGLexem("parse 9", l);
					if (l->res == CMS_CLEX_IDENTIFIER)
					{
						alt += string(1, '\"') + l->Identifier + string(1, '\"');
						l = clex.getCLexem();
					}
				}
			}
			if (l->res == CMS_CLEX_IDENTIFIER
			        && strncasecmp(l->Identifier, "Sub=", strlen("Sub=")) == 0)
			{
				img_title = string(l->Identifier);
				if (strcasecmp(img_title.c_str(), "Sub=") == 0)
				{
					l = clex.getCLexem();
					DEBUGLexem("parse 9", l);
					if (l->res == CMS_CLEX_IDENTIFIER)
					{
						img_title= string(l->Identifier);
						l = clex.getCLexem();
					}
				}
			}

			if (local_write)
				MakeImageLink(p_rcoContext, img_nr, align.c_str(), alt.c_str(), img_title.c_str(), p_rcoOut);
			if (l->res != CMS_CLEX_END_STATEMENT)
				WaitForStatementEnd();
		}
		else if (l->res == CMS_CLEX_CST_CLASS && local_write)	// found Class statement
		{							// take action only if local_write is true
			RequireAtom(l);
			string class_name = string(l->Identifier);
			l = WaitForStatementEnd();
			l = clex.getCLexem();
			if (l->res == CMS_CRES_EOF)
				return l;
			if (l->text_start == 0 || l->text_len == 0)
				continue;
			if (local_write)
				MakeClassLink(p_rcoContext, class_name.c_str(), l->text_start, l->text_len,
							  p_rcoOut, p_SQL);
			WaitForStatementEnd();
		}
		else if (l->res == CMS_CLEX_CST_TITLE)	// found Title statement
		{
			l = clex.getCLexem();
			DEBUGLexem("parse 5", l);
			if (l->res == CMS_CRES_EOF)
				return l;
			if (l->res != CMS_CLEX_END_STATEMENT)
				WaitForStatementEnd();
			l = clex.getCLexem();
			DEBUGLexem("parse 6", l);
			if (l->res == CMS_CRES_EOF)
				return l;
			if (l->text_start == 0 || l->text_len == 0)
				continue;
			string text;
			text.append(l->text_start, l->text_len);
			index ++;
			local_write = p_bWrite && ((index == start_st && !all)
									   || (index >= start_st && all));
			if (do_append)
				p_rcoContext.AppendSubtitle(text);
			if (local_write)
				p_rcoOut << "<a name=\"a" << p_rcoContext.Language() << "."
				<< p_rcoContext.Article() << "_s" << index << "\">" << text << "</a>";
			WaitForStatementEnd();
		}
		else if (l->res == CMS_CLEX_CST_LINK && local_write)	// found Link statement
		{								// take action only if local_write is true
			string mode;
			string link;
			string target;
			RequireAtom(l);
			mode = string(l->Identifier);
			if (strcasecmp(l->Identifier, "external")
				&& strcasecmp(l->Identifier, "internal"))
				return l;
			RequireAtom(l);
			link = string(l->Identifier);
			l = clex.getCLexem();
			DEBUGLexem("parse 7", l);
			if (l->res == CMS_CRES_EOF)
				return l;
			if (l->res != CMS_CLEX_END_STATEMENT)
			{
				RequireAtom(l);
				target = string(l->Identifier);
				l = WaitForStatementEnd();
			}
			if (case_comp(mode, "internal") == 0)
			{
				CContext rcoContext = p_rcoContext;
				String2StringMMap* pcoParams = CURL::readQueryString(link);
				String2StringMMap::const_iterator coIt = pcoParams->find(P_IDLANG);
				if (coIt == pcoParams->end())
					return l;
				rcoContext.SetLanguage(atol((*coIt).second.c_str()));
				if ((coIt = pcoParams->find(P_IDPUBL)) == pcoParams->end())
					return l;
				rcoContext.SetPublication(atol((*coIt).second.c_str()));
				if ((coIt = pcoParams->find(P_NRISSUE)) == pcoParams->end())
					return l;
				rcoContext.SetIssue(atol((*coIt).second.c_str()));
				if ((coIt = pcoParams->find(P_NRSECTION)) == pcoParams->end())
					return l;
				rcoContext.SetSection(atol((*coIt).second.c_str()));
				if ((coIt = pcoParams->find(P_NRARTICLE)) == pcoParams->end())
					return l;
				rcoContext.SetArticle(atol((*coIt).second.c_str()));
				
				link = rcoContext.URL()->getURI();
			}
			if (local_write)
				p_rcoOut << "<a href=\"" << link << "\" TARGET=\"" << target << "\">";
			l = DoParse(p_rcoContext, p_rcoOut, p_SQL, do_append, index, start_st, all,
						p_bWrite);
			if (local_write)
				p_rcoOut << "</a>";
			if (l->res != CMS_CLEX_END_STATEMENT)
				WaitForStatementEnd();
		}
	}
	return NULL;
}

// parse: start the parser
// Parameters:
//		CContext& p_rcoContext - context
//		sockstream& p_rcoOut - output stream
//		MYSQL* p_SQL - pointer to MySQL connection
//		int start = 0 - print subtitles starting from start subtitle number
//		bool all = false - if true, print all subtitles
//		bool p_bWrite = true - if true, write output to p_rcoOut; if false it does not
//			write anything to output stream
int CCParser::parse(CContext& p_rcoContext, sockstream& p_rcoOut, MYSQL* p_SQL,
                      int start, bool all, bool p_bWrite)
{
	clex.reset();				// reset lex
	bool do_append = true;
	if (p_rcoContext.SubtitlesNumber() > 0)	// if article content is already parsed
		do_append = false;					// (subtitles number > 0) do not append to
											// subtitles list
	if (do_append)
	{
		// read article name and insert it as first element in subtitles list
		char pchTmpBuf[200];
		sprintf(pchTmpBuf, "select Name from Articles where IdPublication = %ld and "
		        "NrIssue= %ld and NrSection = %ld and Number = %ld and IdLanguage = %ld",
		        p_rcoContext.Publication(), p_rcoContext.Issue(), p_rcoContext.Section(),
		        p_rcoContext.Article(), p_rcoContext.Language());
		SQLQuery(p_SQL, pchTmpBuf);
		StoreResult(p_SQL, res)
		MYSQL_ROW row = mysql_fetch_row(*res);
		if (row == NULL)
			return -1;
		p_rcoContext.AppendSubtitle(string(row[0]));
	}
	if (all)
		start = 0;
	int index = 0;
	DoParse(p_rcoContext, p_rcoOut, p_SQL, do_append, index, start, all, p_bWrite);
	if (p_rcoContext.SubtitlesNumber() == 1)
		p_rcoContext.ResetSubtitles(p_rcoContext.CurrentField());
	return 0;
}
