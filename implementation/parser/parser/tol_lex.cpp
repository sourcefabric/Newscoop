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
 
Implementation of TOLLexem and TOLLex methods
 
******************************************************************************/

#include <ctype.h>
#include <string.h>

#include "tol_util.h"
#include "tol_atoms.h"
#include "tol_lex.h"
#include "tol_error.h"

const char TOLLex::s_pchTOLTokenStart[] = "<!**";
const char TOLLex::s_chTOLTokenEnd = '>';
const int TOLLex::s_nTempBuffLen = 1000 + strlen(s_pchTOLTokenStart);
TOLStatementHash TOLLex::s_coStatements(4, cpCharHashFn, cpCharEqual, TOLStatementValue);
pthread_once_t TOLLex::s_StatementsInit = PTHREAD_ONCE_INIT;

// NextChar: return next character from text buffer
char TOLLex::NextChar()
{
	if (m_pchInBuf == 0)
		return m_chChar = EOF;
	m_chChar = m_nIndex >= m_nBufLen ? EOF : m_pchInBuf[m_nIndex++];
	if (m_chChar == 0)
		NextChar();
	if (m_nState < 3 || m_nState == 4)
	{
		if (m_nTempIndex >= s_nTempBuffLen)
			FlushTempBuff();
		m_pchTempBuff[m_nTempIndex++] = m_chChar;
	}
	return m_chChar;
}

// FlushTempBuff: flush temporary buffer
inline void TOLLex::FlushTempBuff()
{
	m_nTempIndex = 0;
}

// IdentifyAtom: identifies the current lexem
const TOLLexem* TOLLex::IdentifyAtom()
{
	if (m_nAtomIdIndex == 0) // no atom
	{
		m_coLexem.m_pcoAtom = &m_coAtom;
		m_coLexem.m_DataType = TOL_DT_NONE;
		m_coLexem.m_Res = TOL_LEX_NONE;
		return &m_coLexem;
	}
	m_coAtom.m_pchIdentifier[m_nAtomIdIndex] = 0;
	// search read atom into statements list
	TOLStatementHash::iterator st_it = s_coStatements.find((cpChar)(m_coAtom.m_pchIdentifier));
	if (st_it != s_coStatements.end()) // identified statement
	{
		m_coLexem.m_pcoAtom = (const TOLAtom*)(&(*st_it));
		m_coLexem.m_Res = TOL_LEX_STATEMENT;
	}
	else // regular identifier
	{
		m_coLexem.m_pcoAtom = &m_coAtom;
		m_coLexem.m_Res = TOL_LEX_IDENTIFIER;
	}
	return &m_coLexem;
}

// AppentOnAtom: return true if not end of identifier buffer (can append character to atom
// identifier)
int TOLLex::AppendOnAtom()
{
	if (m_nAtomIdIndex >= ID_MAXLEN)
	{
		m_coAtom.m_pchIdentifier[m_nAtomIdIndex] = 0;
		return 0;
	}
	m_coAtom.m_pchIdentifier[m_nAtomIdIndex++] = m_chChar;
	if (!isdigit(m_chChar))
		m_coLexem.m_DataType = TOL_DT_STRING;
	return 1;
}

// InitStatements: initialise statements
void TOLLex::InitStatements()
{
	TOLTypeAttributesHash* pcoArticleTypeAttributes = NULL;
	GetArticleTypeAttributes(&pcoArticleTypeAttributes);

	TOLAttributeHash ah(4, cpCharHashFn, cpCharEqual, TOLAttributeValue);
	TOLStatementContextHash sch(4, TContextHashFn, TContextEqual, TOLStatementContextValue);
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_INCLUDE, ST_INCLUDE, sch));

	sch.clear();

	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("name", TOL_DT_STRING, "OrigName"));
	ah.insert_unique(TOLAttribute("number", TOL_DT_NUMBER));
	ah.insert_unique(TOLAttribute("englname", TOL_DT_STRING, "Name"));
	ah.insert_unique(TOLAttribute("code", TOL_DT_STRING, "Code"));
	ah.insert_unique(TOLAttribute("codepage", TOL_DT_STRING, "CodePage"));
	sch.insert_unique(TOLStatementContext(TOL_CT_PRINT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_LANGUAGE, ST_LANGUAGE, sch));


	sch.clear();

	ah.clear();
	ah.insert_unique(TOLAttribute("name", TOL_DT_STRING, "Name"));
	ah.insert_unique(TOLAttribute("identifier", TOL_DT_NUMBER, "Id"));
	ah.insert_unique(TOLAttribute("off", TOL_DT_NONE));
	ah.insert_unique(TOLAttribute("default", TOL_DT_NONE));
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("defined", TOL_DT_NONE));
	ah.insert_unique(TOLAttribute("fromstart", TOL_DT_NONE));
	sch.insert_unique(TOLStatementContext(TOL_CT_IF, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("name", TOL_DT_STRING, "Name"));
	ah.insert_unique(TOLAttribute("identifier", TOL_DT_NUMBER, "Id"));
	sch.insert_unique(TOLStatementContext(TOL_CT_PRINT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_PUBLICATION, ST_PUBLICATION, sch));


	sch.clear();

	ah.clear();
	ah.insert_unique(TOLAttribute("off", TOL_DT_NONE, ""));
	ah.insert_unique(TOLAttribute("default", TOL_DT_NONE, ""));
	ah.insert_unique(TOLAttribute("current", TOL_DT_NONE, ""));
	ah.insert_unique(TOLAttribute("number", TOL_DT_NUMBER, "Number"));
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("number", TOL_DT_NUMBER, "Number"));
	ah.insert_unique(TOLAttribute("iscurrent", TOL_DT_NONE));
	ah.insert_unique(TOLAttribute("defined", TOL_DT_NONE));
	ah.insert_unique(TOLAttribute("fromstart", TOL_DT_NONE));
	sch.insert_unique(TOLStatementContext(TOL_CT_IF, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("name", TOL_DT_STRING, "Name"));
	ah.insert_unique(TOLAttribute("number", TOL_DT_NUMBER, "Number"));
	ah.insert_unique(TOLAttribute("year", TOL_DT_NUMBER, "YEAR(PublicationDate)"));
	ah.insert_unique(TOLAttribute("mon_nr", TOL_DT_NUMBER, "MONTH(PublicationDate)"));
	ah.insert_unique(TOLAttribute("mday", TOL_DT_NUMBER, "DAYOFMONTH(PublicationDate)"));
	ah.insert_unique(TOLAttribute("yday", TOL_DT_NUMBER, "DAYOFYEAR(PublicationDate)"));
	ah.insert_unique(TOLAttribute("wday_nr", TOL_DT_NUMBER, "DAYOFWEEK(PublicationDate)"));
	ah.insert_unique(TOLAttribute("hour", TOL_DT_NUMBER, "HOUR(PublicationDate)"));
	ah.insert_unique(TOLAttribute("min", TOL_DT_NUMBER, "MINUTE(PublicationDate)"));
	ah.insert_unique(TOLAttribute("sec", TOL_DT_NUMBER, "SECOND(PublicationDate)"));
	sch.insert_unique(TOLStatementContext(TOL_CT_LIST, ah));

	ah.insert_unique(TOLAttribute("name", TOL_DT_STRING, "Name"));
	ah.insert_unique(TOLAttribute("number", TOL_DT_NUMBER, "Number"));
	ah.insert_unique(TOLAttribute("date", TOL_DT_DATE, "PublicationDate"));
	ah.insert_unique(TOLAttribute("year", TOL_DT_NUMBER, "YEAR(PublicationDate)"));
	ah.insert_unique(TOLAttribute("mon_nr", TOL_DT_NUMBER, "MONTH(PublicationDate)"));
	ah.insert_unique(TOLAttribute("mday", TOL_DT_NUMBER, "DAYOFMONTH(PublicationDate)"));
	ah.insert_unique(TOLAttribute("yday", TOL_DT_NUMBER, "DAYOFYEAR(PublicationDate)"));
	ah.insert_unique(TOLAttribute("wday_nr", TOL_DT_NUMBER, "DAYOFWEEK(PublicationDate)"));
	ah.insert_unique(TOLAttribute("hour", TOL_DT_NUMBER, "HOUR(PublicationDate)"));
	ah.insert_unique(TOLAttribute("min", TOL_DT_NUMBER, "MINUTE(PublicationDate)"));
	ah.insert_unique(TOLAttribute("sec", TOL_DT_NUMBER, "SECOND(PublicationDate)"));
	ah.insert_unique(TOLAttribute("mon_name", TOL_DT_STRING, "PublicationDate"));
	ah.insert_unique(TOLAttribute("wday_name", TOL_DT_STRING, "PublicationDate"));
	sch.insert_unique(TOLStatementContext(TOL_CT_PRINT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_ISSUE, ST_ISSUE, sch));


	sch.clear();

	ah.clear();
	ah.insert_unique(TOLAttribute("name", TOL_DT_STRING, "Name"));
	ah.insert_unique(TOLAttribute("number", TOL_DT_NUMBER, "Number"));
	ah.insert_unique(TOLAttribute("off", TOL_DT_NONE));
	ah.insert_unique(TOLAttribute("default", TOL_DT_NONE));
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	ah.insert_unique(TOLAttribute("name", TOL_DT_STRING, "Name"));
	ah.insert_unique(TOLAttribute("number", TOL_DT_NUMBER, "Number"));
	ah.insert_unique(TOLAttribute("defined", TOL_DT_NONE));
	ah.insert_unique(TOLAttribute("fromstart", TOL_DT_NONE));
	sch.insert_unique(TOLStatementContext(TOL_CT_IF, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("name", TOL_DT_STRING, "Name"));
	ah.insert_unique(TOLAttribute("number", TOL_DT_NUMBER, "Number"));
	sch.insert_unique(TOLStatementContext(TOL_CT_LIST, ah));
	sch.insert_unique(TOLStatementContext(TOL_CT_PRINT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_SECTION, ST_SECTION, sch));


	sch.clear();

	ah.clear();
	ah.insert_unique(TOLAttribute("name", TOL_DT_STRING, "Name"));
	ah.insert_unique(TOLAttribute("off", TOL_DT_NONE));
	ah.insert_unique(TOLAttribute("default", TOL_DT_NONE));
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("defined", TOL_DT_NONE));
	ah.insert_unique(TOLAttribute("type", TOL_DT_STRING, "Type", TOL_TYPE_ATTR));
	ah.insert_unique(TOLAttribute("fromstart", TOL_DT_NONE));
	sch.insert_unique(TOLStatementContext(TOL_CT_IF, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("name", TOL_DT_STRING, "Name"));
	ah.insert_unique(TOLAttribute("number", TOL_DT_NUMBER, "Number"));
	ah.insert_unique(TOLAttribute("keyword", TOL_DT_STRING, "Keywords"));
	ah.insert_unique(TOLAttribute("type", TOL_DT_STRING, "Type", TOL_TYPE_ATTR));
	ah.insert_unique(TOLAttribute("IsOn", TOL_DT_STRING));
	ah.insert_unique(TOLAttribute("IsNotOn", TOL_DT_STRING));
	sch.insert_unique(TOLStatementContext(TOL_CT_LIST, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("name", TOL_DT_STRING, "Name"));
	ah.insert_unique(TOLAttribute("number", TOL_DT_NUMBER, "Number"));
	ah.insert_unique(TOLAttribute("keywords", TOL_DT_STRING, "Keywords"));
	ah.insert_unique(TOLAttribute("type", TOL_DT_STRING, "Type", TOL_TYPE_ATTR));
	ah.insert_unique(TOLAttribute("year", TOL_DT_NUMBER, "YEAR(UploadDate)"));
	ah.insert_unique(TOLAttribute("mon_nr", TOL_DT_NUMBER, "MONTH(UploadDate)"));
	ah.insert_unique(TOLAttribute("mday", TOL_DT_NUMBER, "DAYOFMONTH(UploadDate)"));
	ah.insert_unique(TOLAttribute("yday", TOL_DT_NUMBER, "DAYOFYEAR(UploadDate)"));
	ah.insert_unique(TOLAttribute("wday_nr", TOL_DT_NUMBER, "DAYOFWEEK(UploadDate)"));
	ah.insert_unique(TOLAttribute("hour", TOL_DT_NUMBER, "HOUR(UploadDate)"));
	ah.insert_unique(TOLAttribute("min", TOL_DT_NUMBER, "MINUTE(UploadDate)"));
	ah.insert_unique(TOLAttribute("sec", TOL_DT_NUMBER, "SECOND(UploadDate)"));
	ah.insert_unique(TOLAttribute("mon_name", TOL_DT_STRING, "UploadDate"));
	ah.insert_unique(TOLAttribute("wday_name", TOL_DT_STRING, "UploadDate"));
	ah.insert_unique(TOLAttribute("upload_date", TOL_DT_DATE, "UploadDate"));
	sch.insert_unique(TOLStatementContext(TOL_CT_PRINT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_ARTICLE, ST_ARTICLE, sch,
	                             pcoArticleTypeAttributes));


	sch.clear();

	ah.clear();
	ah.insert_unique(TOLAttribute("bydate", TOL_DT_ORDER, "UploadDate"));
	ah.insert_unique(TOLAttribute("bynumber", TOL_DT_ORDER, "Number"));
	sch.insert_unique(TOLStatementContext(TOL_CT_LIST, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_ORDER, ST_ORDER, sch));


	sch.clear();

	ah.clear();
	ah.insert_unique(TOLAttribute("length", TOL_DT_NUMBER));
	ah.insert_unique(TOLAttribute("columns", TOL_DT_NUMBER));
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("row", TOL_DT_NUMBER_PARITY));
	ah.insert_unique(TOLAttribute("column", TOL_DT_NUMBER_PARITY));
	ah.insert_unique(TOLAttribute("start", TOL_DT_NONE));
	ah.insert_unique(TOLAttribute("end", TOL_DT_NONE));
	ah.insert_unique(TOLAttribute("index", TOL_DT_NUMBER_PARITY));
	sch.insert_unique(TOLStatementContext(TOL_CT_IF, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("row", TOL_DT_NONE));
	ah.insert_unique(TOLAttribute("column", TOL_DT_NONE));
	ah.insert_unique(TOLAttribute("index", TOL_DT_NONE));
	sch.insert_unique(TOLStatementContext(TOL_CT_PRINT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_LIST, ST_LIST, sch));


	sch.clear();

	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_FOREMPTYLIST, "ForEmptyList", sch));
	s_coStatements.insert_unique(TOLStatement(TOL_ST_ENDLIST, ST_ENDLIST, sch));


	sch.clear();

	ah.clear();
	ah.insert_unique(TOLAttribute("photographer", TOL_DT_NONE, "Photographer"));
	ah.insert_unique(TOLAttribute("place", TOL_DT_NONE, "Place"));
	ah.insert_unique(TOLAttribute("description", TOL_DT_NONE, "Description"));
	ah.insert_unique(TOLAttribute("year", TOL_DT_NONE, "YEAR(Date)"));
	ah.insert_unique(TOLAttribute("mon_nr", TOL_DT_NONE, "MONTH(Date)"));
	ah.insert_unique(TOLAttribute("mday", TOL_DT_NONE, "DAYOFMONTH(Date)"));
	ah.insert_unique(TOLAttribute("yday", TOL_DT_NONE, "DAYOFYEAR(Date)"));
	ah.insert_unique(TOLAttribute("wday_nr", TOL_DT_NONE, "DAYOFWEEK(Date)"));
	ah.insert_unique(TOLAttribute("hour", TOL_DT_NONE, "HOUR(Date)"));
	ah.insert_unique(TOLAttribute("min", TOL_DT_NONE, "MINUTE(Date)"));
	ah.insert_unique(TOLAttribute("sec", TOL_DT_NONE, "SECOND(Date)"));
	ah.insert_unique(TOLAttribute("mon_name", TOL_DT_STRING, "Date"));
	ah.insert_unique(TOLAttribute("wday_name", TOL_DT_STRING, "Date"));
	ah.insert_unique(TOLAttribute("date", TOL_DT_DATE, "Date"));
	sch.insert_unique(TOLStatementContext(TOL_CT_PRINT, ah));

	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_URLPARAMETERS, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_IMAGE, ST_IMAGE, sch));


	sch.clear();

	ah.clear();
	ah.insert_unique(TOLAttribute("fromstart"));
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_FORMPARAMETERS, ST_FORMPARAMETERS, sch));


	sch.clear();

	ah.insert_unique(TOLAttribute("allsubtitles"));
	ah.insert_unique(TOLAttribute("fromstart"));
	ah.insert_unique(TOLAttribute("reset_issue_list"));
	ah.insert_unique(TOLAttribute("reset_section_list"));
	ah.insert_unique(TOLAttribute("reset_article_list"));
	ah.insert_unique(TOLAttribute("reset_searchresult_list"));
	ah.insert_unique(TOLAttribute("reset_subtitle_list"));
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_URLPARAMETERS, ST_URLPARAMETERS, sch));


	sch.clear();

	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_PRINT, ST_PRINT, sch));
	s_coStatements.insert_unique(TOLStatement(TOL_ST_IF, ST_IF, sch));
	s_coStatements.insert_unique(TOLStatement(TOL_ST_ELSE, ST_ELSE, sch));
	s_coStatements.insert_unique(TOLStatement(TOL_ST_ENDIF, ST_ENDIF, sch));


	sch.clear();

	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_IF, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_PREVIOUSITEMS, ST_PREVIOUSITEMS, sch));
	s_coStatements.insert_unique(TOLStatement(TOL_ST_NEXTITEMS, ST_NEXTITEMS, sch));
	s_coStatements.insert_unique(TOLStatement(TOL_ST_PREVSUBTITLES, ST_PREVSUBTITLES, sch));
	s_coStatements.insert_unique(TOLStatement(TOL_ST_NEXTSUBTITLES, ST_NEXTSUBTITLES, sch));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_CURRENTSUBTITLE, ST_CURRENTSUBTITLE, sch));


	sch.clear();
	ah.clear();

	ah.insert_unique(TOLAttribute("year"));
	ah.insert_unique(TOLAttribute("mon_nr"));
	ah.insert_unique(TOLAttribute("mday"));
	ah.insert_unique(TOLAttribute("yday"));
	ah.insert_unique(TOLAttribute("wday_nr"));
	ah.insert_unique(TOLAttribute("hour"));
	ah.insert_unique(TOLAttribute("min"));
	ah.insert_unique(TOLAttribute("sec"));
	ah.insert_unique(TOLAttribute("mon_name"));
	ah.insert_unique(TOLAttribute("wday_name"));
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_DATE, ST_DATE, sch));


	sch.clear();
	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_LOCAL, ST_LOCAL, sch));
	s_coStatements.insert_unique(TOLStatement(TOL_ST_ENDLOCAL, ST_ENDLOCAL, sch));


	sch.clear();
	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("ok"));
	ah.insert_unique(TOLAttribute("error"));
	ah.insert_unique(TOLAttribute("trial"));
	ah.insert_unique(TOLAttribute("paid"));
	ah.insert_unique(TOLAttribute("action"));
	sch.insert_unique(TOLStatementContext(TOL_CT_IF, ah));

	ah.insert_unique(TOLAttribute("expdate", TOL_DT_DATE));
	ah.insert_unique(TOLAttribute("unit"));
	ah.insert_unique(TOLAttribute("error"));
	ah.insert_unique(TOLAttribute("unitcost", TOL_DT_NUMBER));
	ah.insert_unique(TOLAttribute("currency"));
	ah.insert_unique(TOLAttribute("trialtime", TOL_DT_NUMBER));
	ah.insert_unique(TOLAttribute("paidtime", TOL_DT_NUMBER));
	ah.insert_unique(TOLAttribute("totalcost", TOL_DT_NUMBER));
	sch.insert_unique(TOLStatementContext(TOL_CT_PRINT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("time"));
	sch.insert_unique(TOLStatementContext(TOL_CT_EDIT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("section"));
	sch.insert_unique(TOLStatementContext(TOL_CT_SELECT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_SUBSCRIPTION, ST_SUBSCRIPTION, sch));


	sch.clear();

	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_ENDSUBSCRIPTION, ST_ENDSUBSCRIPTION, sch));


	sch.clear();

	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("addok"));
	ah.insert_unique(TOLAttribute("modifyok"));
	ah.insert_unique(TOLAttribute("adderror"));
	ah.insert_unique(TOLAttribute("modifyerror"));
	ah.insert_unique(TOLAttribute("defined"));
	ah.insert_unique(TOLAttribute("addaction"));
	ah.insert_unique(TOLAttribute("modifyaction"));
	ah.insert_unique(TOLAttribute("loggedin"));
	sch.insert_unique(TOLStatementContext(TOL_CT_IF, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("adderror"));
	ah.insert_unique(TOLAttribute("modifyerror"));
	ah.insert_unique(TOLAttribute("Name", TOL_DT_NONE, "Name"));
	ah.insert_unique(TOLAttribute("UName", TOL_DT_NONE, "UName"));
	ah.insert_unique(TOLAttribute("EMail", TOL_DT_NONE, "EMail"));
	ah.insert_unique(TOLAttribute("City", TOL_DT_NONE, "City"));
	ah.insert_unique(TOLAttribute("StrAddress", TOL_DT_NONE, "StrAddress"));
	ah.insert_unique(TOLAttribute("State", TOL_DT_NONE, "State"));
	ah.insert_unique(TOLAttribute("Country", TOL_DT_NONE, "Country"));
	ah.insert_unique(TOLAttribute("Phone", TOL_DT_NONE, "Phone"));
	ah.insert_unique(TOLAttribute("Fax", TOL_DT_NONE, "Fax"));
	ah.insert_unique(TOLAttribute("Contact", TOL_DT_NONE, "Contact"));
	ah.insert_unique(TOLAttribute("Phone2", TOL_DT_NONE, "Phone2"));
	ah.insert_unique(TOLAttribute("PostalCode", TOL_DT_NONE, "PostalCode"));
	ah.insert_unique(TOLAttribute("Employer", TOL_DT_NONE, "Employer"));
	ah.insert_unique(TOLAttribute("Position", TOL_DT_NONE, "Position"));
	ah.insert_unique(TOLAttribute("Interests", TOL_DT_NONE, "Interests"));
	ah.insert_unique(TOLAttribute("How", TOL_DT_NONE, "How"));
	ah.insert_unique(TOLAttribute("Languages", TOL_DT_NONE, "Languages"));
	ah.insert_unique(TOLAttribute("Improvements", TOL_DT_NONE, "Improvements"));
	ah.insert_unique(TOLAttribute("Field1", TOL_DT_NONE, "Field1"));
	ah.insert_unique(TOLAttribute("Field2", TOL_DT_NONE, "Field2"));
	ah.insert_unique(TOLAttribute("Field3", TOL_DT_NONE, "Field3"));
	ah.insert_unique(TOLAttribute("Field4", TOL_DT_NONE, "Field4"));
	ah.insert_unique(TOLAttribute("Field5", TOL_DT_NONE, "Field5"));
	ah.insert_unique(TOLAttribute("Text1", TOL_DT_NONE, "Text1"));
	ah.insert_unique(TOLAttribute("Text2", TOL_DT_NONE, "Text2"));
	ah.insert_unique(TOLAttribute("Text3", TOL_DT_NONE, "Text3"));
	ah.insert_unique(TOLAttribute("Title", TOL_DT_NONE, "Title"));
	ah.insert_unique(TOLAttribute("Age", TOL_DT_NONE, "Age"));
	sch.insert_unique(TOLStatementContext(TOL_CT_PRINT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("Name", TOL_DT_NONE, "Name"));
	ah.insert_unique(TOLAttribute("UName", TOL_DT_NONE, "UName"));
	ah.insert_unique(TOLAttribute("Password", TOL_DT_NONE, "Password"));
	ah.insert_unique(TOLAttribute("PasswordAgain", TOL_DT_NONE, ""));
	ah.insert_unique(TOLAttribute("EMail", TOL_DT_NONE, "EMail"));
	ah.insert_unique(TOLAttribute("City", TOL_DT_NONE, "City"));
	ah.insert_unique(TOLAttribute("StrAddress", TOL_DT_NONE, "StrAddress"));
	ah.insert_unique(TOLAttribute("State", TOL_DT_NONE, "State"));
	ah.insert_unique(TOLAttribute("Phone", TOL_DT_NONE, "Phone"));
	ah.insert_unique(TOLAttribute("Fax", TOL_DT_NONE, "Fax"));
	ah.insert_unique(TOLAttribute("Contact", TOL_DT_NONE, "Contact"));
	ah.insert_unique(TOLAttribute("Phone2", TOL_DT_NONE, "Phone2"));
	ah.insert_unique(TOLAttribute("PostalCode", TOL_DT_NONE, "PostalCode"));
	ah.insert_unique(TOLAttribute("Employer", TOL_DT_NONE, "Employer"));
	ah.insert_unique(TOLAttribute("Position", TOL_DT_NONE, "Position"));
	ah.insert_unique(TOLAttribute("Interests", TOL_DT_NONE, "Interests"));
	ah.insert_unique(TOLAttribute("How", TOL_DT_NONE, "How"));
	ah.insert_unique(TOLAttribute("Languages", TOL_DT_NONE, "Languages"));
	ah.insert_unique(TOLAttribute("Improvements", TOL_DT_NONE, "Improvements"));
	ah.insert_unique(TOLAttribute("Field1", TOL_DT_NONE, "Field1"));
	ah.insert_unique(TOLAttribute("Field2", TOL_DT_NONE, "Field2"));
	ah.insert_unique(TOLAttribute("Field3", TOL_DT_NONE, "Field3"));
	ah.insert_unique(TOLAttribute("Field4", TOL_DT_NONE, "Field4"));
	ah.insert_unique(TOLAttribute("Field5", TOL_DT_NONE, "Field5"));
	ah.insert_unique(TOLAttribute("Text1", TOL_DT_NONE, "Text1"));
	ah.insert_unique(TOLAttribute("Text2", TOL_DT_NONE, "Text2"));
	ah.insert_unique(TOLAttribute("Text3", TOL_DT_NONE, "Text3"));
	sch.insert_unique(TOLStatementContext(TOL_CT_EDIT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("Country", TOL_DT_NONE, "CountryCode"));
	ah.insert_unique(TOLAttribute("Title", TOL_DT_NONE, "Title"));
	ah.insert_unique(TOLAttribute("Gender", TOL_DT_NONE, "Gender"));
	ah.insert_unique(TOLAttribute("Age", TOL_DT_NONE, "Age"));
	ah.insert_unique(TOLAttribute("EmployerType", TOL_DT_NONE, "EmployerType"));
	ah.insert_unique(TOLAttribute("Pref1", TOL_DT_NONE, "Pref1"));
	ah.insert_unique(TOLAttribute("Pref2", TOL_DT_NONE, "Pref2"));
	ah.insert_unique(TOLAttribute("Pref3", TOL_DT_NONE, "Pref3"));
	ah.insert_unique(TOLAttribute("Pref4", TOL_DT_NONE, "Pref4"));
	sch.insert_unique(TOLStatementContext(TOL_CT_SELECT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_USER, ST_USER, sch));


	sch.clear();

	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("ok"));
	ah.insert_unique(TOLAttribute("error"));
	ah.insert_unique(TOLAttribute("action"));
	sch.insert_unique(TOLStatementContext(TOL_CT_IF, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("error"));
	sch.insert_unique(TOLStatementContext(TOL_CT_PRINT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("UName", TOL_DT_NONE, "UName"));
	ah.insert_unique(TOLAttribute("Password", TOL_DT_NONE, "Password"));
	sch.insert_unique(TOLStatementContext(TOL_CT_EDIT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_LOGIN, ST_LOGIN, sch));


	sch.clear();

	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("ok"));
	ah.insert_unique(TOLAttribute("error"));
	ah.insert_unique(TOLAttribute("action"));
	sch.insert_unique(TOLStatementContext(TOL_CT_IF, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("error"));
	ah.insert_unique(TOLAttribute("Keywords"));
	sch.insert_unique(TOLStatementContext(TOL_CT_PRINT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("Keywords"));
	sch.insert_unique(TOLStatementContext(TOL_CT_EDIT, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("Mode"));
	ah.insert_unique(TOLAttribute("Level"));
	sch.insert_unique(TOLStatementContext(TOL_CT_SELECT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_SEARCH, ST_SEARCH, sch));


	sch.clear();

	ah.clear();

	sch.insert_unique(TOLStatementContext(TOL_CT_LIST, ah));

	ah.insert_unique(TOLAttribute("number", TOL_DT_NUMBER));
	sch.insert_unique(TOLStatementContext(TOL_CT_IF, ah));

	ah.clear();
	ah.insert_unique(TOLAttribute("name", TOL_DT_STRING));
	sch.insert_unique(TOLStatementContext(TOL_CT_PRINT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_SUBTITLE, ST_SUBTITLE, sch));


	sch.clear();
	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_ENDUSER, ST_ENDUSER, sch));
	s_coStatements.insert_unique(TOLStatement(TOL_ST_ENDLOGIN, ST_ENDLOGIN, sch));
	s_coStatements.insert_unique(TOLStatement(TOL_ST_ENDSEARCH, ST_ENDSEARCH, sch));
	s_coStatements.insert_unique(TOLStatement(TOL_ST_EDIT, ST_EDIT, sch));
	s_coStatements.insert_unique(TOLStatement(TOL_ST_SELECT, ST_SELECT, sch));


	sch.clear();
	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_LIST, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_SEARCHRESULT, ST_SEARCHRESULT, sch));


	sch.clear();
	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_IF, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_ALLOWED, ST_ALLOWED, sch));


	sch.clear();
	ah.clear();
	sch.insert_unique(TOLStatementContext(TOL_CT_DEFAULT, ah));

	s_coStatements.insert_unique(TOLStatement(TOL_ST_WITH, ST_WITH, sch));
	s_coStatements.insert_unique(TOLStatement(TOL_ST_ENDWITH, ST_ENDWITH, sch));
}

// constructor
TOLLex::TOLLex(cpChar i, ULInt bl)
		: m_coLexem(TOL_LEX_NONE, TOL_DT_NONE)
{
	m_pchTextStart = m_pchInBuf = i;
	m_nBufLen = bl;
	m_nIndex = 0;
	m_nLine = 1;
	m_nColumn = 0;
	m_nState = 1;
	m_chChar = 0;
	m_pchTempBuff = new char[s_nTempBuffLen];
	m_pchTempBuff[0] = 0;
	m_nTempIndex = 0;
	m_bLexemStarted = m_bIsEOF = false;
	pthread_once(&s_StatementsInit, InitStatements);
}

// Reset: reset lex
void TOLLex::Reset(cpChar i, ULInt bl)
{
	m_coLexem = TOLLexem(TOL_LEX_NONE, TOL_DT_NONE);
	m_pchTextStart = m_pchInBuf = i;
	m_nBufLen = bl;
	m_nIndex = 0;
	m_nLine = 1;
	m_nColumn = 0;
	m_nState = 1;
	m_chChar = 0;
	if (m_pchTempBuff == NULL)
		m_pchTempBuff = new char[s_nTempBuffLen];
	m_pchTempBuff[0] = 0;
	m_nTempIndex = 0;
	m_bLexemStarted = m_bIsEOF = false;
}

// assign operator
const TOLLex& TOLLex::operator =(const TOLLex& s)
{
	if (this == &s)
		return * this;
	m_pchTextStart = m_pchInBuf = s.m_pchInBuf;
	m_nBufLen = s.m_nBufLen;
	m_nIndex = 0;
	m_nLine = 1;
	m_nColumn = 0;
	m_nState = 1;
	m_chChar = 0;
	if (m_pchTempBuff == NULL)
		m_pchTempBuff = new char[s_nTempBuffLen];
	m_pchTempBuff[0] = 0;
	m_nTempIndex = 0;
	m_bLexemStarted = m_bIsEOF = false;
	m_pchTextStart = 0;
	return *this;
}

// GetLexem: return next lexem
const TOLLexem* TOLLex::GetLexem()
{
	bool FoundLexem;
	bool QuotedLexem;
	m_coLexem.m_pcoAtom = 0;
	m_coLexem.m_DataType = TOL_DT_NUMBER;
	m_coLexem.m_pchTextStart = 0;
	m_coLexem.m_nTextLen = 0;
	m_nAtomIdIndex = 0;
	FoundLexem = QuotedLexem = false;
	if (m_bIsEOF)
	{
		m_coLexem.m_Res = TOL_ERR_EOF;
		return &m_coLexem;
	}
	while (!FoundLexem && !m_bIsEOF)
	{
		NextChar();
		if (m_chChar == EOF) // end of text buffer
		{
			m_bIsEOF = true;
			if (m_bLexemStarted)
			{
				m_bLexemStarted = false;
				return IdentifyAtom();
			}
			m_coLexem.m_pchTextStart = m_pchTextStart;
			m_coLexem.m_nTextLen = m_nIndex - (ULInt)(m_pchTextStart - m_pchInBuf);
			m_coLexem.m_Res = TOL_LEX_NONE;
			return &m_coLexem;
		}
		m_nPrevLine = m_nLine;
		m_nPrevColumn = m_nColumn;
		if (m_chChar == '\n') // increment line and set column to 0 on new line character
		{
			m_nLine ++;
			m_nColumn = 0;
		}
		else
			m_nColumn += m_chChar == '\t' ? 8 : 1; // increment column (by 8 if character is tab)
		switch (m_nState)
		{
		case 1: // start state; read html text
			if (m_chChar == s_pchTOLTokenStart[0])
			{
				m_nTempIndex --;
				FlushTempBuff();
				m_pchTempBuff[m_nTempIndex++] = m_chChar;
			}
			if (strncmp(m_pchTempBuff, s_pchTOLTokenStart, m_nTempIndex) == 0)
				m_nState = 2;
			break;
		case 2: // found some characters matching start token
			if (m_chChar != s_pchTOLTokenStart[m_nTempIndex - 1])
			{
				m_nState = 1;
				break;
			}
			if (m_nTempIndex == (int)strlen(s_pchTOLTokenStart)) // found start token
			{
				m_nTempIndex = 0;
				m_nState = 3;
				m_bLexemStarted = false;
				if (m_pchTextStart)
				{
					m_coLexem.m_nTextLen = m_nIndex - (ULInt)(m_pchTextStart - m_pchInBuf)
					                       - strlen(s_pchTOLTokenStart);
					m_coLexem.m_pchTextStart = m_pchTextStart;
				}
				m_coLexem.m_Res = TOL_LEX_START_STATEMENT;
				return &m_coLexem;
			}
			break;
		case 3: // after a start token; read campsite instruction
			if (!m_bLexemStarted)		// didn't find an atom yet
			{
				if (m_chChar <= ' ')	// separator
				{
					;
				}
				else if (m_chChar == s_chTOLTokenEnd)	// end token
				{
					m_nState = 1;
					m_pchTextStart = m_pchInBuf + m_nIndex;
					m_coLexem.m_Res = TOL_LEX_END_STATEMENT;
					return &m_coLexem;
				}
				else if (m_chChar == '<')	// invalid token inside campsite instruction
				{
					m_pchTempBuff[m_nTempIndex++] = m_chChar;
					m_nState = 1;
					m_pchTextStart = 0;
					m_coLexem.m_Res = TOL_ERR_LESS_IN_TOKEN;
					return &m_coLexem;
				}
				else		// atom found
				{
					m_bLexemStarted = true;
					QuotedLexem = m_chChar == '\"';
					if (!(QuotedLexem))
						AppendOnAtom();
				}
			}
			else if (QuotedLexem)		// lexem (atom) is delimited by quotes
			{
				if (m_chChar < ' ' || m_chChar == s_chTOLTokenEnd)
				{
					m_bLexemStarted = false;
					QuotedLexem = false;
					if (m_chChar == s_chTOLTokenEnd)
						m_nState = 4;
					m_coLexem.m_Res = TOL_ERR_END_QUOTE_MISSING;
					return &m_coLexem;
				}
				else if (m_chChar == '\"')
				{
					FoundLexem = true;
					m_bLexemStarted = false;
				}
				else
				{
					if (!AppendOnAtom())
					{
						m_bLexemStarted = false;
						return IdentifyAtom();
					}
				}
			}
			else				// lexem is not delimited by quotes
			{
				if (m_chChar <= ' ' || m_chChar == s_chTOLTokenEnd)	// separator or end token
				{
					FoundLexem = true;
					m_bLexemStarted = false;
					if (m_chChar == s_chTOLTokenEnd)
					{
						m_nState = 4;
						return IdentifyAtom();
					}
				}
				else if (m_chChar == '\"')		// found another lexem delimited by quotes
				{
					FoundLexem = true;
					m_bLexemStarted = true;
					QuotedLexem = true;
					return IdentifyAtom();
				}
				else		// append character to atom identifier
				{
					if (!AppendOnAtom())
					{
						m_bLexemStarted = false;
						return IdentifyAtom();
					}
				}
			}
			break;
		case 4: // found end token; set the text start pointer
			m_nState = 1;
			m_pchTextStart = m_pchInBuf + m_nIndex - 1;
			m_coLexem.m_Res = TOL_LEX_END_STATEMENT;
			return &m_coLexem;
			break;
		}
	}
	return IdentifyAtom();
}

// PrintStatements: print lex statements (for test purposes)
void TOLLex::PrintStatements() const
{
	TOLAttributeHash::iterator ah_iterator;
	TOLStatementContextHash::iterator sch_iterator;
	TOLTypeAttributesHash::iterator ta_iterator;
	TOLStatementHash::iterator s_iterator;
	for (s_iterator = s_coStatements.begin(); s_iterator != s_coStatements.end(); ++(s_iterator))
	{
		cout << "Statement " << (*s_iterator).m_pchIdentifier << "\n";
		for (ta_iterator = (*s_iterator).type_attributes.begin();
		        ta_iterator != (*s_iterator).type_attributes.end();
		        ++(ta_iterator))
		{
			cout << "\tAttribute type " << (*ta_iterator).type_value << "\n";
			for (sch_iterator = (*ta_iterator).context_attributes.begin();
			        sch_iterator != (*ta_iterator).context_attributes.end();
			        ++(sch_iterator))
			{
				cout << "\t\tContext " << (int)(*sch_iterator).context << "\n\t\t\tAttributes\n";
				for (ah_iterator = (*sch_iterator).attributes.begin();
				        ah_iterator != (*sch_iterator).attributes.end();
				        ++ah_iterator)
					cout << "\t\t\t\t" << (*ah_iterator).m_pchIdentifier
					<< ", DType " << (int)((*ah_iterator).DType)
					<< ", Class " << (int)((*ah_iterator).attr_class)
					<< ", DBField " << ((*ah_iterator).DBField) << "\n";
			}
		}
		for (sch_iterator = (*s_iterator).statement_context.begin();
		        sch_iterator != (*s_iterator).statement_context.end();
		        ++(sch_iterator))
		{
			cout << "\tContext " << (int)(*sch_iterator).context << "\n\t\tAttributes\n";
			for (ah_iterator = (*sch_iterator).attributes.begin();
			        ah_iterator != (*sch_iterator).attributes.end();
			        ++ah_iterator)
				cout << "\t\t\t" << (*ah_iterator).m_pchIdentifier
				<< ", DType " << (int)((*ah_iterator).DType)
				<< ", Class " << (int)((*ah_iterator).attr_class)
				<< ", DBField " << ((*ah_iterator).DBField) << "\n";
		}
	}
}
