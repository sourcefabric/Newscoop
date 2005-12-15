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

Implement CParameter, CAction, CActLanguage, CActInclude, CActPublication,
CActIssue, CActSection, CActArticle, CActList, CActURLParameters,
CActFormParameters, CActPrint, CActIf, CActDate, CActText, CActLocal,
CActSubscription, CActEdit, CActSelect, CActUser, CActLogin,
CActSearch, CActWith methods.

******************************************************************************/

#include <unistd.h>
#include <stdio.h>
#include <fstream>
#include <typeinfo>
#include <sstream>
#include <set>
#include <iomanip>

#include "util.h"
#include "actions.h"
#include "parser.h"
#include "cparser.h"
#include "data_types.h"
#include "attributes_impl.h"
#include "cpublication.h"
#include "auto_ptr.h"

using std::set;
using std::cout;
using std::endl;
using std::stringstream;

//*** start macro definition

#define ResetList(a) (reset_from_list <= a && reset_from_list > CLV_ROOT)

#define CheckFor(attr, val, tbuf, q)\
{\
if (val >= 0) {\
if ((q).length() > 0)\
q += " and ";\
tbuf.str("");\
tbuf << attr << " = " << val;\
q += tbuf.str();\
}\
}

#define SetNrField(a, v, tbuf, q)\
{\
if (q != "")\
q += " and ";\
tbuf.str("");\
tbuf << a << " = " << v;\
q += tbuf.str();\
}

#define AppendConstraint(q, attr, op, val, logic_op)\
{\
if ((q).length() > 0)\
q += string(" ") + logic_op + " ";\
q += string(attr) + " " + op + " '" + val + "'";\
}

#define URLPrintParam(pn, p, os, f)\
{\
if (p >= 0) {\
if (!f) os << "&";\
else f = false;\
os << pn << '=' << p;\
}\
}

#define URLPrintNParam(pn, p, os, f)\
{\
if (p > 0) {\
if (!f) os << "&";\
else f = false;\
os << pn << '=' << p;\
}\
}

#define FormPrintParam(pn, p, os)\
{\
if (p >= 0)\
os << "<input type=hidden name=\"" << pn << "\" value=\"" << p << "\">";\
}

#define CheckForType(t, sql)\
{\
if (IsValidType(t, sql) != 0)\
return ERR_NOTYPE;\
}

//*** end macro definition


//*** start class methods definition

// CParameter assign operator
const CParameter& CParameter::operator =(const CParameter& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return *this;
	m_coAttr = p_rcoSrc.m_coAttr;
	m_coType = p_rcoSrc.m_coType;
	m_coSpec = p_rcoSrc.m_coSpec;
	delete m_pcoOperation;
	m_pcoOperation = NULL;
	if (p_rcoSrc.m_pcoOperation != NULL)
		m_pcoOperation = p_rcoSrc.m_pcoOperation->clone();
	return *this;
}

// assign operator
const CParameterList& CParameterList::operator =(const CParameterList& o)
{
	clear();
	for (CParameterList::const_iterator coIt = o.begin(); coIt != o.end(); ++coIt)
		push_back((*coIt)->clone());
	return *this;
}

void CParameterList::clear()
{
	for (CParameterList::iterator coIt = begin(); coIt != end(); coIt = begin())
	{
		delete *coIt;
		*coIt = NULL;
		erase(coIt);
	}
}

// CAction: initialise static members
TK_MYSQL CAction::m_coSql(NULL);
TK_bool CAction::m_coDebug(NULL);

// DEBUGAct: print debug information
inline void CAction::DEBUGAct(const char* method, const char* expl, sockstream& fs)
{
	if (*m_coDebug == true)
	{
		fs << "<!-- " << actionType() << "." << method << ": " << expl << " -->\n";
	}
}

// SQLEscapeString: escape given string for sql query; returns escaped string
// The returned string must be deallocated by the user using delete operator.
// Parameters:
//		const char* src - source string
//		ulint p_nLength - string length
char* CAction::SQLEscapeString(const char* src, ulint p_nLength)
{
	char* pchDst = new char[2 * p_nLength + 1];
	if (pchDst == NULL)
		return NULL;
	pchDst[mysql_real_escape_string(&m_coSql, pchDst, src, p_nLength) + 1] = 0;
	return pchDst;
}

// InitTempMembers: init thread specific variables
void CAction::initTempMembers()
{
	TK_TRY
	if (&m_coDebug == NULL)
		m_coDebug = new bool;
	TK_CATCH
}

// runActions: run actions in a list of actions
// Parameters:
//		CActionList& al - list of actions
//		CContext& c - current context
//		sockstream& fs - output stream
int CAction::runActions(CActionList& al, CContext& c, sockstream& fs)
{
	CActionList::const_iterator al_i;
	for (al_i = al.begin(); al_i != al.end(); ++al_i)
	{
		int err;
		if (debug())
			fs << "<!-- taking action " << (*al_i)->actionType() << " -->\n";
		try
		{
			err = (*al_i)->takeAction(c, fs);
		}
		catch (InvalidOperation& rcoEx)
		{
			cout << "runActions: InvalidOperation in " << (*al_i)->actionType() << endl;
		}
		catch (InvalidValue& rcoEx)
		{
			const CPublication* pcoPub = 
					CPublicationsRegister::getInstance().getPublication(c.Publication());
			const string& coAlias = *(pcoPub->getAliases().begin());
			fs << "<font color=red><h3>ERROR: " << rcoEx.what() << " in publication "
					<< coAlias << ", language id: " << c.Language() << "</h3></font>";
		}
		catch (ExMutex& rcoEx)
		{
			cout << "runActions: mutex exception in " << (*al_i)->actionType() << endl;
		}
		catch (exception& rcoEx)
		{
			cout << "runActions: " << rcoEx.what() << " in " << (*al_i)->actionType() << endl;
		}
		if (debug())
			fs << "<!-- action " << (*al_i)->actionType() << " result: " << err << " -->\n";
	}
	return RES_OK;
}

// dateFormat: format the given date according to the given format in given language
// Returns string containing formated date
// Parameters:
//		const char* p_pchDate - date to format
//		const char* p_pchFormat - format of the date
//		id_type p_nLanguageId - language to use
string CAction::dateFormat(const char* p_pchDate, const char* p_pchFormat, id_type p_nLanguageId)
{
	const char* pchDate = p_pchDate != NULL ? p_pchDate : "";
	if (p_pchFormat == NULL || *p_pchFormat == 0)
		return string(pchDate);
	stringstream coQuery;
	coQuery << "select MONTH('" << pchDate << "'), WEEKDAY('"<< pchDate << "')";
	if (mysql_query(&m_coSql, coQuery.str().c_str()) != 0)
		return string(pchDate);
	CMYSQL_RES res = mysql_store_result(&m_coSql);
	if (*res == NULL || mysql_num_fields(*res) < 2)
		return string(pchDate);
	MYSQL_ROW row = mysql_fetch_row(*res);
	if (row == NULL || row[0] == NULL || row[1] == NULL)
		return string(pchDate);
	int nMonth = atol(row[0]);
	int nWDay = (atol(row[1]) + 2) % 7;
	nWDay = nWDay == 0 ? 7 : nWDay;

	coQuery.str("");
	coQuery << "select ";
	int nStartFormat = 0;
	int nIndex = 0;
	bool bNeedFormat = false;
	int nParams = 0;
	for (; p_pchFormat[nIndex] != 0; nIndex++)
	{
		if (p_pchFormat[nIndex] != '%')
			continue;
		bNeedFormat = true;
		nIndex++;
		if (p_pchFormat[nIndex] == 0)
			break;
		if (p_pchFormat[nIndex] == 'w')
		{
			coQuery << (nParams > 0 ? ", " : "") << nWDay;
			nStartFormat = nIndex + 1;
			nParams++;
			continue;
		}
		if (p_pchFormat[nIndex] != 'M' && p_pchFormat[nIndex] != 'W')
			continue;
		int nFormatLen = nIndex - nStartFormat - 1;
		if (nFormatLen > 0)
		{
			if (nParams > 0)
				coQuery << ", ";
			coQuery << "DATE_FORMAT('" << pchDate << "', '";
			coQuery.write(p_pchFormat + nStartFormat, nFormatLen);
			coQuery << "')";
			nParams++;
		}
		nStartFormat = nIndex + 1;
		if (nParams > 0)
			coQuery << ", ";
		if (p_pchFormat[nIndex] == 'M')
			coQuery << "Month" << nMonth;
		else
			coQuery << "WDay" << nWDay;
		nParams++;
	}
	if (!bNeedFormat)
		return string("");
	if (nIndex > nStartFormat)
	{
		if (nParams > 0)
			coQuery << ", ";
		coQuery << "DATE_FORMAT('" << pchDate << "', '";
		coQuery.write(p_pchFormat + nStartFormat, nIndex - nStartFormat);
		coQuery << "')";
		nParams++;
	}
	coQuery << " from Languages where Id = " << p_nLanguageId
	        << " or Id = 1 order by Id desc limit 0,1";
	if (mysql_query(&m_coSql, coQuery.str().c_str()) != 0)
		return string("");
	if (*(res = mysql_store_result(&m_coSql)) == NULL)
		return string("");
	if ((row = mysql_fetch_row(*res)) == NULL)
		return string("");
	string coResult = "";
	for (nIndex = 0; nIndex < nParams; nIndex++)
		coResult = coResult + string(row[nIndex]);
	return coResult;
}

// assign operator
const CActionList& CActionList::operator =(const CActionList& o)
{
	clear();
	for (CActionList::const_iterator coIt = o.begin(); coIt != o.end(); ++coIt)
		push_back((*coIt)->clone());
	return *this;
}

void CActionList::clear()
{
	for (CActionList::iterator coIt = begin(); coIt != end(); coIt = begin())
	{
		delete *coIt;
		*coIt = NULL;
		erase(coIt);
	}
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context (modified by action)
//		sockstream& fs - output stream (not used)
int CActLanguage::takeAction(CContext& c, sockstream& fs)
{
	char* pchLang = SQLEscapeString(m_coLang.c_str(), m_coLang.length());
	if (pchLang == NULL)
		return ERR_NOMEM;
	string coQuery = string("select Id from Languages where Name = '") + pchLang + "'";
	delete []pchLang;
	DEBUGAct("takeAction()", coQuery.c_str(), fs);
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[0] == NULL)
		return -1;
	c.SetLanguage(strtol(row[0], 0, 10));
	return RES_OK;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context (may be modified by action)
//		sockstream& fs - output stream
int CActInclude::takeAction(CContext& c, sockstream& fs)
{
	try
	{
		CParser* pcoParser = CParser::map().find(tpl_path);
		if (pcoParser == NULL)
			pcoParser = new CParser(tpl_path, document_root);
		pcoParser->setDebug(*m_coDebug);
		pcoParser->parse();
		pcoParser->setDebug(*m_coDebug);
		return pcoParser->writeOutput(c, fs);
	}
	catch (ExStat& rcoEx)
	{
		fs << endl << "<!-- INCLUDE FILE WARNING!!! -->" << endl;
		fs << "<!-- Included file (" << tpl_path << ") does not exist. -->" << endl;
		fs << "<!----------------------------->" << endl;
		return ERR_NOHASHENT;
	}
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context (modified by action)
//		sockstream& fs - output stream (not used)
int CActPublication::takeAction(CContext& c, sockstream& fs)
{
	if (case_comp(param.attribute(), "off") == 0)
	{
		c.SetPublication( -1);
		return RES_OK;
	}
	if (case_comp(param.attribute(), "default") == 0)
	{
		c.SetPublication(c.DefPublication());
		return RES_OK;
	}
	string coQuery = "select Id from Publications where ";
	string w = "";
	char* pchVal = SQLEscapeString(param.value().c_str(), param.value().length());
	if (pchVal == NULL)
		return ERR_NOMEM;
	AppendConstraint(w, param.attribute(), param.opSymbol(), pchVal, "and");
	delete []pchVal;
	coQuery += w;
	DEBUGAct("takeAction()", coQuery.c_str(), fs);
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[0] == NULL)
		return -1;
	c.SetPublication(strtol(row[0], 0, 10));
	return RES_OK;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context (modified by action)
//		sockstream& fs - output stream (not used)
int CActIssue::takeAction(CContext& c, sockstream& fs)
{
	TK_TRY
	if (case_comp(param.attribute(), "off") == 0)
	{
		c.SetIssue( -1);
		return RES_OK;
	}
	if (case_comp(param.attribute(), "default") == 0)
	{
		c.SetIssue(c.DefIssue());
		return RES_OK;
	}
	string w, coQuery;
	stringstream buf;
	if (case_comp(param.attribute(), "current") == 0)
	{
		coQuery = "select max(Number) from Issues ";
	}
	else if (case_comp(param.attribute(), "number") == 0)
	{
		coQuery = "select Number from Issues ";
		char* pchVal = SQLEscapeString(param.value().c_str(), param.value().length());
		if (pchVal == NULL)
			return ERR_NOMEM;
		AppendConstraint(w, param.attribute(), param.opSymbol(), pchVal, "and");
		delete []pchVal;
	}
	else
		return -1;
	SetNrField("IdLanguage", c.Language(), buf, w);
	SetNrField("IdPublication", c.Publication(), buf, w);
	if (c.Access() == A_PUBLISHED)
		AppendConstraint(w, "Published", "=", "Y", "and");
	if (w != "")
		coQuery += "where ";
	coQuery += w;
	DEBUGAct("takeAction()", coQuery.c_str(), fs);
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[0] == NULL)
		return -1;
	c.SetIssue(strtol(row[0], 0, 10));
	return RES_OK;
	TK_CATCH_ERR
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context (modified by action)
//		sockstream& fs - output stream (not used)
int CActSection::takeAction(CContext& c, sockstream& fs)
{
	if (case_comp(param.attribute(), "off") == 0)
	{
		c.SetSection( -1);
		return RES_OK;
	}
	if (case_comp(param.attribute(), "default") == 0)
	{
		c.SetSection(c.DefSection());
		return RES_OK;
	}
	string coQuery = "select Number from Sections where ";
	string w = "";
	char* pchVal = SQLEscapeString(param.value().c_str(), param.value().length());
	if (pchVal == NULL)
		return ERR_NOMEM;
	AppendConstraint(w, param.attribute(), param.opSymbol(), pchVal, "and");
	delete []pchVal;
	stringstream buf;
	SetNrField("IdLanguage", c.Language(), buf, w);
	SetNrField("IdPublication", c.Publication(), buf, w);
	CheckFor("NrIssue", c.Issue(), buf, w);
	coQuery += w;
	DEBUGAct("takeAction()", coQuery.c_str(), fs);
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[0] == NULL)
		return -1;
	c.SetSection(strtol(row[0], 0, 10));
	return RES_OK;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context (modified by action)
//		sockstream& fs - output stream (not used)
int CActArticle::takeAction(CContext& c, sockstream& fs)
{
	if (case_comp(param.attribute(), "off") == 0)
	{
		c.SetArticle( -1);
		return RES_OK;
	}
	if (case_comp(param.attribute(), "default") == 0)
	{
		c.SetArticle(c.DefArticle());
		return RES_OK;
	}
	string coQuery = "select Number from Articles where ";
	string w = "";
	char* pchVal = SQLEscapeString(param.value().c_str(), param.value().length());
	if (pchVal == NULL)
		return ERR_NOMEM;
	AppendConstraint(w, param.attribute(), param.opSymbol(), pchVal, "and");
	delete []pchVal;
	stringstream buf;
	SetNrField("IdLanguage", c.Language(), buf, w);
	SetNrField("IdPublication", c.Publication(), buf, w);
	CheckFor("NrIssue", c.Issue(), buf, w);
	CheckFor("NrSection", c.Section(), buf, w);
	coQuery += w;
	DEBUGAct("takeAction()", coQuery.c_str(), fs);
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[0] == NULL)
		return -1;
	c.SetArticle(strtol(row[0], 0, 10));
	return RES_OK;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context (modified by action)
//		sockstream& fs - output stream (not used)
int CActTopic::takeAction(CContext& c, sockstream& fs)
{
	if (case_comp(param.attribute(), "off") == 0)
	{
		c.SetTopic( -1);
		return RES_OK;
	}
	if (case_comp(param.attribute(), "default") == 0)
	{
		c.SetTopic(c.DefTopic());
		return RES_OK;
	}
	const Topic* pcoTopic = Topic::topic(param.value());
	if (pcoTopic == NULL)
		return ERR_NODATA;
	c.SetTopic(pcoTopic->id());
	return RES_OK;
}

CListModifiers::CListModifiers()
{
	insert(CMS_ST_ISSUE);
	insert(CMS_ST_SECTION);
	insert(CMS_ST_ARTICLE);
	insert(CMS_ST_SEARCHRESULT);
	insert(CMS_ST_SUBTITLE);
	insert(CMS_ST_ARTICLETOPIC);
	insert(CMS_ST_SUBTOPIC);
	insert(CMS_ST_ARTICLEATTACHMENT);
}

CListModifiers CActList::s_coModifiers;

// WriteModParam: add conditions - corresponding to modifier parameters -
// to where clause of the query. Used for Issue and Section modifiers.
// Parameters:
//		string& s - string to add conditions to (where clause)
//		CContext& c - current context
//		string& table - string containig tables used in query
int CActList::WriteModParam(string& s, CContext& c, string& table)
{
	if (modifier == CMS_ST_SECTION)
		table = "Sections";
	else
		table = "Issues";
	string w = "";
	if (modifier == CMS_ST_ISSUE && c.Access() != A_ALL)
		w = "Published = 'Y'";
	CParameterList::iterator pl_i;
	for (pl_i = mod_param.begin(); pl_i != mod_param.end(); ++pl_i)
	{
		char* pchVal = SQLEscapeString((*pl_i)->value().c_str(), (*pl_i)->value().length());
		if (pchVal == NULL)
			return ERR_NOMEM;
		AppendConstraint(w, (*pl_i)->attribute(), (*pl_i)->opSymbol(), pchVal, "and");
		delete []pchVal;
	}
	stringstream buf;
	CheckFor("IdPublication", c.Publication(), buf, w);
	if (modifier == CMS_ST_SECTION)
		CheckFor("NrIssue", c.Issue(), buf, w);
	buf.str("");
	buf << "IdLanguage = " << c.Language();// << " or IdLanguage = 1)";
	if (w != "")
		w += " and ";
	w += buf.str();
	if (w.length())
		s += string(" where ") + w;
	return RES_OK;
}

// WriteArtParam: add conditions - corresponding to modifier parameters -
// to where clause of the query. Used for Article modifier.
// Parameters:
//		string& s - string to add conditions to (where clause)
//		CContext& c - current context
//		string& table - string containig tables used in query
int CActList::WriteArtParam(string& s, CContext& c, string& table)
{
	CParameterList::iterator pl_i;
	for (pl_i = mod_param.begin(); pl_i != mod_param.end(); ++pl_i)
		if (case_comp((*pl_i)->attribute(), "Type") == 0)
		{
			CheckForType((*pl_i)->value().c_str(), &m_coSql);
			break;
		}
	string val, w, join_w, types_w, typef_w, topic_equal_op, topic_not_equal_op;
	StringSet typesTables;
	if (c.Access() != A_ALL)
		w = "Published = 'Y'";
	table = "Articles";
	bool bTopic = false;
	set<string> coNotTopics;
	stringstream buf;
	for (pl_i = mod_param.begin(); pl_i != mod_param.end(); ++pl_i)
	{
		if (case_comp((*pl_i)->attribute(), "keyword") == 0)
		{
			val = string("%") + (*pl_i)->value() + "%";
			char* pchVal = SQLEscapeString(val.c_str(), val.length());
			if (pchVal == NULL)
				return ERR_NOMEM;
			AppendConstraint(w, "Keywords", "like", pchVal, "and");
			delete []pchVal;
		}
		else if (case_comp((*pl_i)->attribute(), "OnSection") == 0
		         || case_comp((*pl_i)->attribute(), "OnFrontPage") == 0
		         || case_comp((*pl_i)->attribute(), "public") == 0)
		{
			const char* pchVal = case_comp((*pl_i)->value(), "on") == 0 ? "Y" : "N";
			AppendConstraint(w, (*pl_i)->attribute(), (*pl_i)->opSymbol(), pchVal, "and");
		}
		else if (case_comp((*pl_i)->attribute(), "topic") == 0)
		{
			bTopic = true;
			buf.str("");
			buf << ((const CTopicCompOperation*)(*pl_i)->operation())->secondId();
			if ((*pl_i)->operation()->symbol() == g_coEQUAL_Symbol)
			{
				AppendConstraint(topic_equal_op, "ArticleTopics.TopicId", 
				                 (*pl_i)->operation()->symbol(), buf.str(), "or");
			}
			else
			{
				coNotTopics.insert(buf.str());
			}
		}
		else if ((*pl_i)->attrType() != "")
		{
			string tTable = "X" + (*pl_i)->attrType();
			if (typesTables.find(tTable) == typesTables.end())
			{
				typesTables.insert(tTable);
				if (join_w != "")
					join_w += " or ";
				join_w += "Articles.Number = " + tTable + ".NrArticle";
			}
			string w_field = tTable + "." + (*pl_i)->attribute();
			AppendConstraint(typef_w, w_field, (*pl_i)->opSymbol(), (*pl_i)->value(), "and");
		}
		else if (case_comp((*pl_i)->attribute(), "type") == 0)
		{
			char* pchVal = SQLEscapeString((*pl_i)->value().c_str(), (*pl_i)->value().length());
			if (pchVal == NULL)
				return ERR_NOMEM;
			if ((*pl_i)->opSymbol() == g_coNOT_EQUAL_Symbol)
				AppendConstraint(w, (*pl_i)->attribute(), (*pl_i)->opSymbol(), pchVal, "and")
			else
				AppendConstraint(types_w, (*pl_i)->attribute(), (*pl_i)->opSymbol(), pchVal, "or");
			delete []pchVal;
		}
		else
		{
			char* pchVal = SQLEscapeString((*pl_i)->value().c_str(), (*pl_i)->value().length());
			if (pchVal == NULL)
				return ERR_NOMEM;
			AppendConstraint(w, (*pl_i)->attribute(), (*pl_i)->opSymbol(), pchVal, "and");
			delete []pchVal;
		}
	}
	if (c.Topic() > 0)
	{
		bTopic = true;
		buf.str("");
		buf << c.Topic();
		AppendConstraint(topic_equal_op, "ArticleTopics.TopicId", g_coEQUAL_Symbol,
		                 buf.str(), "or");
	}
	CheckFor("IdPublication", c.Publication(), buf, w);
	CheckFor("NrIssue", c.Issue(), buf, w);
	CheckFor("NrSection", c.Section(), buf, w);
	buf.str("");
	buf << "Articles.IdLanguage = " << c.Language();// << " or Articles.IdLanguage = 1)";
	if (w != "")
		w += " and ";
	w += buf.str();
	if (c.Access() == A_PUBLISHED)
		AppendConstraint(w, "Published", "=", "Y", "and");
	if (bTopic)
		table += " LEFT JOIN ArticleTopics ON Articles.Number = ArticleTopics.NrArticle";
	StringSet::const_iterator coTypesIt = typesTables.begin();
	for (; coTypesIt != typesTables.end(); ++coTypesIt)
		table += ", " + *coTypesIt;
	if (join_w != "")
		w += " and (" + join_w + ")";
	if (types_w != "")
		w += " and (" + types_w + ")";
	if (typef_w != "")
		w += " and (" + typef_w + ")";
	if (topic_equal_op != "")
		w += " and (" + topic_equal_op + ")";
	if (!coNotTopics.empty())
	{
		string coQuery = "select NrArticle from ArticleTopics where ";
		StringSet::const_iterator coIt = coNotTopics.begin();
		for (; coIt != coNotTopics.end(); ++coIt)
			coQuery += string(coIt != coNotTopics.begin() ? " or " : "") + "TopicId = " + *coIt;
		SQLQuery(&m_coSql, coQuery.c_str());
		StoreResult(&m_coSql, qRes);
		if (mysql_num_rows(*qRes) > 0)
		{
			MYSQL_ROW row;
			bool first = true;
			w += " and Articles.Number not in (";
			while ((row = mysql_fetch_row(*qRes)) != NULL)
			{
				w += string(first ? "" : ", ") + (row[0] != NULL ? row[0] : "");
				first = false;
			}
			w += ")";
		}
	}
	if (w.length())
		s = string(" where ") + w;
	return RES_OK;
}

// WriteSrcParam: add conditions - corresponding to modifier parameters -
// to where clause of the query. Used for SearchResult modifier.
// Parameters:
//		string& s - string to add conditions to (where clause)
//		CContext& c - current context
//		string& table - string containig tables used in query
int CActList::WriteSrcParam(string& s, CContext& c, string& table)
{
	table = "Articles, ArticleIndex, KeywordIndex";
	string w = "";
	c.ResetKwdIt();
	const char* k;
	bool First = true;
	while ((k = c.NextKwd()) != NULL)
	{
		char* pchVal = SQLEscapeString(k, strlen(k));
		if (pchVal == NULL)
			return ERR_NOMEM;
		if (pchVal[0] == 0)
			return -1;
		if (First)
		{
			w = string("(Keyword = '") + pchVal + "'";
			First = false;
		}
		else
			w += string(" or Keyword = '") + pchVal + "'";
		delete []pchVal;
	}
	if (w == "")
		return -1;
	w += ")";
	stringstream buf;
	CheckFor("Articles.IdPublication", c.Publication(), buf, w);
	if (c.SearchLevel() >= 1)
		CheckFor("Articles.NrIssue", c.Issue(), buf, w);
	if (c.SearchLevel() >= 2)
		CheckFor("Articles.NrSection", c.Section(), buf, w);
	if (w != "")
		w += " and ";
	w += "ArticleIndex.IdKeyword = KeywordIndex.Id"
	     " and Articles.Number = ArticleIndex.NrArticle"
	     " and Articles.IdPublication = ArticleIndex.IdPublication"
	     " and Articles.IdLanguage = ArticleIndex.IdLanguage"
	     " and Articles.NrIssue = ArticleIndex.NrIssue"
	     " and Articles.NrSection = ArticleIndex.NrSection";
	s = string(" where ") + w;
	return RES_OK;
}

// WriteOrdParam: add conditions - corresponding to order parameters -
// to order clause of the query.
// Parameters:
//		string& s - string to add conditions to (order clause)
int CActList::WriteOrdParam(string& s)
{
	CParameterList::iterator pl_i;
	if (modifier != CMS_ST_SEARCHRESULT && modifier != CMS_ST_ARTICLETOPIC
		   && modifier != CMS_ST_SUBTOPIC && modifier != CMS_ST_ARTICLEATTACHMENT)
	{
		string table;
		if (modifier == CMS_ST_ARTICLE)
			table = "Articles.";
		s = string(" order by ") + table + "IdLanguage desc";
		if (modifier == CMS_ST_SECTION)
			s += string(", Number asc");
		for (pl_i = ord_param.begin(); pl_i != ord_param.end(); ++pl_i)
		{
			string coAttribute = (*pl_i)->attribute();
			if (case_comp(coAttribute, "bydate") == 0)
				coAttribute = modifier == CMS_ST_ISSUE ? "PublicationDate" : "UploadDate";
			s += string(", ") + table + coAttribute + " ";
			if ((*pl_i)->spec().length())
				s += (*pl_i)->spec();
		}
		if (modifier == CMS_ST_ARTICLE)
			s += string(", Articles.ArticleOrder asc");
	}
	if (modifier == CMS_ST_SUBTOPIC)
	{
		s = " order by Id asc";
	}
	if (modifier == CMS_ST_SEARCHRESULT)
	{
		s = " order by Articles.IdPublication asc, ArticleIndex.IdLanguage desc";
		for (pl_i = ord_param.begin(); pl_i != ord_param.end(); ++pl_i)
		{
			s += string(", ");
			if ((*pl_i)->attribute() == "Number")
				s += string("NrArticle") + " ";
			else
				s += (*pl_i)->attribute() + " ";
			if ((*pl_i)->spec().length())
				s += (*pl_i)->spec();
			if ((*pl_i)->attribute() != "Number")
				s += ", NrArticle asc";
		}
	}
	if (modifier == CMS_ST_ARTICLEATTACHMENT)
	{
		s = " order by att.file_name asc, att.extension asc";
	}
	return RES_OK;
}

// WriteLimit: add conditions to limit clause of the query.
// Parameters:
//		string& s - string to add conditions to (limit clause)
//		CContext& c - current context
int CActList::WriteLimit(string& s, CContext& c)
{
	if (length > 0)
	{
		s += " limit ";
		stringstream buf;
		buf << (c.ListStart(c.Level()) >= 0 ? c.ListStart(c.Level()) : 0) << ", " << (length + 1);
		s += buf.str();
	}
	return RES_OK;
}

// SetContext: set the context current Issue, Section or Article depending of list
// modifier
// Parameters:
//		CContext& c - current context
// 		id_type value - value to be set
void CActList::SetContext(CContext& c, id_type value)
{
	if (modifier == CMS_ST_ISSUE)
		c.SetIssue(value);
	if (modifier == CMS_ST_SECTION)
		c.SetSection(value);
	if (modifier == CMS_ST_ARTICLE || modifier == CMS_ST_SEARCHRESULT)
		c.SetArticle(value);
	if (modifier == CMS_ST_ARTICLEATTACHMENT)
		c.SetAttachment(value);
}

// IMod2Level: convert from list modifier to level identifier; return level identifier
// Parameters:
//		TListModifier m - list modifier
CListLevel CActList::IMod2Level(int m)
{
	switch (m)
	{
		case CMS_ST_ISSUE:
			return CLV_ISSUE_LIST;
		case CMS_ST_SECTION:
			return CLV_SECTION_LIST;
		case CMS_ST_ARTICLE:
			return CLV_ARTICLE_LIST;
		case CMS_ST_SEARCHRESULT:
			return CLV_SEARCHRESULT_LIST;
		case CMS_ST_SUBTITLE:
			return CLV_SUBTITLE_LIST;
		default:
			return CLV_ROOT;
	}
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context (not modified by action)
//		sockstream& fs - output stream
int CActList::takeAction(CContext& c, sockstream& fs)
{
	TK_TRY
	if (modifier == CMS_ST_SEARCHRESULT && !c.Search())
		return RES_OK;
	CContext lc = c;
	lc.SetLMode(LM_NORMAL);
	lc.SetLevel(IMod2Level(modifier));
	lint listlength;
	if (lc.ListStart(lc.Level()) < 0)
		lc.SetListStart(0, lc.Level());
	CMYSQL_RES res(NULL);
	id_type nTopic;
	if (modifier != CMS_ST_SUBTITLE)
	{
		string where, order, limit, fields, prefix, table, having;
		stringstream buf;
		switch (modifier) {
			case CMS_ST_ISSUE:
			case CMS_ST_SECTION:
				WriteModParam(where, lc, table);
				break;
			case CMS_ST_ARTICLE:
				WriteArtParam(where, lc, table);
				prefix = "Articles.";
				break;
			case CMS_ST_SEARCHRESULT:
				WriteSrcParam(where, lc, table);
				if (where == "")
				{
					runActions(second_block, c, fs);
					return RES_OK;
				}
				if (lc.SearchAnd())
				{
					buf << " having count(NrArticle) = " << lc.KeywordsNr();
					having = buf.str();
				}
				break;
			case CMS_ST_ARTICLETOPIC:
				buf << " where NrArticle = " << lc.Article();
				where = buf.str();
				table = "ArticleTopics";
				break;
			case CMS_ST_SUBTOPIC:
				nTopic = lc.Topic() >= 0 ? lc.Topic() : 0;
				buf << " where ParentId = " << nTopic;
				where = buf.str();
				table = "Topics";
				break;
			case CMS_ST_ARTICLEATTACHMENT:
				table = "ArticleAttachments as art_att, Attachments as att";
				buf << " where art_att.fk_article_number = " << lc.Article()
						<< " and art_att.fk_attachment_id = att.id";
				if (mod_param.begin() != mod_param.end())
				{
					string coParameter = (*(mod_param.begin()))->attribute();
					if (case_comp(coParameter, "ForCurrentLanguage") == 0)
						buf << " and att.fk_language_id = " << lc.Language();
				}
				where = buf.str();
				break;
		}
		WriteOrdParam(order);
		WriteLimit(limit, lc);
		
		switch (modifier) {
			case CMS_ST_ISSUE:
			case CMS_ST_SECTION:
				fields = "select Number, MAX(IdLanguage), IdPublication";
				if (modifier == CMS_ST_SECTION)
					fields += ", NrIssue";
				break;
			case CMS_ST_ARTICLE:
				fields = "select Number, MAX(Articles.IdLanguage), IdPublication"
						", Articles.NrIssue, Articles.NrSection";
				break;
			case CMS_ST_SEARCHRESULT:
				fields = "select NrArticle, MAX(Articles.IdLanguage), Articles.IdPublication"
						", Articles.NrIssue, Articles.NrSection";
				break;
			case CMS_ST_ARTICLETOPIC:
				fields = "select TopicId";
				break;
			case CMS_ST_SUBTOPIC:
				fields = "select distinct Id";
				break;
			case CMS_ST_ARTICLEATTACHMENT:
				fields = "select att.id, att.extension";
				break;
		}
		
		string group;
		if (modifier == CMS_ST_ISSUE || modifier == CMS_ST_SECTION || modifier == CMS_ST_ARTICLE)
		{
			group = " group by Number";
		}
		if (modifier == CMS_ST_SEARCHRESULT)
		{
			group = " group by NrArticle";
		}
		string coQuery = fields + " from " + table + where + group + having + order + limit;
		DEBUGAct("takeAction()", coQuery.c_str(), fs);
		SQLQuery(&m_coSql, coQuery.c_str());
		res = mysql_store_result(&m_coSql);
		if (*res == NULL)
			return ERR_NOMEM;
		listlength = mysql_num_rows(*res);
	}
	else
	{
		listlength = lc.SubtitlesNumber() - lc.StListStart();
		if (listlength > length && length > 0)
			listlength = length + 1;
	}
	if (listlength <= 0)
	{
		return runActions(second_block, c, fs);
	}
	MYSQL_ROW row(NULL);
	lc.SetListIndex(1);
	lc.SetListRow(1);
	lc.SetListColumn(1);
	if (lc.ListStart(lc.Level()) > 0 && length > 0)
	{
		lc.SetPrevStart(length < lc.ListStart(lc.Level()) ?
		                lc.ListStart(lc.Level()) - length : 0, lc.Level());
	}
	lc.SetListLength(listlength - (length > 0 && listlength > length ? 1 : 0));
	if (listlength > (lint)length && length > 0)
		lc.SetNextStart(lc.ListStart(lc.Level()) + length, lc.Level());
	for (int i = 0; (length > 0 && i < length) || length == 0; i++)
	{
		string st = "";
		if (modifier != CMS_ST_SUBTITLE)
		{
			if ((row = mysql_fetch_row(*res)) == NULL)
				break;
			if (modifier == CMS_ST_ISSUE || modifier == CMS_ST_SECTION
						 || modifier == CMS_ST_ARTICLE || modifier == CMS_ST_SEARCHRESULT)
			{
				lc.SetLanguage(strtol(row[1], 0, 10));
				lc.SetPublication(strtol(row[2], 0, 10));
				if (modifier != CMS_ST_ISSUE)
					lc.SetIssue(strtol(row[3], 0, 10));
				if (modifier == CMS_ST_ARTICLE || modifier == CMS_ST_SEARCHRESULT)
					lc.SetSection(strtol(row[4], 0, 10));
				SetContext(lc, strtol(row[0], 0, 10));
			}
			if (modifier == CMS_ST_ARTICLEATTACHMENT)
			{
				lc.SetAttachment(strtol(row[0], 0, 10));
				lc.SetAttachmentExtension(row[1]);
			}
			if (modifier == CMS_ST_ARTICLETOPIC || modifier == CMS_ST_SUBTOPIC)
			{
				lc.SetTopic(strtol(row[0], 0, 10));
			}
		}
		else
		{
			lc.SetStartSubtitle(i);
			if ((st = lc.SelectSubtitle(i + lc.StListStart())) == "")
				break;
		}
		runActions(first_block, lc, fs);
		lc.SetListIndex(lc.ListIndex() + 1);
		lc.SetListColumn(lc.ListColumn() + 1);
		if (lc.ListColumn() > columns)
		{
			lc.SetListRow(lc.ListRow() + 1);
			lc.SetListColumn(1);
		}
	}
	return RES_OK;
	TK_CATCH_ERR
}

// PrintSubtitlesURL: print url parameters for subtitle list/printing
// Parameters:
//		CContext& c - current context
//		sockstream& fs - output stream
//		bool& first - used to signal if first parameter in list (for printing separators)
void CActURLParameters::PrintSubtitlesURL(CContext& c, sockstream& fs, bool& first)
{
	String2String::iterator it;
	int st_index, st_printed = 0;
	for (st_index = 1, it = c.Fields().begin(); it != c.Fields().end(); ++it, ++st_index)
	{
		// compute the start subtitle
		lint start_subtitle;
		if (reset_from_list > 0)
			start_subtitle = 0;
		else if (c.LMode() == LM_NORMAL && c.Level() == CLV_SUBTITLE_LIST)
			start_subtitle = c.StListStart((*it).first) + c.ListIndex() - 1;
		else if (c.StMode() == STM_PREV)
			start_subtitle = c.StartSubtitle((*it).first) - 1;
		else if (c.StMode() == STM_NEXT)
			start_subtitle = c.StartSubtitle((*it).first) + 1;
		else
			start_subtitle = c.StartSubtitle((*it).first);
		if (start_subtitle >= c.SubtitlesNumber())
			start_subtitle = c.SubtitlesNumber() - 1;
		if (start_subtitle < 0)
			start_subtitle = 0;

		if (start_subtitle == 0 && c.Level() != CLV_SUBTITLE_LIST && !allsubtitles)
			continue;
		if (c.LMode() == LM_NORMAL && c.Level() == CLV_SUBTITLE_LIST 
			&& c.StListStart((*it).first) == 0 && !allsubtitles && start_subtitle == 0)
			continue;
		st_printed++;

		if (!first)
			fs << "&";
		else
			first = false;
		fs << "ST" << st_index << "=" << (*it).first << "&ST_T" << st_index << "=" << (*it).second;

		if (start_subtitle > 0)
			fs << "&ST_PS" << st_index << "=" << start_subtitle;
		if (c.LMode() != LM_NORMAL && c.Level() == CLV_SUBTITLE_LIST)
		{
			fs << "&ST_AS" << st_index << "=" << (int)c.AllSubtitles();
		}
		else
			fs << "&ST_AS" << st_index << "=" << (int)allsubtitles;
		if (c.Level() == CLV_ROOT)
			continue;
		if (c.LMode() == LM_PREV && c.Level() == CLV_SUBTITLE_LIST)
			fs << "&ST_LS" << st_index << "=" << c.StPrevStart((*it).first);
		else if (c.LMode() == LM_NEXT && c.Level() == CLV_SUBTITLE_LIST)
			fs << "&ST_LS" << st_index << "=" << c.StNextStart((*it).first);
		else if (c.Level() != CLV_ROOT)
			fs << "&ST_LS" << st_index << "=" << c.StListStart((*it).first);
	}
	if (st_printed > 0)
	{
		first = false;
		fs << "&ST_max=" << st_index - 1;
	}
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream
int CActURLParameters::takeAction(CContext& c, sockstream& fs)
{
	TK_TRY
	bool first = true;
	if (m_bArticleAttachment)
	{
		if (c.Attachment() <= 0)
		{
			return ERR_NODATA;
		}
		return 0;
	}
	if (image_nr >= 0)
	{
		if (c.Publication() < 0 || c.Issue() < 0 || c.Section() < 0 || c.Article() < 0)
			return ERR_NOPARAM;
		URLPrintParam(P_NRIMAGE, image_nr, fs, first);
		URLPrintParam(P_NRARTICLE, c.Article(), fs, first);
		return 0;
	}
	if (c.Language() < 0 && c.Publication() < 0 && c.Issue() < 0
		   && c.Section() < 0 && c.Article() < 0)
	{
		return ERR_NOPARAM;
	}
	CURL* pcoURL = NULL;
	if (fromstart)
		pcoURL = m_nPubLevel < CMS_PL_ARTICLE ? c.DefURL()->clone() : c.DefURL();
	else
		pcoURL = m_nPubLevel < CMS_PL_ARTICLE ? c.URL()->clone() : c.URL();
	if (m_nPubLevel < CMS_PL_ARTICLE)
		pcoURL->deleteParameter(P_NRARTICLE);
	if (m_nPubLevel < CMS_PL_SECTION)
		pcoURL->deleteParameter(P_NRSECTION);
	if (m_nPubLevel < CMS_PL_ISSUE)
		pcoURL->deleteParameter(P_NRISSUE);
	if (m_nPubLevel < CMS_PL_PUBLICATION)
		pcoURL->deleteParameter(P_IDPUBL);
	if (m_nPubLevel < CMS_PL_LANGUAGE)
		pcoURL->deleteParameter(P_IDLANG);
	string coURL = pcoURL->getQueryString();
	fs << (first ? "" : "&") << coURL;
	first = coURL == "";

	URLPrintParam(P_TOPIC_ID, (fromstart ? c.DefTopic() : c.Topic()), fs, first);
	if (pcoURL->needTemplateParameter())
		URLPrintNParam(P_TEMPLATE_ID, m_coTemplate, fs, first);
	if (m_nPubLevel > CMS_PL_ARTICLE)
		PrintSubtitlesURL(c, fs, first);
	if (m_nPubLevel < CMS_PL_ARTICLE)
		delete pcoURL;
	if (c.Level() == CLV_ROOT)
		return RES_OK;

	if (c.LMode() == LM_PREV)
	{
		if (c.Level() == CLV_ISSUE_LIST)
			URLPrintParam(P_ILSTART, (ResetList(CLV_ISSUE_LIST) ? 0 : c.IPrevStart()), fs, first);
		if (c.Level() == CLV_SECTION_LIST)
			URLPrintParam(P_SLSTART, (ResetList(CLV_SECTION_LIST) ? 0 : c.SPrevStart()),
			              fs, first);
		if (c.Level() == CLV_ARTICLE_LIST)
			URLPrintParam(P_ALSTART, (ResetList(CLV_ARTICLE_LIST) ? 0 : c.APrevStart()),
			              fs, first);
		if (c.Level() == CLV_SEARCHRESULT_LIST)
			URLPrintParam(P_SRLSTART, (ResetList(CLV_SEARCHRESULT_LIST) ? 0 : c.SrPrevStart()),
			              fs, first);
	}
	else if (c.LMode() == LM_NEXT)
	{
		if (c.Level() == CLV_ISSUE_LIST)
			URLPrintNParam(P_ILSTART, (ResetList(CLV_ISSUE_LIST) ? 0 : c.INextStart()), fs, first);
		if (c.Level() == CLV_SECTION_LIST)
			URLPrintNParam(P_SLSTART, (ResetList(CLV_SECTION_LIST) ? 0 : c.SNextStart()),
			               fs, first);
		if (c.Level() == CLV_ARTICLE_LIST)
			URLPrintNParam(P_ALSTART, (ResetList(CLV_ARTICLE_LIST) ? 0 : c.ANextStart()),
			               fs, first);
		if (c.Level() == CLV_SEARCHRESULT_LIST)
			URLPrintNParam(P_SRLSTART, (ResetList(CLV_SEARCHRESULT_LIST) ? 0 : c.SrNextStart()),
			               fs, first);
	}
	if (c.LMode() == LM_NORMAL || (c.Level() != CLV_ISSUE_LIST && c.Level() != CLV_ROOT))
		URLPrintNParam(P_ILSTART, (ResetList(CLV_ISSUE_LIST) ? 0 : c.IListStart()), fs, first);
	if (c.LMode() == LM_NORMAL || (c.Level() != CLV_SECTION_LIST && c.Level() != CLV_ROOT))
		URLPrintNParam(P_SLSTART, (ResetList(CLV_SECTION_LIST) ? 0 : c.SListStart()), fs, first);
	if (c.LMode() == LM_NORMAL || (c.Level() != CLV_ARTICLE_LIST && c.Level() != CLV_ROOT))
		URLPrintNParam(P_ALSTART, (ResetList(CLV_ARTICLE_LIST) ? 0 : c.AListStart()), fs, first);
	if (c.Level() == CLV_SEARCHRESULT_LIST && (c.LMode() == LM_PREV || c.LMode() == LM_NEXT))
	{
		if (!first)
			fs << "&";
		const char* pchEscKw = EscapeURL(c.StrKeywords());
		fs << "search=search&SearchKeywords=" << pchEscKw
		<< (c.SearchAnd() ? "&SearchMode=on" : "") << "&SearchLevel=" << c.SearchLevel();
		delete []pchEscKw;
	}
	return RES_OK;
	TK_CATCH_ERR
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream
int CActFormParameters::takeAction(CContext& c, sockstream& fs)
{
	TK_TRY
	if (c.Language() < 0 && c.Publication() < 0 && c.Issue() < 0
	        && c.Section() < 0 && c.Article() < 0)
		return ERR_NOPARAM;
	if (fromstart)
	{
		fs << c.DefURL()->getFormString();
		FormPrintParam(P_TOPIC_ID, c.DefTopic(), fs);
	}
	else
	{
		fs << c.URL()->getFormString();
		FormPrintParam(P_TOPIC_ID, c.Topic(), fs);
	}
	if (c.LMode() == LM_PREV)
	{
		FormPrintParam(P_ILSTART, c.IPrevStart(), fs);
		FormPrintParam(P_SLSTART, c.SPrevStart(), fs);
		FormPrintParam(P_ALSTART, c.APrevStart(), fs);
	}
	else if (c.LMode() == LM_NEXT)
	{
		FormPrintParam(P_ILSTART, c.INextStart(), fs);
		FormPrintParam(P_SLSTART, c.SNextStart(), fs);
		FormPrintParam(P_ALSTART, c.ANextStart(), fs);
	}
	else
	{
		FormPrintParam(P_ILSTART, c.IListStart(), fs);
		FormPrintParam(P_SLSTART, c.SListStart(), fs);
		FormPrintParam(P_ALSTART, c.AListStart(), fs);
	}
	return RES_OK;
	TK_CATCH_ERR
}

CPrintModifiers::CPrintModifiers()
{
	insert(CMS_ST_IMAGE);
	insert(CMS_ST_PUBLICATION);
	insert(CMS_ST_ISSUE);
	insert(CMS_ST_SECTION);
	insert(CMS_ST_ARTICLE);
	insert(CMS_ST_LIST);
	insert(CMS_ST_LANGUAGE);
	insert(CMS_ST_SUBSCRIPTION);
	insert(CMS_ST_USER);
	insert(CMS_ST_LOGIN);
	insert(CMS_ST_SEARCH);
	insert(CMS_ST_SUBTITLE);
	insert(CMS_ST_TOPIC);
	insert(CMS_ST_ARTICLEATTACHMENT);
}

CPrintModifiers CActPrint::s_coModifiers;

// BlobField: return 0 if field of table is blob type
// Parameters:
//		const char* table - table
//		const char* field - table field
int CActPrint::BlobField(const char* table, const char* field)
{
	int result = -1;
	string coQuery = string("desc ") + table + " " + field;
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[1] == NULL)
		return -1;
	if (strstr(row[1], "blob"))
		result = 0;
	return result;
}

// DateField: return 0 if field of table is date type
// Parameters:
//		const char* table - table
//		const char* field - table field
int CActPrint::DateField(const char* table, const char* field)
{
	int result;
	result = -1;
	string coQuery = string("desc ") + table + " " + field;
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[1] == NULL)
		return -1;
	if (strncmp(row[1], "date", 4) == 0)
		result = 0;
	return result;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream
int CActPrint::takeAction(CContext& c, sockstream& fs)
{
	TK_TRY
	if (modifier == CMS_ST_LIST)
	{
		if (c.ListIndex() <= 0)
			return ERR_NOLISTINDEX;
		if (case_comp(attr, "row") == 0)
			fs << c.ListRow();
		else if (case_comp(attr, "column") == 0)
			fs << c.ListColumn();
		else
			fs << (c.ListIndex() + c.ListStart(c.Level()));
		return RES_OK;
	}
	CMYSQL_RES res(NULL);
	MYSQL_ROW row(NULL);
	stringstream buf;
	if (modifier == CMS_ST_SUBSCRIPTION)
	{
		if (case_comp(attr, "totalcost") == 0)
		{
			if (c.SubsType() == ST_NONE)
				return RES_OK;
			const char* subs = c.SubsType() == ST_TRIAL ? "TrialTime" : "PaidTime";
			buf << "select sum(UnitCost) * sdt." << subs << " from Sections as sec, Publications"
			       " as pub, SubsDefTime as sdt where pub.Id = sdt.IdPublication and pub.Id = "
			       "sec.IdPublication and sdt.IdPublication = pub.Id and pub.Id = "
			    << c.Publication() << " and NrIssue = " << c.Issue() << " and IdLanguage = "
			    << c.Language() << " and CountryCode = '" << c.UserInfo("CountryCode") << "'";
			DEBUGAct("takeAction()", buf.str().c_str(), fs);
			SQLQuery(&m_coSql, buf.str().c_str());
			res = mysql_store_result(&m_coSql);
			row = mysql_fetch_row(*res);
			if (row[0] == NULL || row[0][0] == 0)
			{
				buf.str("");
				buf << "select sum(UnitCost) * " << subs << " from Sections, Publications as pub"
				       " where pub.Id = Sections.IdPublication and pub.Id = "
				    << c.Publication() << " and NrIssue = " << c.Issue() << " and IdLanguage = "
				    << c.Language();
				DEBUGAct("takeAction()", buf.str().c_str(), fs);
				SQLQuery(&m_coSql, buf.str().c_str());
				res = mysql_store_result(&m_coSql);
				row = mysql_fetch_row(*res);
				if (row[0] == NULL || row[0][0] == 0)
					return -1;
			}
			fs << row[0];
			return RES_OK;
		}
		if (case_comp(attr, "unit") == 0)
		{
			buf << "select TimeUnits.Name from TimeUnits, Publications where Publications.Id = "
			    << c.Publication() << " and TimeUnits.Unit = Publications.TimeUnit"
			       " and (TimeUnits.IdLanguage = " << c.Language()
			    << " or TimeUnits.IdLanguage = 1) order by IdLanguage desc";
		}
		else if (case_comp(attr, "expdate") == 0)
		{
			buf << "select DATE_ADD(SubsSections.StartDate, INTERVAL SubsSections.Days DAY) "
			       "from SubsSections, Subscriptions where Subscriptions.IdUser = " << c.User()
			    << " and Subscriptions.IdPublication = " << c.Publication()
			    << " and SubsSections.IdSubscription = Subscriptions.Id and "
			       "SubsSections.SectionNumber = " << c.Section();
		}
		else if (case_comp(attr, "currency") == 0)
		{
			buf << "select Currency from Publications where Id = " << c.Publication();
		}
		else if (case_comp(attr, "unitcost") == 0)
		{
			buf << "select UnitCost from Publications where Id = " << c.Publication();
		}
		else if (case_comp(attr, "trialtime") == 0)
		{
			buf << "select SubsDefTime.TrialTime, SubsDefTime.CountryCode = '"
			    << c.UserInfo("CountryCode") << "' as cc, Publications.TrialTime from "
			       "Publications LEFT JOIN SubsDefTime ON Publications.Id = "
			       "SubsDefTime.IdPublication where Publications.Id = " << c.Publication()
			    << " order by cc desc";
		}
		else if (case_comp(attr, "paidtime") == 0)
		{
			buf << "select SubsDefTime.PaidTime, SubsDefTime.CountryCode = '"
			    << c.UserInfo("CountryCode") << "' as cc, Publications.PaidTime from "
			       "Publications LEFT JOIN SubsDefTime ON Publications.Id = "
			       "SubsDefTime.IdPublication where Publications.Id = " << c.Publication()
			    << " order by cc desc";
		}
		else
		{ // error
			buf << "select Message from Errors where Number = " << c.SubsRes() << " and "
			    "(IdLanguage = " << c.Language() << " or IdLanguage = 1) order by IdLanguage desc";
		}
		DEBUGAct("takeAction()", buf.str().c_str(), fs);
		SQLQuery(&m_coSql, buf.str().c_str());
		res = mysql_store_result(&m_coSql);
		CheckForRows(*res, 1);
		row = mysql_fetch_row(*res);
		const char* pchData = row[0];
		if (mysql_field_count(&m_coSql) > 1
		    && (row[0] == NULL || row[1] == NULL || row[1][0] != '1'))
		{
			pchData = row[2];
		}
		if ((pchData = EscapeHTML(pchData)) == NULL)
			return ERR_NOMEM;
		if (format != "")
			fs << dateFormat(pchData, format.c_str(), c.Language());
		else
			fs << pchData;
		delete []pchData;
		return RES_OK;
	}
	if (modifier == CMS_ST_USER)
	{
		if (case_comp(attr, "adderror") == 0)
			buf << "select Message from Errors where Number = " << c.AddUserRes() << " and "
			       "IdLanguage = " << c.Language();
		else if (case_comp(attr, "modifyerror") == 0)
			buf << "select Message from Errors where Number = " << c.ModifyUserRes() << " and "
			        "IdLanguage = " << c.Language();
		else
			buf << "select " << attr << " from Users where Id = " << c.User();
		DEBUGAct("takeAction()", buf.str().c_str(), fs);
		SQLQuery(&m_coSql, buf.str().c_str());
		res = mysql_store_result(&m_coSql);
		CheckForRows(*res, 1);
		row = mysql_fetch_row(*res);
		const char* pchData = row[0];
		if ((pchData = EscapeHTML(pchData)) == NULL)
			return ERR_NOMEM;
		fs << pchData;
		delete []pchData;
		return RES_OK;
	}
	if (modifier == CMS_ST_LOGIN)
	{
		buf << "select Message from Errors where Number = " << c.LoginRes() << " and "
		        "(IdLanguage = " << c.Language() << " or IdLanguage = 1) order by IdLanguage desc";
		SQLQuery(&m_coSql, buf.str().c_str());
		res = mysql_store_result(&m_coSql);
		CheckForRows(*res, 1);
		row = mysql_fetch_row(*res);
		const char* pchData = row[0];
		if ((pchData = EscapeHTML(pchData)) == NULL)
			return ERR_NOMEM;
		fs << pchData;
		delete []pchData;
		return RES_OK;
	}
	if (modifier == CMS_ST_SEARCH)
	{
		if (case_comp(attr, "Keywords") == 0)
		{
			fs << c.StrKeywords();
			return RES_OK;
		}
		buf << "select Message from Errors where Number = " << c.SearchRes() << " and "
		        "(IdLanguage = " << c.Language() << " or IdLanguage = 1) order by IdLanguage desc";
		SQLQuery(&m_coSql, buf.str().c_str());
		res = mysql_store_result(&m_coSql);
		CheckForRows(*res, 1);
		row = mysql_fetch_row(*res);
		const char* pchData = row[0];
		if ((pchData = EscapeHTML(pchData)) == NULL)
			return ERR_NOMEM;
		fs << pchData;
		delete []pchData;
		return RES_OK;
	}
	if (modifier == CMS_ST_SUBTITLE)
	{
		fs << c.CurrentSubtitle();
		return RES_OK;
	}
	if (modifier == CMS_ST_LANGUAGE)
	{
		if (case_comp(attr, "number") == 0)
		{
			fs << c.Language();
			return RES_OK;
		}
		buf << "select " << attr << " from Languages where Id = " << c.Language();
		SQLQuery(&m_coSql, buf.str().c_str());
		res = mysql_store_result(&m_coSql);
		CheckForRows(*res, 1);
		row = mysql_fetch_row(*res);
		const char* pchData = row[0];
		if ((pchData = EscapeHTML(pchData)) == NULL)
			return ERR_NOMEM;
		fs << pchData;
		delete []pchData;
		return RES_OK;
	}
	if (modifier == CMS_ST_TOPIC)
	{
		const Topic* pcoTopic = Topic::topic(c.Topic());
		if (pcoTopic == NULL)
			return ERR_NODATA;
		buf.str("");
		buf << "select Code from Languages where id = " << c.Language();
		SQLQuery(&m_coSql, buf.str().c_str());
		res = mysql_store_result(&m_coSql);
		CheckForRows(*res, 1);
		row = mysql_fetch_row(*res);
		if (case_comp(attr, "name") == 0)
			fs << pcoTopic->name(row[0]);
		if (case_comp(attr, "identifier") == 0)
			fs << pcoTopic->id();
		return RES_OK;
	}
	if (modifier == CMS_ST_ARTICLE && attr == "SingleArticle")
	{
		buf << "select " << attr << " from Issues where IdPublication = "
		     << c.Publication() << " and Number = " << c.Issue();
		SQLQuery(&m_coSql, buf.str().c_str());
		res = mysql_store_result(&m_coSql);
		CheckForRows(*res, 1);
		row = mysql_fetch_row(*res);
		fs << row[0];
		return RES_OK;
	}
	if (modifier == CMS_ST_ARTICLEATTACHMENT)
	{
		if (case_comp(attr, "Description") != 0)
		{
			buf << "select " << attr << " from Attachments where Id = " << c.Attachment();
		}
		else
		{
			buf << "select tr.translation_text, abs(tr.fk_language_id - " << c.Language() << ") "
					<< "as lang_diff from Attachments as att, Translations as tr where att.Id = "
					<< c.Attachment() << " and att.fk_description_id = tr.phrase_id "
					<< "order by lang_diff asc";
		}
		SQLQuery(&m_coSql, buf.str().c_str());
		res = mysql_store_result(&m_coSql);
		CheckForRows(*res, 1);
		row = mysql_fetch_row(*res);
		fs << row[0];
		return RES_OK;
	}
	buf.str("");	
	string w, table, field;
	w = table = "";
	field = attr;	
	bool need_lang = false;
	if (modifier == CMS_ST_IMAGE)
	{
		table = "ArticleImages as ai left join Images as i on ai.IdImage = i.Id";
		SetNrField("ai.NrArticle", c.Article(), buf, w);
		SetNrField("ai.Number", image, buf, w);
	}
	else if (modifier == CMS_ST_PUBLICATION)
	{
		if (c.Publication() < 0)
			return ERR_NOPARAM;
		table = "Publications as p";
		if (field == "a.Name")
		{
			table += ", Aliases as a";
			w = "p.IdDefaultAlias = a.Id";
		}
		SetNrField("p.Id", c.Publication(), buf, w);
	}
	else if (modifier == CMS_ST_ISSUE)
	{
		if (case_comp(field, "template") == 0)
		{
			try {
				fs << "/look/" << CPublication::getIssueTemplate(c.Language(), c.Publication(), 
				                                                 c.Issue(), &m_coSql);
			}
			catch (InvalidValue& rcoEx)
			{
				return ERR_NODATA;
			}
			return RES_OK;
		}
		table = "Issues";
		if (c.Access() != A_ALL)
			w = "Published = 'Y'";
		need_lang = true;
		SetNrField("IdPublication", c.Publication(), buf, w);
		SetNrField("Number", c.Issue(), buf, w);
	}
	else if (modifier == CMS_ST_SECTION)
	{
		if (case_comp(field, "template") == 0)
		{
			try {
				fs << "/look/" << CPublication::getSectionTemplate(c.Language(), c.Publication(), 
				                                              c.Issue(), c.Section(), &m_coSql);
			}
			catch (InvalidValue& rcoEx)
			{
				return ERR_NODATA;
			}
			return RES_OK;
		}
		table = "Sections";
		need_lang = true;
		SetNrField("IdPublication", c.Publication(), buf, w);
		SetNrField("NrIssue", c.Issue(), buf, w);
		SetNrField("Number", c.Section(), buf, w);
	}
	else if (modifier == CMS_ST_LANGUAGE)
	{
		table = "Languages";
		SetNrField("Id", c.Language(), buf, w);
	}
	else
	{ // CMS_ST_ARTICLE
		if (case_comp(field, "template") == 0)
		{
			try {
				fs << "/look/" << CPublication::getArticleTemplate(c.Language(), c.Publication(), 
				                                              c.Issue(), c.Section(), &m_coSql);
			}
			catch (InvalidValue& rcoEx)
			{
				return ERR_NODATA;
			}
			return RES_OK;
		}
		table = "Articles";
		if (type != "")
			field = "Type, Number, IdLanguage";
		if (c.Access() != A_ALL)
			w = "Published = 'Y'";
		need_lang = true;
		SetNrField("IdPublication", c.Publication(), buf, w);
		SetNrField("NrIssue", c.Issue(), buf, w);
		SetNrField("NrSection", c.Section(), buf, w);
		SetNrField("Number", c.Article(), buf, w);
	}
	if (need_lang)
	{
		buf.str("");
		buf << "(IdLanguage = " << c.Language() << " or IdLanguage = 1)";
		w += (w != "" ? string(" and ") : string("")) + buf.str();
	}
	string coQuery = string("select ");
	coQuery += field + " from " + table;
	if (w != "")
		coQuery += string(" where ") + w;
	if (need_lang)
		coQuery += string(" order by IdLanguage desc");
	DEBUGAct("takeAction()", coQuery.c_str(), fs);
	SQLQuery(&m_coSql, coQuery.c_str());
	res = mysql_store_result(&m_coSql);
	CheckForRows(*res, 1);
	row = mysql_fetch_row(*res);
	if (row == NULL || row[0] == NULL)
		return -1;
	if (modifier == CMS_ST_ARTICLE && type != "")
	{
		if (strictType && type != row[0])
			return RES_OK;
		table = string("X") + row[0];
		int blob;
		blob = BlobField(table.c_str(), attr.c_str());
		coQuery = string("select ") + attr + " from " + table + " where NrArticle = " + row[1]
		          + " and IdLanguage = " + row[2];
		DEBUGAct("takeAction()", coQuery.c_str(), fs);
		SQLRealQuery(&m_coSql, coQuery.c_str(), coQuery.length());
		StoreResult(&m_coSql, res2);
		CheckForRows(*res2, 1);
		FetchRow(*res2, row2);
		ulint* lengths = mysql_fetch_lengths(*res2);
		if (blob == 0)
		{
			cparser.setDebug(*m_coDebug);
			cparser.reset(row2[0], lengths[0]);
			cparser.parse(c, fs, &m_coSql, c.StartSubtitle(), c.AllSubtitles(), true);
		}
		else if (DateField(table.c_str(), attr.c_str()) == 0 && format != "")
		{
			string coDate(row2[0], lengths[0]);
			fs << dateFormat(coDate.c_str(), format.c_str(), c.Language());
		}
		else
			fs.write(row2[0], lengths[0]);
	}
	else
	{
		const char* pchData = EscapeHTML(row[0]);
		if (pchData == NULL)
			return ERR_NOMEM;
		if (format != "")
			fs << dateFormat(pchData, format.c_str(), c.Language());
		else
			fs << pchData;
		delete []pchData;
	}
	return RES_OK;
	TK_CATCH_ERR
}

CIfModifiers::CIfModifiers()
{
	insert(CMS_ST_ISSUE);
	insert(CMS_ST_SECTION);
	insert(CMS_ST_ARTICLE);
	insert(CMS_ST_LIST);
	insert(CMS_ST_PREVIOUSITEMS);
	insert(CMS_ST_NEXTITEMS);
	insert(CMS_ST_ALLOWED);
	insert(CMS_ST_SUBSCRIPTION);
	insert(CMS_ST_USER);
	insert(CMS_ST_LOGIN);
	insert(CMS_ST_PUBLICATION);
	insert(CMS_ST_SEARCH);
	insert(CMS_ST_PREVSUBTITLES);
	insert(CMS_ST_NEXTSUBTITLES);
	insert(CMS_ST_SUBTITLE);
	insert(CMS_ST_CURRENTSUBTITLE);
	insert(CMS_ST_IMAGE);
	insert(CMS_ST_LANGUAGE);
	insert(CMS_ST_TOPIC);
	insert(CMS_ST_ARTICLEATTACHMENT);
}

CIfModifiers CActIf::s_coModifiers;

// AccessAllowed: return true if access to hidden content is allowed
// Parameters:
//		CContext& c - current context
//		sockstream& fs - output stream
bool CActIf::AccessAllowed(CContext& c, sockstream& fs)
{
	stringstream buf;
	buf << "select Public from Articles where IdPublication = " << c.Publication() << " and "
	       "NrIssue = " << c.Issue() << " and NrSection = " << c.Section() << " and Number = "
	    << c.Article() << " and (IdLanguage = " << c.Language() << " or IdLanguage = 1) order "
	       "by IdLanguage desc";
	DEBUGAct("AccessAllowed()", buf.str().c_str(), fs);
	if (mysql_query(&m_coSql, buf.str().c_str()))
		return false;
	CMYSQL_RES res = mysql_store_result(&m_coSql);
	if (*res == NULL)
		return false;
	if (mysql_num_rows(*res) <= 0)
		return false;
	MYSQL_ROW row(NULL);
	row = mysql_fetch_row(*res);
	if (row[0] != NULL && row[0][0] == 'Y')
	{
		return true;
	}
	if (c.Key() == 0 && !c.AccessByIP())
		return false;
	if (!c.IsReader())
		return true;
	return c.IsSubs(c.Publication(), c.Section());
}

// CActIf: assign operator
const CActIf& CActIf::operator =(const CActIf& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	param = p_rcoSrc.param;
	block = p_rcoSrc.block;
	sec_block = p_rcoSrc.sec_block;
	modifier = p_rcoSrc.modifier;
	rc_hash = p_rcoSrc.rc_hash;
	return *this;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream
int CActIf::takeAction(CContext& c, sockstream& fs)
{
	TK_TRY
	bool run_first;
	int run;
	stringstream buf;
	if (modifier == CMS_ST_ALLOWED)
	{
		run_first = AccessAllowed(c, fs);
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			runActions(block, c, fs);
		else
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == CMS_ST_LIST)
	{
		run_first = false;
		if (case_comp(param.attribute(), "start") == 0)
			run_first = c.ListIndex() == 1;
		else if (case_comp(param.attribute(), "end") == 0)
			run_first = c.ListIndex() == c.ListLength() && c.ListIndex() > 0;
		else
		{
			int val = case_comp(param.attribute(), "row") == 0 ? c.ListRow()
			          : (case_comp(param.attribute(), "column") == 0 ? c.ListColumn()
			             : (c.ListIndex() + c.ListStart(c.Level())));
			IntSet::iterator i_i = rc_hash.find(val);
			if (i_i != rc_hash.end()
			    || (case_comp(param.spec(), "odd") == 0 && (val % 2) != 0)
			    || (case_comp(param.spec(), "even") == 0 && (val % 2) == 0))
			{
				run_first = true;
			}
		}
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			runActions(block, c, fs);
		else
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == CMS_ST_PREVIOUSITEMS)
	{
		run_first = c.PrevStart(c.Level()) >= 0;
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
		{
			if (!m_bNegated)
				c.SetLMode(LM_PREV);
			runActions(block, c, fs);
			if (!m_bNegated)
				c.SetLMode(LM_NORMAL);
		}
		else
		{
			if (m_bNegated)
				c.SetLMode(LM_PREV);
			runActions(sec_block, c, fs);
			if (m_bNegated)
				c.SetLMode(LM_NORMAL);
		}
		return RES_OK;
	}
	else if (modifier == CMS_ST_NEXTITEMS)
	{
		run_first = c.NextStart(c.Level()) >= 0;
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
		{
			if (!m_bNegated)
				c.SetLMode(LM_NEXT);
			runActions(block, c, fs);
			if (!m_bNegated)
				c.SetLMode(LM_NORMAL);
		}
		else
		{
			if (m_bNegated)
				c.SetLMode(LM_NEXT);
			runActions(sec_block, c, fs);
			if (m_bNegated)
				c.SetLMode(LM_NORMAL);
		}
		return RES_OK;
	}
	else if (modifier == CMS_ST_SUBSCRIPTION)
	{
		run = -1;
		if (case_comp(param.attribute(), "ok") == 0 && c.Subscribe())
			run = c.SubsRes() == 0 ? 0 : 1;
		if (case_comp(param.attribute(), "error") == 0 && c.Subscribe())
			run = c.SubsRes() != 0 ? 0 : 1;
		if (case_comp(param.attribute(), "action") == 0)
			run = c.Subscribe() ? 0 : 1;
		if (case_comp(param.attribute(), "trial") == 0 && c.SubsType() != ST_NONE)
			run = c.SubsType() == ST_TRIAL ? 0 : 1;
		if (case_comp(param.attribute(), "paid") == 0 && c.SubsType() != ST_NONE)
			run = c.SubsType() == ST_PAID ? 0 : 1;
		if (run == 0)
			runActions(block, c, fs);
		else if (run == 1)
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == CMS_ST_USER)
	{
		run = -1;
		if (case_comp(param.attribute(), "addok") == 0 && c.AddUser())
			run = c.AddUserRes() == 0 ? 0 : 1;
		if (case_comp(param.attribute(), "modifyok") == 0 && c.ModifyUser())
			run = c.ModifyUserRes() == 0 ? 0 : 1;
		if (case_comp(param.attribute(), "adderror") == 0 && c.AddUser())
			run = c.AddUserRes() != 0 ? 0 : 1;
		if (case_comp(param.attribute(), "modifyerror") == 0 && c.ModifyUser())
			run = c.ModifyUserRes() != 0 ? 0 : 1;
		if (case_comp(param.attribute(), "defined") == 0)
			run = c.User() >= 0 ? 0 : 1;
		if (case_comp(param.attribute(), "addaction") == 0)
			run = c.AddUser() ? 0 : 1;
		if (case_comp(param.attribute(), "modifyaction") == 0)
			run = c.ModifyUser() ? 0 : 1;
		if (case_comp(param.attribute(), "loggedin") == 0)
			run = (c.User() >= 0 && (c.Key() > 0 || c.AccessByIP())) ? 0 : 1;
		if ((run == 0 && !m_bNegated) || (run == 1 && m_bNegated))
			runActions(block, c, fs);
		else if ((run == 1 && !m_bNegated) || (run == 0 && m_bNegated))
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == CMS_ST_LOGIN)
	{
		run = -1;
		if (case_comp(param.attribute(), "action") == 0)
			run = c.Login() ? 0 : 1;
		if (case_comp(param.attribute(), "ok") == 0 && c.Login())
			run = c.LoginRes() == 0 ? 0 : 1;
		if (case_comp(param.attribute(), "error") == 0 && c.Login())
			run = c.LoginRes() != 0 ? 0 : 1;
		if ((run == 0 && !m_bNegated) || (run == 1 && m_bNegated))
			runActions(block, c, fs);
		else if ((run == 1 && !m_bNegated) || (run == 0 && m_bNegated))
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == CMS_ST_SEARCH)
	{
		run = -1;
		if (case_comp(param.attribute(), "action") == 0)
			run = c.Search() ? 0 : 1;
		if (case_comp(param.attribute(), "ok") == 0 && c.Search())
			run = c.SearchRes() == 0 ? 0 : 1;
		if (case_comp(param.attribute(), "error") == 0 && c.Search())
			run = c.SearchRes() != 0 ? 0 : 1;
		if ((run == 0 && !m_bNegated) || (run == 1 && m_bNegated))
			runActions(block, c, fs);
		else if ((run == 1 && !m_bNegated) || (run == 0 && m_bNegated))
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == CMS_ST_PREVSUBTITLES)
	{
		run_first = c.StartSubtitle() > 0 && !c.AllSubtitles();
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
		{
			if (!m_bNegated)
				c.SetStMode(STM_PREV);
			runActions(block, c, fs);
			if (!m_bNegated)
				c.SetStMode(STM_NORMAL);
		}
		else
		{
			if (m_bNegated)
				c.SetStMode(STM_PREV);
			runActions(sec_block, c, fs);
			if (m_bNegated)
				c.SetStMode(STM_NORMAL);
		}
		return RES_OK;
	}
	else if (modifier == CMS_ST_NEXTSUBTITLES)
	{
		run_first = c.StartSubtitle() < (c.SubtitlesNumber() - 1) && !c.AllSubtitles();
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
		{
			if (!m_bNegated)
				c.SetStMode(STM_NEXT);
			runActions(block, c, fs);
			if (!m_bNegated)
				c.SetStMode(STM_NORMAL);
		}
		else
		{
			if (m_bNegated)
				c.SetStMode(STM_NEXT);
			runActions(sec_block, c, fs);
			if (m_bNegated)
				c.SetStMode(STM_NORMAL);
		}
		return RES_OK;
	}
	else if (modifier == CMS_ST_SUBTITLE)
	{
		buf.str("");
		buf << (c.StartSubtitle() + 1);
		run_first = param.applyOp(buf.str().c_str());
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			runActions(block, c, fs);
		else
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == CMS_ST_CURRENTSUBTITLE)
	{
		run_first = (c.DefaultStartSubtitle() ) == (c.ListIndex() - 1) && !c.AllSubtitles();
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			runActions(block, c, fs);
		else
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == CMS_ST_IMAGE)
	{
		buf << "select count(*) from ArticleImages where NrArticle = " << c.Article()
				<< " and Number = " << param.attribute();
		DEBUGAct("takeAction()", buf.str().c_str(), fs);
		SQLQuery(&m_coSql, buf.str().c_str());
		StoreResult(&m_coSql, res);
		CheckForRows(*res, 1);
		FetchRow(*res, row);
		if (row[0] == NULL)
			return -1;
		run_first = atoi(row[0]) > 0;
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			runActions(block, c, fs);
		else
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == CMS_ST_TOPIC)
	{
		const Topic* pcoTopic = Topic::topic(c.Topic());
		if (pcoTopic == NULL)
		{
			run_first = g_coNOT_EQUAL_Symbol == param.opSymbol();
		}
		else
		{
			const Topic* pcoCompTopic = Topic::topic(param.value());
			if (NULL == pcoCompTopic)
				return ERR_NODATA;
			if (g_coEQUAL_Symbol == param.opSymbol())
				run_first = pcoTopic->id() == pcoCompTopic->id();
			else
				run_first = pcoTopic->id() != pcoCompTopic->id();
		}
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			runActions(block, c, fs);
		else
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	if (case_comp(param.attribute(), "defined") == 0)
	{
		if (modifier == CMS_ST_LANGUAGE)
			run_first = c.Language() >= 0;
		else if (modifier == CMS_ST_PUBLICATION)
			run_first = c.Publication() >= 0;
		else if (modifier == CMS_ST_ISSUE)
			run_first = c.Issue() >= 0;
		else if (modifier == CMS_ST_SECTION)
			run_first = c.Section() >= 0;
		else if (modifier == CMS_ST_ARTICLE)
			run_first = c.Article() >= 0;
		else
			return RES_OK;
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			runActions(block, c, fs);
		else
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	if (case_comp(param.attribute(), "fromstart") == 0)
	{
		if (modifier == CMS_ST_LANGUAGE)
			run_first = c.Language() == c.DefLanguage();
		else if (modifier == CMS_ST_PUBLICATION)
			run_first = c.Publication() == c.DefPublication();
		else if (modifier == CMS_ST_ISSUE)
			run_first = c.Issue() == c.DefIssue();
		else if (modifier == CMS_ST_SECTION)
			run_first = c.Section() == c.DefSection();
		else if (modifier == CMS_ST_ARTICLE)
			run_first = c.Article() == c.DefArticle();
		else
			return RES_OK;
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			runActions(block, c, fs);
		else
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	if (case_comp(param.attribute(), "number") == 0)
	{
		id_type nVal = 0;
		if (modifier == CMS_ST_LANGUAGE)
			nVal = c.Language();
		else if (modifier == CMS_ST_ISSUE)
			nVal = c.Issue();
		else if (modifier == CMS_ST_SECTION)
			nVal = c.Section();
		else
			return -1;
		run_first = param.applyOp(Integer(nVal));
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			runActions(block, c, fs);
		else
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	if (case_comp(param.attribute(), "identifier") == 0)
	{
		id_type nVal = 0;
		if (modifier == CMS_ST_PUBLICATION)
			nVal = c.Publication();
		else
			return -1;
		run_first = param.applyOp(Integer(nVal));
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			runActions(block, c, fs);
		else
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	buf.str("");
	if (modifier == CMS_ST_ARTICLEATTACHMENT)
	{
		buf << "select " << param.attribute() << " from Attachments where id = " << c.Attachment();
		DEBUGAct("takeAction()", buf.str().c_str(), fs);
		SQLQuery(&m_coSql, buf.str().c_str());
		StoreResult(&m_coSql, res);
		CheckForRows(*res, 1);
		FetchRow(*res, row);
		if (row[0] == NULL)
			return ERR_NODATA;
		run_first = param.applyOp(row[0]);
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			runActions(block, c, fs);
		else
			runActions(sec_block, c, fs);
	}
	if (case_comp(param.attribute(), "hasAttachments") == 0 && modifier == CMS_ST_ARTICLE)
	{
		buf << "select count(*) from ArticleAttachments where fk_article_number = " << c.Article();
		DEBUGAct("takeAction()", buf.str().c_str(), fs);
		SQLQuery(&m_coSql, buf.str().c_str());
		StoreResult(&m_coSql, res);
		CheckForRows(*res, 1);
		FetchRow(*res, row);
		if (row[0] == NULL)
			return ERR_NODATA;
		run_first = strtol(row[0], 0, 10) > 0;
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			runActions(block, c, fs);
		else
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	if (c.Language() < 0 || c.Publication() < 0 || c.Issue() < 0)
		return ERR_NOPARAM;
	string w, field, tables, value;
	field = param.attribute();
	bool need_lang = false;
	if (modifier == CMS_ST_LANGUAGE)
	{
		tables = "Languages";
		SetNrField("Id", c.Language(), buf, w);
		need_lang = false;
		value = param.value();
	}
	else if (modifier == CMS_ST_PUBLICATION)
	{
		tables = "Publications";
		SetNrField("Id", c.Publication(), buf, w);
		need_lang = false;
		value = param.value();
	}
	else if (modifier == CMS_ST_ISSUE)
	{
		tables = "Issues";
		if (case_comp(param.attribute(), "iscurrent") == 0)
		{
			buf << "max(Number) = " << c.Issue();
			field = buf.str();
			value = "1";
			if (c.Access() != A_ALL)
				w = "Published = 'Y'";
		}
		else
		{
			SetNrField("Number", c.Issue(), buf, w);
			need_lang = true;
			value = param.value();
		}
	}
	else if (modifier == CMS_ST_SECTION)
	{
		if (c.Section() < 0)
			return ERR_NOPARAM;
		tables = "Sections";
		SetNrField("NrIssue", c.Issue(), buf, w);
		SetNrField("Number", c.Section(), buf, w);
		need_lang = true;
		value = param.value();
	}
	else if (modifier == CMS_ST_ARTICLE)
	{
		if (c.Article() < 0)
			return ERR_NOPARAM;
		need_lang = true;
		if (param.attrType() != "" && m_bStrictType)
		{
			tables = string("X") + param.attrType();
			SetNrField("NrArticle", c.Article(), buf, w);
			value = param.value();
		}
		else
		{
			if (param.attrType() != "" && !m_bStrictType)
				field = "Type";
			tables = "Articles";
			SetNrField("NrIssue", c.Issue(), buf, w);
			SetNrField("NrSection", c.Section(), buf, w);
			SetNrField("Number", c.Article(), buf, w);
			if (case_comp(param.attribute(), "has_keyword") == 0)
			{
				buf.str("");
				buf << "Keywords like '%" << param.spec() << "%'";
				field = buf.str();
				value = "1";
			}
			else if (case_comp(param.attribute(), "Public") == 0)
			{
				field = param.attribute() + " = 'Y'";
				value = "1";
			}
			else if (case_comp(param.attribute(), "OnFrontPage") == 0
			         || case_comp(param.attribute(), "OnSection") == 0)
			{
				field = param.attribute() + " = 'Y'";
				value = param.value();
			}
			else if (case_comp(param.attribute(), "translated_to") == 0)
			{
				tables += ", Languages";
				AppendConstraint(w, "Languages.Code", "=", param.spec(), "and");
				w += " and Articles.IdLanguage = Languages.Id";
				field = "Languages.Code";
				value = param.spec();
				need_lang = false;
			}
			else if (param.attrType() == "")
			{
				value = param.value();
			}
		}
	}
	else
		return -1;
	if (modifier != CMS_ST_LANGUAGE && modifier != CMS_ST_PUBLICATION && param.attrType() == "")
		SetNrField("IdPublication", c.Publication(), buf, w);
	if (need_lang)
	{
		buf.str("");
		buf << "(IdLanguage = " << c.Language() << " or IdLanguage = 1)";
		w += (w != "" ? string(" and ") : string("")) + buf.str();
	}
	string coQuery = string("select ") + field + " from " + tables;
	if (w.length())
		coQuery += string(" where ") + w;
	if (need_lang)
		coQuery += " order by IdLanguage desc";
	DEBUGAct("takeAction()", coQuery.c_str(), fs);
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[0] == NULL)
		return ERR_NODATA;
	if (modifier == CMS_ST_ARTICLE && param.attrType() != "" && !m_bStrictType)
	{
		field = param.attribute();
		tables = string("X") + row[0];
		buf.str("");
		w = "";
		SetNrField("NrArticle", c.Article(), buf, w);
		SetNrField("IdLanguage", c.Language(), buf, w);
		string coQuery = string("select ") + field + " from " + tables;
		if (w.length())
			coQuery += string(" where ") + w;
		DEBUGAct("takeAction()", coQuery.c_str(), fs);
		SQLQuery(&m_coSql, coQuery.c_str());
		res = mysql_store_result(&m_coSql);
		if (*res == NULL)
			return ERR_NOMEM;
		row = mysql_fetch_row(*res);
		if (row == NULL)
			return ERR_NOMEM;
		value = param.value();
	}
	if (param.operation())
	{
		if (case_comp(param.attribute(), "OnFrontPage") == 0
		    || case_comp(param.attribute(), "OnSection") == 0)
		{
			run_first = param.applyOp(Switch::valName(((Switch::SwitchVal)strtol(row[0], 
			                          NULL, 10))), value);
		}
		else
		{
			run_first = param.applyOp(row[0], value);
		}
	}
	else
		run_first = row[0] == value;
	run_first = m_bNegated ? !run_first : run_first;
	if (run_first)
		runActions(block, c, fs);
	else
		runActions(sec_block, c, fs);
	return RES_OK;
	TK_CATCH_ERR
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream
int CActDate::takeAction(CContext& c, sockstream& fs)
{
	const char* format;
	if (case_comp(attr, "year") == 0)
		format = "%Y";
	else if (case_comp(attr, "mon_nr") == 0)
		format = "%c";
	else if (case_comp(attr, "mday") == 0)
		format = "%e";
	else if (case_comp(attr, "yday") == 0)
		format = "%j";
	else if (case_comp(attr, "wday_nr") == 0)
		format = "%w";
	else if (case_comp(attr, "hour") == 0)
		format = "%k";
	else if (case_comp(attr, "min") == 0)
		format = "%i";
	else if (case_comp(attr, "sec") == 0)
		format = "%s";
	else if (case_comp(attr, "mon_name") == 0)
		format = "%M";
	else if (case_comp(attr, "wday_name") == 0)
		format = "%W";
	else
		format = attr.c_str();
	string coQuery = string("select now()");
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	char* pchVal = SQLEscapeString(format, strlen(format));
	if (pchVal == NULL)
		return ERR_NOMEM;
	fs << dateFormat(row[0], pchVal, c.Language());
	delete []pchVal;
	return RES_OK;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream	
int CActSubscription::takeAction(CContext& c, sockstream& fs)
{
	TK_TRY
	stringstream buf;
	if (c.SubsType() == ST_NONE)
	{
		buf << "select Subs.Type, Usr.CountryCode from Subscriptions as Subs, "
		       "Users as Usr where Subs.IdUser = Usr.Id and IdUser = " << c.User()
		    << " and IdPublication = " << c.Publication();
		SQLQuery(&m_coSql, buf.str().c_str());
		StoreResult(&m_coSql, res);
		CheckForRows(*res, 1);
		FetchRow(*res, row);
		if (row[0] == NULL || row[1] == NULL)
			return -1;
		if (row[0][0] == 'T')
			c.SetSubsType(ST_TRIAL);
		else
			c.SetSubsType(ST_PAID);
		c.SetUserInfo("CountryCode", row[1]);
		buf.str("");
	}
	buf << "select UnitCost, Currency from Publications where Id = " << c.Publication();
	SQLQuery(&m_coSql, buf.str().c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[0] == NULL)
		return -1;
	double unit_cost = atof(row[0]);
	string currency = row[1];
	buf.str("");
	buf << "select count(*) from Sections where IdPublication = " << c.Publication() << " and "
	       "NrIssue = " << c.Issue() << " and IdLanguage = " << c.Language();
	SQLQuery(&m_coSql, buf.str().c_str());
	res = mysql_store_result(&m_coSql);
	CheckForRows(*res, 1);
	row = mysql_fetch_row(*res);
	if (row[0] == NULL)
		return -1;
	lint nos = atol(row[0]);
	SafeAutoPtr<CURL> pcoURL(c.URL()->clone());
	try {
		pcoURL->setTemplate(m_nTemplateId);
	}
	catch (InvalidValue& rcoEx)
	{
		return ERR_INVALID_FIELD;
	}
	CContext lc = c;
	lc.SetByPublication(by_publication);
	string coSubsType = (c.SubsType() == ST_TRIAL ? "trial" : "paid");
	fs << "<form name=\"subscription\" action=\"" << pcoURL->getURIPath()
	   << "\" name=\"f1\" method=POST>\n"
	   << "<input type=hidden name=" << P_TEMPLATE_ID << " value=" << m_nTemplateId << ">\n"
	   << "<input type=hidden name=" << P_SUBSTYPE << " value=\"" << coSubsType << "\">" << endl;
	if (c.SubsType() == ST_PAID && total != "")
		fs << "<script>\nvar sum;\nvar i;\n\n"
		"function f(){\n"
		" sum=0;\n"
		" for(i=0; i<document.f1.nos.value; i++){\n"
		"   if(document.f1.cb_subs[i].checked && document.f1[2*i+1].value.length)\n"
		"   sum=parseInt(sum)+parseInt(document.f1[2*i+1].value)\n"
		" }\n"
		" document.f1.suma.value = Math.round(100*sum*document.f1.unitcost.value)/100;\n"
		"}\n</script>\n";
	runActions(block, lc, fs);
	if (c.SubsType() == ST_PAID && total != "" && !by_publication)
		fs << total << " <input type=text name=suma size=10 READONLY> " << currency << endl;
	fs << c.URL()->getFormString();
	if (c.SubsType() == ST_PAID && total != "" && !by_publication)
		fs << "<input type=hidden name=\"unitcost\" value=\"" << unit_cost
		<< "\">\n<input type=hidden name=nos value=\"" << nos << "\">\n"
		<< "<p><input type=button value=\"" << evaluate << "\" onclick=\"f()\"></p>\n";
	if (by_publication)
		fs << "<input type=hidden name=by value=publication>\n";
	fs << "<input type=submit name=\"" P_SUBSCRIBE "\" value=\"" << button_name
	   << "\">\n</form>\n";
	return RES_OK;
	TK_CATCH_ERR
}

CEditModifiers::CEditModifiers()
{
	insert(CMS_ST_SUBSCRIPTION);
	insert(CMS_ST_USER);
	insert(CMS_ST_LOGIN);
	insert(CMS_ST_SEARCH);
}

CEditModifiers CActEdit::s_coModifiers;

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream	
int CActEdit::takeAction(CContext& c, sockstream& fs)
{
	TK_TRY
	stringstream buf;
	DEBUGAct("takeAction()", field.c_str(), fs);
	if (modifier == CMS_ST_USER)
	{
		if (field != "Interests" && field != "Improvements"
		        && field != "Password" && field != "PasswordAgain"
		        && field != "Text1" && field != "Text2" && field != "Text3")
		{
			string attrval = c.UserInfo(field);
			string coQuery = string("desc Users ") + field;
			SQLQuery(&m_coSql, coQuery.c_str());
			StoreResult(&m_coSql, res);
			CheckForRows(*res, 1);
			FetchRow(*res, row);
			if (row[1] == NULL)
				return -1;
			const char* r = strchr(row[1], '(');
			if (r == 0 || *r == 0)
				return RES_OK;
			int len = atol(r + 1);
			fs << "<input type=text name=\"User" << field << "\" size="
			<< (len > 50 ? 50 : len) << " maxlength=" << len;
			if (attrval != "")
				fs << " value=\"" << attrval << "\"";
			fs << ">";
			return RES_OK;
		}
		if (field == "Password" || field == "PasswordAgain")
		{
			fs << "<input type=password name=\"User" << field << "\" size=32 maxlength=32>";
			return RES_OK;
		}
		fs << "<textarea name=\"User" << field << "\" cols=40 rows=4></textarea>";
	}
	if (modifier == CMS_ST_SUBSCRIPTION)
	{
		buf << "select TrialTime, PaidTime from SubsDefTime, Users where "
		       "SubsDefTime.CountryCode = Users.CountryCode and SubsDefTime."
		       "IdPublication = " << c.Publication() << " and Users.Id = " << c.User();
		DEBUGAct("takeAction()", buf.str().c_str(), fs);
		SQLQuery(&m_coSql, buf.str().c_str());
		StoreResult(&m_coSql, res);
		if (mysql_num_rows(*res) < 1)
		{
			buf.str("");
			buf << "select TrialTime, PaidTime from Publications where Id = " << c.Publication();
			SQLQuery(&m_coSql, buf.str().c_str());
			res = mysql_store_result(&m_coSql);
		}
		FetchRow(*res, row);
		fs << "<input type=hidden name=\"" << P_TX_SUBS << c.Section()
		   << "\" value=\"" << (c.SubsType() == ST_TRIAL ? row[0] : row[1])
		   << "\">" << (c.SubsType() == ST_TRIAL ? row[0] : row[1]);
	}
	if (modifier == CMS_ST_LOGIN)
	{
		if (field == "Password")
			fs << "<input type=password name=\"Login" << field << "\" maxlength=32 size=10>";
		else
			fs << "<input type=text name=\"Login" << field << "\" maxlength=32 size=10>";
	}
	if (modifier == CMS_ST_SEARCH)
	{
		if (field == "Keywords")
		{
			const char* pchEscKw = EscapeHTML(c.StrKeywords());
			fs << "<input type=text name=\"Search" << field << "\" maxlength=255 "
			"size=" << size << " value=\"" << pchEscKw << "\">";
			delete []pchEscKw;
		}
	}
	return RES_OK;
	TK_CATCH_ERR
}

CSelectModifiers::CSelectModifiers()
{
	insert(CMS_ST_SUBSCRIPTION);
	insert(CMS_ST_USER);
	insert(CMS_ST_SEARCH);
	insert(CMS_ST_LOGIN);
}

CSelectModifiers CActSelect::s_coModifiers;

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream	
int CActSelect::takeAction(CContext& c, sockstream& fs)
{
	TK_TRY
	stringstream buf;
	if (modifier == CMS_ST_SUBSCRIPTION)
	{
		if (c.ByPublication())
			fs << "<input type=hidden name=\"" << P_CB_SUBS << "\" value=\""
			<< c.Section() << "\">";
		else
			fs << "<input type=checkbox name=\"" << P_CB_SUBS << "\" value=\""
			<< c.Section() << "\">";
	}
	else if (modifier == CMS_ST_USER)
	{
		string attrval = c.UserInfo(field);
		if (case_comp(field, "CountryCode") == 0)
		{
			buf << "select Code, Name from Countries where IdLanguage = " << c.Language();
		}
		else if (case_comp(field, "Title") == 0)
		{
			fs << "<select name=\"User" << field << "\">"
			<< "<option value=\"Mr.\"" << (attrval == "Mr." ? " selected" : "")
			<< ">Mr.</option>"
			<< "<option value=\"Mrs.\"" << (attrval == "Mrs." ? " selected" : "")
			<< ">Mrs.</option>"
			<< "<option value=\"Ms.\"" << (attrval == "Ms." ? " selected" : "")
			<< ">Ms.</option>"
			<< "<option value=\"Dr.\"" << (attrval == "Dr." ? " selected" : "")
			<< ">Dr.</option></select>\n";
			return RES_OK;
		}
		else if (case_comp(field, "Gender") == 0)
		{
			fs << "<input type=radio name=\"User" << field << "\" value=\"M\""
			<< (attrval == "M" ? " checked" : "") << ">" << male_name
			<< " <input type=radio name=\"User" << field << "\" value=\"F\""
			<< (attrval == "F" ? " checked" : "") << ">" << female_name;
			return RES_OK;
		}
		else if (case_comp(field, "Age") == 0)
		{
			fs << "<select name=\"User" << field << "\">"
			<< "<option value=\"0-17\"" << (attrval == "0-17" ? " selected" : "")
			<< ">under 18</option>"
			<< "<option value=\"18-24\"" << (attrval == "18-24" ? " selected" : "")
			<< ">18-24</option>"
			<< "<option value=\"25-39\"" << (attrval == "25-39" ? " selected" : "")
			<< ">25-39</option>"
			<< "<option value=\"40-49\"" << (attrval == "40-49" ? " selected" : "")
			<< ">40-49</option>"
			<< "<option value=\"50-65\"" << (attrval == "50-65" ? " selected" : "")
			<< ">50-65</option>"
			<< "<option value=\"65-\"" << (attrval == "65-" ? " selected" : "")
			<< ">65 or over</option></select>\n";
			return RES_OK;
		}
		else if (case_comp(field, "EmployerType") == 0)
		{
			fs << "<select name=\"User" << field << "\">"
			<< "<option value=\"\""
			<< (attrval == "" ? " selected" : "") << "></option>"
			<< "<option value=\"Corporate\""
			<< (attrval == "Corporate" ? " selected" : "") << ">Corporate</option>"
			<< "<option value=\"NGO\""
			<< (attrval == "NGO" ? " selected" : "") << ">Non-Governmental Organisation</option>"
			<< "<option value=\"Government Agency\""
			<< (attrval == "Government Agency" ? " selected" : "")
			<< ">Government Agency</option>"
			<< "<option value=\"Academic\""
			<< (attrval == "Academic" ? " selected" : "") << ">Academic</option>"
			<< "<option value=\"Media\""
			<< (attrval == "Media" ? " selected" : "") << ">Media</option></select>\n";
			return RES_OK;
		}
		else if (strncasecmp(field.c_str(), "Pref", 4) == 0)
		{
			fs << "<input type=checkbox name=\"User" << field << "\"";
			if (attrval == "Y" || checked)
				fs << " value=\"on\" checked";
			fs << "><input type=hidden name=HasPref" << field.substr(4) << " value=1>";
			return RES_OK;
		}
		else
			return ERR_INVALID_FIELD;
		SQLQuery(&m_coSql, buf.str().c_str());
		StoreResult(&m_coSql, res);
		CheckForRows(*res, 1);
		fs << "<select name=\"User" << field << "\"><option value=\"\">-</option>";
		MYSQL_ROW row;
		while ((row = mysql_fetch_row(*res)))
		{
			fs << "<option value=\"" << row[0] << "\""
			<< (attrval == row[0] ? " selected" : "") << ">"
			<< row[1] << "</option>";
		}
		fs << "</select>\n";
	}
	else if (modifier == CMS_ST_SEARCH)
	{
		if (field == "Mode")
			fs << "<input type=checkbox name=\"Search" << field << "\""
			<< (c.SearchAnd() ? " checked" : "") << ">";
		else
		{
			fs << "<select name=\"Search" << field << "\">"
			"<option value=0" << (c.SearchLevel() == 0 ? " selected" : "")
			<< ">Publication</option>"
			"<option value=1" << (c.SearchLevel() == 1 ? " selected" : "")
			<< ">Issue</option>"
			"<option value=2" << (c.SearchLevel() == 2 ? " selected" : "")
			<< ">Section</option></select>";
		}
	}
	else if (modifier == CMS_ST_LOGIN)
	{
		fs << "<input type=checkbox name=\"" P_REMEMBER_USER "\">";
	}
	return RES_OK;
	TK_CATCH_ERR
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream	
int CActUser::takeAction(CContext& c, sockstream& fs)
{
	TK_TRY
	stringstream buf;
	static const char *params[28] =
	    {
	        "Name", "UName", "EMail", "City", "StrAddress", "State", "CountryCode",
	        "Phone", "Fax", "Contact", "Phone2", "Title", "Gender", "Age",
	        "PostalCode", "Employer", "EmployerType", "Position", "Languages", "Pref1",
	        "Pref2", "Pref3", "Pref4", "Field1", "Field2", "Field3", "Field4", "Field5"
	    };
	if (c.Key() <= 0 && !add)
		return ERR_NOKEY;
	SafeAutoPtr<CURL> pcoURL(c.URL()->clone());
	try {
		pcoURL->setTemplate(m_nTemplateId);
	}
	catch (InvalidValue& rcoEx)
	{
		return ERR_INVALID_FIELD;
	}

	string coSubsType = (c.SubsType() == ST_TRIAL ? "trial" : "paid");
	fs << "<form name=\"user\" action=\"" << pcoURL->getURIPath() << "\" method=POST>\n"
	   << "<input type=hidden name=" << P_TEMPLATE_ID << " value=" << m_nTemplateId << ">\n"
	   << "<input type=hidden name=" << P_SUBSTYPE << " value=" << coSubsType << ">" << endl;
	fs << c.URL()->getFormString();
	CContext lc = c;
	if (!add)
	{
		buf << "select " << params[0];
		for (int i = 1; i < 28; i++)
			buf << ", " << params[i];
		buf << " from Users where Id = " << c.User();
		DEBUGAct("takeAction()", buf.str().c_str(), fs);
		SQLQuery(&m_coSql, buf.str().c_str());
		StoreResult(&m_coSql, res);
		CheckForRows(*res, 1);
		FetchRow(*res, row);
		for (int i = 0; i < 28; i++)
			if (!lc.IsUserInfo(string(params[i])))
				lc.SetUserInfo(string(params[i]), string(row[i] != NULL ? row[i] : ""));
	}
	runActions(block, lc, fs);
	fs << "<input type=submit name=\"" << (add ? P_USERADD : P_USERMODIFY)
	<< "\" value=\"" << button_name << "\">\n</form>\n";
	return RES_OK;
	TK_CATCH_ERR
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream	
int CActLogin::takeAction(CContext& c, sockstream& fs)
{
	SafeAutoPtr<CURL> pcoURL(c.URL()->clone());
	try {
		pcoURL->setTemplate(m_nTemplateId);
	}
	catch (InvalidValue& rcoEx)
	{
		return ERR_INVALID_FIELD;
	}
	CContext lc = c;
	fs << "<form name=\"login\" action=\"" << pcoURL->getURIPath() << "\" method=POST>\n"
	   << "<input type=hidden name=" << P_TEMPLATE_ID << " value=" << m_nTemplateId << ">" << endl;
	fs << c.URL()->getFormString();
	runActions(block, lc, fs);
	fs << "<input type=submit name=\"" P_LOGIN "\" value=\"" << button_name << "\">\n</form>\n";
	return RES_OK;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream	
int CActSearch::takeAction(CContext& c, sockstream& fs)
{
	SafeAutoPtr<CURL> pcoURL(c.URL()->clone());
	try {
		pcoURL->setTemplate(m_nTemplateId);
	}
	catch (InvalidValue& rcoEx)
	{
		return ERR_INVALID_FIELD;
	}
	CContext lc = c;
	fs << "<form name=\"search\" action=\"" << pcoURL->getURIPath() << "\" method=POST>\n"
	   << "<input type=hidden name=" << P_TEMPLATE_ID << " value=" << m_nTemplateId << ">" << endl;
	fs << c.URL()->getFormString();
	runActions(block, lc, fs);
	fs << "<input type=submit name=\"" P_SEARCH "\" value=\"" << button_name << "\">\n</form>\n";
	return RES_OK;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream	
int CActWith::takeAction(CContext& c, sockstream& fs)
{
	TK_TRY
	stringstream buf;
	CContext lc = c;
	lc.SetCurrentField(field);
	lc.SetCurrentArtType(art_type);
	buf << "select F" << field << " from X" << art_type << " where NrArticle = " << c.Article()
	    << " and IdLanguage = " << c.Language();
	DEBUGAct("takeAction()", buf.str().c_str(), fs);
	SQLQuery(&m_coSql, buf.str().c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	cparser.setDebug(*m_coDebug);
	cparser.reset(row[0], (mysql_fetch_lengths(*res))[0]);
	cparser.parse(lc, fs, &m_coSql, 0, true, false);
	runActions(block, lc, fs);
	return RES_OK;
	TK_CATCH_ERR
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream	
int CActURIPath::takeAction(CContext& c, sockstream& fs)
{
	using std::setw;
	using std::setfill;
	using std::right;
	using std::resetiosflags;
	if (m_bArticleAttachment)
	{
		fs << "/attachment/" << setw(9) << setfill('0') << right << c.Attachment()
				<< "." << c.AttachmentExtension();
		return RES_OK;
	}
	if (m_nTemplate > 0 || m_nPubLevel < CMS_PL_SUBTITLE)
	{
		SafeAutoPtr<CURL> pcoURL(c.URL()->clone());
		if (m_nTemplate > 0)
			pcoURL->setTemplate(m_nTemplate);
		if (m_nPubLevel <= CMS_PL_SECTION)
			pcoURL->deleteParameter(P_NRARTICLE);
		if (m_nPubLevel <= CMS_PL_ISSUE)
			pcoURL->deleteParameter(P_NRSECTION);
		if (m_nPubLevel <= CMS_PL_PUBLICATION)
			pcoURL->deleteParameter(P_NRISSUE);
		if (m_nPubLevel <= CMS_PL_LANGUAGE)
			pcoURL->deleteParameter(P_IDPUBL);
		if (m_nPubLevel <= CMS_PL_ROOT)
			pcoURL->deleteParameter(P_IDLANG);
		fs << pcoURL->getURIPath();
		return RES_OK;
	}
	fs << c.URL()->getURIPath();
	return RES_OK;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream	
int CActURI::takeAction(CContext& c, sockstream& fs)
{
	if (m_nImageNr > 0)
		fs << "/cgi-bin/get_img";
	else
		m_coURIPath.takeAction(c, fs);
	stringstream coURLParametersStr;
	m_coURLParameters.takeAction(c, coURLParametersStr);
	if (coURLParametersStr.str() != "")
		fs << "?" << coURLParametersStr.str();
	return RES_OK;
}
