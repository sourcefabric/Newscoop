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
 
Implement TOLParameter, TOLAction, TOLActLanguage, TOLActInclude, TOLActPublication,
TOLActIssue, TOLActSection, TOLActArticle, TOLActList, TOLActURLParameters,
TOLActFormParameters, TOLActPrint, TOLActIf, TOLActDate, TOLActText, TOLActLocal,
TOLActSubscription, TOLActEdit, TOLActSelect, TOLActUser, TOLActLogin,
TOLActSearch, TOLActWith methods.
 
******************************************************************************/

#include <unistd.h>
#include <stdio.h>
#include <fstream.h>

#include "tol_util.h"
#include "sql_connect.h"
#include "tol_actions.h"
#include "tol_parser.h"
#include "tol_cparser.h"

//*** start macro definition

#define ResetList(a) (reset_from_list <= a && reset_from_list > CLV_ROOT)

#define MapOperator(i2s_it, op)\
Int2String::iterator i2s_it = m_coOpMap.find(op);\
if (i2s_it == m_coOpMap.end())\
return ERR_NOOP;

#define CheckFor(attr, val, tbuf, q)\
{\
if (val >= 0) {\
if (strlen((q).c_str()))\
q += " and ";\
sprintf(tbuf, "%s = %ld", attr, val);\
q += tbuf;\
}\
}

#define SetNrField(a, v, tbuf, q)\
{\
if (q != "")\
q += " and ";\
sprintf(tbuf, "%s = %ld", a, v);\
q += tbuf;\
}

#define AppendConstraint(q, attr, op, val)\
{\
if (strlen((q).c_str()))\
q += " and ";\
q += string(attr) + " " + op + " \"" + val + "\"";\
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

// default constructor
// Parameters:
//		cpChar p_pchAttr = NULL - attribute
//		cpChar p_pchValue = NULL - value
//		TOperator p_Operator = TOL_NO_OP - operator to apply on value
TOLParameter::TOLParameter(cpChar p_pchAttr, cpChar p_pchValue, TOperator p_Operator)
{
	m_coAttr = p_pchAttr != NULL ? p_pchAttr : "";
	m_coValue = p_pchValue != NULL ? p_pchValue : "";
	m_Operator = p_Operator;
}

// TOLParameter assign operator
const TOLParameter& TOLParameter::operator =(const TOLParameter& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	m_coAttr = p_rcoSrc.m_coAttr;
	m_coValue = p_rcoSrc.m_coValue;
	m_Operator = p_rcoSrc.m_Operator;
	return *this;
}

// TOLAction: initialise static members
TK_MYSQL TOLAction::m_coSql(NULL);
TK_char TOLAction::m_coBuf(NULL);
Int2String TOLAction::m_coOpMap;
TK_bool TOLAction::m_coDebug(NULL);
pthread_once_t TOLAction::m_InitControl = PTHREAD_ONCE_INIT;

// Init: initialise operators map
void TOLAction::Init()
{
	m_coOpMap[(int)TOL_OP_IS] = "=";
	m_coOpMap[(int)TOL_OP_IS_NOT] = "!=";
	m_coOpMap[(int)TOL_OP_GREATER] = ">";
	m_coOpMap[(int)TOL_OP_SMALLER] = "<";
}

// DEBUGAct: print debug information
inline void TOLAction::DEBUGAct(cpChar method, cpChar expl, fstream& fs)
{
	if (*m_coDebug == true)
	{
		fs << "<!-- " << ClassName() << "." << method << ": " << expl << " -->\n";
	}
}

// SQLEscapeString: escape given string for sql query; returns escaped string
// The returned string must be deallocated by the user using delete operator.
// Parameters:
//		cpChar src - source string
//		UInt p_nLength - string length
pChar TOLAction::SQLEscapeString(cpChar src, UInt p_nLength)
{
	pChar pchDst = new char[2 * p_nLength + 1];
	if (pchDst == NULL)
		return NULL;
	pchDst[mysql_real_escape_string(&m_coSql, pchDst, src, p_nLength) + 1] = 0;
	return pchDst;
}

// constructor
// Parameters:
//		TAction p_Action = TOL_ACT_NONE - action identifier
TOLAction::TOLAction(TAction p_Action)
{
	m_Action = p_Action;
	pthread_once(&m_InitControl, Init);
}

// InitTempMembers: init thread specific variables
void TOLAction::InitTempMembers()
{
	TK_TRY
	while (&m_coBuf == NULL)
	{
		m_coBuf = new char[MAX_BUF_LEN + 1];
		usleep(10);
	}
	while (&m_coDebug == NULL)
	{
		m_coDebug = new bool;
		usleep(10);
	}
	TK_CATCH
}

// TOLAction: assign operator
const TOLAction& TOLAction::operator =(const TOLAction& p_pcoSrc)
{
	if (this == &p_pcoSrc)
		return * this;
	m_Action = p_pcoSrc.m_Action;
	return *this;
}

// DateFormat: format the given date according to the given format in given language
// Returns string containing formated date
// Parameters:
//		cpChar p_pchDate - date to format
//		cpChar p_pchFormat - format of the date
//		long int p_nLanguageId - language to use
string TOLAction::DateFormat(cpChar p_pchDate, cpChar p_pchFormat, long int p_nLanguageId)
{
	if (p_pchFormat == NULL || *p_pchFormat == 0)
		return string(p_pchDate);
	sprintf(&m_coBuf, "select MONTH('%s'), WEEKDAY('%s')", p_pchDate, p_pchDate);
	if (mysql_query(&m_coSql, &m_coBuf) != 0)
		return string(p_pchDate);
	CMYSQL_RES res = mysql_store_result(&m_coSql);
	if (*res == NULL || mysql_num_fields(*res) < 2)
		return string(p_pchDate);
	MYSQL_ROW row = mysql_fetch_row(*res);
	if (row == NULL || row[0] == NULL || row[1] == NULL)
		return string(p_pchDate);
	int nMonth = atol(row[0]);
	int nWDay = atol(row[1]);
	pChar pchQuery = &m_coBuf;
	sprintf(pchQuery, "select ");
	int nStartFormat = 0;
	int nBufLen = strlen(pchQuery);
	int nIndex = 0;
	bool bNeedFormat = false;
	int nParams = 0;
	for (; p_pchFormat[nIndex] != 0; nIndex++)
	{
		if (p_pchFormat[nIndex] == '%')
		{
			bNeedFormat = true;
			nIndex++;
			if (p_pchFormat[nIndex] == 0)
				break;
			if (p_pchFormat[nIndex] != 'M' && p_pchFormat[nIndex] != 'W')
				continue;
			int nFormatLen = nIndex - nStartFormat - 1;
			if (nFormatLen > 0)
			{
				sprintf(pchQuery + nBufLen, "%sDATE_FORMAT('%s', '",
				        nParams == 0 ? "" : ", ", p_pchDate);
				nBufLen = strlen(pchQuery);
				strncpy(pchQuery + nBufLen, p_pchFormat + nStartFormat, nFormatLen);
				nBufLen += nFormatLen;
				strcpy(pchQuery + nBufLen, "')");
				nBufLen += 2;
				pchQuery[nBufLen] = 0;
				nParams++;
			}
			nStartFormat = nIndex + 1;
			if (nParams > 0)
			{
				strcpy(pchQuery + nBufLen, ", ");
				nBufLen += 2;
			}
			if (p_pchFormat[nIndex] == 'M')
				sprintf(pchQuery + nBufLen, "%s%d", "Month", nMonth);
			else
				sprintf(pchQuery + nBufLen, "%s%d", "WDay", nWDay + 1);
			nBufLen = strlen(pchQuery);
			nParams++;
		}
	}
	if (!bNeedFormat)
		return string("");
	if (nIndex > nStartFormat)
	{
		sprintf(pchQuery + nBufLen, "%sDATE_FORMAT('%s', '",
		        nParams == 0 ? "" : ", ", p_pchDate);
		nBufLen = strlen(pchQuery);
		strncpy(pchQuery + nBufLen, p_pchFormat + nStartFormat, nIndex - nStartFormat);
		nBufLen += nIndex - nStartFormat;
		strcpy(pchQuery + nBufLen, "')");
		nBufLen += 2;
		pchQuery[nBufLen] = 0;
		nParams++;
	}
	sprintf(pchQuery + nBufLen, " from Languages where Id = %ld or Id = 1 order by Id desc limit 0,1",
	        p_nLanguageId);
	if (mysql_query(&m_coSql, pchQuery) != 0)
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

// TOLActLanguage: assign operator
const TOLActLanguage& TOLActLanguage::operator =(const TOLActLanguage& p_pcoSrc)
{
	if (this == &p_pcoSrc)
		return * this;
	if (m_pchLang != NULL)
		free(m_pchLang);
	m_pchLang = strdup(m_pchLang);
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context (modified by action)
//		fstream& fs - output stream (not used)
int TOLActLanguage::TakeAction(TOLContext& c, fstream& fs)
{
	pChar pchLang = SQLEscapeString(m_pchLang, strlen(m_pchLang));
	if (pchLang == NULL)
		return ERR_NOMEM;
	string coQuery = string("select Id from Languages where Name = '") + pchLang + "'";
	delete pchLang;
	DEBUGAct("TakeAction()", coQuery.c_str(), fs);
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[0] == NULL)
		return -1;
	c.SetLanguage(strtol(row[0], 0, 10));
	return RES_OK;
}

// TOLActInclude: assign operator
const TOLActInclude& TOLActInclude::operator =(const TOLActInclude& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	if (tpl_path)
		free(tpl_path);
	tpl_path = strdup(p_rcoSrc.tpl_path);
	parser_hash = p_rcoSrc.parser_hash;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context (may be modified by action)
//		fstream& fs - output stream
int TOLActInclude::TakeAction(TOLContext& c, fstream& fs)
{
	if (parser_hash == 0)
		return ERR_NOHASH;
	try
	{
		TOLParser::LockHash();
		TOLParserHash::iterator ph_i;
		if ((ph_i = parser_hash->find(tpl_path)) == parser_hash->end())
		{
			parser_hash->insert_unique(new TOLParser(tpl_path));
			if ((ph_i = parser_hash->find(tpl_path)) == parser_hash->end())
			{
				TOLParser::UnlockHash();
				return ERR_NOHASHENT;
			}
		}
		TOLParser::UnlockHash();
		(*ph_i)->SetDebug(*m_coDebug);
		(*ph_i)->Parse();
		(*ph_i)->SetDebug(*m_coDebug);
		return (*ph_i)->WriteOutput(c, fs);
	}
	catch (ExTK& rcoEx)
	{
		TOLParser::UnlockHash();
		return ERR_NOMEM;
	}
	catch (ExMutex& rcoEx)
	{
		TOLParser::UnlockHash();
		return ERR_LOCKHASH;
	}
}

// TOLActPublication: assign operator
const TOLActPublication& TOLActPublication::operator =(const TOLActPublication& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	param = p_rcoSrc.param;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context (modified by action)
//		fstream& fs - output stream (not used)
int TOLActPublication::TakeAction(TOLContext& c, fstream& fs)
{
	if (strcasecmp(param.Attribute(), "off") == 0)
	{
		c.SetPublication( -1);
		return RES_OK;
	}
	if (strcasecmp(param.Attribute(), "default") == 0)
	{
		c.SetPublication(c.DefPublication());
		return RES_OK;
	}
	MapOperator(i2s_it, (int)param.Operator());
	string coQuery = "select Id from Publications where ";
	string w = "";
	pChar pchVal = SQLEscapeString(param.Value(), strlen(param.Value()));
	if (pchVal == NULL)
		return ERR_NOMEM;
	AppendConstraint(w, param.Attribute(), (*i2s_it).second, pchVal);
	delete pchVal;
	coQuery += w;
	DEBUGAct("TakeAction()", coQuery.c_str(), fs);
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[0] == NULL)
		return -1;
	c.SetPublication(strtol(row[0], 0, 10));
	return RES_OK;
}

// TOLActIssue: assign operator
const TOLActIssue& TOLActIssue::operator =(const TOLActIssue& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	param = p_rcoSrc.param;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context (modified by action)
//		fstream& fs - output stream (not used)
int TOLActIssue::TakeAction(TOLContext& c, fstream& fs)
{
	TK_TRY
	if (strcasecmp(param.Attribute(), "off") == 0)
	{
		c.SetIssue( -1);
		return RES_OK;
	}
	if (strcasecmp(param.Attribute(), "default") == 0)
	{
		c.SetIssue(c.DefIssue());
		return RES_OK;
	}
	string w, coQuery;
	if (strcasecmp(param.Attribute(), "current") == 0)
	{
		coQuery = "select max(Number) from Issues ";
	}
	else if (strcasecmp(param.Attribute(), "number") == 0)
	{
		coQuery = "select Number from Issues ";
		MapOperator(i2s_it, (int)param.Operator());
		pChar pchVal = SQLEscapeString(param.Value(), strlen(param.Value()));
		if (pchVal == NULL)
			return ERR_NOMEM;
		AppendConstraint(w, param.Attribute(), (*i2s_it).second, pchVal);
		delete pchVal;
		SetNrField("IdLanguage", c.Language(), &m_coBuf, w);
	}
	else
		return -1;
	SetNrField("IdPublication", c.Publication(), &m_coBuf, w);
	if (c.Access() == A_PUBLISHED)
		AppendConstraint(w, "Published", "=", "Y");
	if (w != "")
		coQuery += "where ";
	coQuery += w;
	DEBUGAct("TakeAction()", coQuery.c_str(), fs);
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

// TOLActSection: assign operator
const TOLActSection& TOLActSection::operator =(const TOLActSection& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	param = p_rcoSrc.param;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context (modified by action)
//		fstream& fs - output stream (not used)
int TOLActSection::TakeAction(TOLContext& c, fstream& fs)
{
	if (strcasecmp(param.Attribute(), "off") == 0)
	{
		c.SetSection( -1);
		return RES_OK;
	}
	if (strcasecmp(param.Attribute(), "default") == 0)
	{
		c.SetSection(c.DefSection());
		return RES_OK;
	}
	MapOperator(i2s_it, (int)param.Operator());
	string coQuery = "select Number from Sections where ";
	string w = "";
	pChar pchVal = SQLEscapeString(param.Value(), strlen(param.Value()));
	if (pchVal == NULL)
		return ERR_NOMEM;
	AppendConstraint(w, param.Attribute(), (*i2s_it).second, pchVal);
	delete pchVal;
	SetNrField("IdLanguage", c.Language(), &m_coBuf, w);
	SetNrField("IdPublication", c.Publication(), &m_coBuf, w);
	SetNrField("NrIssue", c.Issue(), &m_coBuf, w);
	coQuery += w;
	DEBUGAct("TakeAction()", coQuery.c_str(), fs);
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[0] == NULL)
		return -1;
	c.SetSection(strtol(row[0], 0, 10));
	return RES_OK;
}

// TOLActArticle: assign operator
const TOLActArticle& TOLActArticle::operator =(const TOLActArticle& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	param = p_rcoSrc.param;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context (modified by action)
//		fstream& fs - output stream (not used)
int TOLActArticle::TakeAction(TOLContext& c, fstream& fs)
{
	if (strcasecmp(param.Attribute(), "off") == 0)
	{
		c.SetArticle( -1);
		return RES_OK;
	}
	if (strcasecmp(param.Attribute(), "default") == 0)
	{
		c.SetArticle(c.DefArticle());
		return RES_OK;
	}
	MapOperator(i2s_it, (int)param.Operator());
	string coQuery = "select Number from Articles where ";
	string w = "";
	pChar pchVal = SQLEscapeString(param.Value(), strlen(param.Value()));
	if (pchVal == NULL)
		return ERR_NOMEM;
	AppendConstraint(w, param.Attribute(), (*i2s_it).second, pchVal);
	delete pchVal;
	SetNrField("IdLanguage", c.Language(), &m_coBuf, w);
	SetNrField("IdPublication", c.Publication(), &m_coBuf, w);
	SetNrField("NrIssue", c.Issue(), &m_coBuf, w);
	SetNrField("NrSection", c.Section(), &m_coBuf, w);
	coQuery += w;
	DEBUGAct("TakeAction()", coQuery.c_str(), fs);
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[0] == NULL)
		return -1;
	c.SetArticle(strtol(row[0], 0, 10));
	return RES_OK;
}

// WriteModParam: add conditions - corresponding to modifier parameters -
// to where clause of the query. Used for Issue and Section modifiers.
// Parameters:
//		string& s - string to add conditions to (where clause)
//		TOLContext& c - current context
//		string& table - string containig tables used in query
int TOLActList::WriteModParam(string& s, TOLContext& c, string& table)
{
	if (modifier == TOL_LMOD_SECTION)
		table = "Sections";
	else
		table = "Issues";
	string w = "";
	if (modifier == TOL_LMOD_ISSUE && c.Access() != A_ALL)
		w = "Published = 'Y'";
	TOLParameterList::iterator pl_i;
	for (pl_i = mod_param.begin(); pl_i != mod_param.end(); ++pl_i)
	{
		MapOperator(i2s_i, (int)(*pl_i).Operator());
		pChar pchVal = SQLEscapeString((*pl_i).Value(), strlen((*pl_i).Value()));
		if (pchVal == NULL)
			return ERR_NOMEM;
		AppendConstraint(w, (*pl_i).Attribute(), (*i2s_i).second, pchVal);
		delete pchVal;
	}
	CheckFor("IdPublication", c.Publication(), &m_coBuf, w);
	if (modifier == TOL_LMOD_SECTION)
		CheckFor("NrIssue", c.Issue(), &m_coBuf, w);
	sprintf(&m_coBuf, "(IdLanguage = %ld or IdLanguage = 1)", c.Language());
	w += (w != "" ? string(" and ") : string("")) + &m_coBuf;
	if (strlen((w).c_str()))
		s += string(" where ") + w;
	return RES_OK;
}

// WriteArtParam: add conditions - corresponding to modifier parameters -
// to where clause of the query. Used for Article modifier.
// Parameters:
//		string& s - string to add conditions to (where clause)
//		TOLContext& c - current context
//		string& table - string containig tables used in query
int TOLActList::WriteArtParam(string& s, TOLContext& c, string& table)
{
	TOLParameterList::iterator pl_i;
	for (pl_i = mod_param.begin(); pl_i != mod_param.end(); ++pl_i)
		if (strcasecmp((*pl_i).Attribute(), "Type") == 0)
		{
			CheckForType((*pl_i).Value(), &m_coSql);
			break;
		}
	string val, w;
	if (c.Access() != A_ALL)
		w = "Published = 'Y'";
	table = "Articles";
	for (pl_i = mod_param.begin(); pl_i != mod_param.end(); ++pl_i)
	{
		if (strcasecmp((*pl_i).Attribute(), "keyword") == 0)
		{
			val = string("%") + (*pl_i).Value() + "%";
			pChar pchVal = SQLEscapeString(val.c_str(), strlen(val.c_str()));
			if (pchVal == NULL)
				return ERR_NOMEM;
			AppendConstraint(w, "Keywords", "like", pchVal);
			delete pchVal;
		}
		else
		{
			MapOperator(i2s_i, (int)(*pl_i).Operator());
			pChar pchVal = SQLEscapeString((*pl_i).Value(), strlen((*pl_i).Value()));
			if (pchVal == NULL)
				return ERR_NOMEM;
			AppendConstraint(w, (*pl_i).Attribute(), (*i2s_i).second, pchVal);
			delete pchVal;
		}
	}
	CheckFor("IdPublication", c.Publication(), &m_coBuf, w);
	CheckFor("NrIssue", c.Issue(), &m_coBuf, w);
	CheckFor("NrSection", c.Section(), &m_coBuf, w);
	sprintf(&m_coBuf, "(IdLanguage = %ld or IdLanguage = 1)", c.Language());
	w += (w != "" ? string(" and ") : string("")) + &m_coBuf;
	if (c.Access() == A_PUBLISHED)
		AppendConstraint(w, "Published", "=", "Y");
	if (strlen(w.c_str()))
		s = string(" where ") + w;
	return RES_OK;
}

// WriteSrcParam: add conditions - corresponding to modifier parameters -
// to where clause of the query. Used for SearchResult modifier.
// Parameters:
//		string& s - string to add conditions to (where clause)
//		TOLContext& c - current context
//		string& table - string containig tables used in query
int TOLActList::WriteSrcParam(string& s, TOLContext& c, string& table)
{
	table = "Articles, ArticleIndex, KeywordIndex";
	string w = "";
	c.ResetKwdIt();
	cpChar k;
	bool First = true;
	while ((k = c.NextKwd()) != NULL)
	{
		pChar pchVal = SQLEscapeString(k, strlen(k));
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
		delete pchVal;
	}
	if (w == "")
		return -1;
	w += ")";
	CheckFor("Articles.IdPublication", c.Publication(), &m_coBuf, w);
	if (c.SearchLevel() >= 1)
		CheckFor("Articles.NrIssue", c.Issue(), &m_coBuf, w);
	if (c.SearchLevel() >= 2)
		CheckFor("Articles.NrSection", c.Section(), &m_coBuf, w);
	if (w != "")
		w += " and ";
	w += "ArticleIndex.IdKeyword = KeywordIndex.Id"
	     " and Articles.Number = ArticleIndex.NrArticle"
	     " and Articles.IdLanguage = ArticleIndex.IdLanguage";
	s = string(" where ") + w;
	return RES_OK;
}

// WriteOrdParam: add conditions - corresponding to order parameters -
// to order clause of the query.
// Parameters:
//		string& s - string to add conditions to (order clause)
int TOLActList::WriteOrdParam(string& s)
{
	TOLParameterList::iterator pl_i;
	if (modifier != TOL_LMOD_SEARCHRESULT)
	{
		s = " order by IdLanguage desc";
		for (pl_i = ord_param.begin(); pl_i != ord_param.end(); ++pl_i)
		{
			const char* pchAttribute = (*pl_i).Attribute();
			if (strcasecmp(pchAttribute, "bydate") == 0)
				pchAttribute = modifier == TOL_LMOD_ISSUE ? "PublicationDate" : "UploadDate";
			s += string(", ") + pchAttribute + " ";
			if (strlen((*pl_i).Value()))
				s += (*pl_i).Value();
		}
	}
	else // modifier == TOL_LMOD_SEARCHRESULT
	{
		s = " order by Articles.IdPublication asc, ArticleIndex.IdLanguage desc";
		for (pl_i = ord_param.begin(); pl_i != ord_param.end(); ++pl_i)
		{
			s += string(", ");
			if ((*pl_i).Attribute() == "Number")
				s += string("NrArticle") + string(" ");
			else
				s += (*pl_i).Attribute() + string(" ");
			if (strlen((*pl_i).Value()))
				s += (*pl_i).Value();
			if ((*pl_i).Attribute() != "Number")
				s += ", NrArticle asc";
		}
	}
	return RES_OK;
}

// WriteLimit: add conditions to limit clause of the query.
// Parameters:
//		string& s - string to add conditions to (limit clause)
//		TOLContext& c - current context
int TOLActList::WriteLimit(string& s, TOLContext& c)
{
	if (length > 0)
	{
		s += " limit ";
		sprintf(&m_coBuf, "%ld, %ld",
		        c.ListStart(c.Level()) >= 0 ? c.ListStart(c.Level()) : 0, length + 1);
		s += &m_coBuf;
	}
	return RES_OK;
}

// RunBlock: run actions in a list of actions
// Parameters:
//		TOLPActionList& al - list of actions
//		TOLContext& c - current context
//		fstream& fs - output stream
int TOLActList::RunBlock(TOLPActionList& al, TOLContext& c, fstream& fs)
{
	for (TOLPActionList::iterator al_i = al.begin(); al_i != al.end(); ++al_i)
		(*al_i)->TakeAction(c, fs);
	return RES_OK;
}

// SetContext: set the context current Issue, Section or Article depending of list
// modifier
// Parameters:
//		TOLContext& c - current context
// 		long int value - value to be set
void TOLActList::SetContext(TOLContext& c, long int value)
{
	if (modifier == TOL_LMOD_ISSUE)
		c.SetIssue(value);
	else if (modifier == TOL_LMOD_SECTION)
		c.SetSection(value);
	else if (modifier == TOL_LMOD_ARTICLE || modifier == TOL_LMOD_SEARCHRESULT)
		c.SetArticle(value);
}

// IMod2Level: convert from list modifier to level identifier; return level identifier
// Parameters:
//		TListModifier m - list modifier
CLevel TOLActList::IMod2Level(TListModifier m)
{
	switch (m)
	{
	case TOL_LMOD_ISSUE:
		return CLV_ISSUE_LIST;
	case TOL_LMOD_SECTION:
		return CLV_SECTION_LIST;
	case TOL_LMOD_ARTICLE:
		return CLV_ARTICLE_LIST;
	case TOL_LMOD_SEARCHRESULT:
		return CLV_SEARCHRESULT_LIST;
	case TOL_LMOD_SUBTITLE:
		return CLV_SUBTITLE_LIST;
	default:
		return CLV_ROOT;
	}
}

// constructor
// Parameters:
//		TListModifier m - list modifier
//		long int l - list length
//		long int c - list columns
//		TOLParameterList& mp - modifier parameter list
//		TOLParameterList& op - order parameter list
TOLActList::TOLActList(TListModifier m, long int l, long int c, TOLParameterList& mp,
                       TOLParameterList& op)
		: TOLAction(TOL_ACT_LIST)
{
	modifier = m;
	length = l;
	columns = c;
	mod_param = mp;
	ord_param = op;
}

// destructor
TOLActList::~TOLActList()
{
	TOLPActionList::iterator al_i;
	for (al_i = first_block.begin(); al_i != first_block.end(); ++al_i)
	{
		delete (*al_i);
		*al_i = NULL;
	}
	first_block.clear();
	for (al_i = second_block.begin(); al_i != second_block.end(); ++al_i)
	{
		delete (*al_i);
		*al_i = NULL;
	}
	second_block.clear();
}

// TOLActList: assign operator
const TOLActList& TOLActList::operator =(const TOLActList& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	length = p_rcoSrc.length;
	columns = p_rcoSrc.columns;
	mod_param = p_rcoSrc.mod_param;
	ord_param = p_rcoSrc.ord_param;
	first_block = p_rcoSrc.first_block;
	second_block = p_rcoSrc.second_block;
	modifier = p_rcoSrc.modifier;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context (not modified by action)
//		fstream& fs - output stream
int TOLActList::TakeAction(TOLContext& c, fstream& fs)
{
	TK_TRY
	if (modifier == TOL_LMOD_SEARCHRESULT && !c.Search())
		return RES_OK;
	TOLContext lc = c;
	lc.SetLMode(LM_NORMAL);
	lc.SetLevel(IMod2Level(modifier));
	long int listlength;
	if (lc.ListStart(lc.Level()) < 0)
		lc.SetListStart(0, lc.Level());
	CMYSQL_RES res(NULL);
	if (modifier != TOL_LMOD_SUBTITLE)
	{
		string where, order, limit, fields, prefix, table, having;
		if (modifier == TOL_LMOD_ARTICLE)
		{
			WriteArtParam(where, lc, table);
			prefix = "Articles.";
		}
		else if (modifier == TOL_LMOD_SEARCHRESULT)
		{
			WriteSrcParam(where, lc, table);
			if (where == "")
			{
				RunBlock(second_block, c, fs);
				return RES_OK;
			}
		}
		else
			WriteModParam(where, lc, table);
		if (modifier == TOL_LMOD_SEARCHRESULT && lc.SearchAnd())
		{
			sprintf(&m_coBuf, " having count(NrArticle) = %u", lc.KeywordsNr());
			having = &m_coBuf;
		}
		WriteOrdParam(order);
		WriteLimit(limit, lc);
		if (modifier == TOL_LMOD_SEARCHRESULT)
			fields = "select NrArticle, MAX(Articles.IdLanguage), Articles.IdPublication";
		else if (modifier == TOL_LMOD_ARTICLE)
			fields = "select Number, MAX(Articles.IdLanguage), IdPublication";
		else
			fields = "select Number, MAX(IdLanguage), IdPublication";
		if (modifier == TOL_LMOD_ARTICLE || modifier == TOL_LMOD_SEARCHRESULT)
			fields += ", Articles.NrIssue, Articles.NrSection";
		else if (modifier == TOL_LMOD_SECTION)
			fields += ", NrIssue";
		string grfield;
		grfield = (modifier == TOL_LMOD_SEARCHRESULT ? "NrArticle" : "Number");
		string coQuery = fields + string(" from ") + table + where + " group by " + grfield
		                 + having + order + limit;
		DEBUGAct("TakeAction()", coQuery.c_str(), fs);
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
	if (listlength > 0)
	{
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
		if (listlength > (long int)length && length > 0)
			lc.SetNextStart(lc.ListStart(lc.Level()) + length, lc.Level());
		for (int i = 0; (length > 0 && i < length) || length == 0; i++)
		{
			string st = "";
			if (modifier != TOL_LMOD_SUBTITLE)
			{
				if ((row = mysql_fetch_row(*res)) == NULL)
					break;
				lc.SetLanguage(strtol(row[1], 0, 10));
				lc.SetPublication(strtol(row[2], 0, 10));
				if (modifier != TOL_LMOD_ISSUE)
					lc.SetIssue(strtol(row[3], 0, 10));
				if (modifier == TOL_LMOD_ARTICLE || modifier == TOL_LMOD_SEARCHRESULT)
					lc.SetSection(strtol(row[4], 0, 10));
				SetContext(lc, strtol(row[0], 0, 10));
			}
			else
			{
				if ((st = lc.SelectSubtitle(i + lc.StListStart())) == "")
					break;
			}
			RunBlock(first_block, lc, fs);
			lc.SetListIndex(lc.ListIndex() + 1);
			lc.SetListColumn(lc.ListColumn() + 1);
			if (lc.ListColumn() > columns)
			{
				lc.SetListRow(lc.ListRow() + 1);
				lc.SetListColumn(1);
			}
		}
	}
	else
	{
		RunBlock(second_block, c, fs);
	}
	return RES_OK;
	TK_CATCH_ERR
}

// PrintSubtitlesURL: print url parameters for subtitle list/printing
// Parameters:
//		TOLContext& c - current context
//		fstream& fs - output stream
//		bool& first - used to signal if first parameter in list (for printing separators)
void TOLActURLParameters::PrintSubtitlesURL(TOLContext& c, fstream& fs, bool& first)
{
	string2string::iterator it;
	int i;
	for (i = 1, it = c.Fields().begin(); it != c.Fields().end(); ++it, i++)
	{
		sprintf(&m_coBuf, "ST%d", i);
		if (!first)
			fs << "&";
		else
			first = false;
		fs << &m_coBuf << "=" << (*it).first;
		sprintf(&m_coBuf, "&ST_T%d", i);
		fs << &m_coBuf << "=" << (*it).second;
		sprintf(&m_coBuf, "ST_PS%d", i);
		long int start_subtitle;
		if (reset_from_list > 0)
			start_subtitle = 0;
		else if (c.LMode() == LM_NORMAL && c.Level() == CLV_SUBTITLE_LIST)
			start_subtitle = c.StListStart((*it).first) + c.ListIndex() - 1;
		else if (c.StMode() == STM_PREV)
			start_subtitle = c.StartSubtitle((*it).first) - 1;
		else if (c.StMode() == STM_NEXT)
		{
			start_subtitle = c.StartSubtitle((*it).first) + 1;
		}
		else
			start_subtitle = c.StartSubtitle((*it).first);
		if (start_subtitle < 0)
			start_subtitle = 0;
		if (start_subtitle >= c.SubtitlesNumber())
			start_subtitle = c.SubtitlesNumber() - 1;
		URLPrintNParam(&m_coBuf, start_subtitle, fs, first);
		sprintf(&m_coBuf, "ST_AS%d", i);
		if (c.LMode() != LM_NORMAL && c.Level() == CLV_SUBTITLE_LIST)
		{
			URLPrintNParam(&m_coBuf, c.AllSubtitles(), fs, first)
		}
		else
			URLPrintNParam(&m_coBuf, allsubtitles, fs, first);
		if (c.Level() == CLV_ROOT)
			continue;
		sprintf(&m_coBuf, "ST_LS%d", i);
		if (c.LMode() == LM_PREV && c.Level() == CLV_SUBTITLE_LIST)
			URLPrintNParam(&m_coBuf, c.StPrevStart((*it).first), fs, first)
			else if (c.LMode() == LM_NEXT && c.Level() == CLV_SUBTITLE_LIST)
				URLPrintNParam(&m_coBuf, c.StNextStart((*it).first), fs, first)
				else if (c.Level() != CLV_ROOT)
					URLPrintNParam(&m_coBuf, c.StListStart((*it).first), fs, first);
	}
	if (first)
		return ;
	first = false;
	fs << "&ST_max=" << i - 1;
}

// TOLActURLParameters: assign operator
const TOLActURLParameters& TOLActURLParameters::operator =(const TOLActURLParameters& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	image_nr = p_rcoSrc.image_nr;
	fromstart = p_rcoSrc.fromstart;
	allsubtitles = p_rcoSrc.allsubtitles;
	reset_from_list = p_rcoSrc.reset_from_list;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context
//		fstream& fs - output stream
int TOLActURLParameters::TakeAction(TOLContext& c, fstream& fs)
{
	TK_TRY
	bool first = true;
	if (image_nr >= 0)
	{
		if (c.Publication() < 0 || c.Issue() < 0 || c.Section() < 0 || c.Article() < 0)
			return ERR_NOPARAM;
		URLPrintParam(P_NRIMAGE, image_nr, fs, first);
	}
	else
	{
		if (c.Language() < 0 && c.Publication() < 0 && c.Issue() < 0
		        && c.Section() < 0 && c.Article() < 0)
			return ERR_NOPARAM;
		URLPrintParam(P_IDLANG, c.Language(), fs, first);
	}
	if (fromstart)
	{
		URLPrintParam(P_IDPUBL, c.DefPublication(), fs, first);
		URLPrintParam(P_NRISSUE, c.DefIssue(), fs, first);
		URLPrintParam(P_NRSECTION, c.DefSection(), fs, first);
		URLPrintParam(P_NRARTICLE, c.DefArticle(), fs, first);
	}
	else
	{
		URLPrintParam(P_IDPUBL, c.Publication(), fs, first);
		URLPrintParam(P_NRISSUE, c.Issue(), fs, first);
		URLPrintParam(P_NRSECTION, c.Section(), fs, first);
		URLPrintParam(P_NRARTICLE, c.Article(), fs, first);
	}
	if (c.SubsType() != ST_NONE)
		fs << (first ? "" : "&") << P_SUBSTYPE << "="
		<< (c.SubsType() == ST_TRIAL ? "trial" : "paid");
	PrintSubtitlesURL(c, fs, first);
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
	if (c.LMode() == LM_NORMAL || (c.Level() != CLV_SEARCHRESULT_LIST && c.Level() != CLV_ROOT))
		URLPrintNParam(P_SRLSTART, (ResetList(CLV_SEARCHRESULT_LIST) ? 0 : c.SrListStart()),
		               fs, first);
	if (c.Level() == CLV_SEARCHRESULT_LIST)
	{
		if (!first)
			fs << "&";
		cpChar pchEscKw = EscapeURL(c.StrKeywords());
		fs << "search=search&SearchKeywords=" << pchEscKw
		<< (c.SearchAnd() ? "&SearchMode=on" : "") << "&SearchLevel=" << c.SearchLevel();
		delete pchEscKw;
	}
	return RES_OK;
	TK_CATCH_ERR
}

// TOLActFormParameters: assign operator
const TOLActFormParameters& TOLActFormParameters::operator =(const TOLActFormParameters& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	fromstart = p_rcoSrc.fromstart;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context
//		fstream& fs - output stream
int TOLActFormParameters::TakeAction(TOLContext& c, fstream& fs)
{
	TK_TRY
	if (c.Language() < 0 && c.Publication() < 0 && c.Issue() < 0
	        && c.Section() < 0 && c.Article() < 0)
		return ERR_NOPARAM;
	FormPrintParam(P_IDLANG, c.Language(), fs);
	if (fromstart)
	{
		FormPrintParam(P_IDPUBL, c.DefPublication(), fs);
		FormPrintParam(P_NRISSUE, c.DefIssue(), fs);
		FormPrintParam(P_NRSECTION, c.DefSection(), fs);
		FormPrintParam(P_NRARTICLE, c.DefArticle(), fs);
	}
	else
	{
		FormPrintParam(P_IDPUBL, c.Publication(), fs);
		FormPrintParam(P_NRISSUE, c.Issue(), fs);
		FormPrintParam(P_NRSECTION, c.Section(), fs);
		FormPrintParam(P_NRARTICLE, c.Article(), fs);
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

// BlobField: return 0 if field of table is blob type
// Parameters:
//		cpChar table - table
//		cpChar field - table field
int TOLActPrint::BlobField(cpChar table, cpChar field)
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
//		cpChar table - table
//		cpChar field - table field
int TOLActPrint::DateField(cpChar table, cpChar field)
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

// constructor
// Parameters:
//		cpChar a - attribute to print
//		TPrintModifier m - print modifier
//		cpChar t = NULL - special type (may be NULL)
//		string f = "" - format (for date type attributes)
TOLActPrint::TOLActPrint(cpChar a, TPrintModifier m, cpChar t, string f)
		: TOLAction(TOL_ACT_PRINT)
{
	if (a != NULL)
	{
		strncpy(attr, a, ID_MAXLEN > strlen(a) ? strlen(a) : ID_MAXLEN);
		attr[ID_MAXLEN > strlen(a) ? strlen(a) : ID_MAXLEN] = 0;
	}
	else
		attr[0] = 0;
	if (t != NULL)
	{
		strncpy(type, t, ID_MAXLEN > strlen(t) ? strlen(t) : ID_MAXLEN);
		type[ID_MAXLEN > strlen(t) ? strlen(t) : ID_MAXLEN] = 0;
	}
	else
		type[0] = 0;
	modifier = m;
	format = f;
}

// TOLActPrint: assign operator
const TOLActPrint& TOLActPrint::operator =(const TOLActPrint& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	modifier = p_rcoSrc.modifier;
	format = p_rcoSrc.format;
	strcpy(attr, p_rcoSrc.attr);
	strcpy(type, p_rcoSrc.type);
	cparser = p_rcoSrc.cparser;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context
//		fstream& fs - output stream
int TOLActPrint::TakeAction(TOLContext& c, fstream& fs)
{
	TK_TRY
	if (modifier == TOL_PMOD_LIST)
	{
		if (c.ListIndex() <= 0)
			return ERR_NOLISTINDEX;
		if (strcasecmp(attr, "row") == 0)
			fs << c.ListRow();
		else if (strcasecmp(attr, "column") == 0)
			fs << c.ListColumn();
		else
			fs << (c.ListIndex() + c.ListStart(c.Level()));
		return RES_OK;
	}
	CMYSQL_RES res(NULL);
	MYSQL_ROW row(NULL);
	if (modifier == TOL_PMOD_SUBSCRIPTION)
	{
		if (strcasecmp(attr, "unit") == 0)
		{
			sprintf(&m_coBuf, "select TimeUnits.Name from TimeUnits, Publications where "
			        "Publications.Id = %ld and TimeUnits.Unit = Publications.TimeUnit"
			        " and (TimeUnits.IdLanguage = %ld or TimeUnits.IdLanguage = 1) "
			        " order by IdLanguage desc", c.Publication(), c.Language());
		}
		else if (strcasecmp(attr, "expdate") == 0)
		{
			sprintf(&m_coBuf, "select DATE_ADD(SubsSections.StartDate, INTERVAL "
			        "SubsSections.Days DAY) from SubsSections, Subscriptions where "
			        "Subscriptions.IdUser = %ld and Subscriptions.IdPublication = "
			        "%ld and SubsSections.IdSubscription = Subscriptions.Id and "
			        "SubsSections.SectionNumber = %ld", c.User(), c.Publication(), c.Section());
		}
		else if (strcasecmp(attr, "currency") == 0)
		{
			sprintf(&m_coBuf, "select Currency from Publications where Id = %ld", c.Publication());
		}
		else if (strcasecmp(attr, "unitcost") == 0)
		{
			sprintf(&m_coBuf, "select UnitCost from Publications where Id = %ld", c.Publication());
		}
		else if (strcasecmp(attr, "trialtime") == 0)
		{
			sprintf(&m_coBuf, "select TrialTime from SubsDefTime where IdPublication = %ld and "
			        "CountryCode = \"%s\"", c.Publication(), c.UserInfo("CountryCode").c_str());
		}
		else if (strcasecmp(attr, "paidtime") == 0)
		{
			sprintf(&m_coBuf, "select PaidTime from SubsDefTime where IdPublication = %ld and "
			        "CountryCode = \"%s\"", c.Publication(), c.UserInfo("CountryCode").c_str());
		}
		else if (strcasecmp(attr, "totalcost") == 0)
		{
			if (c.SubsType() == ST_NONE)
				return RES_OK;
			sprintf(&m_coBuf, "select sum(UnitCost) * %sTime from Sections,Publications,"
			        "SubsDefTime where Publications.Id = Sections.IdPublication and "
			        "SubsDefTime.IdPublication = Publications.Id and Publications.Id = %ld and "
			        "NrIssue = %ld and IdLanguage = %ld and CountryCode = '%s'",
			        c.SubsType() == ST_TRIAL ? "Trial" : "Paid", c.Publication(), c.Issue(),
			        c.Language(), c.UserInfo("CountryCode").c_str());
		}
		else
		{ // error
			sprintf(&m_coBuf, "select Message from Errors where Number = %ld and "
			        "(IdLanguage = %ld or IdLanguage = 1) order by IdLanguage desc",
			        c.SubsRes(), c.Language());
		}
		DEBUGAct("TakeAction()", &m_coBuf, fs);
		SQLQuery(&m_coSql, &m_coBuf);
		res = mysql_store_result(&m_coSql);
		CheckForRows(*res, 1);
		row = mysql_fetch_row(*res);
		if (format != "")
			fs << DateFormat(row[0], format.c_str(), c.Language());
		else
			fs << row[0];
		return RES_OK;
	}
	if (modifier == TOL_PMOD_USER)
	{
		if (strcasecmp(attr, "adderror") == 0)
			sprintf(&m_coBuf, "select Message from Errors where Number = %ld and "
			        "IdLanguage = %ld", c.AddUserRes(), c.Language());
		else if (strcasecmp(attr, "modifyerror") == 0)
			sprintf(&m_coBuf, "select Message from Errors where Number = %ld and "
			        "IdLanguage = %ld", c.ModifyUserRes(), c.Language());
		else
			sprintf(&m_coBuf, "select %s from Users where Id = %lu", attr, c.User());
		DEBUGAct("TakeAction()", &m_coBuf, fs);
		SQLQuery(&m_coSql, &m_coBuf);
		res = mysql_store_result(&m_coSql);
		CheckForRows(*res, 1);
		row = mysql_fetch_row(*res);
		fs << row[0];
		return RES_OK;
	}
	if (modifier == TOL_PMOD_LOGIN)
	{
		sprintf(&m_coBuf, "select Message from Errors where Number = %ld and "
		        "(IdLanguage = %ld or IdLanguage = 1) order by IdLanguage desc",
		        c.LoginRes(), c.Language());
		SQLQuery(&m_coSql, &m_coBuf);
		res = mysql_store_result(&m_coSql);
		CheckForRows(*res, 1);
		row = mysql_fetch_row(*res);
		fs << row[0];
		return RES_OK;
	}
	if (modifier == TOL_PMOD_SEARCH)
	{
		if (strcasecmp(attr, "Keywords") == 0)
		{
			fs << c.StrKeywords();
			return RES_OK;
		}
		sprintf(&m_coBuf, "select Message from Errors where Number = %ld and "
		        "(IdLanguage = %ld or IdLanguage = 1) order by IdLanguage desc",
		        c.SearchRes(), c.Language());
		SQLQuery(&m_coSql, &m_coBuf);
		res = mysql_store_result(&m_coSql);
		CheckForRows(*res, 1);
		row = mysql_fetch_row(*res);
		fs << row[0];
		return RES_OK;
	}
	if (modifier == TOL_PMOD_SUBTITLE)
	{
		fs << c.CurrentSubtitle();
		return RES_OK;
	}
	if (modifier == TOL_PMOD_LANGUAGE)
	{
		if (strcasecmp(attr, "number") == 0)
		{
			fs << c.Language();
			return RES_OK;
		}
		sprintf(&m_coBuf, "select %s from Languages where Id = %lu", attr, c.Language());
		SQLQuery(&m_coSql, &m_coBuf);
		res = mysql_store_result(&m_coSql);
		CheckForRows(*res, 1);
		row = mysql_fetch_row(*res);
		fs << row[0];
		return RES_OK;
	}
	string w, table, field;
	w = table = "";
	field = attr;
	bool need_lang = false;
	if (modifier == TOL_PMOD_IMAGE)
	{
		table = "Images";
		SetNrField("IdPublication", c.Publication(), &m_coBuf, w);
		SetNrField("NrIssue", c.Issue(), &m_coBuf, w);
		SetNrField("NrSection", c.Section(), &m_coBuf, w);
		SetNrField("NrArticle", c.Article(), &m_coBuf, w);
	}
	else if (modifier == TOL_PMOD_PUBLICATION)
	{
		if (c.Publication() < 0)
			return ERR_NOPARAM;
		table = "Publications";
		SetNrField("Id", c.Publication(), &m_coBuf, w);
	}
	else if (modifier == TOL_PMOD_ISSUE)
	{
		table = "Issues";
		if (c.Access() != A_ALL)
			w = "Published = 'Y'";
		need_lang = true;
		SetNrField("IdPublication", c.Publication(), &m_coBuf, w);
		SetNrField("Number", c.Issue(), &m_coBuf, w);
	}
	else if (modifier == TOL_PMOD_SECTION)
	{
		table = "Sections";
		need_lang = true;
		SetNrField("IdPublication", c.Publication(), &m_coBuf, w);
		SetNrField("NrIssue", c.Issue(), &m_coBuf, w);
		SetNrField("Number", c.Section(), &m_coBuf, w);
	}
	else if (modifier == TOL_PMOD_LANGUAGE)
	{
		table = "Languages";
		SetNrField("Id", c.Language(), &m_coBuf, w);
	}
	else
	{ // TOL_PMOD_ARTICLE
		table = "Articles";
		if (type[0])
			field = "Type, Number, IdLanguage";
		if (c.Access() != A_ALL)
			w = "Published = 'Y'";
		need_lang = true;
		SetNrField("IdPublication", c.Publication(), &m_coBuf, w);
		SetNrField("NrIssue", c.Issue(), &m_coBuf, w);
		SetNrField("NrSection", c.Section(), &m_coBuf, w);
		SetNrField("Number", c.Article(), &m_coBuf, w);
	}
	if (need_lang)
	{
		sprintf(&m_coBuf, "(IdLanguage = %ld or IdLanguage = 1)", c.Language());
		w += (w != "" ? string(" and ") : string("")) + &m_coBuf;
	}
	string coQuery = string("select ") + field + " from " + table;
	if (w != "")
		coQuery += string(" where ") + w;
	if (need_lang)
		coQuery += string(" order by IdLanguage desc");
	DEBUGAct("TakeAction()", coQuery.c_str(), fs);
	SQLQuery(&m_coSql, coQuery.c_str());
	res = mysql_store_result(&m_coSql);
	CheckForRows(*res, 1);
	row = mysql_fetch_row(*res);
	if (modifier == TOL_PMOD_ARTICLE && type[0])
	{
		table = string("X") + row[0];
		int blob;
		blob = BlobField((table).c_str(), attr);
		coQuery = string("select ") + attr + " from " + table + " where NrArticle = " + row[1]
		          + " and IdLanguage = " + row[2];
		DEBUGAct("TakeAction()", coQuery.c_str(), fs);
		SQLRealQuery(&m_coSql, coQuery.c_str(), strlen(coQuery.c_str()));
		StoreResult(&m_coSql, res2);
		CheckForRows(*res2, 1);
		FetchRow(*res2, row2);
		unsigned long* lengths = mysql_fetch_lengths(*res2);
		if (blob == 0)
		{
			cparser.SetDebug(*m_coDebug);
			cparser.Reset(row2[0], lengths[0]);
			cparser.Parse(c, fs, &m_coSql, c.StartSubtitle(), c.AllSubtitles(), true);
		}
		else if (DateField(table.c_str(), attr) == 0 && format != "")
		{
			string coDate(row2[0], lengths[0]);
			fs << DateFormat(coDate.c_str(), format.c_str(), c.Language());
		}
		else
			fs.write(row2[0], lengths[0]);
	}
	else
	{
		if (format != "")
			fs << DateFormat(row[0], format.c_str(), c.Language());
		else
			fs << row[0];
	}
	return RES_OK;
	TK_CATCH_ERR
}

// RunBlock: run actions in a list of actions
// Parameters:
//		TOLPActionList& al - list of actions
//		TOLContext& c - current context
//		fstream& fs - output stream
int TOLActIf::RunBlock(TOLPActionList& al, TOLContext& c, fstream& fs)
{
	for (TOLPActionList::iterator al_i = al.begin(); al_i != al.end(); ++al_i)
		(*al_i)->TakeAction(c, fs);
	return RES_OK;
}

// AccessAllowed: return true if access to hidden content is allowed
// Parameters:
//		TOLContext& c - current context
//		fstream& fs - output stream
bool TOLActIf::AccessAllowed(TOLContext& c, fstream& fs)
{
	sprintf(&m_coBuf, "select Public from Articles where IdPublication = %ld and "
	        "NrIssue = %ld and NrSection = %ld and Number = %ld and (IdLanguage "
	        "= %ld or IdLanguage = 1) order by IdLanguage desc", c.Publication(),
	        c.Issue(), c.Section(), c.Article(), c.Language());
	DEBUGAct("AccessAllowed()", &m_coBuf, fs);
	if (mysql_query(&m_coSql, &m_coBuf))
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

// destructor
TOLActIf::~TOLActIf()
{
	TOLPActionList::iterator al_i;
	for (al_i = block.begin(); al_i != block.end(); ++al_i)
	{
		delete (*al_i);
		*al_i = NULL;
	}
	block.clear();
	for (al_i = sec_block.begin(); al_i != sec_block.end(); ++al_i)
	{
		delete (*al_i);
		*al_i = NULL;
	}
	sec_block.clear();
}

// TOLActIf: assign operator
const TOLActIf& TOLActIf::operator =(const TOLActIf& p_rcoSrc)
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

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context
//		fstream& fs - output stream
int TOLActIf::TakeAction(TOLContext& c, fstream& fs)
{
	TK_TRY
	bool run_first;
	int run;
	if (modifier == TOL_IMOD_ALLOWED)
	{
		run_first = AccessAllowed(c, fs);
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			RunBlock(block, c, fs);
		else
			RunBlock(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == TOL_IMOD_LIST)
	{
		run_first = false;
		if (strcasecmp(param.Attribute(), "start") == 0)
			run_first = c.ListIndex() == 1;
		else if (strcasecmp(param.Attribute(), "end") == 0)
			run_first = c.ListIndex() == c.ListLength() && c.ListIndex() > 0;
		else
		{
			int val = strcasecmp(param.Attribute(), "row") == 0 ? c.ListRow()
			          : (strcasecmp(param.Attribute(), "column") == 0 ? c.ListColumn()
			             : (c.ListIndex() + c.ListStart(c.Level())));
			intHash::iterator i_i = rc_hash.find(val);
			if (i_i != rc_hash.end()
			    || (strcasecmp(param.Value(), "odd") == 0 && (val % 2) != 0)
			    || (strcasecmp(param.Value(), "even") == 0 && (val % 2) == 0))
			{
				run_first = true;
			}
		}
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			RunBlock(block, c, fs);
		else
			RunBlock(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == TOL_IMOD_PREVIOUSITEMS)
	{
		run_first = c.PrevStart(c.Level()) >= 0;
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
		{
			if (!m_bNegated)
				c.SetLMode(LM_PREV);
			RunBlock(block, c, fs);
			if (!m_bNegated)
				c.SetLMode(LM_NORMAL);
		}
		else
		{
			if (m_bNegated)
				c.SetLMode(LM_PREV);
			RunBlock(sec_block, c, fs);
			if (m_bNegated)
				c.SetLMode(LM_NORMAL);
		}
		return RES_OK;
	}
	else if (modifier == TOL_IMOD_NEXTITEMS)
	{
		run_first = c.NextStart(c.Level()) >= 0;
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
		{
			if (!m_bNegated)
				c.SetLMode(LM_NEXT);
			RunBlock(block, c, fs);
			if (!m_bNegated)
				c.SetLMode(LM_NORMAL);
		}
		else
		{
			if (m_bNegated)
				c.SetLMode(LM_NEXT);
			RunBlock(sec_block, c, fs);
			if (m_bNegated)
				c.SetLMode(LM_NORMAL);
		}
		return RES_OK;
	}
	else if (modifier == TOL_IMOD_SUBSCRIPTION)
	{
		run = -1;
		if (strcasecmp(param.Attribute(), "ok") == 0 && c.Subscribe())
			run = c.SubsRes() == 0 ? 0 : 1;
		if (strcasecmp(param.Attribute(), "error") == 0 && c.Subscribe())
			run = c.SubsRes() != 0 ? 0 : 1;
		if (strcasecmp(param.Attribute(), "action") == 0)
			run = c.Subscribe() ? 0 : 1;
		if (strcasecmp(param.Attribute(), "trial") == 0 && c.SubsType() != ST_NONE)
			run = c.SubsType() == ST_TRIAL ? 0 : 1;
		if (strcasecmp(param.Attribute(), "paid") == 0 && c.SubsType() != ST_NONE)
			run = c.SubsType() == ST_PAID ? 0 : 1;
		if (run == 0)
			RunBlock(block, c, fs);
		else if (run == 1)
			RunBlock(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == TOL_IMOD_USER)
	{
		run = -1;
		if (strcasecmp(param.Attribute(), "addok") == 0 && c.AddUser())
			run = c.AddUserRes() == 0 ? 0 : 1;
		if (strcasecmp(param.Attribute(), "modifyok") == 0 && c.ModifyUser())
			run = c.ModifyUserRes() == 0 ? 0 : 1;
		if (strcasecmp(param.Attribute(), "adderror") == 0 && c.AddUser())
			run = c.AddUserRes() != 0 ? 0 : 1;
		if (strcasecmp(param.Attribute(), "modifyerror") == 0 && c.ModifyUser())
			run = c.ModifyUserRes() != 0 ? 0 : 1;
		if (strcasecmp(param.Attribute(), "defined") == 0)
			run = c.User() >= 0 ? 0 : 1;
		if (strcasecmp(param.Attribute(), "addaction") == 0)
			run = c.AddUser() ? 0 : 1;
		if (strcasecmp(param.Attribute(), "modifyaction") == 0)
			run = c.ModifyUser() ? 0 : 1;
		if (strcasecmp(param.Attribute(), "loggedin") == 0)
			run = (c.User() >= 0 && c.Key() > 0) ? 0 : 1;
		if ((run == 0 && !m_bNegated) || (run == 1 && m_bNegated))
			RunBlock(block, c, fs);
		else if ((run == 1 && !m_bNegated) || (run == 0 && m_bNegated))
			RunBlock(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == TOL_IMOD_LOGIN)
	{
		run = -1;
		if (strcasecmp(param.Attribute(), "action") == 0)
			run = c.Login() ? 0 : 1;
		if (strcasecmp(param.Attribute(), "ok") == 0 && c.Login())
			run = c.LoginRes() == 0 ? 0 : 1;
		if (strcasecmp(param.Attribute(), "error") == 0 && c.Login())
			run = c.LoginRes() != 0 ? 0 : 1;
		if ((run == 0 && !m_bNegated) || (run == 1 && m_bNegated))
			RunBlock(block, c, fs);
		else if ((run == 1 && !m_bNegated) || (run == 0 && m_bNegated))
			RunBlock(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == TOL_IMOD_SEARCH)
	{
		run = -1;
		if (strcasecmp(param.Attribute(), "action") == 0)
			run = c.Search() ? 0 : 1;
		if (strcasecmp(param.Attribute(), "ok") == 0 && c.Search())
			run = c.SearchRes() == 0 ? 0 : 1;
		if (strcasecmp(param.Attribute(), "error") == 0 && c.Search())
			run = c.SearchRes() != 0 ? 0 : 1;
		if ((run == 0 && !m_bNegated) || (run == 1 && m_bNegated))
			RunBlock(block, c, fs);
		else if ((run == 1 && !m_bNegated) || (run == 0 && m_bNegated))
			RunBlock(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == TOL_IMOD_PREVSUBTITLES)
	{
		run_first = c.StartSubtitle() > 0 && !c.AllSubtitles();
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
		{
			if (!m_bNegated)
				c.SetStMode(STM_PREV);
			RunBlock(block, c, fs);
			if (!m_bNegated)
				c.SetStMode(STM_NORMAL);
		}
		else
		{
			if (m_bNegated)
				c.SetStMode(STM_PREV);
			RunBlock(sec_block, c, fs);
			if (m_bNegated)
				c.SetStMode(STM_NORMAL);
		}
		return RES_OK;
	}
	else if (modifier == TOL_IMOD_NEXTSUBTITLES)
	{
		run_first = c.StartSubtitle() < (c.SubtitlesNumber() - 1) && !c.AllSubtitles();
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
		{
			if (!m_bNegated)
				c.SetStMode(STM_NEXT);
			RunBlock(block, c, fs);
			if (!m_bNegated)
				c.SetStMode(STM_NORMAL);
		}
		else
		{
			if (m_bNegated)
				c.SetStMode(STM_NEXT);
			RunBlock(sec_block, c, fs);
			if (m_bNegated)
				c.SetStMode(STM_NORMAL);
		}
		return RES_OK;
	}
	else if (modifier == TOL_IMOD_SUBTITLE)
	{
		run_first = (c.StartSubtitle() + 1) == atol(param.Value()) && !c.AllSubtitles();
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			RunBlock(block, c, fs);
		else
			RunBlock(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == TOL_IMOD_CURRENTSUBTITLE)
	{
		run_first = (c.StartSubtitle() ) == (c.ListIndex() - 1) && !c.AllSubtitles();
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			RunBlock(block, c, fs);
		else
			RunBlock(sec_block, c, fs);
		return RES_OK;
	}
	else if (modifier == TOL_IMOD_IMAGE)
	{
		sprintf(&m_coBuf, "select count(*) from Images where IdPublication = %ld and NrIssue = %ld"
		        " and NrSection = %ld and NrArticle = %ld and Number = %ld", c.Publication(),
		        c.Issue(), c.Section(), c.Article(), atol(param.Attribute()));
		DEBUGAct("TakeAction()", &m_coBuf, fs);
		SQLQuery(&m_coSql, &m_coBuf);
		StoreResult(&m_coSql, res);
		CheckForRows(*res, 1);
		FetchRow(*res, row);
		if (row[0] == NULL)
			return -1;
		run_first = atoi(row[0]) > 0;
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			RunBlock(block, c, fs);
		else
			RunBlock(sec_block, c, fs);
		return RES_OK;
	}
	if (strcasecmp(param.Attribute(), "defined") == 0)
	{
		if (modifier == TOL_IMOD_LANGUAGE)
			run_first = c.Language() >= 0;
		else if (modifier == TOL_IMOD_PUBLICATION)
			run_first = c.Publication() >= 0;
		else if (modifier == TOL_IMOD_ISSUE)
			run_first = c.Issue() >= 0;
		else if (modifier == TOL_IMOD_SECTION)
			run_first = c.Section() >= 0;
		else if (modifier == TOL_IMOD_ARTICLE)
			run_first = c.Article() >= 0;
		else
			return RES_OK;
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			RunBlock(block, c, fs);
		else
			RunBlock(sec_block, c, fs);
		return RES_OK;
	}
	if (strcasecmp(param.Attribute(), "fromstart") == 0)
	{
		if (modifier == TOL_IMOD_LANGUAGE)
			run_first = c.Language() == c.DefLanguage();
		else if (modifier == TOL_IMOD_PUBLICATION)
			run_first = c.Publication() == c.DefPublication();
		else if (modifier == TOL_IMOD_ISSUE)
			run_first = c.Issue() == c.DefIssue();
		else if (modifier == TOL_IMOD_SECTION)
			run_first = c.Section() == c.DefSection();
		else if (modifier == TOL_IMOD_ARTICLE)
			run_first = c.Article() == c.DefArticle();
		else
			return RES_OK;
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			RunBlock(block, c, fs);
		else
			RunBlock(sec_block, c, fs);
		return RES_OK;
	}
	if (strcasecmp(param.Attribute(), "number") == 0)
	{
		long int nVal = 0;
		if (modifier == TOL_IMOD_LANGUAGE)
			nVal = c.Language();
		else if (modifier == TOL_IMOD_ISSUE)
			nVal = c.Issue();
		else if (modifier == TOL_IMOD_SECTION)
			nVal = c.Section();
		else
			return -1;
		long int nComp = atol(param.Value());
		switch (param.Operator())
		{
			case TOL_OP_IS: run_first = nVal == nComp; break;
			case TOL_OP_IS_NOT: run_first = nVal != nComp; break;
			case TOL_OP_GREATER: run_first = nVal > nComp; break;
			case TOL_OP_SMALLER: run_first = nVal < nComp; break;
			default: run_first = nVal == nComp;
		}
		run_first = m_bNegated ? !run_first : run_first;
		if (run_first)
			RunBlock(block, c, fs);
		else
			RunBlock(sec_block, c, fs);
		return RES_OK;
	}
	if (modifier != TOL_IMOD_LANGUAGE &&
	    (c.Language() < 0 || c.Publication() < 0 || c.Issue() < 0))
	{
		return ERR_NOPARAM;
	}
	string w, field, tables, value;
	field = param.Attribute();
	value = param.Value();
	bool need_lang = false;
	if (modifier == TOL_IMOD_LANGUAGE)
	{
		tables = "Languages";
		SetNrField("Id", c.Language(), &m_coBuf, w);
		need_lang = false;
	}
	else if (modifier == TOL_IMOD_ISSUE)
	{
		tables = "Issues";
		if (strcasecmp(param.Attribute(), "iscurrent") == 0)
		{
			sprintf(&m_coBuf, "max(Number) = %ld", c.Issue());
			field = &m_coBuf;
			value = "1";
			if (c.Access() != A_ALL)
				w = "Published = 'Y'";
		}
		else
		{
			SetNrField("Number", c.Issue(), &m_coBuf, w);
			need_lang = true;
		}
	}
	else if (modifier == TOL_IMOD_SECTION)
	{
		if (c.Section() < 0)
			return ERR_NOPARAM;
		tables = "Sections";
		SetNrField("Number", c.Section(), &m_coBuf, w);
		SetNrField("NrIssue", c.Issue(), &m_coBuf, w);
		need_lang = true;
	}
	else if (modifier == TOL_IMOD_ARTICLE)
	{
		if (c.Article() < 0)
			return ERR_NOPARAM;
		tables = "Articles";
		SetNrField("NrSection", c.Section(), &m_coBuf, w);
		SetNrField("Number", c.Article(), &m_coBuf, w);
		SetNrField("NrIssue", c.Issue(), &m_coBuf, w);
		need_lang = true;
	}
	if (modifier != TOL_IMOD_LANGUAGE)
		SetNrField("IdPublication", c.Publication(), &m_coBuf, w);
	if (need_lang)
	{
		sprintf(&m_coBuf, "(IdLanguage = %ld or IdLanguage = 1)", c.Language());
		w += (w != "" ? string(" and ") : string("")) + &m_coBuf;
	}
	string coQuery = string("select ") + field + " from " + tables;
	if (strlen(w.c_str()))
		coQuery += string(" where ") + w;
	if (need_lang)
		coQuery += " order by IdLanguage desc";
	DEBUGAct("TakeAction()", coQuery.c_str(), fs);
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	switch (param.Operator())
	{
		case TOL_OP_IS: run_first = strcasecmp(value.c_str(), row[0]) == 0; break;
		case TOL_OP_IS_NOT: run_first = strcasecmp(value.c_str(), row[0]) != 0; break;
		default: run_first = strcasecmp(value.c_str(), row[0]) == 0;
	}
	run_first = m_bNegated ? !run_first : run_first;
	if (run_first)
		RunBlock(block, c, fs);
	else
		RunBlock(sec_block, c, fs);
	return RES_OK;
	TK_CATCH_ERR
}

// constructor
// Parameters:
//		cpChar d - date attribute
TOLActDate::TOLActDate(cpChar d)
		: TOLAction(TOL_ACT_DATE)
{
	if (d)
	{
		strncpy(attr, d, ID_MAXLEN > strlen(d) ? strlen(d) : ID_MAXLEN);
		attr[ID_MAXLEN > strlen(d) ? strlen(d) : ID_MAXLEN] = 0;
	}
	else
		attr[0] = 0;
}

// TOLActDate: assign operator
const TOLActDate& TOLActDate::operator =(const TOLActDate& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	strcpy(attr, p_rcoSrc.attr);
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context
//		fstream& fs - output stream
int TOLActDate::TakeAction(TOLContext& c, fstream& fs)
{
	cpChar format;
	if (strcasecmp(attr, "year") == 0)
		format = "%Y";
	else if (strcasecmp(attr, "mon_nr") == 0)
		format = "%c";
	else if (strcasecmp(attr, "mday") == 0)
		format = "%e";
	else if (strcasecmp(attr, "yday") == 0)
		format = "%j";
	else if (strcasecmp(attr, "wday_nr") == 0)
		format = "%u";
	else if (strcasecmp(attr, "hour") == 0)
		format = "%k";
	else if (strcasecmp(attr, "min") == 0)
		format = "%i";
	else if (strcasecmp(attr, "sec") == 0)
		format = "%s";
	else if (strcasecmp(attr, "mon_name") == 0)
		format = "%M";
	else if (strcasecmp(attr, "wday_name") == 0)
		format = "%W";
	else
		format = attr;
	string coQuery = string("select now()");
	SQLQuery(&m_coSql, coQuery.c_str());
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	pChar pchVal = SQLEscapeString(format, strlen(format));
	if (pchVal == NULL)
		return ERR_NOMEM;
	fs << DateFormat(row[0], pchVal, c.Language());
	delete pchVal;
	return RES_OK;
}

// TOLActText: assign operator
const TOLActText& TOLActText::operator =(const TOLActText& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	text = p_rcoSrc.text;
	text_len = p_rcoSrc.text_len;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context
//		fstream& fs - output stream
int TOLActText::TakeAction(TOLContext& c, fstream& fs)
{
	fs.write(text, text_len);
	return RES_OK;
}

// TOLActLocal: assign operator
const TOLActLocal& TOLActLocal::operator =(const TOLActLocal& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	block = p_rcoSrc.block;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context
//		fstream& fs - output stream
int TOLActLocal::TakeAction(TOLContext& c, fstream& fs)
{
	TOLContext lc = c;
	for (TOLPActionList::iterator al_i = block.begin(); al_i != block.end(); ++al_i)
	{
		if (DoDebug())
			fs << "<!-- Local: taking action " << (*al_i)->ClassName() << " -->\n";
		int res = (*al_i)->TakeAction(lc, fs);
		if (DoDebug())
			fs << "<!-- Local: action " << (*al_i)->ClassName() << " result: " << res << " -->\n";
	}
	return RES_OK;
}

// TOLActSubscription: assign operator
const TOLActSubscription& TOLActSubscription::operator =(const TOLActSubscription& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	block = p_rcoSrc.block;
	tpl_file = p_rcoSrc.tpl_file;
	button_name = p_rcoSrc.button_name;
	total = p_rcoSrc.total;
	evaluate = p_rcoSrc.evaluate;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context
//		fstream& fs - output stream	
int TOLActSubscription::TakeAction(TOLContext& c, fstream& fs)
{
	TK_TRY
	if (c.SubsType() == ST_NONE)
	{
		sprintf(&m_coBuf, "select Subs.Type, Usr.CountryCode from Subscriptions as Subs, "
		        "Users as Usr where Subs.IdUser = Usr.Id and IdUser = %ld and IdPublication = %ld",
		        c.User(), c.Publication());
		SQLQuery(&m_coSql, &m_coBuf);
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
	}
	sprintf(&m_coBuf, "select UnitCost, Currency from Publications where Id = %ld",
			c.Publication());
	SQLQuery(&m_coSql, &m_coBuf);
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	if (row[0] == NULL)
		return -1;
	double unit_cost = atof(row[0]);
	string currency = row[1];
	sprintf(&m_coBuf, "select count(*) from Sections where IdPublication = %ld and "
	        "NrIssue = %ld and IdLanguage = %ld", c.Publication(), c.Issue(), c.Language());
	SQLQuery(&m_coSql, &m_coBuf);
	res = mysql_store_result(&m_coSql);
	CheckForRows(*res, 1);
	row = mysql_fetch_row(*res);
	if (row[0] == NULL)
		return -1;
	long int nos = atol(row[0]);
	TOLContext lc = c;
	lc.SetByPublication(by_publication);
	fs << "<form action=\"" << tpl_file << "\" name=\"f1\" method=POST>";
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
	for (TOLPActionList::iterator al_i = block.begin(); al_i != block.end(); ++al_i)
		(*al_i)->TakeAction(lc, fs);
	if (c.SubsType() == ST_PAID && total != "" && !by_publication)
		fs << total << " <input type=text name=suma size=10 READONLY> " << currency;
	fs << "<input type=hidden name=\"" P_IDPUBL "\" value=\"" << c.Publication()
	<< "\"><input type=hidden name=\"" P_IDLANG "\" value=\"" << c.Language()
	<< "\"><input type=hidden name=\"" P_NRISSUE "\" value=\"" << c.Issue() << "\">";
	if (c.SubsType() == ST_PAID && total != "" && !by_publication)
		fs << "<input type=hidden name=\"unitcost\" value=\"" << unit_cost
		<< "\"><input type=hidden name=nos value=\"" << nos << "\">"
		<< "<p><input type=button value=\"" << evaluate << "\" onclick=\"f()\"> ";
	if (c.SubsType() == ST_PAID)
		fs << "<input type=hidden name=SubsType value=\"paid\"> ";
	else
		fs << "<input type=hidden name=SubsType value=\"trial\">";
	if (by_publication)
		fs << "<input type=hidden name=by value=publication>";
	fs << " <input type=submit name=\"" P_SUBSCRIBE "\" value=\"" << button_name
	   << "\"></form>";
	return RES_OK;
	TK_CATCH_ERR
}

// TOLActEdit: assign operator
const TOLActEdit& TOLActEdit::operator =(const TOLActEdit& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	modifier = p_rcoSrc.modifier;
	field = p_rcoSrc.field;
	size = p_rcoSrc.size;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context
//		fstream& fs - output stream	
int TOLActEdit::TakeAction(TOLContext& c, fstream& fs)
{
	TK_TRY
	DEBUGAct("TakeAction()", field.c_str(), fs);
	if (modifier == TOL_EMOD_USER)
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
			cpChar r = strchr(row[1], '(');
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
	if (modifier == TOL_EMOD_SUBSCRIPTION)
	{
		sprintf(&m_coBuf, "select TrialTime, PaidTime from SubsDefTime, Users where "
		        "SubsDefTime.CountryCode = Users.CountryCode and SubsDefTime."
		        "IdPublication = %ld and Users.Id = %ld", c.Publication(), c.User());
		DEBUGAct("TakeAction()", &m_coBuf, fs);
		SQLQuery(&m_coSql, &m_coBuf);
		StoreResult(&m_coSql, res);
		if (mysql_num_rows(*res) > 0)
		{
			FetchRow(*res, row);
			fs << "<input type=hidden name=\"" << P_TX_SUBS << c.Section()
			<< "\" value=\"" << (c.SubsType() == ST_TRIAL ? row[0] : row[1])
			<< "\">" << (c.SubsType() == ST_TRIAL ? row[0] : row[1]);
		}
		else
			fs << "<input type=text name=\"" << P_TX_SUBS << c.Section() << "\">";
	}
	if (modifier == TOL_EMOD_LOGIN)
	{
		if (field == "Password")
			fs << "<input type=password name=\"Login" << field << "\" maxlength=32 size=10>";
		else
			fs << "<input type=text name=\"Login" << field << "\" maxlength=32 size=10>";
	}
	if (modifier == TOL_EMOD_SEARCH)
	{
		if (field == "Keywords")
		{
			cpChar pchEscKw = EscapeHTML(c.StrKeywords());
			fs << "<input type=text name=\"Search" << field << "\" maxlength=255 "
			"size=" << size << " value=\"" << pchEscKw << "\">";
			delete pchEscKw;
		}
	}
	return RES_OK;
	TK_CATCH_ERR
}

// TOLActSelect: assign operator
const TOLActSelect& TOLActSelect::operator =(const TOLActSelect& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	modifier = p_rcoSrc.modifier;
	field = p_rcoSrc.field;
	male_name = p_rcoSrc.male_name;
	female_name = p_rcoSrc.female_name;
	checked = p_rcoSrc.checked;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context
//		fstream& fs - output stream	
int TOLActSelect::TakeAction(TOLContext& c, fstream& fs)
{
	TK_TRY
	if (modifier == TOL_SMOD_SUBSCRIPTION)
	{
		if (c.ByPublication())
			fs << "<input type=hidden name=\"" << P_CB_SUBS << "\" value=\""
			<< c.Section() << "\">";
		else
			fs << "<input type=checkbox name=\"" << P_CB_SUBS << "\" value=\""
			<< c.Section() << "\">";
	}
	else if (modifier == TOL_SMOD_USER)
	{
		string attrval = c.UserInfo(field);
		if (strcasecmp(field.c_str(), "CountryCode") == 0)
		{
			sprintf(&m_coBuf, "select Code, Name from Countries where IdLanguage = %ld",
			        c.Language());
		}
		else if (strcasecmp(field.c_str(), "Title") == 0)
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
		else if (strcasecmp(field.c_str(), "Gender") == 0)
		{
			fs << "<input type=radio name=\"User" << field << "\" value=\"M\""
			<< (attrval == "M" ? " checked" : "") << ">" << male_name
			<< " <input type=radio name=\"User" << field << "\" value=\"F\""
			<< (attrval == "F" ? " checked" : "") << ">" << female_name;
			return RES_OK;
		}
		else if (strcasecmp(field.c_str(), "Age") == 0)
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
		else if (strcasecmp(field.c_str(), "EmployerType") == 0)
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
			if (attrval == "on" || checked)
				fs << " value=\"on\" checked>";
			return RES_OK;
		}
		else
			return ERR_INVALID_FIELD;
		SQLQuery(&m_coSql, &m_coBuf);
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
	else if (modifier == TOL_SMOD_SEARCH)
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
	return RES_OK;
	TK_CATCH_ERR
}

// TOLActUser: assign operator
const TOLActUser& TOLActUser::operator =(const TOLActUser& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	block = p_rcoSrc.block;
	add = p_rcoSrc.add;
	tpl_file = p_rcoSrc.tpl_file;
	button_name = p_rcoSrc.button_name;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context
//		fstream& fs - output stream	
int TOLActUser::TakeAction(TOLContext& c, fstream& fs)
{
	TK_TRY
	static const char *params[27] =
	    {
	        "Name", "UName", "EMail", "City", "StrAddress", "State", "CountryCode",
	        "Phone", "Fax", "Contact", "Phone2", "Title", "Gender", "Age",
	        "PostalCode", "Employer", "EmployerType", "Position", "Languages", "Pref1",
	        "Pref2", "Pref3", "Field1", "Field2", "Field3", "Field4", "Field5"
	    };
	if (c.Key() <= 0 && !add)
		return ERR_NOKEY;
	fs << "<form action=\"" << tpl_file << "\" method=POST><input type=hidden "
	"name=\"" P_IDLANG "\" value=\"" << c.Language() << "\"><input type=hidden"
	" name=\"" P_IDPUBL "\" value=\"" << c.Publication() << "\"><input "
	"type=hidden name=\"" P_NRISSUE "\" value=\"" << c.Issue() << "\">";
	if (c.SubsType() != ST_NONE)
		fs << "<input type=hidden name=\"SubsType\" value=\""
		<< (c.SubsType() == ST_TRIAL ? "trial" : "paid") << "\">";
	TOLContext lc = c;
	if (!add)
	{
		sprintf(&m_coBuf, "select %s", params[0]);
		for (int i = 1; i < 27; i++)
			sprintf(&m_coBuf + strlen(&m_coBuf), ", %s", params[i]);
		sprintf(&m_coBuf + strlen(&m_coBuf), " from Users where Id = %ld", c.User());
		DEBUGAct("TakeAction()", &m_coBuf, fs);
		SQLQuery(&m_coSql, &m_coBuf);
		StoreResult(&m_coSql, res);
		CheckForRows(*res, 1);
		FetchRow(*res, row);
		for (int i = 0; i < 27; i++)
			if (!lc.IsUserInfo(string(params[i])))
				lc.SetUserInfo(string(params[i]), string(row[i]));
	}
	TOLPActionList::iterator al_i;
	for (al_i = block.begin(); al_i != block.end(); ++al_i)
		(*al_i)->TakeAction(lc, fs);
	fs << "<input type=submit name=\"" << (add ? P_USERADD : P_USERMODIFY)
	<< "\" value=\"" << button_name << "\"></form>";
	return RES_OK;
	TK_CATCH_ERR
}

// TOLActLogin: assign operator
const TOLActLogin& TOLActLogin::operator =(const TOLActLogin& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	block = p_rcoSrc.block;
	tpl_file = p_rcoSrc.tpl_file;
	button_name = p_rcoSrc.button_name;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context
//		fstream& fs - output stream	
int TOLActLogin::TakeAction(TOLContext& c, fstream& fs)
{
	TOLPActionList::iterator al_i;
	TOLContext lc = c;
	fs << "<form action=\"" << tpl_file << "\" method=POST><input type=hidden "
	"name=\"" P_IDLANG "\" value=\"" << c.Language() << "\"><input type=hidden"
	" name=\"" P_IDPUBL "\" value=\"" << c.Publication() << "\"><input "
	"type=hidden name=\"" P_NRISSUE "\" value=\"" << c.Issue() << "\">";
	for (al_i = block.begin(); al_i != block.end(); ++al_i)
		(*al_i)->TakeAction(lc, fs);
	fs << "<input type=submit name=\"" P_LOGIN "\" value=\"" << button_name << "\"></form>";
	return RES_OK;
}

// TOLActSearch: assign operator
const TOLActSearch& TOLActSearch::operator =(const TOLActSearch& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	block = p_rcoSrc.block;
	tpl_file = p_rcoSrc.tpl_file;
	button_name = p_rcoSrc.button_name;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context
//		fstream& fs - output stream	
int TOLActSearch::TakeAction(TOLContext& c, fstream& fs)
{
	TOLPActionList::iterator al_i;
	TOLContext lc = c;
	fs << "<form action=\"" << tpl_file << "\" method=POST><input type=hidden "
	"name=\"" P_IDLANG "\" value=\"" << c.Language() << "\"><input type=hidden"
	" name=\"" P_IDPUBL "\" value=\"" << c.Publication() << "\"><input type="
	"hidden name=\"" P_NRISSUE "\" value=\"" << c.Issue() << "\"><input "
	"type=hidden name=\"" P_NRSECTION "\" value=\"" << c.Section() << "\">";
	for (al_i = block.begin(); al_i != block.end(); ++al_i)
		(*al_i)->TakeAction(lc, fs);
	fs << "<input type=submit name=\"" P_SEARCH "\" value=\"" << button_name << "\"></form>";
	return RES_OK;
}

// TOLActWith: assign operator
const TOLActWith& TOLActWith::operator =(const TOLActWith& p_rcoSrc)
{
	if (this == &p_rcoSrc)
		return * this;
	block = p_rcoSrc.block;
	field = p_rcoSrc.field;
	art_type = p_rcoSrc.art_type;
	cparser = p_rcoSrc.cparser;
	return *this;
}

// TakeAction: performs the action
// Parametes:
//		TOLContext& c - current context
//		fstream& fs - output stream	
int TOLActWith::TakeAction(TOLContext& c, fstream& fs)
{
	TK_TRY
	TOLPActionList::iterator al_i;
	TOLContext lc = c;
	lc.SetCurrentField(field);
	lc.SetCurrentArtType(art_type);
	sprintf(&m_coBuf, "select F%s from X%s where NrArticle = %ld and IdLanguage = %ld",
	        field.c_str(), art_type.c_str(), c.Article(), c.Language());
	DEBUGAct("TakeAction()", &m_coBuf, fs);
	SQLQuery(&m_coSql, &m_coBuf);
	StoreResult(&m_coSql, res);
	CheckForRows(*res, 1);
	FetchRow(*res, row);
	cparser.SetDebug(*m_coDebug);
	cparser.Reset(row[0], (mysql_fetch_lengths(*res))[0]);
	cparser.Parse(lc, fs, &m_coSql, 0, true, false);
	for (al_i = block.begin(); al_i != block.end(); ++al_i)
		(*al_i)->TakeAction(lc, fs);
	return RES_OK;
	TK_CATCH_ERR
}
