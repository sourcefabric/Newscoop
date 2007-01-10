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

Implementation of CLexem and CLex methods

******************************************************************************/

#include "lex.h"
#include "attributes.h"
#include "util.h"
#include "error.h"
#include "exceptions.h"

using std::list;
using std::cout;
using std::endl;

const int CMS_CT_DEFAULT = 0;
const int CMS_CT_LIST = CMS_ST_LIST;
const int CMS_CT_IF = CMS_ST_IF;
const int CMS_CT_PRINT = CMS_ST_PRINT;
const int CMS_CT_EDIT = CMS_ST_EDIT;
const int CMS_CT_SELECT = CMS_ST_SELECT;
const int CMS_CT_URLPARAMETERS = CMS_ST_URLPARAMETERS;
const int CMS_CT_WITH = CMS_ST_WITH;
const int CMS_CT_ISSUE = CMS_ST_ISSUE;
const int CMS_CT_ARTICLE = CMS_ST_ARTICLE;
const int CMS_CT_ARTICLECOMMENT = CMS_ST_ARTICLECOMMENT;
const int CMS_CT_SEARCHRESULT = CMS_ST_SEARCHRESULT;

const CStatementMap& CStatementMap::operator =(const CStatementMap& o)
{
	if (this == &o)
		return *this;
	clear();
	for (const_iterator coIt = o.begin(); coIt != o.end(); ++coIt)
	{
		insert(new CStatement(*(*coIt).second));
	}
	return *this;
}

bool CStatementMap::operator ==(const CStatementMap& o) const
{
	if (this == &o)
		return true;
	const_iterator coIt1 = begin();
	const_iterator coIt2 = o.begin();
	for (; coIt1 != end(); ++coIt1, ++coIt2)
	{
		if (coIt2 == o.end())
			return false;
		if ((*coIt1).first != (*coIt2).first
		    || *(*coIt1).second != *(*coIt2).second)
		{
			return false;
		}
	}
	return true;
}

void CStatementMap::clear()
{
	for (iterator coIt = begin(); coIt != end(); coIt = begin())
	{
		delete (*coIt).second;
		(*coIt).second = NULL;
		erase(coIt);
	}
}

const char CLex::s_pchCTokenStart[] = "<!**";
const char CLex::s_chCTokenEnd = '>';
CStatementMap CLex::s_coStatements;
//pthread_once_t CLex::s_StatementsInit = PTHREAD_ONCE_INIT;

// NextChar: return next character from text buffer
char CLex::NextChar()
{
	if (m_pchInBuf == 0)
		return m_chChar = EOF;
	if (m_nState == 4)
		return m_chChar;
	m_chChar = m_nIndex >= m_nBufLen ? EOF : m_pchInBuf[m_nIndex++];
	if (m_chChar == 0)
		NextChar();
	return m_chChar;
}

// IdentifyAtom: identifies the current lexem
const CLexem* CLex::IdentifyAtom()
{
	// accept empty string as identifiers
	if (m_coAtom.identifier() == "")
	{
		m_coLexem.setAtom(&m_coAtom);
		m_coLexem.setRes(CMS_LEX_IDENTIFIER);
		m_coLexem.setDataType(CMS_DT_STRING);
	}
	// search read atom into statements list
	CStatementMap::iterator st_it = s_coStatements.find(m_coAtom.identifier());
	if (st_it != s_coStatements.end()) // identified statement
	{
		m_coLexem.setAtom((*st_it).second);
		m_coLexem.setRes(CMS_LEX_STATEMENT);
	}
	else // regular identifier
	{
		m_coLexem.setAtom(&m_coAtom);
		m_coLexem.setRes(CMS_LEX_IDENTIFIER);
	}
	return &m_coLexem;
}

// AppentOnAtom: return true if not end of identifier buffer (can append character to atom
// identifier)
int CLex::AppendOnAtom()
{
	m_coAtom.push_back(m_chChar);
	if (!isdigit(m_chChar))
		m_coLexem.setDataType(CMS_DT_STRING);
	return 1;
}

CStatementMap::CStatementMap()
{
	InitStatements();
}

// InitStatements: initialize statements
int CStatementMap::InitStatements()
{
	OPEN_TRY
	// register enum types
	list<pair<string, lint> > coOrderValues;
	coOrderValues.push_back(pair<string, lint>("asc", 1));
	coOrderValues.push_back(pair<string, lint>("desc", -1));
	Enum::registerEnum("order_direction", coOrderValues);

	// register statements
	CTypeAttributesMap* pcoArticleTypeAttributes = NULL;
//	GetArticleTypeAttributes(&pcoArticleTypeAttributes);

	CStatement* pcoSt = NULL;
	CStatementContext* pcoCtx = NULL;

	// include statement
	pcoSt = new CStatement(CMS_ST_INCLUDE, ST_INCLUDE);

	this->insert(pcoSt);

	// language statement
	pcoSt = new CStatement(CMS_ST_LANGUAGE, ST_LANGUAGE);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoCtx->insertAttr(new CAttribute("off"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoCtx->insertAttr(new CStringAttr("name", "OrigName"));
	pcoCtx->insertAttr(new CIntegerAttr("number"));
	pcoCtx->insertAttr(new CStringAttr("englname", "Name"));
	pcoCtx->insertAttr(new CStringAttr("code", "Code"));
	pcoCtx->insertAttr(new CAttribute("defined"));
	pcoCtx->insertAttr(new CAttribute("fromstart"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CStringAttr("name", "OrigName"));
	pcoCtx->insertAttr(new CIntegerAttr("number"));
	pcoCtx->insertAttr(new CStringAttr("englname", "Name"));
	pcoCtx->insertAttr(new CStringAttr("code", "Code"));
	pcoCtx->insertAttr(new CStringAttr("codepage", "CodePage"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// publication statement
	pcoSt = new CStatement(CMS_ST_PUBLICATION, ST_PUBLICATION);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoCtx->insertAttr(new CStringAttr("name", "Name"));
	pcoCtx->insertAttr(new CIntegerAttr("identifier", "Id"));
	pcoCtx->insertAttr(new CAttribute("off"));
	pcoCtx->insertAttr(new CAttribute("default"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoCtx->insertAttr(new CAttribute("defined"));
	pcoCtx->insertAttr(new CAttribute("fromstart"));
	pcoCtx->insertAttr(new CStringAttr("name", "Name"));
	pcoCtx->insertAttr(new CIntegerAttr("identifier", "Id"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CStringAttr("name", "Name"));
	pcoCtx->insertAttr(new CIntegerAttr("identifier", "Id"));
	pcoCtx->insertAttr(new CStringAttr("site", "a.Name"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// issue statement
	pcoSt = new CStatement(CMS_ST_ISSUE, ST_ISSUE);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoCtx->insertAttr(new CAttribute("off"));
	pcoCtx->insertAttr(new CAttribute("default"));
	pcoCtx->insertAttr(new CAttribute("current"));
	pcoCtx->insertAttr(new CIntegerAttr("number", "Number"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoCtx->insertAttr(new CIntegerAttr("number", "Number"));
	pcoCtx->insertAttr(new CStringAttr("name", "Name"));
	pcoCtx->insertAttr(new CDateTimeAttr("publish_date", "PublicationDate"));
	pcoCtx->insertAttr(new CAttribute("iscurrent"));
	pcoCtx->insertAttr(new CAttribute("defined"));
	pcoCtx->insertAttr(new CAttribute("fromstart"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_LIST);
	pcoCtx->insertAttr(new CStringAttr("name", "Name"));
	pcoCtx->insertAttr(new CIntegerAttr("number", "Number"));
	pcoCtx->insertAttr(new CDateTimeAttr("publish_date", "PublicationDate"));
	pcoCtx->insertAttr(new CIntegerAttr("publish_year", "YEAR(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("publish_month", "MONTH(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("publish_mday", "DAYOFMONTH(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("year", "YEAR(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("mon_nr", "MONTH(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("mday", "DAYOFMONTH(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("yday", "DAYOFYEAR(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("wday_nr", "DAYOFWEEK(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("hour", "HOUR(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("min", "MINUTE(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("sec", "SECOND(PublicationDate)"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CStringAttr("name", "Name"));
	pcoCtx->insertAttr(new CIntegerAttr("number", "Number"));
	pcoCtx->insertAttr(new CDateTimeAttr("date", "PublicationDate"));
	pcoCtx->insertAttr(new CIntegerAttr("year", "YEAR(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("mon_nr", "MONTH(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("mday", "DAYOFMONTH(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("yday", "DAYOFYEAR(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("wday_nr", "DAYOFWEEK(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("hour", "HOUR(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("min", "MINUTE(PublicationDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("sec", "SECOND(PublicationDate)"));
	pcoCtx->insertAttr(new CStringAttr("mon_name", "PublicationDate"));
	pcoCtx->insertAttr(new CStringAttr("wday_name", "PublicationDate"));
	pcoCtx->insertAttr(new CStringAttr("template"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// section statement
	pcoSt = new CStatement(CMS_ST_SECTION, ST_SECTION);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoCtx->insertAttr(new CStringAttr("name", "Name"));
	pcoCtx->insertAttr(new CIntegerAttr("number", "Number"));
	pcoCtx->insertAttr(new CAttribute("off"));
	pcoCtx->insertAttr(new CAttribute("default"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoCtx->insertAttr(new CStringAttr("name", "Name"));
	pcoCtx->insertAttr(new CIntegerAttr("number", "Number"));
	pcoCtx->insertAttr(new CAttribute("defined"));
	pcoCtx->insertAttr(new CAttribute("fromstart"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_LIST);
	pcoCtx->insertAttr(new CStringAttr("name", "Name"));
	pcoCtx->insertAttr(new CIntegerAttr("number", "Number"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CStringAttr("name", "Name"));
	pcoCtx->insertAttr(new CIntegerAttr("number", "Number"));
	pcoCtx->insertAttr(new CStringAttr("template"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// article statement
	pcoSt = new CStatement(CMS_ST_ARTICLE, ST_ARTICLE);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoCtx->insertAttr(new CStringAttr("name", "Name"));
	pcoCtx->insertAttr(new CStringAttr("number", "Number"));
	pcoCtx->insertAttr(new CAttribute("off"));
	pcoCtx->insertAttr(new CAttribute("default"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoCtx->insertAttr(new CStringAttr("name", "Name"));
	pcoCtx->insertAttr(new CDateTimeAttr("upload_date", "UploadDate"));
	pcoCtx->insertAttr(new CDateTimeAttr("publish_date", "PublishDate"));
	pcoCtx->insertAttr(new CAttribute("has_keyword"));
	pcoCtx->insertAttr(new CAttribute("public", "Public"));
	pcoCtx->insertAttr(new CSwitchAttr("OnFrontPage", "OnFrontPage"));
	pcoCtx->insertAttr(new CSwitchAttr("OnSection", "OnSection"));
	pcoCtx->insertAttr(new CAttribute("defined"));
	pcoCtx->insertAttr(new CStringAttr("type", "Type", CMS_TYPE_ATTR));
	pcoCtx->insertAttr(new CAttribute("fromstart"));
	pcoCtx->insertAttr(new CAttribute("translated_to"));
	pcoCtx->insertAttr(new CAttribute("hasAttachments"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_LIST);
	pcoCtx->insertAttr(new CStringAttr("name", "Name"));
	pcoCtx->insertAttr(new CIntegerAttr("number", "Number"));
	pcoCtx->insertAttr(new CDateTimeAttr("upload_date", "UploadDate"));
	pcoCtx->insertAttr(new CDateTimeAttr("publish_date", "PublishDate"));
	pcoCtx->insertAttr(new CStringAttr("keyword", "Keywords"));
	pcoCtx->insertAttr(new CStringAttr("type", "Type", CMS_TYPE_ATTR));
	pcoCtx->insertAttr(new CSwitchAttr("OnFrontPage", "OnFrontPage"));
	pcoCtx->insertAttr(new CSwitchAttr("OnSection", "OnSection"));
	pcoCtx->insertAttr(new CSwitchAttr("public", "Public"));
	pcoCtx->insertAttr(new CTopicAttr("topic"));
	pcoCtx->insertAttr(new CAttribute("matchAllTopics"));
	pcoCtx->insertAttr(new CAttribute("matchAnyTopic"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CStringAttr("name", "Name"));
	pcoCtx->insertAttr(new CIntegerAttr("number", "Number"));
	pcoCtx->insertAttr(new CStringAttr("keywords", "Keywords"));
	pcoCtx->insertAttr(new CStringAttr("type", "Type", CMS_TYPE_ATTR));
	pcoCtx->insertAttr(new CIntegerAttr("year", "YEAR(UploadDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("mon_nr", "MONTH(UploadDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("mday", "DAYOFMONTH(UploadDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("yday", "DAYOFYEAR(UploadDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("wday_nr", "DAYOFWEEK(UploadDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("hour", "HOUR(UploadDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("min", "MINUTE(UploadDate)"));
	pcoCtx->insertAttr(new CIntegerAttr("sec", "SECOND(UploadDate)"));
	pcoCtx->insertAttr(new CStringAttr("mon_name", "UploadDate"));
	pcoCtx->insertAttr(new CStringAttr("wday_name", "UploadDate"));
	pcoCtx->insertAttr(new CDateTimeAttr("upload_date", "UploadDate"));
	pcoCtx->insertAttr(new CDateTimeAttr("uploaddate", "UploadDate"));
	pcoCtx->insertAttr(new CDateTimeAttr("publishdate", "PublishDate"));
	pcoCtx->insertAttr(new CStringAttr("template"));
	pcoSt->insertCtx(pcoCtx);

	pcoSt->updateTypes(pcoArticleTypeAttributes);
	this->insert(pcoSt);

	// topic statement
	pcoSt = new CStatement(CMS_ST_TOPIC, ST_TOPIC);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoCtx->insertAttr(new CIntegerAttr("identifier"));
	pcoCtx->insertAttr(new CTopicAttr("name"));
	pcoCtx->insertAttr(new CAttribute("off"));
	pcoCtx->insertAttr(new CAttribute("default"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoCtx->insertAttr(new CTopicAttr("name"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CIntegerAttr("identifier"));
	pcoCtx->insertAttr(new CStringAttr("name"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// order statement
	pcoSt = new CStatement(CMS_ST_ORDER, ST_ORDER);

	pcoCtx = new CStatementContext(CMS_CT_ISSUE);
	pcoCtx->insertAttr(new CEnumAttr("bydate", "order_direction", "PublicationDate"));
	pcoCtx->insertAttr(new CEnumAttr("bycreationdate", "order_direction", "PublicationDate"));
	pcoCtx->insertAttr(new CEnumAttr("bypublishdate", "order_direction", "PublicationDate"));
	pcoCtx->insertAttr(new CEnumAttr("bynumber", "order_direction", "Number"));
	pcoCtx->insertAttr(new CEnumAttr("byname", "order_direction", "Name"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_ARTICLE);
	pcoCtx->insertAttr(new CEnumAttr("bydate", "order_direction", "UploadDate"));
	pcoCtx->insertAttr(new CEnumAttr("bycreationdate", "order_direction", "UploadDate"));
	pcoCtx->insertAttr(new CEnumAttr("bypublishdate", "order_direction", "PublishDate"));
	pcoCtx->insertAttr(new CEnumAttr("bynumber", "order_direction", "Number"));
	pcoCtx->insertAttr(new CEnumAttr("byname", "order_direction", "Name"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_ARTICLECOMMENT);
	pcoCtx->insertAttr(new CEnumAttr("bydate", "order_direction", "message_id"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_SEARCHRESULT);
	pcoCtx->insertAttr(new CEnumAttr("bydate", "order_direction", "UploadDate"));
	pcoCtx->insertAttr(new CEnumAttr("bycreationdate", "order_direction", "UploadDate"));
	pcoCtx->insertAttr(new CEnumAttr("bypublishdate", "order_direction", "PublishDate"));
	pcoCtx->insertAttr(new CEnumAttr("bynumber", "order_direction", "Number"));
	pcoCtx->insertAttr(new CEnumAttr("byname", "order_direction", "Name"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	pcoSt = new CStatement(CMS_ST_HTMLENCODING, ST_HTMLENCODING);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoCtx->insertAttr(new CAttribute("on"));
	pcoCtx->insertAttr(new CAttribute("off"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// list statement
	pcoSt = new CStatement(CMS_ST_LIST, ST_LIST);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoCtx->insertAttr(new CIntegerAttr("length"));
	pcoCtx->insertAttr(new CIntegerAttr("columns"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoCtx->insertAttr(new CIntegerAttr("row"));
	pcoCtx->insertAttr(new CIntegerAttr("column"));
	pcoCtx->insertAttr(new CAttribute("start"));
	pcoCtx->insertAttr(new CAttribute("end"));
	pcoCtx->insertAttr(new CIntegerAttr("index"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CAttribute("row"));
	pcoCtx->insertAttr(new CAttribute("column"));
	pcoCtx->insertAttr(new CAttribute("index"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// ForEmptyList statement
	this->insert(new CStatement(CMS_ST_FOREMPTYLIST, ST_FOREMPTYLIST));

	// EndList statement
	this->insert(new CStatement(CMS_ST_ENDLIST, ST_ENDLIST));

	// image statement
	pcoSt = new CStatement(CMS_ST_IMAGE, ST_IMAGE);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CIntegerAttr("number", "Number"));
	pcoCtx->insertAttr(new CAttribute("photographer", "Photographer"));
	pcoCtx->insertAttr(new CAttribute("place", "Place"));
	pcoCtx->insertAttr(new CAttribute("description", "Description"));
	pcoCtx->insertAttr(new CAttribute("year", "YEAR(Date)"));
	pcoCtx->insertAttr(new CAttribute("mon_nr", "MONTH(Date)"));
	pcoCtx->insertAttr(new CAttribute("mday", "DAYOFMONTH(Date)"));
	pcoCtx->insertAttr(new CAttribute("yday", "DAYOFYEAR(Date)"));
	pcoCtx->insertAttr(new CAttribute("wday_nr", "DAYOFWEEK(Date)"));
	pcoCtx->insertAttr(new CAttribute("hour", "HOUR(Date)"));
	pcoCtx->insertAttr(new CAttribute("min", "MINUTE(Date)"));
	pcoCtx->insertAttr(new CAttribute("sec", "SECOND(Date)"));
	pcoCtx->insertAttr(new CStringAttr("mon_name", "Date"));
	pcoCtx->insertAttr(new CStringAttr("wday_name", "Date"));
	pcoCtx->insertAttr(new CDateTimeAttr("date", "Date"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_URLPARAMETERS);
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// ArticleImage statement
	pcoSt = new CStatement(CMS_ST_ARTICLEIMAGE, ST_ARTICLEIMAGE);

	pcoCtx = new CStatementContext(CMS_CT_LIST);
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// FormParameters statement
	pcoSt = new CStatement(CMS_ST_FORMPARAMETERS, ST_FORMPARAMETERS);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoCtx->insertAttr(new CAttribute("fromstart"));
	pcoCtx->insertAttr(new CAttribute("articleComment"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// URLParameters statement
	pcoSt = new CStatement(CMS_ST_URLPARAMETERS, ST_URLPARAMETERS);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoCtx->insertAttr(new CAttribute("fromstart"));
	pcoCtx->insertAttr(new CAttribute("reset_issue_list"));
	pcoCtx->insertAttr(new CAttribute("reset_section_list"));
	pcoCtx->insertAttr(new CAttribute("reset_article_list"));
	pcoCtx->insertAttr(new CAttribute("reset_searchresult_list"));
	pcoCtx->insertAttr(new CAttribute("reset_subtitle_list"));
	pcoCtx->insertAttr(new CAttribute("root_level"));
	pcoCtx->insertAttr(new CAttribute("language"));
	pcoCtx->insertAttr(new CAttribute("publication"));
	pcoCtx->insertAttr(new CAttribute("issue"));
	pcoCtx->insertAttr(new CAttribute("section"));
	pcoCtx->insertAttr(new CAttribute("article"));
	pcoCtx->insertAttr(new CAttribute("allsubtitles"));
	pcoCtx->insertAttr(new CAttribute("articleAttachment"));
	pcoCtx->insertAttr(new CAttribute("articleComment"));
	pcoCtx->insertAttr(new CIntegerAttr("image"));
	pcoCtx->insertAttr(new CStringAttr("template"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// URI statement
	pcoSt = new CStatement(CMS_ST_URI, ST_URI);

	// use the same context as URLParameters
	pcoSt->insertCtx(new CStatementContext(*pcoCtx));

	this->insert(pcoSt);

	// URL statement
	pcoSt = new CStatement(CMS_ST_URL, ST_URL);

	// use the same context as URLParameters
	pcoSt->insertCtx(new CStatementContext(*pcoCtx));

	this->insert(pcoSt);

	// print statement
	this->insert(new CStatement(CMS_ST_PRINT, ST_PRINT));

	// if statement
	this->insert(new CStatement(CMS_ST_IF, ST_IF));

	// else statement
	this->insert(new CStatement(CMS_ST_ELSE, ST_ELSE));

	// endif statement
	this->insert(new CStatement(CMS_ST_ENDIF, ST_ENDIF));

	// PreviousItems statement
	this->insert(new CStatement(CMS_ST_PREVIOUSITEMS, ST_PREVIOUSITEMS));

	// NextItems statement
	this->insert(new CStatement(CMS_ST_NEXTITEMS, ST_NEXTITEMS));

	// PreviousSubtitles statement
	this->insert(new CStatement(CMS_ST_PREVSUBTITLES, ST_PREVSUBTITLES));

	// NextSubtitles statement
	this->insert(new CStatement(CMS_ST_NEXTSUBTITLES, ST_NEXTSUBTITLES));

	// CurrentSubtitle statement
	this->insert(new CStatement(CMS_ST_CURRENTSUBTITLE, ST_CURRENTSUBTITLE));

	// date statement
	pcoSt = new CStatement(CMS_ST_DATE, ST_DATE);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoCtx->insertAttr(new CIntegerAttr("year"));
	pcoCtx->insertAttr(new CIntegerAttr("mon_nr"));
	pcoCtx->insertAttr(new CIntegerAttr("mday"));
	pcoCtx->insertAttr(new CIntegerAttr("yday"));
	pcoCtx->insertAttr(new CIntegerAttr("wday_nr"));
	pcoCtx->insertAttr(new CIntegerAttr("hour"));
	pcoCtx->insertAttr(new CIntegerAttr("min"));
	pcoCtx->insertAttr(new CIntegerAttr("sec"));
	pcoCtx->insertAttr(new CIntegerAttr("mon_name"));
	pcoCtx->insertAttr(new CIntegerAttr("wday_name"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// local statement
	this->insert(new CStatement(CMS_ST_LOCAL, ST_LOCAL));

	// endlocal statement
	this->insert(new CStatement(CMS_ST_ENDLOCAL, ST_ENDLOCAL));

	// subscription statement
	pcoSt = new CStatement(CMS_ST_SUBSCRIPTION, ST_SUBSCRIPTION);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoCtx->insertAttr(new CAttribute("ok"));
	pcoCtx->insertAttr(new CAttribute("error"));
	pcoCtx->insertAttr(new CAttribute("trial"));
	pcoCtx->insertAttr(new CAttribute("paid"));
	pcoCtx->insertAttr(new CAttribute("action"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CDateAttr("expdate"));
	pcoCtx->insertAttr(new CAttribute("unit"));
	pcoCtx->insertAttr(new CAttribute("error"));
	pcoCtx->insertAttr(new CIntegerAttr("unitcost"));
	pcoCtx->insertAttr(new CAttribute("currency"));
	pcoCtx->insertAttr(new CIntegerAttr("trialtime"));
	pcoCtx->insertAttr(new CIntegerAttr("paidtime"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_EDIT);
	pcoCtx->insertAttr(new CAttribute("time"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_SELECT);
	pcoCtx->insertAttr(new CAttribute("section"));
	pcoCtx->insertAttr(new CAttribute("languages"));
	pcoCtx->insertAttr(new CAttribute("alllanguages"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoCtx->insertAttr(new CAttribute("by_publication"));
	pcoCtx->insertAttr(new CAttribute("by_section"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// EndSubscription statement
	this->insert(new CStatement(CMS_ST_ENDSUBSCRIPTION, ST_ENDSUBSCRIPTION));

	// user statement
	pcoSt = new CStatement(CMS_ST_USER, ST_USER);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoCtx->insertAttr(new CAttribute("addok"));
	pcoCtx->insertAttr(new CAttribute("modifyok"));
	pcoCtx->insertAttr(new CAttribute("adderror"));
	pcoCtx->insertAttr(new CAttribute("modifyerror"));
	pcoCtx->insertAttr(new CAttribute("defined"));
	pcoCtx->insertAttr(new CAttribute("addaction"));
	pcoCtx->insertAttr(new CAttribute("modifyaction"));
	pcoCtx->insertAttr(new CAttribute("loggedin"));
	pcoCtx->insertAttr(new CAttribute("BlockedFromComments"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CAttribute("adderror"));
	pcoCtx->insertAttr(new CAttribute("modifyerror"));
	pcoCtx->insertAttr(new CAttribute("Identifier", "Id"));
	pcoCtx->insertAttr(new CAttribute("Name", "Name"));
	pcoCtx->insertAttr(new CAttribute("UName", "UName"));
	pcoCtx->insertAttr(new CAttribute("EMail", "EMail"));
	pcoCtx->insertAttr(new CAttribute("City", "City"));
	pcoCtx->insertAttr(new CAttribute("StrAddress", "StrAddress"));
	pcoCtx->insertAttr(new CAttribute("State", "State"));
	pcoCtx->insertAttr(new CAttribute("Country", "Country"));
	pcoCtx->insertAttr(new CAttribute("Phone", "Phone"));
	pcoCtx->insertAttr(new CAttribute("Fax", "Fax"));
	pcoCtx->insertAttr(new CAttribute("Contact", "Contact"));
	pcoCtx->insertAttr(new CAttribute("Phone2", "Phone2"));
	pcoCtx->insertAttr(new CAttribute("PostalCode", "PostalCode"));
	pcoCtx->insertAttr(new CAttribute("Employer", "Employer"));
	pcoCtx->insertAttr(new CAttribute("Position", "Position"));
	pcoCtx->insertAttr(new CAttribute("Interests", "Interests"));
	pcoCtx->insertAttr(new CAttribute("How", "How"));
	pcoCtx->insertAttr(new CAttribute("Languages", "Languages"));
	pcoCtx->insertAttr(new CAttribute("Improvements", "Improvements"));
	pcoCtx->insertAttr(new CAttribute("Field1", "Field1"));
	pcoCtx->insertAttr(new CAttribute("Field2", "Field2"));
	pcoCtx->insertAttr(new CAttribute("Field3", "Field3"));
	pcoCtx->insertAttr(new CAttribute("Field4", "Field4"));
	pcoCtx->insertAttr(new CAttribute("Field5", "Field5"));
	pcoCtx->insertAttr(new CAttribute("Text1", "Text1"));
	pcoCtx->insertAttr(new CAttribute("Text2", "Text2"));
	pcoCtx->insertAttr(new CAttribute("Text3", "Text3"));
	pcoCtx->insertAttr(new CAttribute("Title", "Title"));
	pcoCtx->insertAttr(new CAttribute("Age", "Age"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_EDIT);
	pcoCtx->insertAttr(new CAttribute("Name", "Name"));
	pcoCtx->insertAttr(new CAttribute("UName", "UName"));
	pcoCtx->insertAttr(new CAttribute("Password", "Password"));
	pcoCtx->insertAttr(new CAttribute("PasswordAgain", ""));
	pcoCtx->insertAttr(new CAttribute("EMail", "EMail"));
	pcoCtx->insertAttr(new CAttribute("City", "City"));
	pcoCtx->insertAttr(new CAttribute("StrAddress", "StrAddress"));
	pcoCtx->insertAttr(new CAttribute("State", "State"));
	pcoCtx->insertAttr(new CAttribute("Phone", "Phone"));
	pcoCtx->insertAttr(new CAttribute("Fax", "Fax"));
	pcoCtx->insertAttr(new CAttribute("Contact", "Contact"));
	pcoCtx->insertAttr(new CAttribute("Phone2", "Phone2"));
	pcoCtx->insertAttr(new CAttribute("PostalCode", "PostalCode"));
	pcoCtx->insertAttr(new CAttribute("Employer", "Employer"));
	pcoCtx->insertAttr(new CAttribute("Position", "Position"));
	pcoCtx->insertAttr(new CAttribute("Interests", "Interests"));
	pcoCtx->insertAttr(new CAttribute("How", "How"));
	pcoCtx->insertAttr(new CAttribute("Languages", "Languages"));
	pcoCtx->insertAttr(new CAttribute("Improvements", "Improvements"));
	pcoCtx->insertAttr(new CAttribute("Field1", "Field1"));
	pcoCtx->insertAttr(new CAttribute("Field2", "Field2"));
	pcoCtx->insertAttr(new CAttribute("Field3", "Field3"));
	pcoCtx->insertAttr(new CAttribute("Field4", "Field4"));
	pcoCtx->insertAttr(new CAttribute("Field5", "Field5"));
	pcoCtx->insertAttr(new CAttribute("Text1", "Text1"));
	pcoCtx->insertAttr(new CAttribute("Text2", "Text2"));
	pcoCtx->insertAttr(new CAttribute("Text3", "Text3"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_SELECT);
	pcoCtx->insertAttr(new CAttribute("Country", "CountryCode"));
	pcoCtx->insertAttr(new CAttribute("Title", "Title"));
	pcoCtx->insertAttr(new CAttribute("Gender", "Gender"));
	pcoCtx->insertAttr(new CAttribute("Age", "Age"));
	pcoCtx->insertAttr(new CAttribute("EmployerType", "EmployerType"));
	pcoCtx->insertAttr(new CAttribute("Pref1", "Pref1"));
	pcoCtx->insertAttr(new CAttribute("Pref2", "Pref2"));
	pcoCtx->insertAttr(new CAttribute("Pref3", "Pref3"));
	pcoCtx->insertAttr(new CAttribute("Pref4", "Pref4"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// login statement
	pcoSt = new CStatement(CMS_ST_LOGIN, ST_LOGIN);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoCtx->insertAttr(new CAttribute("ok"));
	pcoCtx->insertAttr(new CAttribute("error"));
	pcoCtx->insertAttr(new CAttribute("action"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CAttribute("error"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_EDIT);
	pcoCtx->insertAttr(new CAttribute("UName", "UName"));
	pcoCtx->insertAttr(new CAttribute("Password", "Password"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_SELECT);
	pcoCtx->insertAttr(new CAttribute("RememberUser"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// search statement
	pcoSt = new CStatement(CMS_ST_SEARCH, ST_SEARCH);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoCtx->insertAttr(new CAttribute("ok"));
	pcoCtx->insertAttr(new CAttribute("error"));
	pcoCtx->insertAttr(new CAttribute("action"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CAttribute("error"));
	pcoCtx->insertAttr(new CAttribute("Keywords"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_EDIT);
	pcoCtx->insertAttr(new CAttribute("Keywords"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_SELECT);
	pcoCtx->insertAttr(new CAttribute("Mode"));
	pcoCtx->insertAttr(new CAttribute("Level"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// subtitle statement
	pcoSt = new CStatement(CMS_ST_SUBTITLE, ST_SUBTITLE);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoCtx->insertAttr(new CIntegerAttr("number"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CStringAttr("name"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// EndUser statement
	this->insert(new CStatement(CMS_ST_ENDUSER, ST_ENDUSER));

	// EndLogin statement
	this->insert(new CStatement(CMS_ST_ENDLOGIN, ST_ENDLOGIN));

	// EndSearch statement
	this->insert(new CStatement(CMS_ST_ENDSEARCH, ST_ENDSEARCH));

	// Edit statement
	this->insert(new CStatement(CMS_ST_EDIT, ST_EDIT));

	// Select statement
	this->insert(new CStatement(CMS_ST_SELECT, ST_SELECT));

	// SearchResult statement
	this->insert(new CStatement(CMS_ST_SEARCHRESULT, ST_SEARCHRESULT));

	// Allowed statement
	this->insert(new CStatement(CMS_ST_ALLOWED, ST_ALLOWED));

	// With statement
	this->insert(new CStatement(CMS_ST_WITH, ST_WITH));

	// EndWith statement
	this->insert(new CStatement(CMS_ST_ENDWITH, ST_ENDWITH));

	// URIPath statement
	pcoSt = new CStatement(CMS_ST_URIPATH, ST_URIPATH);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoCtx->insertAttr(new CStringAttr("template"));
	pcoCtx->insertAttr(new CAttribute("root_level"));
	pcoCtx->insertAttr(new CAttribute("language"));
	pcoCtx->insertAttr(new CAttribute("publication"));
	pcoCtx->insertAttr(new CAttribute("issue"));
	pcoCtx->insertAttr(new CAttribute("section"));
	pcoCtx->insertAttr(new CAttribute("article"));
	pcoCtx->insertAttr(new CAttribute("articleAttachment"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// ArticleTopic statement
	pcoSt = new CStatement(CMS_ST_ARTICLETOPIC, ST_ARTICLETOPIC);

	pcoCtx = new CStatementContext(CMS_CT_LIST);
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// Subtopic statement
	pcoSt = new CStatement(CMS_ST_SUBTOPIC, ST_SUBTOPIC);

	pcoCtx = new CStatementContext(CMS_CT_LIST);
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// ArticleAttachment statement
	pcoSt = new CStatement(CMS_ST_ARTICLEATTACHMENT, ST_ARTICLEATTACHMENT);

	pcoCtx = new CStatementContext(CMS_CT_LIST);
	pcoCtx->insertAttr(new CAttribute("ForCurrentLanguage"));
	pcoCtx->insertAttr(new CAttribute("ForAllLanguages"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CStringAttr("FileName", "file_name"));
	pcoCtx->insertAttr(new CStringAttr("MimeType", "mime_type"));
	pcoCtx->insertAttr(new CStringAttr("Extension", "extension"));
	pcoCtx->insertAttr(new CStringAttr("Description"));
	pcoCtx->insertAttr(new CIntegerAttr("sizeB", "size_in_bytes"));
	pcoCtx->insertAttr(new CIntegerAttr("sizeKB", "ROUND(size_in_bytes/1024)"));
	pcoCtx->insertAttr(new CIntegerAttr("sizeMB", "ROUND(size_in_bytes/1048576)"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoCtx->insertAttr(new CStringAttr("FileName", "file_name"));
	pcoCtx->insertAttr(new CStringAttr("MimeType", "mime_type"));
	pcoCtx->insertAttr(new CStringAttr("Extension", "extension"));
	pcoCtx->insertAttr(new CIntegerAttr("sizeB", "size_in_bytes"));
	pcoCtx->insertAttr(new CIntegerAttr("sizeKB", "ROUND(size_in_bytes/1024)"));
	pcoCtx->insertAttr(new CIntegerAttr("sizeMB", "ROUND(size_in_bytes/1048576)"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// ArticleCommentForm statement
	pcoSt = new CStatement(CMS_ST_ARTICLECOMMENTFORM, ST_ARTICLECOMMENTFORM);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// EndArticleCommentForm statement
	pcoSt = new CStatement(CMS_ST_ENDARTICLECOMMENTFORM, ST_ENDARTICLECOMMENTFORM);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// ArticleComment statement
	pcoSt = new CStatement(CMS_ST_ARTICLECOMMENT, ST_ARTICLECOMMENT);

	pcoCtx = new CStatementContext(CMS_CT_DEFAULT);
	pcoCtx->insertAttr(new CAttribute("off"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_EDIT);
	pcoCtx->insertAttr(new CAttribute("ReaderEMail"));
	pcoCtx->insertAttr(new CAttribute("Subject"));
	pcoCtx->insertAttr(new CAttribute("Content"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CIntegerAttr("Identifier"));
	pcoCtx->insertAttr(new CStringAttr("ReaderEMail"));
	pcoCtx->insertAttr(new CStringAttr("ReaderEMailObfuscated"));
	pcoCtx->insertAttr(new CDateTimeAttr("SubmitDate"));
	pcoCtx->insertAttr(new CStringAttr("Subject"));
	pcoCtx->insertAttr(new CStringAttr("Content"));
	pcoCtx->insertAttr(new CStringAttr("ReaderEMailPreview"));
	pcoCtx->insertAttr(new CStringAttr("ReaderEMailPreviewObfuscated"));
	pcoCtx->insertAttr(new CStringAttr("SubjectPreview"));
	pcoCtx->insertAttr(new CStringAttr("ContentPreview"));
	pcoCtx->insertAttr(new CIntegerAttr("Count"));
	pcoCtx->insertAttr(new CIntegerAttr("Level"));
	pcoCtx->insertAttr(new CStringAttr("SubmitError"));
	pcoCtx->insertAttr(new CIntegerAttr("SubmitErrorNo"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_LIST);
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_IF);
	pcoCtx->insertAttr(new CAttribute("Defined"));
	pcoCtx->insertAttr(new CAttribute("Submitted"));
	pcoCtx->insertAttr(new CAttribute("SubmitError"));
	pcoCtx->insertAttr(new CAttribute("Preview"));
	pcoCtx->insertAttr(new CAttribute("PublicModerated"));
	pcoCtx->insertAttr(new CAttribute("SubscribersModerated"));
	pcoCtx->insertAttr(new CAttribute("Enabled"));
	pcoCtx->insertAttr(new CAttribute("Rejected"));
	pcoCtx->insertAttr(new CAttribute("PublicAllowed"));
	pcoCtx->insertAttr(new CAttribute("CAPTCHAEnabled"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	// CAPTCHA statement
	pcoSt = new CStatement(CMS_ST_CAPTCHA, ST_CAPTCHA);

	pcoCtx = new CStatementContext(CMS_CT_EDIT);
	pcoCtx->insertAttr(new CAttribute("Code"));
	pcoSt->insertCtx(pcoCtx);

	pcoCtx = new CStatementContext(CMS_CT_PRINT);
	pcoCtx->insertAttr(new CAttribute("ImageLink"));
	pcoSt->insertCtx(pcoCtx);

	this->insert(pcoSt);

	CLOSE_TRY
	CATCH(InvalidValue &rcoEx)
		cout << "InitStatements: " << rcoEx.what() << endl;
		return false;
	END_CATCH
	catch(...)
	{
		cout << "InitStatements: some unknown exception" << endl;
		return false;
	}
	return true;
}

// constructor
CLex::CLex(const char* i, ulint bl)
	: m_coAtom(""), m_coLexem(CMS_LEX_NONE, CMS_DT_NONE)
{
	m_pchTextStart = m_pchInBuf = i;
	m_nBufLen = bl;
	m_nIndex = 0;
	m_nLine = 1;
	m_nColumn = 0;
	m_nState = 1;
	m_chChar = 0;
	m_nTempIndex = 0;
	m_bLexemStarted = m_bIsEOF = m_bQuotedLexem = m_bEscapedCharacter = false;
	m_nHtmlCodeLevel = 0;
}

// reset: reset lex
void CLex::reset(const char* i, ulint bl) throw()
{
	m_coLexem = CLexem(CMS_LEX_NONE, CMS_DT_NONE);
	m_pchTextStart = m_pchInBuf = i;
	m_nBufLen = bl;
	m_nIndex = 0;
	m_nLine = 1;
	m_nColumn = 0;
	m_nState = 1;
	m_chChar = 0;
	m_nTempIndex = 0;
	m_bLexemStarted = m_bIsEOF = m_bQuotedLexem = false;
	m_nHtmlCodeLevel = 0;
}

// updateArticleTypes: update article types structure from database
bool CLex::updateArticleTypes()
{
	CStatementMap::iterator coIt = s_coStatements.find(ST_ARTICLE);
	if (coIt == s_coStatements.end())
	{
		return false;
	}
	CTypeAttributesMap* pcoArticleTypeAttributes = NULL;
	GetArticleTypeAttributes(&pcoArticleTypeAttributes);
	bool bModified = ((*coIt).second)->updateTypes(pcoArticleTypeAttributes);
	return bModified;
}

// assign operator
const CLex& CLex::operator =(const CLex& s)
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
	m_nTempIndex = 0;
	m_bLexemStarted = m_bIsEOF = m_bQuotedLexem = m_bEscapedCharacter = false;
	m_pchTextStart = 0;
	return *this;
}

// getLexem: return next lexem
const CLexem* CLex::getLexem()
{
	bool bValidText = m_nHtmlCodeLevel > 0 ? true : false;
	bool FoundLexem = false;
	bool bInsertSpace = false;
	m_coLexem.setAtom(NULL);
	m_coLexem.setDataType(CMS_DT_INTEGER);
	m_coLexem.setTextStart(0);
	m_coLexem.setTextLen(0);
	m_coLexem.setInsertSpace(false);
	m_coAtom.setId("");
	if (m_bIsEOF)
	{
		m_coLexem.setRes(CMS_ERR_EOF);
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
			m_coLexem.setTextStart(m_pchTextStart);
			m_coLexem.setTextLen(m_nIndex - (ulint)(m_pchTextStart - m_pchInBuf));
			m_coLexem.setInsertSpace(bInsertSpace);
			m_coLexem.setRes(CMS_LEX_NONE);
			return &m_coLexem;
		}
		if (m_nState != 4)
		{
			// if I didn't find valid text yet and the current character was not valid, set the
			// text start pointer to the next character
			// a valid text character is anything that is not between 0 and space
			// characters between 0 and space are special characters, tab and space
			if ((m_chChar > 0 && m_chChar <= ' ') && !bValidText && m_pchTextStart != NULL
					&& m_nState < 2)
			{
				m_pchTextStart = m_pchInBuf + m_nIndex;
				if (!bInsertSpace && (m_chChar == ' ' || m_chChar == '\t'))
				{
					bInsertSpace = true;
				}
			}
			m_nPrevLine = m_nLine;
			m_nPrevColumn = m_nColumn;
			if (m_chChar == '\n') // increment line and set column to 0 on new line character
			{
				m_nLine ++;
				m_nColumn = 0;
			}
			else
			{
				m_nColumn += m_chChar == '\t' ? 8 : 1;
				// increment column (by 8 if character is tab)
			}
		}
		switch (m_nState)
		{
		case 1: // start state; read html text
			if (m_chChar == '>')
			{
				m_nHtmlCodeLevel--;
			}
			if (m_chChar == s_pchCTokenStart[0] || m_nTempIndex == 1)
			{
				m_nTempIndex = 1;
				m_nState = 2;
			}
			else if (m_chChar < 0 || m_chChar > ' ')
			{
				bValidText = true;
			}
			break;
		case 2: // found some characters matching start token
			if (m_chChar != s_pchCTokenStart[m_nTempIndex])
			{
				m_nTempIndex = 0;
				m_nState = 1;
				bValidText = true;
				break;
			}
			m_nTempIndex++;
			if (m_nTempIndex == (int)strlen(s_pchCTokenStart)) // found start token
			{
				m_nTempIndex = 0;
				m_nState = 3;
				m_bLexemStarted = false;
				if (m_pchTextStart && bValidText)
				{
					m_coLexem.setTextLen(m_nIndex - (ulint)(m_pchTextStart - m_pchInBuf)
					                     - strlen(s_pchCTokenStart));
					m_coLexem.setTextStart(m_pchTextStart);
					m_coLexem.setInsertSpace(bInsertSpace);
				}
				if (!(m_pchTextStart && bValidText) && bInsertSpace)
				{
					m_coLexem.setTextStart(m_pchInBuf + m_nIndex);
					m_coLexem.setTextLen(0);
					m_coLexem.setInsertSpace(true);
				}
				m_coLexem.setRes(CMS_LEX_START_STATEMENT);
				return &m_coLexem;
			}
			break;
		case 3: // after a start token; read campsite instruction
			if (!m_bLexemStarted)		// didn't find an atom yet
			{
				if (m_chChar <= ' ' && m_chChar >= 0)	// separator
				{
					;
				}
				else if (m_chChar == s_chCTokenEnd)	// end token
				{
					m_nState = 1;
					m_pchTextStart = m_pchInBuf + m_nIndex;
					m_coLexem.setRes(CMS_LEX_END_STATEMENT);
					return &m_coLexem;
				}
				else if (m_chChar == s_pchCTokenStart[0])
				{						// invalid token inside campsite instruction
					m_nTempIndex = 1;
					m_nState = 1;
					m_pchTextStart = 0;
					m_coLexem.setRes(CMS_ERR_LESS_IN_TOKEN);
					return &m_coLexem;
				}
				else		// atom found
				{
					m_bLexemStarted = true;
					m_bQuotedLexem = m_chChar == '\"';
					if (!m_bQuotedLexem)
						AppendOnAtom();
				}
			}
			else if (m_bQuotedLexem)		// lexem (atom) is delimited by quotes
			{
				if (((m_chChar >= 0 && m_chChar < ' ') || m_chChar == s_chCTokenEnd)
								  && !m_bEscapedCharacter)
				{
					m_bLexemStarted = false;
					m_bQuotedLexem = false;
					if (m_chChar == s_chCTokenEnd)
						m_nState = 4;
					m_coLexem.setRes(CMS_ERR_END_QUOTE_MISSING);
					return &m_coLexem;
				}
				else if (m_chChar == '\"' && !m_bEscapedCharacter)
				{
					FoundLexem = true;
					m_bLexemStarted = false;
				}
				else if (m_chChar == '\\' && !m_bEscapedCharacter)
				{
					m_bEscapedCharacter = true;
				}
				else
				{
					m_bEscapedCharacter = false;
					if ((m_chChar < 0 || m_chChar >= ' ') && !AppendOnAtom())
					{
						m_bLexemStarted = false;
						return IdentifyAtom();
					}
				}
			}
			else				// lexem is not delimited by quotes
			{
				if (((m_chChar >= 0 && m_chChar <= ' ') || m_chChar == s_chCTokenEnd)
								  && !m_bEscapedCharacter)
				{	// separator or end token
					FoundLexem = true;
					m_bLexemStarted = false;
					if (m_chChar == s_chCTokenEnd)
					{
						m_nState = 4;
						return IdentifyAtom();
					}
				}
				else if (m_chChar == '\"' && !m_bEscapedCharacter)
				{	// found another lexem delimited by quotes
					FoundLexem = true;
					m_bLexemStarted = true;
					m_bQuotedLexem = true;
					return IdentifyAtom();
				}
				else if (m_chChar == '\\' && !m_bEscapedCharacter)
				{
					m_bEscapedCharacter = true;
				}
				else
				{	// append character to atom identifier
					m_bEscapedCharacter = false;
					if ((m_chChar < 0 || m_chChar >= ' ') && !AppendOnAtom())
					{
						m_bLexemStarted = false;
						return IdentifyAtom();
					}
				}
			}
			break;
		case 4: // found end token; set the text start pointer
			m_nState = 1;
			m_pchTextStart = m_pchInBuf + m_nIndex;
			m_coLexem.setRes(CMS_LEX_END_STATEMENT);
			return &m_coLexem;
			break;
		}
	}
	return IdentifyAtom();
}

// return pointer to statement identified by name
const CStatement* CLex::findSt(const string& p_rcoId)
{
	CStatementMap::const_iterator coIt = s_coStatements.find(p_rcoId);
	if (coIt == s_coStatements.end())
		return NULL;
	return (*coIt).second;
}

// printStatements: print lex statements (for test purposes)
void CLex::printStatements()
{
	CStatementMap::iterator coIt = s_coStatements.begin();
	for (; coIt != s_coStatements.end(); ++coIt)
	{
		(*coIt).second->print();
	}
}
