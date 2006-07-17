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
CActSearch, CActWith and other action classes.

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
#include "lex.h"

using std::set;
using std::cout;
using std::endl;
using std::stringstream;
using std::ostringstream;

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

#define FormPrintParamHTML(pn, p, os, c)\
{\
if (p >= 0)\
os << "<input type=hidden name=\"" << encodeHTML(pn, c.EncodeHTML()) \
<< "\" value=\"" << encodeHTML(p, c.EncodeHTML()) << "\">";\
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
inline void CAction::DEBUGAct(const char* method, const char* expl, sockstream& fs,
							  bool p_bEncodeHTML)
{
	if (*m_coDebug == true)
	{
		fs << "<!-- " << encodeHTML(actionType(), p_bEncodeHTML) << "."
				<< encodeHTML(method, p_bEncodeHTML) << ": "
				<< encodeHTML(expl, p_bEncodeHTML) << " -->\n";
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
		{
			fs << "<!-- taking action "
					<< encodeHTML((*al_i)->actionType(), c.EncodeHTML()) << " -->\n";
		}
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
			if (c.EncodeHTML())
			{
				fs << "<font color=red><h3>ERROR: " << encodeHTML(rcoEx.what(), true)
						<< " in publication " << encodeHTML(coAlias, true) << ", language id: "
						<< c.Language() << "</h3></font>" << endl;
			}
			else
			{
				fs << "ERROR: " << rcoEx.what() << " in publication " << coAlias
						<< ", language id: " << c.Language() << endl;

			}
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
		{
			fs << "<!-- action " << encodeHTML((*al_i)->actionType(), c.EncodeHTML())
					<< " result: " << err << " -->\n";
		}
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
		if (p_pchFormat[nIndex] == 0)
			break;
		if (p_pchFormat[nIndex] != '%')
			continue;
		bNeedFormat = true;
		nIndex++;
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
			char* pchBuf = SQLEscapeString(p_pchFormat + nStartFormat, nFormatLen);
			coQuery << "DATE_FORMAT('" << pchDate << "', '" << pchBuf << "')";
			delete []pchBuf;
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
		char* pchBuf = SQLEscapeString(p_pchFormat + nStartFormat, nIndex - nStartFormat);
		coQuery << "DATE_FORMAT('" << pchDate << "', '" << pchBuf << "')";
		delete []pchBuf;
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

// obfuscateString: obfuscate the given string so it can't be picked up by spammers
// Returns obfuscated string
// Parameters:
//		const string& p_rcoStr - string to obfuscate
string CAction::obfuscateString(const string& p_rcoStr)
{
	stringstream coBuf;
	string::const_iterator coIt;
	for (coIt = p_rcoStr.begin(); coIt != p_rcoStr.end(); ++coIt)
	{
		coBuf << "&#" << (int) *coIt << ";";
	}
	return coBuf.str();
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
	if (case_comp(m_coParam.attribute(), "off") == 0)
	{
		c.SetLanguage(-1);
		return RES_OK;
	}
	char* pchLang = SQLEscapeString(m_coParam.attribute().c_str(),
									m_coParam.attribute().length());
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
		fs << "<!-- Included file (" << encodeHTML(tpl_path, c.EncodeHTML())
				<< ") does not exist. -->" << endl;
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
	const Topic* pcoTopic = NULL;
	if (case_comp(param.attribute(), "identifier") == 0)
	{
		try {
			pcoTopic = Topic::topic((lint)Integer(param.value()));
		}
		catch (const InvalidValue &rcoEx)
		{
			return ERR_NODATA;
		}
	}
	else
	{
		pcoTopic = Topic::topic(param.value());
	}
	if (pcoTopic == NULL)
	{
		return ERR_NODATA;
	}
	c.SetTopic(pcoTopic->id());
	return RES_OK;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context (modified by action)
//		sockstream& fs - output stream (not used)
int CActHTMLEncoding::takeAction(CContext& c, sockstream& fs)
{
	if (case_comp(param.attribute(), "on") == 0)
	{
		c.SetEncodeHTML(true);
		return RES_OK;
	}
	if (case_comp(param.attribute(), "off") == 0)
	{
		c.SetEncodeHTML(false);
		return RES_OK;
	}
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
	insert(CMS_ST_ARTICLECOMMENT);
	insert(CMS_ST_ARTICLEIMAGE);
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
	{
		table = "Sections";
	}
	else if (modifier == CMS_ST_ISSUE)
	{
		table = "Issues";
	}
	else
	{
		return ERR_NODATA;
	}
	string w = "";
	if (modifier == CMS_ST_ISSUE && c.Access() != A_ALL)
	{
		w = "Published = 'Y'";
	}
	CParameterList::iterator pl_i;
	for (pl_i = mod_param.begin(); pl_i != mod_param.end(); ++pl_i)
	{
		char* pchVal = SQLEscapeString((*pl_i)->value().c_str(), (*pl_i)->value().length());
		if (pchVal == NULL)
		{
			return ERR_NOMEM;
		}
		AppendConstraint(w, (*pl_i)->attribute(), (*pl_i)->opSymbol(), pchVal, "and");
		delete []pchVal;
	}
	stringstream buf;
	CheckFor("IdPublication", c.Publication(), buf, w);
	if (modifier == CMS_ST_SECTION)
	{
		CheckFor("NrIssue", c.Issue(), buf, w);
	}
	buf.str("");
	buf << "IdLanguage = " << c.Language();
	if (w != "")
	{
		w += " and ";
	}
	w += buf.str();
	if (w.length() > 0)
	{
		s += string(" where ") + w;
	}
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
	buf << "Articles.IdLanguage = " << c.Language();
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
	if (!c.MultiplePublicationSearch())
	{
		CheckFor("Articles.IdPublication", c.Publication(), buf, w);
	}
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
	s = "";
	CParameterList::iterator pl_i;
	if (modifier == CMS_ST_ISSUE || modifier == CMS_ST_ARTICLE)
	{
		string table = (modifier == CMS_ST_ARTICLE ? "Articles." : "");
		for (pl_i = ord_param.begin(); pl_i != ord_param.end(); ++pl_i)
		{
			s += string(s != "" ? ", " : " order by ") + table + (*pl_i)->attribute() + " ";
			if ((*pl_i)->spec().length() > 0)
			{
				s += (*pl_i)->spec();
			}
		}
		if (modifier == CMS_ST_ARTICLE)
		{
			if (ord_param.empty())
			{
				s = " order by Articles.NrIssue desc, Articles.NrSection asc";
			}
			s += ", Articles.ArticleOrder asc";
		}
		s += string(s != "" ? ", " : " order by ") + table + "IdLanguage desc";
	}
	if (modifier == CMS_ST_SECTION)
	{
		s = " order by Number asc, IdLanguage desc";
	}
	if (modifier == CMS_ST_SUBTOPIC)
	{
		s = " order by Id asc";
	}
	if (modifier == CMS_ST_SEARCHRESULT)
	{
		for (pl_i = ord_param.begin(); pl_i != ord_param.end(); ++pl_i)
		{
			s += string(s != "" ? ", " : " order by ")
					+ string("Articles.") + (*pl_i)->attribute() + " ";
			if ((*pl_i)->spec().length() > 0)
			{
				s += (*pl_i)->spec();
			}
		}
		s += (ord_param.empty() ? " order by " : ", ");
		s += "ArticleIndex.IdPublication asc, ArticleIndex.NrIssue desc, "
				"ArticleIndex.NrSection asc, Articles.ArticleOrder asc, "
				"ArticleIndex.IdLanguage desc";
	}
	if (modifier == CMS_ST_ARTICLEATTACHMENT)
	{
		s = " order by att.file_name asc, att.extension asc";
	}
	if (modifier == CMS_ST_ARTICLECOMMENT)
	{
		for (pl_i = ord_param.begin(); pl_i != ord_param.end(); ++pl_i)
		{
			s += string(s != "" ? ", " : " order by ") + (*pl_i)->attribute() + " ";
			if ((*pl_i)->spec().length() > 0)
			{
				s += (*pl_i)->spec();
			}
		}
		if (ord_param.empty())
		{
			s = " order by pm.thread_order asc";
		}
	}
	if (modifier == CMS_ST_ARTICLEIMAGE)
	{
		s = " order by Number asc";
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
			case CMS_ST_ARTICLECOMMENT:
				if (!c.ArticleCommentEnabled())
				{
					return ERR_NODATA;
				}
				table = "ArticleComments as ac left join phorum_messages as pm "
						"on ac.fk_comment_id = pm.thread";
				buf << " where pm.status > 0 and parent_id != 0 "
						"and ac.fk_article_number = " << lc.Article();
				where = buf.str();
				break;
			case CMS_ST_ARTICLEIMAGE:
				table = "ArticleImages";
				buf << " where NrArticle = " << lc.Article();
				where = buf.str();
				break;
		}
		WriteOrdParam(order);
		WriteLimit(limit, lc);
		
		string coLanguageId;
		switch (modifier) {
			case CMS_ST_ISSUE:
			case CMS_ST_SECTION:
				fields = "select Number, IdLanguage, IdPublication";
				if (modifier == CMS_ST_SECTION)
					fields += ", NrIssue";
				break;
			case CMS_ST_ARTICLE:
				fields = "select Number, Articles.IdLanguage, IdPublication"
						", Articles.NrIssue, Articles.NrSection";
				break;
			case CMS_ST_SEARCHRESULT:
				coLanguageId = (string)Integer(c.Language());
				fields = "select NrArticle, " + coLanguageId +  ", Articles.IdPublication, "
						"Articles.NrIssue, Articles.NrSection, MIN(ABS(Articles.IdLanguage - "
						+ coLanguageId + ")), MIN(Articles.IdLanguage - 1)";
				break;
			case CMS_ST_ARTICLETOPIC:
				fields = "select distinct TopicId";
				break;
			case CMS_ST_SUBTOPIC:
				fields = "select distinct Id";
				break;
			case CMS_ST_ARTICLEATTACHMENT:
				fields = "select att.id, att.extension";
				break;
			case CMS_ST_ARTICLECOMMENT:
				fields = "select pm.message_id";
				break;
			case CMS_ST_ARTICLEIMAGE:
				fields = "select IdImage";
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
				if (modifier != CMS_ST_SEARCHRESULT)
				{
					lc.SetLanguage(strtol(row[1], 0, 10));
				}
				else
				{
					Integer coCurrentLangDiff(row[5]);
					Integer coEnglishDiff(row[6]);
					if ((lint)coCurrentLangDiff == 0)
					{
						lc.SetLanguage(strtol(row[1], 0, 10));
					}
					else
					{
						lc.SetLanguage(1 + (lint)coEnglishDiff);
					}
				}
				lc.SetPublication(strtol(row[2], 0, 10));
				if (modifier != CMS_ST_ISSUE)
				{
					lc.SetIssue(strtol(row[3], 0, 10));
				}
				if (modifier == CMS_ST_ARTICLE || modifier == CMS_ST_SEARCHRESULT)
				{
					lc.SetSection(strtol(row[4], 0, 10));
				}
				SetContext(lc, strtol(row[0], 0, 10));
			}
			if (modifier == CMS_ST_ARTICLEATTACHMENT)
			{
				lc.SetAttachment(strtol(row[0], 0, 10));
				lc.SetAttachmentExtension(row[1]);
			}
			if (modifier == CMS_ST_ARTICLECOMMENT)
			{
				lc.SetArticleCommentId(strtol(row[0], 0, 10));
			}
			if (modifier == CMS_ST_ARTICLETOPIC || modifier == CMS_ST_SUBTOPIC)
			{
				lc.SetTopic(strtol(row[0], 0, 10));
			}
			if (modifier == CMS_ST_ARTICLEIMAGE)
			{
				lc.SetImage(strtol(row[0], 0, 10));
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
	ostringstream coOut;
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
		{
			return ERR_NOPARAM;
		}
		if (image_nr > 0)
		{
			URLPrintParam(P_NRIMAGE, image_nr, coOut, first);
		}
		if (image_nr == 0)
		{
			if (c.Image() <= 0)
			{
				return ERR_NODATA;
			}
			URLPrintParam(P_NRIMAGE, c.Image(), coOut, first);
		}
		URLPrintParam(P_NRARTICLE, c.Article(), coOut, first);
		fs << encodeHTML(coOut.str(), c.EncodeHTML());
		return 0;
	}
	if (c.Language() < 0 && c.Publication() < 0 && c.Issue() < 0
		   && c.Section() < 0 && c.Article() < 0)
	{
		return ERR_NOPARAM;
	}
	CURL* pcoURL = (fromstart ? c.DefURL()->clone() : c.URL()->clone());
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

	// delete preview comment fields - we don't want to pass them along
	pcoURL->deleteParameter("previewComment");
	pcoURL->deleteParameter("CommentReaderEMail");
	pcoURL->deleteParameter("CommentSubject");
	pcoURL->deleteParameter("CommentContent");

	// read the URL query string
	string coURL = pcoURL->getQueryString();
	coOut << (first ? "" : "&") << coURL;
	first = coURL == "";

	if (m_bArticleComment && c.ArticleCommentId() > 0)
	{
		coOut << (first ? "" : "&") << "acid=" << c.ArticleCommentId();
		first = false;
	}
	URLPrintParam(P_TOPIC_ID, (fromstart ? c.DefTopic() : c.Topic()), coOut, first);
	if (pcoURL->needTemplateParameter())
	{
		URLPrintNParam(P_TEMPLATE_ID, m_coTemplate, coOut, first);
	}
	if (m_nPubLevel > CMS_PL_ARTICLE)
	{
		PrintSubtitlesURL(c, coOut, first);
	}
	if (m_nPubLevel < CMS_PL_ARTICLE)
	{
		delete pcoURL;
	}
	if (c.Level() == CLV_ROOT)
	{
		fs << encodeHTML(coOut.str(), c.EncodeHTML());
		return RES_OK;
	}

	if (c.LMode() == LM_PREV)
	{
		if (c.Level() == CLV_ISSUE_LIST)
			URLPrintParam(P_ILSTART, (ResetList(CLV_ISSUE_LIST) ? 0 : c.IPrevStart()), coOut,
						  first);
		if (c.Level() == CLV_SECTION_LIST)
			URLPrintParam(P_SLSTART, (ResetList(CLV_SECTION_LIST) ? 0 : c.SPrevStart()),
			              coOut, first);
		if (c.Level() == CLV_ARTICLE_LIST)
			URLPrintParam(P_ALSTART, (ResetList(CLV_ARTICLE_LIST) ? 0 : c.APrevStart()),
			              coOut, first);
		if (c.Level() == CLV_SEARCHRESULT_LIST)
			URLPrintParam(P_SRLSTART, (ResetList(CLV_SEARCHRESULT_LIST) ? 0 : c.SrPrevStart()),
			              coOut, first);
	}
	else if (c.LMode() == LM_NEXT)
	{
		if (c.Level() == CLV_ISSUE_LIST)
			URLPrintNParam(P_ILSTART, (ResetList(CLV_ISSUE_LIST) ? 0 : c.INextStart()), coOut,
						   first);
		if (c.Level() == CLV_SECTION_LIST)
			URLPrintNParam(P_SLSTART, (ResetList(CLV_SECTION_LIST) ? 0 : c.SNextStart()),
			               coOut, first);
		if (c.Level() == CLV_ARTICLE_LIST)
			URLPrintNParam(P_ALSTART, (ResetList(CLV_ARTICLE_LIST) ? 0 : c.ANextStart()),
			               coOut, first);
		if (c.Level() == CLV_SEARCHRESULT_LIST)
			URLPrintNParam(P_SRLSTART, (ResetList(CLV_SEARCHRESULT_LIST) ? 0 : c.SrNextStart()),
			               coOut, first);
	}
	if (c.LMode() == LM_NORMAL || (c.Level() != CLV_ISSUE_LIST && c.Level() != CLV_ROOT))
		URLPrintNParam(P_ILSTART, (ResetList(CLV_ISSUE_LIST) ? 0 : c.IListStart()), coOut,
					   first);
	if (c.LMode() == LM_NORMAL || (c.Level() != CLV_SECTION_LIST && c.Level() != CLV_ROOT))
		URLPrintNParam(P_SLSTART, (ResetList(CLV_SECTION_LIST) ? 0 : c.SListStart()), coOut,
					   first);
	if (c.LMode() == LM_NORMAL || (c.Level() != CLV_ARTICLE_LIST && c.Level() != CLV_ROOT))
		URLPrintNParam(P_ALSTART, (ResetList(CLV_ARTICLE_LIST) ? 0 : c.AListStart()), coOut,
					   first);
	if (c.Level() == CLV_SEARCHRESULT_LIST && (c.LMode() == LM_PREV || c.LMode() == LM_NEXT))
	{
		if (!first)
			coOut << "&";
		const char* pchEscKw = EscapeURL(c.StrKeywords());
		coOut << "search=search&SearchKeywords=" << pchEscKw
		<< (c.SearchAnd() ? "&SearchMode=on" : "") << "&SearchLevel=" << c.SearchLevel();
		delete []pchEscKw;
	}
	fs << encodeHTML(coOut.str(), c.EncodeHTML());
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
	if (articleComment)
	{
		FormPrintParam("acid", c.ArticleCommentId(), fs);
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
	insert(CMS_ST_ARTICLECOMMENT);
	insert(CMS_ST_CAPTCHA);
}

CPrintModifiers CActPrint::s_coModifiers;

// BlobField: return 0 if the field from the given article type is blob type
// Parameters:
//		const char* pchArticleType - article type name
//		const char* pchField - article type field
int CActPrint::BlobField(const char* pchArticleType, const char* pchField)
{
	int result = -1;
	string coQuery = string("desc X") + pchArticleType + " " + pchField;
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

// IsDateField: return true if the field from the given article type is date type
// Parameters:
//		const char* pchArticleType - article type name
//		const char* pchField - article type field
bool CActPrint::IsDateField(const char* pchArticleType, const char* pchField)
{
	const CStatement* pcoPrintStatement = CLex::findSt(string("Article"));
	if (pcoPrintStatement == NULL)
	{
		return false;
	}
	try {
		SafeAutoPtr<CPairAttrType> pcoAttrPair(NULL);
		pcoAttrPair.reset(pcoPrintStatement->findTypeAttr(pchField, pchArticleType, CMS_CT_PRINT));
		TDataType nAttributeType = (*pcoAttrPair).first->dataType();
		return (nAttributeType == CMS_DT_DATE || nAttributeType == CMS_DT_DATETIME
				|| nAttributeType == CMS_DT_TIME);
	}
	catch (...) {
		return false;
	}
}

// IsTopicField: return true if the field from the given article type is topic type
// Parameters:
//		const char* pchArticleType - article type name
//		const char* pchField - article type field
bool CActPrint::IsTopicField(const char* pchArticleType, const char* pchField)
{
	const CStatement* pcoPrintStatement = CLex::findSt(string("Article"));
	if (pcoPrintStatement == NULL)
	{
		return false;
	}
	try {
		SafeAutoPtr<CPairAttrType> pcoAttrPair(NULL);
		pcoAttrPair.reset(pcoPrintStatement->findTypeAttr(pchField, pchArticleType, CMS_CT_PRINT));
		TDataType nAttributeType = (*pcoAttrPair).first->dataType();
		return (nAttributeType == CMS_DT_TOPIC);
	}
	catch (...) {
		return false;
	}
}

// IsPEntity: returns true if it finds a <P> HTML entity at the given position
// Parameters:
//		string::const_iterator& p_rcoCurrent - the position in the string where to search
//			for the <P> HTML entity
//		const string::const_iterator& p_rcoEnd - the end of the string
bool CActPrint::IsPEntity(string::const_iterator& p_rcoCurrent,
						  const string::const_iterator& p_rcoEnd)
{
	if (*p_rcoCurrent != '<')
	{
		return false;
	}
	do {
		++p_rcoCurrent;
	} while(p_rcoCurrent != p_rcoEnd && *p_rcoCurrent >= 0 && *p_rcoCurrent <= ' ');
	if (p_rcoCurrent == p_rcoEnd)
	{
		return false;
	}
	char chFirstChar = *p_rcoCurrent;
	char chSecondChar = *(++p_rcoCurrent);
	if (p_rcoCurrent == p_rcoEnd)
	{
		return false;
	}
	if (tolower(chFirstChar) == 'p' && (chSecondChar == '/' || chSecondChar == '>'
		   || (chSecondChar >= 0 && chSecondChar <= ' ')))
	{
		for (; p_rcoCurrent != p_rcoEnd && *p_rcoCurrent != '>'; ++p_rcoCurrent);
		return true;
	}
	return false;
}

// IsBREntity: returns true if it finds a <BR> HTML entity at the given position
// Parameters:
//		string::const_iterator& p_rcoCurrent - the position in the string where to search
//			for the <BR> HTML entity
//		const string::const_iterator& p_rcoEnd - the end of the string
bool CActPrint::IsBREntity(string::const_iterator& p_rcoCurrent,
						   const string::const_iterator& p_rcoEnd)
{
	if (*p_rcoCurrent != '<')
	{
		return false;
	}
	do {
		++p_rcoCurrent;
	} while(p_rcoCurrent != p_rcoEnd && *p_rcoCurrent >= 0 && *p_rcoCurrent <= ' ');
	if (p_rcoCurrent == p_rcoEnd)
	{
		return false;
	}
	char chFirstChar = *p_rcoCurrent;
	char chSecondChar = *(++p_rcoCurrent);
	if (p_rcoCurrent == p_rcoEnd)
	{
		return false;
	}
	char chThirdChar = *(++p_rcoCurrent);
	if (tolower(chFirstChar) == 'b' && tolower(chSecondChar) == 'r'
		   && (chThirdChar == '/' || chThirdChar == '>'
		   || (chThirdChar >= 0 && chThirdChar <= ' ')))
	{
		for (; p_rcoCurrent != p_rcoEnd && *p_rcoCurrent != '>'; ++p_rcoCurrent);
		return true;
	}
	return false;
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
		if (format != "")
			fs << encodeHTML(dateFormat(pchData, format.c_str(), c.Language()), c.EncodeHTML());
		else
			fs << encodeHTML(pchData, c.EncodeHTML());
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
		fs << encodeHTML(row[0], c.EncodeHTML());
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
		fs << encodeHTML(row[0], c.EncodeHTML());
		return RES_OK;
	}
	if (modifier == CMS_ST_SEARCH)
	{
		if (case_comp(attr, "Keywords") == 0)
		{
			fs << encodeHTML(c.StrKeywords(), c.EncodeHTML());
			return RES_OK;
		}
		buf << "select Message from Errors where Number = " << c.SearchRes() << " and "
		        "(IdLanguage = " << c.Language() << " or IdLanguage = 1) order by IdLanguage desc";
		SQLQuery(&m_coSql, buf.str().c_str());
		res = mysql_store_result(&m_coSql);
		CheckForRows(*res, 1);
		row = mysql_fetch_row(*res);
		fs << encodeHTML(row[0], c.EncodeHTML());
		return RES_OK;
	}
	if (modifier == CMS_ST_SUBTITLE)
	{
		fs << encodeHTML(c.CurrentSubtitle(), c.EncodeHTML());
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
		fs << encodeHTML(row[0], c.EncodeHTML());
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
			fs << encodeHTML(pcoTopic->name(row[0]), c.EncodeHTML());
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
		fs << encodeHTML(row[0], c.EncodeHTML());
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
		fs << encodeHTML(row[0], c.EncodeHTML());
		return RES_OK;
	}
	if (modifier == CMS_ST_ARTICLECOMMENT)
	{
		if (!c.ArticleCommentEnabled() || c.ArticleComment() == NULL)
		{
			return ERR_NODATA;
		}
		if (case_comp(attr, "Identifier") == 0)
		{
			fs << c.ArticleComment()->getMessageId();
		}
		else if (case_comp(attr, "ReaderEMail") == 0)
		{
			fs << encodeHTML(c.ArticleComment()->getEmail(), c.EncodeHTML());
		}
		else if (case_comp(attr, "ReaderEMailObfuscated") == 0)
		{
			fs << CAction::obfuscateString(c.ArticleComment()->getEmail());
		}
		else if (case_comp(attr, "SubmitDate") == 0)
		{
			string coSubmitTime = c.ArticleComment()->getDateStamp();
			if (format != "")
			{
				fs << encodeHTML(dateFormat(coSubmitTime.c_str(), format.c_str(), c.Language()),
								 c.EncodeHTML());
			}
			else
			{
				fs << encodeHTML(coSubmitTime, c.EncodeHTML());
			}
		}
		else if (case_comp(attr, "Subject") == 0)
		{
			fs << encodeHTML(c.ArticleComment()->getSubject(), c.EncodeHTML());
		}
		else if (case_comp(attr, "Content") == 0)
		{
			fs << encodeHTML(c.ArticleComment()->getBody(), c.EncodeHTML());
		}
		else if (case_comp(attr, "ReaderEMailPreview") == 0)
		{
			fs << encodeHTML(c.URL()->getValue("CommentReaderEMail"), c.EncodeHTML());
		}
		else if (case_comp(attr, "ReaderEMailPreviewObfuscated") == 0)
		{
			fs << CAction::obfuscateString(c.URL()->getValue("CommentReaderEMail"));
		}
		else if (case_comp(attr, "SubjectPreview") == 0)
		{
			fs << encodeHTML(c.URL()->getValue("CommentSubject"), c.EncodeHTML());
		}
		else if (case_comp(attr, "ContentPreview") == 0)
		{
			fs << encodeHTML(c.URL()->getValue("CommentContent"), c.EncodeHTML());
		}
		else if (case_comp(attr, "Count") == 0)
		{
			fs << CArticleComment::ArticleCommentCount(c.Article(), c.Language());
		}
		else if (case_comp(attr, "Level") == 0)
		{
			fs << c.ArticleComment()->getLevel();
		}
		else if (case_comp(attr, "SubmitError") == 0)
		{
			buf << "select Message from Errors where Number = " << c.SubmitArticleCommentResult()
					<< " and IdLanguage = " << c.Language();
			SQLQuery(&m_coSql, buf.str().c_str());
			res = mysql_store_result(&m_coSql);
			CheckForRows(*res, 1);
			row = mysql_fetch_row(*res);
			fs << encodeHTML(row[0], c.EncodeHTML());
		}
		else if (case_comp(attr, "SubmitErrorNo") == 0)
		{
			lint nResult = c.SubmitArticleCommentResult();
			if (nResult > 0)
			{
				fs << nResult;
			}
		}
		return RES_OK;
	}
	if (modifier == CMS_ST_CAPTCHA)
	{
		fs << "/include/captcha/image.php";
		return RES_OK;
	}
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
				string coTpl = CPublication::getIssueTemplate(c.Language(),
						c.Publication(), c.Issue(), &m_coSql);
				fs << "/look/" << encodeHTML(coTpl, c.EncodeHTML());
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
				string coTpl = CPublication::getSectionTemplate(c.Language(), c.Publication(), 
						c.Issue(), c.Section(), &m_coSql);
				fs << "/look/" << encodeHTML(coTpl, c.EncodeHTML());
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
				string coTpl = CPublication::getArticleTemplate(c.Language(), c.Publication(), 
						c.Issue(), c.Section(), &m_coSql);
				fs << "/look/" << encodeHTML(coTpl, c.EncodeHTML());
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
		coQuery = string("select ") + attr + " from X" + row[0] + " where NrArticle = " + row[1]
		          + " and IdLanguage = " + row[2];
		DEBUGAct("takeAction()", coQuery.c_str(), fs);
		SQLRealQuery(&m_coSql, coQuery.c_str(), coQuery.length());
		StoreResult(&m_coSql, res2);
		CheckForRows(*res2, 1);
		FetchRow(*res2, row2);
		ulint* lengths = mysql_fetch_lengths(*res2);
		if (BlobField(row[0], attr.c_str()) == 0)
		{
			cparser.setDebug(*m_coDebug);
			cparser.reset(row2[0], lengths[0]);
			if (m_nParagraphNumber <= 0)
			{
				cparser.parse(c, fs, &m_coSql, c.StartSubtitle(), c.AllSubtitles(), true);
			}
			else
			{
				ostringstream coOut;
				cparser.parse(c, coOut, &m_coSql, c.StartSubtitle(), c.AllSubtitles(), true);
				printParagraph(coOut.str(), fs, m_nParagraphNumber);
			}
		}
		else if (IsDateField(row[0], attr.c_str() + 1) && format != "")
		{
			string coDate(row2[0], lengths[0]);
			fs << encodeHTML(dateFormat(coDate.c_str(), format.c_str(), c.Language()),
							 c.EncodeHTML());
		}
		else if (IsTopicField(row[0], attr.c_str() + 1))
		{
			const Topic* pcoTopic = Topic::topic(atol(row2[0]));
			if (pcoTopic == NULL)
			{
				return -1;
			}
			buf.str("");
			buf << "select Code from Languages where id = " << c.Language();
			SQLQuery(&m_coSql, buf.str().c_str());
			res = mysql_store_result(&m_coSql);
			CheckForRows(*res, 1);
			row = mysql_fetch_row(*res);
			fs << encodeHTML(pcoTopic->name(row[0]), c.EncodeHTML());
		}
		else
		{
			string coStr(row2[0], lengths[0]);
			fs << encodeHTML(coStr, c.EncodeHTML());
		}
	}
	else
	{
		if (format != "")
			fs << encodeHTML(dateFormat(row[0], format.c_str(), c.Language()), c.EncodeHTML());
		else
			fs << encodeHTML(row[0], c.EncodeHTML());
	}
	return RES_OK;
	TK_CATCH_ERR
}

bool CActPrint::isParagraphStart(string::const_iterator& p_rcoCurrent,
								 const string::const_iterator& p_rcoEnd,
								 string::const_iterator& p_rcoParagraphStart)
{
	if (*p_rcoCurrent != '<')
	{
		return false;
	}
	string::const_iterator p_rcoMyCurrent = p_rcoCurrent;
	if (CActPrint::IsPEntity(p_rcoMyCurrent, p_rcoEnd))
	{
		p_rcoParagraphStart = p_rcoCurrent;
		p_rcoCurrent = p_rcoMyCurrent;
		return true;
	}
	p_rcoMyCurrent = p_rcoCurrent;
	if (CActPrint::IsBREntity(p_rcoMyCurrent, p_rcoEnd))
	{
		for (; p_rcoMyCurrent != p_rcoEnd && *p_rcoMyCurrent != '<'; ++p_rcoMyCurrent);
		if (CActPrint::IsBREntity(p_rcoMyCurrent, p_rcoEnd))
		{
			p_rcoParagraphStart = p_rcoCurrent;
			p_rcoCurrent = p_rcoMyCurrent;
			return true;
		}
	}
	return false;
}

// printParagraph: prints only the paragraph identifier by the number "p_nParagraphNumber"
//		to the output stream
// Parameters:
//		const string& p_rcoText - the text to be printed
//		sockstream& p_rcoStream - output stream
//		int p_nParagraphNumber - the number of the paragraph to be printed
void CActPrint::printParagraph(const string& p_rcoText, sockstream& p_rcoStream,
							   int p_nParagraphNumber)
{
	if (p_nParagraphNumber <= 0)
	{
		p_rcoStream << p_rcoText;
		return;
	}
	int nCurrentParagraph = 1;
	string::const_iterator coParagraphStart = p_rcoText.begin();
	string::const_iterator coNextParagraphStart = p_rcoText.begin();
	string::const_iterator coFoundParagraph;
	string::const_iterator coCurrent = p_rcoText.begin();
	do {
		for (; coCurrent != p_rcoText.end() && *coCurrent != '<'; ++coCurrent);
		if (coCurrent == p_rcoText.end()
					 || CActPrint::isParagraphStart(coCurrent, p_rcoText.end(),
				coFoundParagraph))
		{
			coParagraphStart = coNextParagraphStart;
			coNextParagraphStart = coCurrent == p_rcoText.end() ?
					p_rcoText.end() : coFoundParagraph;
			nCurrentParagraph++;
		}
		else
		{
			++coCurrent;
		}
	} while (coCurrent != p_rcoText.end() && nCurrentParagraph <= p_nParagraphNumber);
	if (coParagraphStart != coNextParagraphStart)
	{
		p_rcoStream << p_rcoText.substr(distance(p_rcoText.begin(), coParagraphStart),
										distance(coParagraphStart, coNextParagraphStart));
	}
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
	insert(CMS_ST_ARTICLECOMMENT);
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
	    << c.Article() << " and IdLanguage = " << c.Language();
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
	return c.IsSubs(c.Publication(), c.Section(), c.Language());
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
		if ((run == 0 && !m_bNegated) || (run == 1 && m_bNegated))
			runActions(block, c, fs);
		else if ((run == 1 && !m_bNegated) || (run == 0 && m_bNegated))
			runActions(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == CMS_ST_ARTICLECOMMENT)
	{
		run = -1;
		if (case_comp(param.attribute(), "Enabled") == 0)
		{
			run = c.ArticleCommentEnabled() ? 0 : 1;
		}
		else if (!c.ArticleCommentEnabled() || c.ArticleComment() == NULL)
		{
			return ERR_NODATA;
		}
		if (case_comp(param.attribute(), "Defined") == 0)
		{
			run = c.ArticleCommentId() > 0 ? 0 : 1;
		}
		if (case_comp(param.attribute(), "Submitted") == 0)
		{
			run = c.SubmitArticleCommentEvent() ? 0 : 1;
		}
		if (case_comp(param.attribute(), "SubmitError") == 0 && c.SubmitArticleCommentEvent())
		{
			run = c.SubmitArticleCommentResult() != 0 ? 0 : 1;
		}
		if (case_comp(param.attribute(), "Preview") == 0)
		{
			run = c.URL()->getValue("previewComment") != "" ? 0 : 1;
		}
		if (case_comp(param.attribute(), "Rejected") == 0 && c.SubmitArticleCommentEvent())
		{
			run = c.SubmitArticleCommentResult() == ACERR_REJECTED ? 0 : 1;
		}
		if (case_comp(param.attribute(), "PublicModerated") == 0)
		{
			run = CArticleComment::PublicModerated(c.Publication()) ? 0 : 1;
		}
		if (case_comp(param.attribute(), "PublicAllowed") == 0)
		{
			run = CArticleComment::PublicAllowed(c.Publication()) ? 0 : 1;
		}
		if (case_comp(param.attribute(), "SubscribersModerated") == 0)
		{
			run = CArticleComment::SubscribersModerated(c.Publication()) ? 0 : 1;
		}
		if (case_comp(param.attribute(), "CAPTCHAEnabled") == 0)
		{
			run = CArticleComment::CAPTCHAEnabled(c.Publication()) ? 0 : 1;
		}
		if ((run == 0 && !m_bNegated) || (run == 1 && m_bNegated))
		{
			runActions(block, c, fs);
		}
		else if ((run == 1 && !m_bNegated) || (run == 0 && m_bNegated))
		{
			runActions(sec_block, c, fs);
		}
		return RES_OK;
	}
	else if (modifier == CMS_ST_USER)
	{
		run = -1;
		if (case_comp(param.attribute(), "BlockedFromComments") == 0 && c.ArticleCommentEnabled())
			run = CArticleComment::IsUserBlocked(c.User()) ? 0 : 1;
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
	{
		return -1;
	}
	if (modifier != CMS_ST_LANGUAGE && modifier != CMS_ST_PUBLICATION && param.attrType() == "")
	{
		SetNrField("IdPublication", c.Publication(), buf, w);
	}
	if (need_lang)
	{
		buf.str("");
		buf << "(IdLanguage = " << c.Language() << " or IdLanguage = 1)";
		w += (w != "" ? string(" and ") : string("")) + buf.str();
	}
	string coQuery = string("select ") + field + " from " + tables;
	if (w.length())
	{
		coQuery += string(" where ") + w;
	}
	if (need_lang)
	{
		coQuery += " order by IdLanguage desc";
	}
	DEBUGAct("takeAction()", coQuery.c_str(), fs);
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[0] == NULL)
	{
		return ERR_NODATA;
	}
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
	{
		run_first = row[0] == value;
	}
	run_first = m_bNegated ? !run_first : run_first;
	if (run_first)
	{
		runActions(block, c, fs);
	}
	else
	{
		runActions(sec_block, c, fs);
	}
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
	fs << encodeHTML(dateFormat(row[0], format, c.Language()), c.EncodeHTML());
	return RES_OK;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream
int CActText::takeAction(CContext& c, sockstream& fs)
{
	if (m_bInsertSpace)
	{
		fs << ' ';
	}
	fs.write(text, text_len);
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
		{
			return -1;
		}
		c.SetSubsType((row[0][0] == 'T') ? ST_TRIAL : ST_PAID);
		c.SetUserInfo("CountryCode", row[1]);
		buf.str("");
	}
	
	// Compute the subscription length; search for the default subscription intervals in
	// the countries subscriptions intervals table first
	const char* pchSubsTypeTime = c.SubsType() == ST_TRIAL ? "TrialTime" : "PaidTime";
	buf << "select " << pchSubsTypeTime << " from SubsDefTime, Users where IdPublication = "
			<< c.Publication() << " and SubsDefTime.CountryCode = Users.CountryCode and "
			"Users.Id = " << c.User();
	SQLQuery(&m_coSql, buf.str().c_str());
	StoreResult(&m_coSql, res);
	MYSQL_ROW row;
	if ((row = mysql_fetch_row(*res)) != NULL)
	{
		c.SetSubsTimeUnits(atol(row[0]));
	}
	buf.str("");
	
	buf << "select Currency, " << pchSubsTypeTime << ", UnitCost, UnitCostAllLang from "
			"Publications where Id = " << c.Publication();
	SQLQuery(&m_coSql, buf.str().c_str());
	res = mysql_store_result(&m_coSql);
	CheckForRows(*res, 1);
	row = mysql_fetch_row(*res);
	string currency = row[0];
	// If the default subscription interval was not found in the countries subscriptions
	// intervals table read it from the publications table
	if (c.SubsTimeUnits() == 0)
	{
		c.SetSubsTimeUnits(atol(row[1]));
	}
	double nUnitCost = atof(row[2]);
	double nUnitCostAllLang = atof(row[3]);
	buf.str("");
	
	buf << "select * from Sections where IdPublication = " << c.Publication()
			<< " group by Number";
	SQLQuery(&m_coSql, buf.str().c_str());
	res = mysql_store_result(&m_coSql);
	CheckForRows(*res, 1);
	lint nos = mysql_num_rows(*res);
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
	string coSubsType = (lc.SubsType() == ST_TRIAL ? "trial" : "paid");
	fs << "<form action=\"" << encodeHTML(pcoURL->getURIPath(), lc.EncodeHTML())
			<< "\" name=\"subscription_form\" method=\"POST\">\n"
			<< "<input type=\"hidden\" name=\"" << P_TEMPLATE_ID
			<< "\" value=\"" << m_nTemplateId << "\">\n"
			<< "<input type=\"hidden\" name=\"" << P_SUBSTYPE << "\" value=\""
			<< encodeHTML(coSubsType, lc.EncodeHTML()) << "\">\n"
			<< "<input type=\"hidden\" name=\"tx_subs\" value=\"" << lc.SubsTimeUnits() << "\">\n"
			<< "<input type=\"hidden\" name=\"nos\" value=\"" << nos << "\">\n"
			<< "<input type=\"hidden\" name=\"unitcost\" value=\"" << nUnitCost << "\">\n"
			<< "<input type=\"hidden\" name=\"unitcostalllang\" value=\""
			<< nUnitCostAllLang << "\">" << endl;
	fs << "<script>\n"
			"function ToggleElementEnabled(id) {\n"
			"	if (document.getElementById(id).disabled) {\n"
			"		document.getElementById(id).disabled = false\n"
			"	} else {\n"
			"		document.getElementById(id).disabled = true\n"
			"	}\n"
			"}\n"
			"</script>\n";
	runActions(block, lc, fs);
	if (lc.SubsType() == ST_PAID && total != "")
	{
		fs << encodeHTML(total, lc.EncodeHTML())
				<< " <input type=\"text\" name=\"suma\" size=\"10\" READONLY> "
				<< encodeHTML(currency, lc.EncodeHTML()) << endl;
	}
	lc.URL()->deleteParameter(P_TEMPLATE_ID);
	lc.URL()->deleteParameter(P_SUBSTYPE);
	lc.URL()->deleteParameter("tx_subs");
	lc.URL()->deleteParameter("nos");
	lc.URL()->deleteParameter("suma");
	lc.URL()->deleteParameter("unitcost");
	lc.URL()->deleteParameter("unitcostalllang");
	lc.URL()->deleteParameter("subs_all_languages");
	fs << lc.URL()->getFormString();
	if (lc.SubsType() == ST_PAID && evaluate != "")
	{
		fs << "<p><input type=\"button\" value=\"" << encodeHTML(evaluate, lc.EncodeHTML())
				<< "\" onclick=\"update_subscription_payment()\"></p>\n";
	}
	if (by_publication)
	{
		fs << "<input type=\"hidden\" name=\"by\" value=\"publication\">\n"
				"<input type=\"hidden\" name=\"cb_subs\" value=\"0\">\n";
	}
	fs << "<p><input type=\"submit\" name=\"" P_SUBSCRIBE "\" value=\""
			<< encodeHTML(button_name, lc.EncodeHTML()) << "\"></p>\n</form>\n";
	if (lc.SubsType() == ST_PAID && total != "")
	{
		fs << "<script>\n"
				"function element_exists(object, property) {\n"
				"	for (i in object) {\n"
				"		if (object[i].name == property) {\n"
				"			return true\n"
				"		}\n"
				"	}\n"
				"	return false\n"
				"}\n"
				"function update_subscription_payment() {\n"
				"	var sum = 0\n"
				"	var i\n"
				"	var my_form = document.forms[\"subscription_form\"]\n"
				"	var subs_all_lang = false\n"
				"	var unitcost = my_form.unitcost.value\n"
				"	var lang_count = 1\n"
				"	if (element_exists(my_form.elements, \"subs_all_languages\")\n"
				"		&& my_form.subs_all_languages.checked) {\n"
				"		unitcost = my_form.unitcostalllang.value\n"
				"	} else if (element_exists(my_form.elements, \"subscription_language[]\")) {\n"
				"		lang_count = 0\n"
				"		for (i=0; i<my_form[\"subscription_language[]\"].options.length; i++) {\n"
				"			if (my_form[\"subscription_language[]\"].options[i].selected) {\n"
				"				lang_count++\n"
				"			}\n"
				"		}\n"
				"	}\n"
				"	for (i = 0; i < my_form.nos.value; i++) {\n"
				"		if (element_exists(my_form.elements, \"by\")\n"
				"			&& my_form.by.value == \"publication\") {\n"
				"			sum = parseInt(sum) + parseInt(my_form[\"tx_subs\"].value)\n"
				"			continue\n"
				"		}\n"
				"		if (!my_form[\"cb_subs[]\"][i].checked) {\n"
				"			continue\n"
				"		}\n"
				"		var section = my_form[\"cb_subs[]\"][i].value\n"
				"		var time_var_name = \"tx_subs\" + section\n"
				"		if (element_exists(my_form.elements, time_var_name)) {\n"
				"			sum = parseInt(sum) + parseInt(my_form[time_var_name].value)\n"
				"		} else if (element_exists(my_form.elements, \"tx_subs\")) {\n"
				"			sum = parseInt(sum) + parseInt(my_form[\"tx_subs\"].value)\n"
				"		}\n"
				"	}\n"
				"	my_form.suma.value = Math.round(100 * sum * unitcost * lang_count) / 100\n"
				"}\n"
				"update_subscription_payment()\n"
				"</script>\n";
	}
	else
	{
		fs << "<script>\n"
				"function update_subscription_payment() {\n"
				"}\n"
				"</script>\n";
	}
	return RES_OK;
	TK_CATCH_ERR
}

CEditModifiers::CEditModifiers()
{
	insert(CMS_ST_SUBSCRIPTION);
	insert(CMS_ST_USER);
	insert(CMS_ST_LOGIN);
	insert(CMS_ST_SEARCH);
	insert(CMS_ST_ARTICLECOMMENT);
	insert(CMS_ST_CAPTCHA);
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
			fs << "<input type=\"text\" name=\"User" << field << "\" size=\""
					<< (len > 50 ? 50 : len) << "\" maxlength=\"" << len;
			if (attrval != "")
				fs << "\" value=\"" << encodeHTML(attrval, c.EncodeHTML());
			fs << "\" " << m_coHTML << ">";
			return RES_OK;
		}
		if (field == "Password" || field == "PasswordAgain")
		{
			fs << "<input type=\"password\" name=\"User" << field
					<< "\" size=\"32\" maxlength=\"32\" " << m_coHTML << ">";
			return RES_OK;
		}
		fs << "<textarea name=\"User" << field << "\" cols=\"40\" rows=\"4\" "
				<< m_coHTML << "></textarea>";
	}
	if (modifier == CMS_ST_SUBSCRIPTION)
	{
		fs << "<input type=\"hidden\" name=\"" << P_TX_SUBS << c.Section() << "\" value=\""
				<< c.SubsTimeUnits() << "\" " << m_coHTML << ">" << c.SubsTimeUnits();
	}
	if (modifier == CMS_ST_LOGIN)
	{
		if (field == "Password")
			fs << "<input type=\"password\" name=\"Login" << field
					<< "\" maxlength=\"32\" size=\"10\" " << m_coHTML << ">";
		else
			fs << "<input type=\"text\" name=\"Login" << field
					<< "\" maxlength=\"32\" size=\"10\" " << m_coHTML << ">";
	}
	if (modifier == CMS_ST_SEARCH)
	{
		if (field == "Keywords")
		{
			fs << "<input type=\"text\" name=\"Search" << field << "\" maxlength=\"255\" "
					"size=\"" << size << "\" value=\""
					<< encodeHTML(c.StrKeywords(), c.EncodeHTML())
					<< "\" " << m_coHTML << ">";
		}
	}
	if (modifier == CMS_ST_ARTICLECOMMENT && c.ArticleCommentEnabled())
	{
		string coFieldName = string("Comment") + field;
		if (field == "Content")
		{
			fs << "<textarea name=\"" << coFieldName << "\" cols=\"40\" rows=\"4\" "
					<< m_coHTML << ">"
					<< encodeHTML(c.URL()->getValue(coFieldName), c.EncodeHTML())
					<< "</textarea>";
		}
		else
		{
			fs << "<input type=\"text\" name=\"" << coFieldName << "\" maxlength=\"255\" "
					"size=\"" << size << "\" value=\""
					<< encodeHTML(c.URL()->getValue(coFieldName), c.EncodeHTML())
					<< "\" " << m_coHTML << ">";
		}
	}
	if (modifier == CMS_ST_CAPTCHA)
	{
		fs << "<input type=\"text\" name=\"f_captcha_code\" maxlength=\"255\" "
				"size=\"" << size << "\" " << m_coHTML << ">";
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
		string coClassAttr = m_coClass != "" ? string(" class=\"") + m_coClass + "\"" : "";
		if (case_comp(field, "Section") == 0)
		{
			if (c.ByPublication())
			{
				fs << "<input type=\"hidden\" name=\"" << P_CB_SUBS << "[]\" value=\""
						<< c.Section() << "\"" << coClassAttr << ">";
			}
			else
			{
				fs << "<input type=\"checkbox\" name=\"" << P_CB_SUBS << "[]\" value=\""
						<< c.Section() << "\" onchange=\"update_subscription_payment()\""
						<< coClassAttr << ">";
			}
		}
		else if (case_comp(field, "Languages") == 0)
		{
			buf << "select l.Id, l.OrigName from Issues as i, Languages as l where "
					"i.IdLanguage = l.Id and i.IdPublication = " << c.Publication()
					<< " group by l.Id";
			SQLQuery(&m_coSql, buf.str().c_str());
			StoreResult(&m_coSql, res);
			fs << "<select name=\"subscription_language[]\"" << coClassAttr << " size=\""
					<< m_nSize << "\"" << (m_bMultipleSelect ? " multiple" : "")
					<< " onchange=\"update_subscription_payment()\" id=\"select_language\">\n";
			MYSQL_ROW row;
			buf.str("");
			buf << c.Language();
			while ((row = mysql_fetch_row(*res)))
			{
				printOption(row[0], buf.str(), row[1], fs, c.EncodeHTML());
			}
			fs << "</select>" << endl;
		}
		else if (case_comp(field, "AllLanguages") == 0)
		{
			fs << "<input type=\"checkbox\" name=\"subs_all_languages\"" << coClassAttr
					<< " onchange=\"update_subscription_payment(); "
					"ToggleElementEnabled('select_language');\">";
		}
	}
	else if (modifier == CMS_ST_USER)
	{
		string attrval = c.UserInfo(field);
		if (case_comp(field, "CountryCode") == 0)
		{
			buf << "select Code, Name from Countries group by Code asc order by Name asc";
		}
		else if (case_comp(field, "Title") == 0)
		{
			fs << "<select name=\"User" << field << "\">" << endl;
			printOption("Mr.", attrval, "Mr.", fs, c.EncodeHTML());
			printOption("Mrs.", attrval, "Mrs.", fs, c.EncodeHTML());
			printOption("Ms.", attrval, "Ms.", fs, c.EncodeHTML());
			printOption("Dr.", attrval, "Dr.", fs, c.EncodeHTML());
			fs << "</select>" << endl;
			return RES_OK;
		}
		else if (case_comp(field, "Gender") == 0)
		{
			fs << "<input type=\"radio\" name=\"User" << field << "\" value=\"M\""
					<< (attrval == "M" ? " checked" : "") << ">"
					<< encodeHTML(male_name, c.EncodeHTML())
					<< " <input type=\"radio\" name=\"User" << field << "\" value=\"F\""
					<< (attrval == "F" ? " checked" : "") << ">"
					<< encodeHTML(female_name, c.EncodeHTML());
			return RES_OK;
		}
		else if (case_comp(field, "Age") == 0)
		{
			fs << "<select name=\"User" << field << "\">" << endl;
			printOption("0-17", attrval, "0-17", fs, c.EncodeHTML());
			printOption("18-24", attrval, "18-24", fs, c.EncodeHTML());
			printOption("25-39", attrval, "25-39", fs, c.EncodeHTML());
			printOption("40-49", attrval, "40-49", fs, c.EncodeHTML());
			printOption("50-65", attrval, "50-65", fs, c.EncodeHTML());
			printOption("65-", attrval, "65 or over", fs, c.EncodeHTML());
			fs << "</select>" << endl;
			return RES_OK;
		}
		else if (case_comp(field, "EmployerType") == 0)
		{
			if (attrval == "")
			{
				attrval = "Other";
			}
			fs << "<select name=\"User" << field << "\">" << endl;
			printOption("Corporate", attrval, "Corporate", fs, c.EncodeHTML());
			printOption("NGO", attrval, "Non-Governmental Organisation", fs, c.EncodeHTML());
			printOption("Government Agency", attrval, "Government Agency", fs, c.EncodeHTML());
			printOption("Academic", attrval, "Academic", fs, c.EncodeHTML());
			printOption("Media", attrval, "Media", fs, c.EncodeHTML());
			printOption("Other", attrval, "Other", fs, c.EncodeHTML());
			fs << "</select>" << endl;
			return RES_OK;
		}
		else if (strncasecmp(field.c_str(), "Pref", 4) == 0)
		{
			fs << "<input type=\"checkbox\" name=\"User" << field << "\"";
			if (attrval == "Y" || checked)
				fs << " value=\"on\" checked";
			fs << "><input type=\"hidden\" name=\"HasPref" << field.substr(4) << "\" value=\"1\">";
			return RES_OK;
		}
		else
		{
			return ERR_INVALID_FIELD;
		}
		SQLQuery(&m_coSql, buf.str().c_str());
		StoreResult(&m_coSql, res);
		CheckForRows(*res, 1);
		fs << "<select name=\"User" << field << "\">" << endl;
		printOption("", attrval, "", fs, c.EncodeHTML());
		MYSQL_ROW row;
		while ((row = mysql_fetch_row(*res)))
		{
			printOption(row[0], attrval, row[1], fs, c.EncodeHTML());
		}
		fs << "</select>" << endl;
	}
	else if (modifier == CMS_ST_SEARCH)
	{
		if (field == "Mode")
			fs << "<input type=\"checkbox\" name=\"Search" << field << "\""
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
		fs << "<input type=\"checkbox\" name=\"" P_REMEMBER_USER "\">";
	}
	return RES_OK;
	TK_CATCH_ERR
}

void CActSelect::printOption(const string& p_rcoValue, const string& p_rcoDefaultValue,
							 const string& p_rcoOption, sockstream& fs, bool p_bEncodeHTML)
{
	fs << "\t<option value=\"" << encodeHTML(p_rcoValue, p_bEncodeHTML)
			<< (p_rcoValue == p_rcoDefaultValue ? "\" selected>" : "\">")
			<< encodeHTML(p_rcoOption, p_bEncodeHTML) << "</option>" << endl;
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
	fs << "<form name=\"user\" action=\"" << pcoURL->getURIPath() << "\" method=\"POST\">\n"
			<< "<input type=\"hidden\" name=\"" << P_TEMPLATE_ID << "\" value=\""
			<< m_nTemplateId << "\">\n"
			<< "<input type=\"hidden\" name=\"" << P_SUBSTYPE << "\" value=\""
			<< encodeHTML(coSubsType, c.EncodeHTML()) << "\">" << endl;
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
	fs << "<input type=\"submit\" name=\"" << (add ? P_USERADD : P_USERMODIFY)
			<< "\" value=\"" << encodeHTML(button_name, c.EncodeHTML()) << "\">\n</form>\n";
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
	fs << "<form name=\"login\" action=\"" << pcoURL->getURIPath() << "\" method=\"POST\">\n"
			<< "<input type=\"hidden\" name=" << P_TEMPLATE_ID << " value=\"" << m_nTemplateId
			<< "\">" << endl;
	fs << c.URL()->getFormString();
	runActions(block, lc, fs);
	fs << "<input type=\"submit\" name=\"" P_LOGIN "\" value=\""
			<< encodeHTML(button_name, c.EncodeHTML()) << "\">\n</form>\n";
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
	fs << "<form name=\"search\" action=\"" << pcoURL->getURIPath() << "\" method=\"POST\">\n"
			<< "<input type=\"hidden\" name=\"" << P_TEMPLATE_ID << "\" value=\""
			<< m_nTemplateId << "\">" << endl;
	fs << c.URL()->getFormString();
	runActions(block, lc, fs);
	fs << "<input type=\"submit\" name=\"" P_SEARCH "\" value=\""
			<< encodeHTML(button_name, c.EncodeHTML()) << "\">\n</form>\n";
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
		if (c.Attachment() <= 0)
		{
			return ERR_NODATA;
		}
		fs << "/attachment/" << setw(9) << setfill('0') << right << c.Attachment()
				<< "." << encodeHTML(c.AttachmentExtension(), c.EncodeHTML());
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
	stringstream coURLParametersStr;
	m_coURLParameters.takeAction(c, coURLParametersStr);
	if (coURLParametersStr.str() == "" && m_nImageNr > 0)
	{
		return ERR_NODATA;
	}
	if (m_nImageNr > 0)
	{
		fs << "/cgi-bin/get_img";
	}
	else
	{
		m_coURIPath.takeAction(c, fs);
	}
	if (coURLParametersStr.str() != "")
	{
		fs << "?" << coURLParametersStr.str();
	}
	return RES_OK;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream	
int CActURL::takeAction(CContext& c, sockstream& fs)
{
	int nServerPort = c.URL()->getServerPort();
	fs << (nServerPort == 443 ? "https://" : "http://") << c.URL()->getHostName();
	if (nServerPort != 80 && nServerPort != 443)
	{
		fs << ":" << nServerPort;
	}
	m_coURI.takeAction(c, fs);
	return RES_OK;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream	
int CActArticleCommentForm::takeAction(CContext& c, sockstream& fs)
{
	if (!c.ArticleCommentEnabled())
	{
		return ERR_NODATA;
	}
	SafeAutoPtr<CURL> pcoURL(c.URL()->clone());
	try {
		pcoURL->setTemplate(m_nTemplateId);
	}
	catch (InvalidValue& rcoEx)
	{
		return ERR_INVALID_FIELD;
	}
	CContext lc = c;
	fs << "<form name=\"articleComment\" action=\""
			<< encodeHTML(c.URL()->getURIPath(), c.EncodeHTML())
			<< "\" method=\"POST\">\n<input type=\"hidden\" name=\""
			<< P_TEMPLATE_ID << "\" value=\"" << m_nTemplateId << "\">\n";
	if (c.URL()->getURLType() == "short names")
	{
		fs << "<input type=\"hidden\" name=\"" << P_IDLANG << "\" value=\""
				<< c.Language() << "\">\n"
				<< "<input type=\"hidden\" name=\"" << P_IDPUBL << "\" value=\""
				<< c.Publication() << "\">\n"
				<< "<input type=\"hidden\" name=\"" << P_NRISSUE << "\" value=\""
				<< c.Issue() << "\">\n"
				<< "<input type=\"hidden\" name=\"" << P_NRSECTION << "\" value=\""
				<< c.Section() << "\">\n"
				<< "<input type=\"hidden\" name=\"" << P_NRARTICLE << "\" value=\""
				<< c.Article() << "\">\n";
	}
	if (c.ArticleCommentId() > 0)
	{
		fs << "<input type=\"hidden\" name=\"acid\" value=\"" << c.ArticleCommentId() << "\">\n";
	}
	fs << c.URL()->getFormString();
	runActions(block, lc, fs);
	fs << "<input type=\"submit\" name=\"submitComment\" class=\"submitButton\" "
			<< "id=\"articleCommentSubmit\" value=\""
			<< encodeHTML(m_coSubmitButton, c.EncodeHTML()) << "\">" << endl;
	if (m_coPreviewButton != "")
	{
		fs << "<input type=\"submit\" name=\"previewComment\" class=\"submitButton\" "
				<< "id=\"articleCommentPreview\" value=\""
				<< encodeHTML(m_coPreviewButton, c.EncodeHTML()) << "\">" << endl;
	}
	fs << "</form>" << endl;
	return RES_OK;
}

// takeAction: performs the action
// Parametes:
//		CContext& c - current context
//		sockstream& fs - output stream	
int CActArticleComment::takeAction(CContext& c, sockstream& fs)
{
	if (m_coParameter.attribute() == "off")
	{
		c.SetArticleCommentId(-1);
	}
	return RES_OK;
}
