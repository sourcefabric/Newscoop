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
 
TOLParser methods implementation
 
******************************************************************************/

#include <stdio.h>
#include <string.h>
#include <list>
#include <unistd.h>
#include <sys/mman.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>

#include "tol_actions.h"
#include "tol_parser.h"
#include "tol_util.h"
#include "tol_error.h"


#define ROOT_STATEMENTS ST_PUBLICATION ", " ST_ISSUE ", " ST_SECTION ", "\
ST_ARTICLE ", " ST_LIST ", " ST_INCLUDE ", " ST_IF ", " ST_URLPARAMETERS ", "\
ST_FORMPARAMETERS ", " ST_PRINT ", " ST_DATE ", " ST_LOCAL ", "\
ST_SUBSCRIPTION ", " ST_EDIT ", " ST_SELECT ", " ST_USER ", " ST_SEARCH

#define LISSUE_STATEMENTS ST_LIST ", " ST_SECTION ", " ST_ARTICLE ", "\
ST_URLPARAMETERS ", " ST_FORMPARAMETERS ", " ST_PRINT ST_DATE ", "\
ST_INCLUDE ", " ST_IF ", " ST_LOCAL ", " ST_SUBSCRIPTION ", " ST_EDIT\
", " ST_SELECT ", " ST_USER ", " ST_SEARCH

#define LSECTION_STATEMENTS ST_LIST ", " ST_ARTICLE ", " ST_URLPARAMETERS\
", " ST_FORMPARAMETERS ", " ST_PRINT ST_DATE ", " ST_INCLUDE ", " ST_IF\
"," ST_LOCAL ", " ST_SUBSCRIPTION ", " ST_EDIT ", " ST_SELECT ", " ST_USER\
", " ST_SEARCH

#define LARTICLE_STATEMENTS ST_URLPARAMETERS ", " ST_FORMPARAMETERS ", "\
ST_PRINT ", " ST_DATE ", " ST_INCLUDE ", " ST_IF ", " ST_LOCAL ", "\
ST_SUBSCRIPTION ", " ST_EDIT ", " ST_SELECT ", " ST_USER ", " ST_SEARCH

#define LIST_STATEMENTS ST_ISSUE ", " ST_SECTION ", " ST_ARTICLE ", "\
ST_SEARCHRESULT

#define CL_TOLATOM "TOLAtom"
#define CL_TOLATTRIBUTE "TOLAttribute"
#define CL_TOLSTATEMENT "TOLStatement"

#define LV_ROOT 1
#define LV_LISSUE 2
#define LV_LSECTION 4
#define LV_LARTICLE 8
#define LV_LSUBTITLE 16

#define SUBLV_NONE 0
#define SUBLV_IFPREV 1
#define SUBLV_IFNEXT 2
#define SUBLV_IFLIST 4
#define SUBLV_IFISSUE 8
#define SUBLV_IFSECTION 16
#define SUBLV_IFARTICLE 32
#define SUBLV_EMPTYLIST 64
#define SUBLV_IFALLOWED 128
#define SUBLV_SUBSCRIPTION 256
#define SUBLV_USER 512
#define SUBLV_LOGIN 1024
#define SUBLV_LOCAL 2048
#define SUBLV_IFPUBLICATION 4096
#define SUBLV_SEARCH 8192
#define SUBLV_SEARCHRESULT 16384
#define SUBLV_WITH 32768


// macro definition

#define ONE_OF_LV(cl, gl) (cl & (gl))

#define CheckForLevel(lv, lvs, line, column)\
{\
if (!ONE_OF_LV(lv, lvs)) {\
SetPError(parse_err, PERR_INVALID_STATEMENT, MODE_PARSE,\
LvStatements(lv), line, column);\
WaitForStatementEnd(false);\
return 1;\
}\
}

#define CheckNotLevel(lv, lvs, line, column)\
{\
if (ONE_OF_LV(lv, lvs)) {\
SetPError(parse_err, PERR_INVALID_STATEMENT, MODE_PARSE,\
LvStatements(lv), line, column);\
WaitForStatementEnd(false);\
return 1;\
}\
}

#define CheckForStatement(l, req, r, c)\
{\
if (l->m_Res != TOL_LEX_STATEMENT) {\
SetPError(parse_err, PERR_STATEMENT_MISSING, MODE_PARSE, req, r, c);\
WaitForStatementEnd(false);\
}\
}
//  return 1;

#define CheckForIdentifier(l, req, r, c)\
{\
if (l->m_Res != TOL_LEX_IDENTIFIER) {\
SetPError(parse_err, PERR_IDENTIFIER_MISSING, MODE_PARSE, req, r, c);\
WaitForStatementEnd(false);\
return 1;\
}\
}

#define CheckForAtom(l)\
{\
if (l->m_pcoAtom == 0)\
FatalPError(parse_err, PERR_NO_ATOM_IN_LEXEM, MODE_PARSE, "",\
lex.PrevLine(), lex.PrevColumn());\
}

#define CheckForAtomType(a, tn, er, req, line, column)\
{\
if (strcmp(a->ClassName(), tn))\
FatalPError(parse_err, er, MODE_PARSE, req, line, column);\
}

#define CheckForText(al, l)\
{\
if (l->m_pchTextStart && l->m_nTextLen > 0)\
al.insert(al.end(), new TOLActText(l->m_pchTextStart, l->m_nTextLen));\
}

#define CheckForEOF(l, err)\
{\
if (l->m_Res == TOL_ERR_EOF) {\
if (err > 0)\
SetPError(parse_err, err, MODE_PARSE, "", lex.PrevLine(), lex.PrevColumn());\
return -1;\
}\
}

#define ValidateAttr(attr, st, lexem, context, rv)\
const TOLAttribute* attr;\
if ((attr = st->FindAttr(lexem->m_pcoAtom->Identifier(), context)) == 0) {\
string a_req;\
st->PrintAttrs(a_req, context);\
SetPError(parse_err, PERR_INVALID_ATTRIBUTE, MODE_PARSE, \
a_req.c_str(), lex.PrevLine(), lex.PrevColumn());\
WaitForStatementEnd(false);\
return rv;\
}

#define ValidateOperator(op_i, lexem, dt, op_r)\
{\
if ((op_i = op_map.find(lexem->m_pcoAtom->Identifier())) == op_map.end()\
|| (TypeOperators(dt) & (TOperator)(*op_i).second) == 0) {\
SetPError(parse_err, PERR_INVALID_OPERATOR, MODE_PARSE,\
(strlen(op_r) ? op_r : DTOperators(dt)), lex.PrevLine(), lex.PrevColumn());\
WaitForStatementEnd(false);\
return 1;\
}\
}

#define ValidateDType(l, dt, res)\
{\
if (!ValidDataType(l, dt)) {\
SetPError(parse_err, PERR_DATA_TYPE, MODE_PARSE, TypeName(dt),\
lex.PrevLine(), lex.PrevColumn());\
return res;\
}\
}

#define CheckForEndSt(l)\
{\
if (l->m_Res == TOL_LEX_END_STATEMENT) {\
SetPError(parse_err, PERR_IDENTIFIER_MISSING, MODE_PARSE, "",\
lex.PrevLine(), lex.PrevColumn());\
}\
}
//  return 1;

#define RequireAtom(l)\
{\
l = lex.GetLexem();\
DEBUGLexem("req atom", l);\
CheckForEOF(l, PERR_EOS_MISSING);\
CheckForEndSt(l);\
CheckForAtom(l);\
}

// end macro definition


String2Int TOLParser::op_map;
pthread_once_t TOLParser::m_OpMapControl = PTHREAD_ONCE_INIT;
TOLParserHash TOLParser::m_coPHash(4, cpCharHashFn, cpCharEqual, TOLParserValue);
CMutex TOLParser::m_coHashMutex;
TK_MYSQL TOLParser::s_MYSQL(NULL);

// OpMapInit: called only once to initialise the operators map
void TOLParser::OpMapInit()
{
	op_map["is"] = (int)TOL_OP_IS;
	op_map["not"] = (int)TOL_OP_IS_NOT;
	op_map["greater"] = (int)TOL_OP_GREATER;
	op_map["smaller"] = (int)TOL_OP_SMALLER;
}

// MapTpl: map template file to m_pchTplBuf buffer
void TOLParser::MapTpl() throw (ExStat)
{
	if (tpl == NULL)
		return ;
	struct stat last_tpl_stat;
	try
	{
		int res;
		if (m_nTplFD >= 0)
			res = fstat(m_nTplFD, &last_tpl_stat);
		else
			res = stat(tpl, &last_tpl_stat);
		if (res != 0)
			throw ExStat(EMAP_STAT);
		if (!S_ISREG(last_tpl_stat.st_mode))
			throw ExStat(EMAP_NOTREGFILE);
		if (tpl_stat.st_mtime != last_tpl_stat.st_mtime)
			parsed = false;
		if (tpl_stat.st_ctime != last_tpl_stat.st_ctime || m_pchTplBuf == NULL)
		{
			parsed = false;
			UnMapTpl();
			lex.Reset(NULL, 0);
			if ((m_nTplFD = open(tpl, O_RDONLY)) < 0)
				throw ExStat(EMAP_EOPENFILE);
			m_pchTplBuf = (cpChar)mmap(0, last_tpl_stat.st_size, PROT_READ, MAP_SHARED, m_nTplFD, 0);
			close(m_nTplFD);
			m_nTplFD = -1;
			if (m_pchTplBuf == MAP_FAILED || m_pchTplBuf == NULL)
				throw ExStat(EMAP_FAILED);
			m_nTplFileLen = last_tpl_stat.st_size;
			lex.Reset(m_pchTplBuf, m_nTplFileLen);
		}
		tpl_stat = last_tpl_stat;
	}
	catch (ExStat& rcoEx)
	{
		tpl_stat = last_tpl_stat;
		throw rcoEx;
	}
}

// UnMapTpl: unmap template file
void TOLParser::UnMapTpl()
{
	lex.Reset(NULL, 0);
	if (m_pchTplBuf != NULL)
	{
		munmap((void*)m_pchTplBuf, m_nTplFileLen);
		m_pchTplBuf = NULL;
		m_nTplFileLen = 0;
	}
	if (m_nTplFD >= 0)
	{
		close(m_nTplFD);
		m_nTplFD = -1;
	}
}

// clearParseErrors: clear parse errors list
void TOLParser::clearParseErrors()
{
	m_coOpMutex.Lock();
	for (TOLErrorList::iterator coIt = parse_err.begin(); coIt != parse_err.end(); ++coIt)
	{
		delete *coIt;
		*coIt = NULL;
	}
	parse_err.clear();
	m_coOpMutex.Unlock();
}

// clearWriteErrors: clear write errors list
void TOLParser::clearWriteErrors()
{
	m_coOpMutex.Lock();
	for (TOLErrorList::iterator coIt = write_err.begin(); coIt != write_err.end(); ++coIt)
	{
		delete *coIt;
		*coIt = NULL;
	}
	write_err.clear();
	m_coOpMutex.Unlock();
}

// LvStatements: return string containig statement names allowed in a given level:
// root, list issue, list section, list article, list subtitle
// Parameters:
//		int level - level: LV_ROOT, LV_LISSUE, LV_LSECTION, LV_LARTICLE, LV_LSUBTITLE
cpChar TOLParser::LvStatements(int level)
{
	if (level & LV_LARTICLE || level & LV_LSUBTITLE)
		return LARTICLE_STATEMENTS;
	else if (level & LV_LSECTION)
		return LSECTION_STATEMENTS;
	else if (level & LV_LISSUE)
		return LISSUE_STATEMENTS;
	else if (level & LV_ROOT)
		return ROOT_STATEMENTS;
	else
		return "";
}

// LvListSt: return string containig list type statements allowed in a given level:
// root, list issue, list section, list article, list subtitle
// Parameters:
//		int level - level: LV_ROOT, LV_LISSUE, LV_LSECTION, LV_LARTICLE, LV_LSUBTITLE
cpChar TOLParser::LvListSt(int level)
{
	if (level & LV_LARTICLE || level & LV_LSUBTITLE)
		return ST_SEARCH;
	else if (level & LV_LSECTION)
		return ST_ARTICLE ", " ST_SEARCH;
	else if (level & LV_LISSUE)
		return ST_SECTION ", " ST_ARTICLE ", " ST_SEARCH;
	else if (level & LV_ROOT)
		return ST_ISSUE ", " ST_SECTION ", " ST_ARTICLE ", " ST_SEARCH;
	else
		return "";
}

// IfStatements: return string containing if type statements allowed in a given level
// (root, list issue, list section, list article, list subtitle)
// and sublevel (user, login, search, with etc.)
// Parameters:
//		int level - level: LV_ROOT, LV_LISSUE, LV_LSECTION, LV_LARTICLE, LV_LSUBTITLE
//		int sublevel - sublevel: SUBLV_NONE, SUBLV_IFPREV, SUBLV_IFNEXT, SUBLV_IFLIST,
//			SUBLV_IFISSUE, SUBLV_IFSECTION, SUBLV_IFARTICLE, SUBLV_EMPTYLIST,
//			SUBLV_IFALLOWED, SUBLV_SUBSCRIPTION, SUBLV_USER, SUBLV_LOGIN, SUBLV_LOCAL
//			SUBLV_IFPUBLICATION, SUBLV_SEARCH, SUBLV_SEARCHRESULT, SUBLV_WITH
string TOLParser::IfStatements(int level, int sublevel)
{
	string s_str;
	if (level == LV_ROOT || sublevel & SUBLV_EMPTYLIST)
		s_str = ST_PUBLICATION ", " ST_ISSUE ", " ST_SECTION ", " ST_ARTICLE ", " ST_ALLOWED;
	else if (sublevel & SUBLV_IFNEXT || sublevel & SUBLV_IFPREV)
		s_str = ST_LIST ", " ST_PUBLICATION ", " ST_ISSUE ", " ST_SECTION ", "
		        ST_ARTICLE ", " ST_ALLOWED;
	else
		s_str = ST_PREVIOUSITEMS ", " ST_NEXTITEMS ", " ST_LIST ", " ST_PUBLICATION
		        ", " ST_ISSUE ", " ST_SECTION ", " ST_ARTICLE ", " ST_ALLOWED;
	s_str += ", " ST_IF " " ST_PUBLICATION "|" ST_ISSUE "|" ST_SECTION
	         "|" ST_ARTICLE", " ST_SUBSCRIPTION;
	if ((sublevel & SUBLV_USER) == 0)
		s_str += ", " ST_USER;
	if ((sublevel & SUBLV_LOGIN) == 0)
		s_str += ", " ST_LOGIN;
	if ((sublevel & SUBLV_SEARCH) == 0)
		s_str += ", " ST_SEARCH;
	if ((sublevel & SUBLV_WITH) == 0)
		s_str += ", " ST_SUBTITLE;
	if (level & LV_LSUBTITLE && sublevel & SUBLV_WITH)
	{
		s_str += ", " ST_CURRENTSUBTITLE;
	}
	return s_str;
}

// PrintStatements: return string containig print type statements allowed in a given level
// (root, list issue, list section, list article, list subtitle)
// and sublevel (user, login, search, with etc.)
// Parameters:
//		int level
//		int sublevel
string TOLParser::PrintStatements(int level, int sublevel)
{
	string s_str;
	s_str = ST_PUBLICATION ", " ST_ISSUE ", " ST_SECTION ", " ST_ARTICLE ", " ST_IMAGE ", "
	        ST_SEARCH ", " ST_SUBSCRIPTION ", " ST_USER;
	if (level > LV_ROOT || sublevel & SUBLV_SEARCHRESULT)
		s_str += ", " ST_LIST;
	if ((sublevel & SUBLV_LOGIN) == 0)
		s_str += ", " ST_LOGIN;
	if ((sublevel & SUBLV_SEARCH) == 0)
		s_str += ", " ST_SEARCH;
	if (level & LV_LSUBTITLE)
		s_str += ", " ST_SUBTITLE;
	return s_str;
}

// EditStatements: return string containig edit type statements allowed in a given
// sublevel (user, login, search, with etc.)
// Parameters:
//		int sublevel
string TOLParser::EditStatements(int sublevel)
{
	string s_str;
	if (sublevel & SUBLV_SUBSCRIPTION)
		s_str += (s_str == "" ? string("") : string(", ")) + ST_SUBSCRIPTION;
	if (sublevel & SUBLV_USER)
		s_str += (s_str == "" ? string("") : string(", ")) + ST_USER;
	if ((sublevel & SUBLV_LOGIN) == 0)
		s_str += (s_str == "" ? string("") : string(", ")) + ST_LOGIN;
	if ((sublevel & SUBLV_SEARCH) == 0)
		s_str += (s_str == "" ? string("") : string(", ")) + ST_SEARCH;
	return s_str;
}

// SelectStatements: return string containig select type statements allowed in a given
// sublevel (user, login, search, with etc.)
// Parameters:
//		int sublevel
string TOLParser::SelectStatements(int sublevel)
{
	string s_str;
	if (sublevel & SUBLV_SUBSCRIPTION)
		s_str += (s_str == "" ? string("") : string(", ")) + ST_SUBSCRIPTION;
	if (sublevel & SUBLV_USER)
		s_str += (s_str == "" ? string("") : string(", ")) + ST_USER;
	if ((sublevel & SUBLV_SEARCH) == 0)
		s_str += (s_str == "" ? string("") : string(", ")) + ST_SEARCH;
	return s_str;
}

// DEBUGLexem: print lexem debug information
void TOLParser::DEBUGLexem(cpChar c, const TOLLexem* l)
{
	if (DoDebug() == true)
	{
		cout << "<!-- @LEXEM " << c << ": " << (int)l->m_Res;
		if (l->m_pcoAtom)
			cout << " atom: " << l->m_pcoAtom->Identifier() << ", " << l->m_pcoAtom->ClassName();
		if (l->m_pchTextStart)
		{
			cout << " text %";
			cout.write(l->m_pchTextStart, l->m_nTextLen);
			cout << "% len: " << l->m_nTextLen;
		}
		cout << " -->\n";
	}
}

// WaitForStatementStart: read from input file until it finds a start statement
// Parameters:
//		TOLPActionList& al - reference to list of actions
const TOLLexem* TOLParser::WaitForStatementStart(TOLPActionList& al)
{
	const TOLLexem* c_lexem = lex.GetLexem();
	DEBUGLexem("wf start 1", c_lexem);
	while (c_lexem->m_Res != TOL_LEX_START_STATEMENT
	        && c_lexem->m_Res != TOL_ERR_EOF)
	{
		CheckForText(al, c_lexem);
		if (c_lexem->m_Res < 0)
			return c_lexem;
		c_lexem = lex.GetLexem();
		DEBUGLexem("wf start 2", c_lexem);
	}
	CheckForText(al, c_lexem);
	return c_lexem;
}

// WaitForStatementEnd: read from input file until it finds an end statement
const TOLLexem* TOLParser::WaitForStatementEnd(bool write_errors)
{
	const TOLLexem *c_lexem = lex.GetLexem();
	DEBUGLexem("wf end 1", c_lexem);
	static char te[4];
	sprintf(te, "%c", lex.s_chTOLTokenEnd);
	while (c_lexem->m_Res != TOL_LEX_END_STATEMENT
	        && c_lexem->m_Res != TOL_ERR_EOF)
	{
		if (write_errors == true)
			SetPError(parse_err, PERR_END_STATEMENT_MISSING, MODE_PARSE, te,
			          lex.PrevLine(), lex.PrevColumn());
		c_lexem = lex.GetLexem();
		DEBUGLexem("wf end 2", c_lexem);
	}
	if (c_lexem->m_Res == TOL_ERR_EOF)
		SetPError(parse_err, PERR_END_STATEMENT_MISSING, MODE_PARSE, te,
		          lex.PrevLine(), lex.PrevColumn());
	return c_lexem;
}

// DTOperators: return string containing valid operators for a given type
cpChar TOLParser::DTOperators(TDataType dt)
{
	if (dt == TOL_DT_NONE)
		return NULL;
	else if (dt == TOL_DT_STRING || dt == TOL_DT_NUMBER_PARITY)
		return "is, not";
	else if (dt == TOL_DT_ORDER)
		return "asc, desc";
	else if (dt == TOL_DT_NUMBER)
		return "is, not, greater, smaller";
	else if (dt == TOL_DT_ON_OFF)
		return "on, off";
	else if (dt == TOL_DT_DATE)
		return "is, not, before, after";
	return NULL;
}

// TypeOperators: return mask containing valid operators for a given type
inline int TOLParser::TypeOperators(TDataType dt)
{
	if (dt == TOL_DT_NONE)
		return TOL_NO_OP;
	else if (dt == TOL_DT_STRING || dt == TOL_DT_NUMBER_PARITY)
		return TOL_OP_IS | TOL_OP_IS_NOT;
	else if (dt == TOL_DT_ORDER || dt == TOL_DT_ON_OFF || dt == TOL_DT_ART_POS)
		return TOL_NO_OP;
	return TOL_OP_IS | TOL_OP_IS_NOT | TOL_OP_GREATER | TOL_OP_SMALLER;
}

// St2PMod: convert statement identifier to print modifier
inline int TOLParser::St2PMod(int stm)
{
	if (stm == TOL_ST_IMAGE)
		return TOL_PMOD_IMAGE;
	else if (stm == TOL_ST_PUBLICATION)
		return TOL_PMOD_PUBLICATION;
	else if (stm == TOL_ST_ISSUE)
		return TOL_PMOD_ISSUE;
	else if (stm == TOL_ST_SECTION)
		return TOL_PMOD_SECTION;
	else if (stm == TOL_ST_ARTICLE)
		return TOL_PMOD_ARTICLE;
	else if (stm == TOL_ST_LIST)
		return TOL_PMOD_LIST;
	else if (stm == TOL_ST_LANGUAGE)
		return TOL_PMOD_LANGUAGE;
	else if (stm == TOL_ST_SUBSCRIPTION)
		return TOL_PMOD_SUBSCRIPTION;
	else if (stm == TOL_ST_USER)
		return TOL_PMOD_USER;
	else if (stm == TOL_ST_LOGIN)
		return TOL_PMOD_LOGIN;
	else if (stm == TOL_ST_SEARCH)
		return TOL_PMOD_SEARCH;
	else if (stm == TOL_ST_SUBTITLE)
		return TOL_PMOD_SUBTITLE;
	return 0;
}

// LMod2Level: return level corresponding to given list modifier
inline int TOLParser::LMod2Level(TListModifier lm)
{
	if (lm == TOL_LMOD_ISSUE)
		return LV_LISSUE;
	else if (lm == TOL_LMOD_SECTION)
		return LV_LSECTION;
	else if (lm == TOL_LMOD_SUBTITLE)
		return LV_LSUBTITLE;
	else if (lm == TOL_LMOD_ARTICLE)
		return LV_LARTICLE;
	return 0;
}

// IMod2St: convert from if modifier to statement identifier
inline cpChar TOLParser::IMod2St(TIfModifier m)
{
	if (m == TOL_IMOD_PREVIOUSITEMS)
		return ST_PREVIOUSITEMS;
	if (m == TOL_IMOD_NEXTITEMS)
		return ST_NEXTITEMS;
	if (m == TOL_IMOD_LIST)
		return ST_LIST;
	if (m == TOL_IMOD_SECTION)
		return ST_SECTION;
	if (m == TOL_IMOD_ARTICLE)
		return ST_ARTICLE;
	if (m == TOL_IMOD_ALLOWED)
		return ST_ALLOWED;
	if (m == TOL_IMOD_SUBSCRIPTION)
		return ST_SUBSCRIPTION;
	if (m == TOL_IMOD_USER)
		return ST_USER;
	if (m == TOL_IMOD_LOGIN)
		return ST_LOGIN;
	if (m == TOL_IMOD_SEARCH)
		return ST_SEARCH;
	if (m == TOL_IMOD_PREVSUBTITLES)
		return ST_PREVSUBTITLES;
	if (m == TOL_IMOD_NEXTSUBTITLES)
		return ST_NEXTSUBTITLES;
	if (m == TOL_IMOD_CURRENTSUBTITLE)
		return ST_CURRENTSUBTITLE;
	return "";
}

// MatchIModSt: return true if given if modifier matches given statement
inline int TOLParser::MatchIModSt(TIfModifier m, int s)
{
	if ((m == TOL_IMOD_PREVIOUSITEMS && s == TOL_ST_PREVIOUSITEMS)
	        || (m == TOL_IMOD_NEXTITEMS && s == TOL_ST_NEXTITEMS)
	        || (m == TOL_IMOD_LIST && s == TOL_ST_LIST)
	        || (m == TOL_IMOD_SECTION && s == TOL_ST_SECTION)
	        || (m == TOL_IMOD_ARTICLE && s == TOL_ST_ARTICLE)
	        || (m == TOL_IMOD_ALLOWED && s == TOL_ST_ALLOWED)
	        || (m == TOL_IMOD_SUBSCRIPTION && s == TOL_ST_SUBSCRIPTION)
	        || (m == TOL_IMOD_USER && s == TOL_ST_USER)
	        || (m == TOL_IMOD_LOGIN && s == TOL_ST_LOGIN)
	        || (m == TOL_IMOD_SEARCH && s == TOL_ST_SEARCH)
	        || (m == TOL_IMOD_PREVSUBTITLES && s == TOL_ST_PREVSUBTITLES)
	        || (m == TOL_IMOD_NEXTSUBTITLES && s == TOL_ST_NEXTSUBTITLES)
	        || (m == TOL_IMOD_CURRENTSUBTITLE && s == TOL_ST_CURRENTSUBTITLE))
		return 1;
	return 0;
}

// TypeName: return string containing the name of the given type
inline cpChar TOLParser::TypeName(TDataType dt)
{
	if (dt == TOL_DT_NONE)
		return "";
	else if (dt == TOL_DT_NUMBER)
		return "number";
	else if (dt == TOL_DT_STRING)
		return "string";
	else if (dt == TOL_DT_NUMBER_PARITY)
		return "number and parity (odd, even)";
	else if (dt == TOL_DT_ORDER)
		return "order (asc, desc)";
	else if (dt == TOL_DT_ON_OFF)
		return "on/off (on, off)";
	else if (dt == TOL_DT_ART_POS)
		return "article position (FrontPage, Section)";
	else // TOL_DT_DATE
		return "date";
}

// ValidDataType: return true if the lexem is of requested data type
// Parameters:
//		const TOLLexem* l - lexem
//		TDataType dt - requested data type
int TOLParser::ValidDataType(const TOLLexem* l, TDataType dt)
{
	if (l->m_DataType == dt)
		return 1;
	if (dt == TOL_DT_NONE || dt == TOL_DT_STRING)
		return 1;
	if (dt == TOL_DT_NUMBER)
		return 0;
	if (dt == TOL_DT_NUMBER_PARITY
	        && (l->m_DataType == TOL_DT_NUMBER
	            || strcasecmp(l->m_pcoAtom->Identifier(), "odd") == 0
	            || strcasecmp(l->m_pcoAtom->Identifier(), "even") == 0))
	{
		return 1;
	}
	if (dt == TOL_DT_ORDER
	        && (strcasecmp(l->m_pcoAtom->Identifier(), "asc") == 0
	            || strcasecmp(l->m_pcoAtom->Identifier(), "desc") == 0))
	{
		return 1;
	}
	if (dt == TOL_DT_ON_OFF
	        && (strcasecmp(l->m_pcoAtom->Identifier(), "on") == 0
	            || strcasecmp(l->m_pcoAtom->Identifier(), "off") == 0))
	{
		return 1;
	}
	if (dt == TOL_DT_ART_POS
	        && (strcasecmp(l->m_pcoAtom->Identifier(), "FrontPage") == 0
	            || strcasecmp(l->m_pcoAtom->Identifier(), "Section") == 0))
	{
		return 1;
	}
	if (dt == TOL_DT_DATE && l->m_DataType == TOL_DT_NUMBER)
		return 1;
	return 0;
}

// ValidDateForm: return true if given string is a valid date format
int TOLParser::ValidDateForm(const char* df)
{
	if (df == NULL)
		return 1;
	static char valid_chars[] = "MWYymcdejD%";
	while (*df)
	{
		if (*df == '%' && strchr(valid_chars, *(++df)) == 0)
			return 0;
		if (*df)
			df++;
	}
	return 1;
}

// SetWriteErrors: set the parse_err_printed and write_err_printed members
// for this parser instance and for included templates
void TOLParser::SetWriteErrors(bool p_bWriteErrors)
{
	m_coOpMutex.Lock();
	parse_err_printed = !p_bWriteErrors;
	write_err_printed = !p_bWriteErrors;
	for (StringHash::iterator sh_i = child_tpl.begin(); sh_i != child_tpl.end(); ++sh_i)
	{
		if (*sh_i == tpl)
			continue;
		TOLParserHash::iterator ph_i;
		if ((ph_i = m_coPHash.find((*sh_i).c_str())) != m_coPHash.end())
			(*ph_i)->SetWriteErrors(p_bWriteErrors);
	}
	m_coOpMutex.Unlock();
}

// HLanguage: parse language statement; add TOLActLanguage action to actions list (al)
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int TOLParser::HLanguage(TOLPActionList& al, int lv, int sublv)
{
	if ((sublv & SUBLV_LOCAL) == 0)
	{
		CheckForLevel(lv, LV_ROOT, lex.PrevLine(), lex.PrevColumn());
		CheckNotLevel(sublv, SUBLV_SEARCHRESULT, lex.PrevLine(), lex.PrevColumn());
	}
	const TOLLexem* l;
	RequireAtom(l);
	al.insert(al.end(), new TOLActLanguage(l->m_pcoAtom->Identifier()));
	WaitForStatementEnd(true);
	return 0;
}

// HInclude: parse include statement; add TOLActInclude action to actions list (al)
// Parameters:
//		TOLPActionList& al - reference to actions list
inline int TOLParser::HInclude(TOLPActionList& al)
{
	const TOLLexem* l;
	RequireAtom(l);
	CheckForAtomType(l->m_pcoAtom, CL_TOLATOM, PERR_NOT_VALUE, "",
	                 lex.PrevLine(), lex.PrevColumn());
	string itpl_name;
	if ((l->m_pcoAtom->Identifier())[0] == '/')
		itpl_name = document_root + l->m_pcoAtom->Identifier();
	else
	{
		itpl_name = tpl;
		itpl_name.erase(itpl_name.rfind('/'));
		itpl_name += string("/") + l->m_pcoAtom->Identifier();
	}
	if (parent_tpl.find(itpl_name.c_str()) != parent_tpl.end())
		FatalPError(parse_err, PERR_INCLUDE_CICLE, MODE_PARSE, "", lex.PrevLine(), lex.PrevColumn());
	child_tpl.insert_unique(itpl_name);
	LockHash();
	TOLParserHash::iterator ph_i;
	if ((ph_i = m_coPHash.find(itpl_name.c_str())) == m_coPHash.end())
		m_coPHash.insert_unique(new TOLParser(itpl_name.c_str()));
	if ((ph_i = m_coPHash.find(itpl_name.c_str())) == m_coPHash.end())
	{
		UnlockHash();
		return ERR_NOHASHENT;
	}
	UnlockHash();
	try
	{
		(*ph_i)->parent_tpl.insert_unique(parent_tpl.begin(), parent_tpl.end());
		(*ph_i)->Parse();
	}
	catch (ExStat& rcoEx)
	{
		return rcoEx.ErrNr();
	}
	catch (ExMutex& rcoEx)
	{
		return ERR_NOACCESS;
	}
	al.insert(al.end(), new TOLActInclude(itpl_name.c_str(), &m_coPHash));
	WaitForStatementEnd(true);
	return 0;
}

// HPublication: parse publication statement; add TOLActPublication action to actions list (al)
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int level - current level
//		int sublevel - current sublevel
//		TOLStatement* st - pointer to publication statement (from lex statements)
inline int TOLParser::HPublication(TOLPActionList& al, int level, int sublevel,
								   TOLStatement* st)
{
	if ((sublevel & SUBLV_LOCAL) == 0)
	{
		CheckForLevel(level, LV_ROOT, lex.PrevLine(), lex.PrevColumn());
		CheckNotLevel(sublevel, SUBLV_SEARCHRESULT, lex.PrevLine(), lex.PrevColumn());
	}
	const TOLLexem* l;
	RequireAtom(l);
	ValidateAttr(attr, st, l, TOL_CT_DEFAULT, 0);
	if (strcasecmp(l->m_pcoAtom->Identifier(), "off") != 0
	        && strcasecmp(l->m_pcoAtom->Identifier(), "default") != 0)
	{
		RequireAtom(l);
		ValidateDType(l, attr->DataType(), 0);
		al.insert(al.end(), new TOLActPublication(TOLParameter(attr->Attribute(),
	                                              l->m_pcoAtom->Identifier(), TOL_OP_IS)));
	}
	else
		al.insert(al.end(), new TOLActPublication(TOLParameter(attr->Attribute(), "",
												  TOL_NO_OP)));
	WaitForStatementEnd(true);
	return 0;
}

// HIssue: parse include statement; add TOLActIssue action to actions list (al)
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int level - current level
//		int sublevel - current sublevel
//		const TOLLexem* l - pointer to last lexem
inline int TOLParser::HIssue(TOLPActionList& al, int level, int sublevel, const TOLLexem* l)
{
	if ((sublevel & SUBLV_LOCAL) == 0)
	{
		CheckForLevel(level, LV_ROOT, lex.PrevLine(), lex.PrevColumn());
		CheckNotLevel(sublevel, SUBLV_SEARCHRESULT, lex.PrevLine(), lex.PrevColumn());
	}
	TOLStatement* st = &(*lex.s_coStatements.find(l->m_pcoAtom->Identifier()));
	RequireAtom(l);
	ValidateAttr(attr, st, l, TOL_CT_DEFAULT, 0);
	if (strcasecmp(l->m_pcoAtom->Identifier(), "off") != 0
	        && strcasecmp(l->m_pcoAtom->Identifier(), "default") != 0
	        && strcasecmp(l->m_pcoAtom->Identifier(), "current") != 0)
	{
		RequireAtom(l);
		ValidateDType(l, attr->DataType(), 0);
		al.insert(al.end(), new TOLActIssue(TOLParameter(attr->Attribute(),
		                                    l->m_pcoAtom->Identifier(), TOL_OP_IS)));
	}
	else
		al.insert(al.end(), new TOLActIssue(TOLParameter(attr->Attribute(), "", TOL_NO_OP)));
	WaitForStatementEnd(true);
	return 0;
}

// HSection: parse include statement; add TOLActSection action to actions list (al)
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int level - current level
//		int sublevel - current sublevel
//		const TOLLexem* l - pointer to last lexem
inline int TOLParser::HSection(TOLPActionList& al, int level, int sublevel, const TOLLexem* l)
{
	if ((sublevel & SUBLV_LOCAL) == 0)
	{
		CheckForLevel(level, LV_ROOT | LV_LISSUE, lex.PrevLine(), lex.PrevColumn());
		CheckNotLevel(sublevel, SUBLV_SEARCHRESULT, lex.PrevLine(), lex.PrevColumn());
	}
	TOLStatement* st = &(*lex.s_coStatements.find(l->m_pcoAtom->Identifier()));
	RequireAtom(l);
	ValidateAttr(attr, st, l, TOL_CT_DEFAULT, 0);
	if (strcasecmp(l->m_pcoAtom->Identifier(), "off") != 0
	        && strcasecmp(l->m_pcoAtom->Identifier(), "default") != 0)
	{
		RequireAtom(l);
		ValidateDType(l, attr->DataType(), 0);
		al.insert(al.end(), new TOLActSection(TOLParameter(attr->Attribute(),
		                                      l->m_pcoAtom->Identifier(), TOL_OP_IS)));
	}
	else
		al.insert(al.end(), new TOLActSection(TOLParameter(attr->Attribute(), "", TOL_NO_OP)));
	WaitForStatementEnd(true);
	return 0;
}

// HArticle: parse include statement; add TOLActArticle action to actions list (al)
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int level - current level
//		int sublevel - current sublevel
//		const TOLLexem* l - pointer to last lexem
inline int TOLParser::HArticle(TOLPActionList& al, int level, int sublevel, const TOLLexem* l)
{
	if ((sublevel & SUBLV_LOCAL) == 0)
		CheckForLevel(level, LV_ROOT | LV_LISSUE | LV_LSECTION, lex.PrevLine(),
					  lex.PrevColumn());
	TOLStatement *st = &(*lex.s_coStatements.find(l->m_pcoAtom->Identifier()));
	RequireAtom(l);
	ValidateAttr(attr, st, l, TOL_CT_DEFAULT, 0);
	if (strcasecmp(l->m_pcoAtom->Identifier(), "off") != 0
	        && strcasecmp(l->m_pcoAtom->Identifier(), "default") != 0)
	{
		RequireAtom(l);
		ValidateDType(l, attr->DataType(), 0);
		al.insert(al.end(), new TOLActArticle(TOLParameter(attr->Attribute(),
		                                                   l->m_pcoAtom->Identifier(), TOL_OP_IS)));
	}
	else
		al.insert(al.end(), new TOLActArticle(TOLParameter(attr->Attribute(), "", TOL_NO_OP)));
	WaitForStatementEnd(true);
	return 0;
}

// HURLParameters: parse include statement; add TOLActURLParameters action to actions list (al)
// Parameters:
//		TOLPActionList& al - reference to actions list
//		const TOLLexem* l - pointer to last lexem
inline int TOLParser::HURLParameters(TOLPActionList& al, const TOLLexem* l)
{
	TOLStatement *st = &(*lex.s_coStatements.find(l->m_pcoAtom->Identifier()));
	l = lex.GetLexem();
	DEBUGLexem("urlparam", l);
	long int img = -1;
	bool fromstart = false, allsubtitles = false;
	CLevel reset_from_list = CLV_ROOT;
	while (l->m_Res != TOL_LEX_END_STATEMENT)
	{
		if (!l->m_pcoAtom)
		{
			SetPError(parse_err, PERR_ATOM_MISSING, MODE_PARSE, "",
			          lex.PrevLine(), lex.PrevColumn());
			return 0;
		}
		if (strcasecmp(l->m_pcoAtom->Identifier(), "fromstart") == 0)
		{
			fromstart = true;
		}
		else if (strcasecmp(l->m_pcoAtom->Identifier(), "allsubtitles") == 0)
		{
			allsubtitles = true;
		}
		else if (strcasecmp(l->m_pcoAtom->Identifier(), "reset_issue_list") == 0)
		{
			reset_from_list = CLV_ISSUE_LIST;
		}
		else if (strcasecmp(l->m_pcoAtom->Identifier(), "reset_section_list") == 0)
		{
			reset_from_list = CLV_SECTION_LIST;
		}
		else if (strcasecmp(l->m_pcoAtom->Identifier(), "reset_article_list") == 0)
		{
			reset_from_list = CLV_ARTICLE_LIST;
		}
		else if (strcasecmp(l->m_pcoAtom->Identifier(), "reset_searchresult_list") == 0)
		{
			reset_from_list = CLV_SEARCHRESULT_LIST;
		}
		else if (strcasecmp(l->m_pcoAtom->Identifier(), "reset_subtitle_list") == 0)
		{
			reset_from_list = CLV_SUBTITLE_LIST;
		}
		else if (strcasecmp(l->m_pcoAtom->Identifier(), ST_IMAGE) == 0)
		{
			RequireAtom(l);
			ValidateDType(l, TOL_DT_NUMBER, 0);
			img = strtol(l->m_pcoAtom->Identifier(), 0, 10);
		}
		else
		{
			string r_attrs = ST_IMAGE;
			st->PrintAttrs(r_attrs, TOL_CT_DEFAULT);
			SetPError(parse_err, PERR_INVALID_ATTRIBUTE, MODE_PARSE, r_attrs.c_str(),
			          lex.PrevLine(), lex.PrevColumn());
			WaitForStatementEnd(false);
			return 0;
		}
		l = lex.GetLexem();
		DEBUGLexem("urlparam2", l);
	}

	al.insert(al.end(), new TOLActURLParameters(fromstart, allsubtitles, img, reset_from_list));
	if (l->m_Res != TOL_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	return 0;
}

// HFormParameters: parse FormParameters statement; add TOLActFormParameters action to
// actions list (al)
// Parameters:
//		TOLPActionList& al - reference to actions list
inline int TOLParser::HFormParameters(TOLPActionList& al)
{
	const TOLLexem *l = lex.GetLexem();
	DEBUGLexem("formparam", l);
	bool fromstart = false;
	if (l->m_Res != TOL_LEX_END_STATEMENT && l->m_pcoAtom
	        && strcasecmp(l->m_pcoAtom->Identifier(), "fromstart") == 0)
	{
		fromstart = true;
	}
	al.insert(al.end(), new TOLActFormParameters(fromstart));
	if (l->m_Res != TOL_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	return 0;
}

// HDate: parse date statement; add TOLActDate action to actions list (al)
// Parameters:
//		TOLPActionList& al - reference to actions list
//		const TOLLexem* l - pointer to last lexem
inline int TOLParser::HDate(TOLPActionList& al, const TOLLexem* l)
{
	TOLStatement *st = &(*lex.s_coStatements.find(l->m_pcoAtom->Identifier()));
	RequireAtom(l);
	const TOLAttribute* attr;
	if ((attr = st->FindAttr(l->m_pcoAtom->Identifier(), TOL_CT_DEFAULT)) == 0
	        && !ValidDateForm(l->m_pcoAtom->Identifier()))
	{
		string a_req;
		st->PrintAttrs(a_req, TOL_CT_DEFAULT);
		a_req += ", format";
		FatalPError(parse_err, PERR_INVALID_ATTRIBUTE, MODE_PARSE,
		            a_req.c_str(), lex.PrevLine(), lex.PrevColumn());
	}
	al.insert(al.end(), new TOLActDate(l->m_pcoAtom->Identifier()));
	WaitForStatementEnd(true);
	return 0;
}

// HPrint: parse print statement; add TOLActPrint action to actions list (al)
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int TOLParser::HPrint(TOLPActionList& al, int lv, int sublv)
{
	const TOLLexem* l;
	RequireAtom(l);
	CheckForAtomType(l->m_pcoAtom, CL_TOLSTATEMENT, PERR_ATOM_NOT_STATEMENT,
	                 PrintStatements(lv, sublv), lex.PrevLine(), lex.PrevColumn());
	TOLStatement *st = &(*lex.s_coStatements.find(l->m_pcoAtom->Identifier()));
	if (St2PMod(st->statement) == 0)
	{
		SetPError(parse_err, PERR_INVALID_STATEMENT, MODE_PARSE,
		          PrintStatements(lv, sublv), lex.PrevLine(),
		          lex.PrevColumn());
		WaitForStatementEnd(false);
		return 0;
	}
	if (st->statement == TOL_ST_LIST
	        && (lv & (LV_LISSUE | LV_LSECTION | LV_LARTICLE | LV_LSUBTITLE)) == 0
	        && (sublv & SUBLV_SEARCHRESULT) == 0)
	{
		SetPError(parse_err, PERR_INVALID_STATEMENT, MODE_PARSE,
		          LvStatements(lv), lex.PrevLine(), lex.PrevColumn());
		WaitForStatementEnd(false);
		return 1;
	}
	if ((st->statement == TOL_ST_LOGIN && (sublv & SUBLV_LOGIN))
	        || (st->statement == TOL_ST_SUBTITLE && !(lv & LV_LSUBTITLE))
	        || (st->statement == TOL_ST_SEARCH && (sublv & SUBLV_SEARCH)))
		FatalPError(parse_err, PERR_INVALID_STATEMENT, MODE_PARSE,
		            PrintStatements(lv, sublv), lex.PrevLine(), lex.PrevColumn());
	RequireAtom(l);
	const TOLAttribute* attr = NULL;
	TOLAttribute* tattr = NULL;
	TOLTypeAttributes* pTa = NULL;
	TOLTypeAttributes* pTa2 = NULL;
	string a_req, format;
	if ((attr = st->FindAttr(l->m_pcoAtom->Identifier(), TOL_CT_PRINT)) == NULL
	    && (tattr = st->FindTypeAttr(l->m_pcoAtom->Identifier(), "", TOL_CT_PRINT, &pTa)) == NULL
	    && (pTa2 = st->FindType(l->m_pcoAtom->Identifier())) == NULL)
	{
		st->PrintAttrs(a_req, TOL_CT_PRINT);
		st->PrintTypes(a_req);
		st->PrintTAttrs(a_req, "", TOL_CT_PRINT);
		SetPError(parse_err, PERR_INVALID_ATTRIBUTE, MODE_PARSE,
		          a_req.c_str(), lex.PrevLine(), lex.PrevColumn());
		WaitForStatementEnd(false);
		delete tattr;
		delete pTa;
		delete pTa2;
		return 0;
	}
	if (pTa2 != NULL)
	{
		RequireAtom(l);
		TOLTypeAttributes* pTa3 = NULL;
		if ((tattr = st->FindTypeAttr(l->m_pcoAtom->Identifier(), pTa2->type_value,
		                             TOL_CT_PRINT, &pTa3)) == NULL)
		{
			st->PrintTAttrs(a_req, pTa2->type_value, TOL_CT_PRINT);
			SetPError(parse_err, PERR_INVALID_ATTRIBUTE, MODE_PARSE,
			          a_req.c_str(), lex.PrevLine(), lex.PrevColumn());
			WaitForStatementEnd(false);
			delete tattr;
			delete pTa;
			delete pTa2;
			delete pTa3;
			return 0;
		}
		delete pTa3;
	}
	TDataType attrDataType = (attr != NULL) ? attr->DataType() : tattr->DataType();
	const char* attrIdentifier = (attr != NULL) ? attr->Identifier() : tattr->Identifier();
	const char* attrAttribute = (attr != NULL) ? attr->Attribute() : tattr->Attribute();
	if (attrDataType == TOL_DT_DATE)
	{
		l = lex.GetLexem();
		DEBUGLexem("print", l);
		CheckForEOF(l, PERR_EOS_MISSING);
		if (l->m_Res != TOL_LEX_END_STATEMENT)
		{
			CheckForAtom(l);
			if (ValidDateForm(l->m_pcoAtom->Identifier()))
				format = l->m_pcoAtom->Identifier();
			else
			{
				SetPError(parse_err, PERR_INVALID_DATE_FORM, MODE_PARSE,
				          "", lex.PrevLine(), lex.PrevColumn());
				WaitForStatementEnd(true);
				delete tattr;
				delete pTa;
				delete pTa2;
				return 0;
			}
		}
	}
	if (strcasecmp(attrIdentifier, "mon_name") == 0)
		format = "%M";
	if (strcasecmp(attrIdentifier, "wday_name") == 0)
		format = "%W";
	al.insert(al.end(),
	          new TOLActPrint(attrAttribute, (TPrintModifier)St2PMod(st->statement),
	                          pTa2 ? pTa2->type_value : (pTa ? pTa->type_value : 0), format));
	if (l->m_Res != TOL_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	delete tattr;
	delete pTa;
	delete pTa2;
	return 0;
}

// HList: parse list statement; add TOLActList action to actions list (al)
// All statements between List and EndList statements are parsed, added as actions
// in TOLActList's list of actions
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int level - current level
//		int sublevel - current sublevel
//		const TOLLexem* l - pointer to last lexem
inline int TOLParser::HList(TOLPActionList& al, int level, int sublevel, const TOLLexem* l)
{
	if (level >= LV_LARTICLE)
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            LARTICLE_STATEMENTS, lex.PrevLine(), lex.PrevColumn());
	long int lines = 0, columns = 0;
	TOLStatement *st = &(*lex.s_coStatements.find(l->m_pcoAtom->Identifier()));
	RequireAtom(l);
	// check for List attributes
	while (strcmp(l->m_pcoAtom->ClassName(), "TOLStatement"))
	{
		ValidateAttr(attr, st, l, TOL_CT_DEFAULT, 1);
		if (strcasecmp(l->m_pcoAtom->Identifier(), "length") == 0 && lines > 0)
			SetPError(parse_err, PERR_ATTRIBUTE_REDEF, MODE_PARSE, "",
			          lex.PrevLine(), lex.PrevColumn());
		if (strcasecmp(l->m_pcoAtom->Identifier(), "columns") == 0 && columns > 0)
			SetPError(parse_err, PERR_ATTRIBUTE_REDEF, MODE_PARSE, "",
			          lex.PrevLine(), lex.PrevColumn());
		string attr_name = l->m_pcoAtom->Identifier();
		RequireAtom(l);
		ValidateDType(l, TOL_DT_NUMBER, 1);
		if (strcasecmp(attr_name.c_str(), "length") == 0)
			lines = strtol(l->m_pcoAtom->Identifier(), 0, 10);
		else
			columns = strtol(l->m_pcoAtom->Identifier(), 0, 10);
		RequireAtom(l);
	}
	// check for modifier (Issue, Section, Article, SearchResult, Subtitle)
	st = (TOLStatement*)l->m_pcoAtom;
	TListModifier mod;
	if (st->statement == TOL_ST_ISSUE)
	{
		if (level >= LV_LISSUE)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            LvListSt(level), lex.PrevLine(), lex.PrevColumn());
		mod = TOL_LMOD_ISSUE;
	}
	else if (st->statement == TOL_ST_SECTION)
	{
		if (level >= LV_LSECTION)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            LvListSt(level), lex.PrevLine(), lex.PrevColumn());
		mod = TOL_LMOD_SECTION;
	}
	else if (st->statement == TOL_ST_ARTICLE)
	{
		mod = TOL_LMOD_ARTICLE;
	}
	else if (st->statement == TOL_ST_SEARCHRESULT)
	{
		mod = TOL_LMOD_SEARCHRESULT;
	}
	else if (st->statement == TOL_ST_SUBTITLE)
	{
		if ((sublevel & SUBLV_WITH) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE, LvListSt(level),
			            lex.PrevLine(), lex.PrevColumn());
		mod = TOL_LMOD_SUBTITLE;
	}
	else
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE, LvListSt(level),
		            lex.PrevLine(), lex.PrevColumn());
	// check for modifier attributes
	TOLParameterList params;
	l = lex.GetLexem();
	DEBUGLexem("hlist1", l);
	while (l->m_Res == TOL_LEX_IDENTIFIER
	       && mod != TOL_LMOD_SEARCHRESULT
	       && mod != TOL_LMOD_SUBTITLE)
	{
		TOLAttributeHash ah(4, cpCharHashFn, cpCharEqual, TOLAttributeValue);
		TOLAttributeHash::iterator ah_i;
		StringHash keywords(4, cpCharHashFn, cpCharEqual, stringValue);
		String2Int::iterator op_i;
		CheckForAtom(l);
		ValidateAttr(attr, st, l, TOL_CT_LIST, 1);
		if (strcasecmp(l->m_pcoAtom->Identifier(), "keyword")
	        && strcasecmp(l->m_pcoAtom->Identifier(), "IsOn")
	        && strcasecmp(l->m_pcoAtom->Identifier(), "IsNotOn"))
		{
			if ((ah_i = ah.find(l->m_pcoAtom->Identifier())) != ah.end())
				SetPError(parse_err, PERR_ATTRIBUTE_REDEF, MODE_PARSE, "",
				          lex.PrevLine(), lex.PrevColumn());
			ah.insert_unique(*attr);
			RequireAtom(l);
			if (attr->Class() != TOL_NORMAL_ATTR)
			{
				ValidateOperator(op_i, l, attr->DataType(), "");
				RequireAtom(l);
				TOLTypeAttributes* ta;
				if ((ta = st->FindType(l->m_pcoAtom->Identifier())) == NULL)
				{
					delete ta;
					FatalPError(parse_err, PERR_INV_TYPE_VAL, MODE_PARSE, "",
					            lex.PrevLine(), lex.PrevColumn());
				}
				delete ta;
				params.insert(params.end(), TOLParameter(attr->Attribute(),
				              l->m_pcoAtom->Identifier(), (TOperator)(*op_i).second));
				l = lex.GetLexem();
				DEBUGLexem("hlist2", l);
				continue;
			}
			ValidateOperator(op_i, l, attr->DataType(), "");
			RequireAtom(l);
			ValidateDType(l, attr->DataType(), 1);
			params.insert(params.end(),
			              TOLParameter(attr->Attribute(), l->m_pcoAtom->Identifier(),
			                           (TOperator)(*op_i).second));
		}
		else
		{
			RequireAtom(l);
			if (strcasecmp(attr->Identifier(), "keyword") == 0)
			{
			    if (keywords.find(l->m_pcoAtom->Identifier()) == keywords.end())
			    {
			    	keywords.insert_unique(l->m_pcoAtom->Identifier());
					params.insert(params.end(),
				              TOLParameter("keyword", l->m_pcoAtom->Identifier(), TOL_NO_OP));
				}
				l = lex.GetLexem();
				DEBUGLexem("hlist4", l);
				continue;
			}
			TOperator op = TOL_OP_IS_NOT;
			if (strcasecmp(attr->Identifier(), "IsOn") == 0)
				op = TOL_OP_IS;
			ValidateDType(l, TOL_DT_ART_POS, 1);
			string dbf = "OnSection";
			if (strcasecmp(l->m_pcoAtom->Identifier(), "FrontPage") == 0)
				dbf = "OnFrontPage";
			params.insert(params.end(), TOLParameter(dbf.c_str(), "Y", op));
		}
		l = lex.GetLexem();
		DEBUGLexem("hlist3", l);
	}
	// check for order params (Article)
	TOLParameterList ord_params;
	if ((st->statement == TOL_ST_ARTICLE || st->statement == TOL_ST_SEARCHRESULT)
	    && l->m_Res == TOL_LEX_STATEMENT)
	{
		CheckForAtom(l);
		CheckForAtomType(l->m_pcoAtom, CL_TOLSTATEMENT, PERR_ATOM_NOT_STATEMENT,
		                 ST_ORDER, lex.PrevLine(), lex.PrevColumn());
		if (TOL_ST_ORDER != ((TOLStatement*)l->m_pcoAtom)->statement)
		{
			SetPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE, ST_ORDER,
			          lex.PrevLine(), lex.PrevColumn());
		}
		TOLStatement* ost = &(*lex.s_coStatements.find(l->m_pcoAtom->Identifier()));
		RequireAtom(l);
		while (l->m_Res == TOL_LEX_IDENTIFIER)
		{
			TOLAttributeHash ah(4, cpCharHashFn, cpCharEqual, TOLAttributeValue);
			TOLAttributeHash::iterator ah_i;
			CheckForAtom(l);
			ValidateAttr(attr, ost, l, TOL_CT_LIST, 1);
			if ((ah_i = ah.find(l->m_pcoAtom->Identifier())) != ah.end())
				SetPError(parse_err, PERR_ATTRIBUTE_REDEF, MODE_PARSE, "",
				          lex.PrevLine(), lex.PrevColumn());
			ah.insert_unique(*attr);
			RequireAtom(l);
			ValidateDType(l, attr->DataType(), 1);
			const char* param_name = attr->Attribute();
			ord_params.insert(ord_params.end(),
			                  TOLParameter(param_name, l->m_pcoAtom->Identifier(), TOL_NO_OP));
			l = lex.GetLexem();
			if (l->m_Res != TOL_LEX_IDENTIFIER)
				break;
		}
	}
	if (l->m_Res != TOL_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	TOLActList* lal = new TOLActList(mod, lines, columns, params, ord_params);
	if (lal == 0)
		FatalPError(parse_err, ERR_NOMEM, MODE_PARSE, "", lex.PrevLine(), lex.PrevColumn());
	int last_st, res;
	sublevel &= (~SUBLV_IFPREV & ~SUBLV_IFNEXT & ~SUBLV_IFLIST
	             & ~SUBLV_EMPTYLIST & ~SUBLV_LOCAL);
	if (mod == TOL_LMOD_SEARCHRESULT)
		sublevel |= SUBLV_SEARCHRESULT;
	int tmp_level = LMod2Level(mod);
	if (tmp_level == 0)
		tmp_level = level;
	if ((res = LevelParser(lal->first_block, tmp_level, sublevel, last_st)))
	{
		delete lal;
		return res;
	}
	int found_fel = 0;
	if (last_st == TOL_ST_FOREMPTYLIST)
	{
		WaitForStatementEnd(true);
		if ((res = LevelParser(lal->second_block, LMod2Level(mod),
		                       sublevel | SUBLV_EMPTYLIST, last_st)))
		{
			delete lal;
			return res;
		}
		found_fel = 1;
	}
	if (last_st != TOL_ST_ENDLIST)
	{
		delete lal;
		string exp = ST_ENDLIST;
		if (!found_fel)
			exp += string(", ") + ST_FOREMPTYLIST;
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE, exp.c_str(),
		            lex.PrevLine(), lex.PrevColumn());
	}
	al.insert(al.end(), lal);
	l = lex.GetLexem();
	DEBUGLexem("hlist4", l);
	if (l->m_Res == TOL_LEX_END_STATEMENT)
		return 0;
	string req_s = string(st->Identifier()) + ", >";
	CheckForStatement(l, req_s, lex.PrevLine(), lex.PrevColumn());
	CheckForAtom(l);
	CheckForAtomType(l->m_pcoAtom, CL_TOLSTATEMENT, PERR_ATOM_NOT_STATEMENT,
	                 req_s, lex.PrevLine(), lex.PrevColumn());
	if (st->statement != ((const TOLStatement*)l->m_pcoAtom)->statement)
		SetPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE, req_s,
		          lex.PrevLine(), lex.PrevColumn());
	WaitForStatementEnd(true);
	return 0;
}

// HIf: parse if statement; add TOLActIf action to actions list (al)
// All statements between If and EndIf statements are parsed, added as actions
// in TOLActIf's list of actions
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
//		const TOLLexem* l - pointer to last lexem
inline int TOLParser::HIf(TOLPActionList& al, int lv, int sublv, const TOLLexem* l)
{
	TOLParameter param("", "", TOL_NO_OP);
	TIfModifier modifier;
	intHash rc_hash(4, intHashFn, intEqual, intValue);
	RequireAtom(l);
	CheckForAtomType(l->m_pcoAtom, CL_TOLSTATEMENT, PERR_ATOM_NOT_STATEMENT,
	                 IfStatements(lv, sublv), lex.PrevLine(), lex.PrevColumn());
	TOLStatement *ist = &(*lex.s_coStatements.find(l->m_pcoAtom->Identifier()));
	if (ist->statement == TOL_ST_ALLOWED)
	{
		modifier = TOL_IMOD_ALLOWED;
		sublv |= SUBLV_IFALLOWED;
	}
	else if (ist->statement == TOL_ST_SUBSCRIPTION)
	{
		RequireAtom(l);
		ValidateAttr(attr, ist, l, TOL_CT_IF, 1);
		param = TOLParameter(l->m_pcoAtom->Identifier(), "", TOL_NO_OP);
		modifier = TOL_IMOD_SUBSCRIPTION;
	}
	else if (ist->statement == TOL_ST_USER)
	{
		if (sublv & SUBLV_USER)
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.PrevLine(), lex.PrevColumn());
		}
		RequireAtom(l);
		ValidateAttr(attr, ist, l, TOL_CT_IF, 1);
		param = TOLParameter(l->m_pcoAtom->Identifier(), "", TOL_NO_OP);
		modifier = TOL_IMOD_USER;
	}
	else if (ist->statement == TOL_ST_LOGIN)
	{
		if (sublv & SUBLV_LOGIN)
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.PrevLine(), lex.PrevColumn());
		}
		RequireAtom(l);
		ValidateAttr(attr, ist, l, TOL_CT_IF, 1);
		param = TOLParameter(l->m_pcoAtom->Identifier(), "", TOL_NO_OP);
		modifier = TOL_IMOD_LOGIN;
	}
	else if (ist->statement == TOL_ST_SEARCH)
	{
		if (sublv & SUBLV_SEARCH)
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.PrevLine(), lex.PrevColumn());
		}
		RequireAtom(l);
		ValidateAttr(attr, ist, l, TOL_CT_IF, 1);
		param = TOLParameter(l->m_pcoAtom->Identifier(), "", TOL_NO_OP);
		modifier = TOL_IMOD_SEARCH;
	}
	else if (ist->statement == TOL_ST_PREVIOUSITEMS)
	{
		if (sublv & SUBLV_EMPTYLIST || sublv & SUBLV_IFPREV
		        || sublv & SUBLV_IFNEXT
		        || (lv == LV_ROOT && (sublv & SUBLV_SEARCHRESULT) == 0))
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.PrevLine(), lex.PrevColumn());
		}
		modifier = TOL_IMOD_PREVIOUSITEMS;
	}
	else if (ist->statement == TOL_ST_NEXTITEMS)
	{
		if (sublv & SUBLV_EMPTYLIST || sublv & SUBLV_IFPREV
		        || sublv & SUBLV_IFNEXT
		        || (lv == LV_ROOT && (sublv & SUBLV_SEARCHRESULT) == 0))
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.PrevLine(), lex.PrevColumn());
		}
		modifier = TOL_IMOD_NEXTITEMS;
	}
	else if (ist->statement == TOL_ST_CURRENTSUBTITLE)
	{
		if ((lv & LV_LSUBTITLE ) && (sublv & SUBLV_WITH ))
		{
			modifier = TOL_IMOD_CURRENTSUBTITLE;
		}
		else
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.PrevLine(), lex.PrevColumn());
		}
	}
	else if (ist->statement == TOL_ST_PREVSUBTITLES)
	{
		modifier = TOL_IMOD_PREVSUBTITLES;
	}
	else if (ist->statement == TOL_ST_NEXTSUBTITLES)
	{
		modifier = TOL_IMOD_NEXTSUBTITLES;
	}
	else if (ist->statement == TOL_ST_SUBTITLE)
	{
		if (sublv & SUBLV_WITH == 0)
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.PrevLine(), lex.PrevColumn());
		}
		RequireAtom(l);
		ValidateAttr(attr, ist, l, TOL_CT_IF, 1);
		RequireAtom(l);
		ValidateDType(l, attr->DataType(), 1);
		param = TOLParameter(attr->Identifier(), l->m_pcoAtom->Identifier(), TOL_OP_IS);
		modifier = TOL_IMOD_SUBTITLE;
	}
	else if (ist->statement == TOL_ST_LIST)
	{
		if ((sublv & SUBLV_EMPTYLIST)
		        || (lv == LV_ROOT && (sublv & SUBLV_SEARCHRESULT) == 0))
		{
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IfStatements(lv, sublv), lex.PrevLine(), lex.PrevColumn());
		}
		RequireAtom(l);
		ValidateAttr(attr, ist, l, TOL_CT_IF, 1);
		sublv |= SUBLV_IFLIST;
		modifier = TOL_IMOD_LIST;
		param = TOLParameter(attr->Identifier(), "", TOL_NO_OP);
		bool first = true;
		do
		{
			if (strcasecmp(attr->Identifier(), "start") == 0
			        || strcasecmp(attr->Identifier(), "end") == 0)
			{
				break;
			}
			l = lex.GetLexem();
			DEBUGLexem("hif1", l);
			if (l->m_Res != TOL_LEX_IDENTIFIER)
			{
				if (first == true)
					FatalPError(parse_err, PERR_IDENTIFIER_MISSING, MODE_PARSE,
					            TypeName(attr->DataType()), lex.PrevLine(), lex.PrevColumn());
				break;
			}
			CheckForAtom(l);
			ValidateDType(l, attr->DataType(), 1);
			if (l->m_DataType != TOL_DT_NUMBER)
			{
				if (strlen(param.Value()))
					FatalPError(parse_err, PERR_INVALID_VALUE, MODE_PARSE,
					            "number", lex.PrevLine(), lex.PrevColumn());
				param = TOLParameter(attr->Identifier(), l->m_pcoAtom->Identifier(), TOL_NO_OP);
			}
			else
				rc_hash.insert_unique(strtol(l->m_pcoAtom->Identifier(), 0, 10));
			first = false;
		} while (1);
	}
	else if (ist->statement == TOL_ST_PUBLICATION)
	{
		RequireAtom(l);
		ValidateAttr(attr, ist, l, TOL_CT_IF, 1);
		param = TOLParameter(attr->Identifier(), "", TOL_NO_OP);
		sublv |= SUBLV_IFPUBLICATION;
		modifier = TOL_IMOD_PUBLICATION;
	}
	else if (ist->statement == TOL_ST_ISSUE)
	{
		RequireAtom(l);
		ValidateAttr(attr, ist, l, TOL_CT_IF, 1);
		if (strcasecmp(attr->Identifier(), "iscurrent")
		        && strcasecmp(attr->Identifier(), "defined")
		        && strcasecmp(attr->Identifier(), "fromstart"))
		{
			RequireAtom(l);
			String2Int::iterator op_i;
			ValidateOperator(op_i, l, attr->DataType(), "");
			RequireAtom(l);
			ValidateDType(l, attr->DataType(), 1);
			param = TOLParameter(attr->Attribute(), l->m_pcoAtom->Identifier(),
			                     (TOperator)(*op_i).second);
		}
		else
			param = TOLParameter(attr->Attribute(), "", TOL_NO_OP);
		sublv |= SUBLV_IFISSUE;
		modifier = TOL_IMOD_ISSUE;
	}
	else if (ist->statement == TOL_ST_SECTION || ist->statement == TOL_ST_ARTICLE)
	{
		RequireAtom(l);
		ValidateAttr(attr, ist, l, TOL_CT_IF, 1);
		if (strcasecmp(attr->Identifier(), "defined")
		        && strcasecmp(attr->Identifier(), "fromstart"))
		{
			RequireAtom(l);
			if (attr->Class() == TOL_TYPE_ATTR)
			{
				TOLTypeAttributes* ta;
				if ((ta = ist->FindType(l->m_pcoAtom->Identifier())) == 0)
				{
					delete ta;
					string t;
					ist->PrintTypes(t);
					FatalPError(parse_err, PERR_INV_TYPE_VAL, MODE_PARSE,
					            t, lex.PrevLine(), lex.PrevColumn());
				}
				delete ta;
				param = TOLParameter(attr->Attribute(), l->m_pcoAtom->Identifier(), TOL_OP_IS);
			}
			else
			{
				String2Int::iterator op_i;
				ValidateOperator(op_i, l, attr->DataType(), "");
				RequireAtom(l);
				ValidateDType(l, attr->DataType(), 1);
				param = TOLParameter(attr->Attribute(), l->m_pcoAtom->Identifier(),
				                     (TOperator)(*op_i).second);
			}
		}
		else
			param = TOLParameter(attr->Attribute(), "", TOL_NO_OP);
		if (ist->statement == TOL_ST_SECTION)
		{
			sublv |= SUBLV_IFSECTION;
			modifier = TOL_IMOD_SECTION;
		}
		else
		{ // TOL_ST_ARTICLE
			sublv |= SUBLV_IFARTICLE;
			modifier = TOL_IMOD_ARTICLE;
		}
	}
	else
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            IfStatements(lv, sublv), lex.PrevLine(), lex.PrevColumn());
	}
	if (l->m_Res != TOL_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	TOLActIf *ai = new TOLActIf(modifier, param);
	ai->rc_hash = rc_hash;
	int last_st, res;
	if ((res = LevelParser(ai->block, lv, sublv, last_st)))
	{
		delete ai;
		return res;
	}
	if (last_st == TOL_ST_ELSE)
	{
		WaitForStatementEnd(true);
		if ((res = LevelParser(ai->sec_block, lv, sublv, last_st)))
		{
			delete ai;
			return res;
		}
	}
	if (last_st != TOL_ST_ENDIF)
	{
		delete ai;
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            ST_ENDIF, lex.PrevLine(), lex.PrevColumn());
	}
	al.insert(al.end(), ai);
	l = lex.GetLexem();
	DEBUGLexem("hif2", l);
	if (l->m_Res != TOL_LEX_END_STATEMENT)
	{
		CheckForAtom(l);
		CheckForAtomType(l->m_pcoAtom, CL_TOLSTATEMENT, PERR_ATOM_NOT_STATEMENT,
		                 IMod2St(modifier), lex.PrevLine(), lex.PrevColumn());
		if (!MatchIModSt(modifier, ((const TOLStatement*)(l->m_pcoAtom))->statement))
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            IMod2St(modifier), lex.PrevLine(), lex.PrevColumn());
		WaitForStatementEnd(true);
	}
	return 0;
}

// HLocal: parse local statement; add TOLActLocal action to actions list (al)
// All statements between Local and EndLocal statements are parsed, added as actions
// in TOLActLocal's list of actions
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int TOLParser::HLocal(TOLPActionList& al, int lv, int sublv)
{
	int res, last_st;
	WaitForStatementEnd(true);
	TOLActLocal *aloc = new TOLActLocal();
	if ((res = LevelParser(aloc->block, lv, sublv | SUBLV_LOCAL, last_st)))
	{
		delete aloc;
		return res;
	}
	if (last_st != TOL_ST_ENDLOCAL)
	{
		delete aloc;
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            ST_ENDLOCAL, lex.PrevLine(), lex.PrevColumn());
	}
	WaitForStatementEnd(true);
	al.insert(al.end(), aloc);
	return 0;
}

// HSubscription: parse subscription statement; add TOLActSubscription action to actions
// list (al)
// All statements between Subscription and EndSubscription statements are parsed,
// added as actions in TOLActSubscription's list of actions
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int TOLParser::HSubscription(TOLPActionList& al, int lv, int sublv)
{
	if (sublv & SUBLV_USER || sublv & SUBLV_LOGIN || sublv & SUBLV_SUBSCRIPTION)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            LvStatements(lv), lex.PrevLine(), lex.PrevColumn());
	}
	string by, tpl_file, unit_name, button_name, total, evaluate;
	bool by_publication;
	const TOLLexem *l;
	RequireAtom(l);
	by = l->m_pcoAtom->Identifier();
	if (strcasecmp(by.c_str(), "by_publication") == 0)
		by_publication = true;
	else if (strcasecmp(by.c_str(), "by_section") == 0)
		by_publication = false;
	else
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE, "by_publication, "
		            "by_section", lex.PrevLine(), lex.PrevColumn());
	RequireAtom(l);
	tpl_file = l->m_pcoAtom->Identifier();
	RequireAtom(l);
	button_name = l->m_pcoAtom->Identifier();
	l = lex.GetLexem();
	DEBUGLexem("hsubs atom", l);
	CheckForEOF(l, PERR_EOS_MISSING);
	if (l->m_Res != TOL_LEX_END_STATEMENT)
	{
		CheckForAtom(l);
		total = l->m_pcoAtom->Identifier();
		RequireAtom(l);
		evaluate = l->m_pcoAtom->Identifier();
	}
	int res, last_st;
	if (l->m_Res != TOL_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	TOLActSubscription* aloc = new TOLActSubscription(by_publication, tpl_file, button_name,
	                           total, evaluate);
	sublv |= SUBLV_SUBSCRIPTION;
	if ((res = LevelParser(aloc->block, lv, sublv, last_st)))
	{
		delete aloc;
		return res;
	}
	if (last_st != TOL_ST_ENDSUBSCRIPTION)
	{
		delete aloc;
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            ST_ENDSUBSCRIPTION, lex.PrevLine(), lex.PrevColumn());
	}
	WaitForStatementEnd(true);
	al.insert(al.end(), aloc);
	return 0;
}

// HEdit: parse edit statement; add TOLActEdit action to actions list (al)
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int TOLParser::HEdit(TOLPActionList& al, int lv, int sublv)
{
	const TOLLexem *l;
	string size;
	RequireAtom(l);
	CheckForStatement(l, EditStatements(sublv), lex.PrevLine(), lex.PrevColumn());
	TOLStatement *ist = &(*lex.s_coStatements.find(l->m_pcoAtom->Identifier()));
	RequireAtom(l);
	ValidateAttr(attr, ist, l, TOL_CT_EDIT, 1);
	TEditModifier modifier;
	if (ist->statement == TOL_ST_SUBSCRIPTION)
	{
		if ((sublv & SUBLV_SUBSCRIPTION) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            EditStatements(sublv), lex.PrevLine(), lex.PrevColumn());
		modifier = TOL_EMOD_SUBSCRIPTION;
	}
	else if (ist->statement == TOL_ST_USER)
	{
		if ((sublv & SUBLV_USER) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            EditStatements(sublv), lex.PrevLine(), lex.PrevColumn());
		modifier = TOL_EMOD_USER;
	}
	else if (ist->statement == TOL_ST_LOGIN)
	{
		if ((sublv & SUBLV_LOGIN) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            EditStatements(sublv), lex.PrevLine(), lex.PrevColumn());
		modifier = TOL_EMOD_LOGIN;
	}
	else if (ist->statement == TOL_ST_SEARCH)
	{
		if ((sublv & SUBLV_SEARCH) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            EditStatements(sublv), lex.PrevLine(), lex.PrevColumn());
		modifier = TOL_EMOD_SEARCH;
		l = lex.GetLexem();
		if (l->m_Res != TOL_LEX_END_STATEMENT)
		{
			CheckForAtom(l);
			ValidateDType(l, TOL_DT_NUMBER, PERR_INVALID_VALUE);
			size = l->m_pcoAtom->Identifier();
		}
	}
	else
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            EditStatements(sublv), lex.PrevLine(), lex.PrevColumn());
	}
	TOLActEdit* edit = new TOLActEdit(modifier, attr->Attribute(), atol(size.c_str()));
	al.insert(al.end(), edit);
	if (l->m_Res != TOL_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	return 0;
}

// HSelect: parse select statement; add TOLActSelect action to actions list (al)
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int TOLParser::HSelect(TOLPActionList& al, int lv, int sublv)
{
	string male_name, female_name;
	bool checked = false;
	const TOLLexem *l;
	RequireAtom(l);
	CheckForStatement(l, ST_SUBSCRIPTION ", " ST_USER ", " ST_SEARCH,
	                  lex.PrevLine(), lex.PrevColumn());
	TOLStatement *ist = &(*lex.s_coStatements.find(l->m_pcoAtom->Identifier()));
	RequireAtom(l);
	ValidateAttr(attr, ist, l, TOL_CT_SELECT, 1);
	TSelectModifier modifier;
	if (ist->statement == TOL_ST_SUBSCRIPTION)
	{
		if ((sublv & SUBLV_SUBSCRIPTION) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            SelectStatements(sublv), lex.PrevLine(), lex.PrevColumn());
		modifier = TOL_SMOD_SUBSCRIPTION;
	}
	else if (ist->statement == TOL_ST_USER)
	{
		if ((sublv & SUBLV_USER) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            SelectStatements(sublv), lex.PrevLine(), lex.PrevColumn());
		modifier = TOL_SMOD_USER;
		if (strcasecmp(attr->Identifier(), "Gender") == 0)
		{
			RequireAtom(l);
			male_name = l->m_pcoAtom->Identifier();
			RequireAtom(l);
			female_name = l->m_pcoAtom->Identifier();
		}
		else if (strncasecmp(attr->Identifier(), "Pref", 4) == 0)
		{
			l = lex.GetLexem();
			CheckForEOF(l, PERR_EOS_MISSING);
			if (l->m_Res != TOL_LEX_END_STATEMENT)
			{
				CheckForAtom(l);
				if (strcasecmp(l->m_pcoAtom->Identifier(), "checked") == 0)
					checked = true;
			}
		}
	}
	else if (ist->statement == TOL_ST_SEARCH)
	{
		if ((sublv & SUBLV_SEARCH) == 0)
			FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
			            SelectStatements(sublv), lex.PrevLine(), lex.PrevColumn());
		modifier = TOL_SMOD_SEARCH;
	}
	else
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE, ST_SUBSCRIPTION,
		            lex.PrevLine(), lex.PrevColumn());
	TOLActSelect* select = new TOLActSelect(modifier, attr->Attribute(), male_name, female_name, checked);
	al.insert(al.end(), select);
	if (l->m_Res != TOL_LEX_END_STATEMENT)
		WaitForStatementEnd(true);
	return 0;
}

// HUser: parse user statement; add TOLActUser action to actions list (al)
// All statements between User and EndUser statements are parsed, added as actions
// in TOLActUsers's list of actions
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int TOLParser::HUser(TOLPActionList& al, int lv, int sublv)
{
	if (sublv & SUBLV_USER || sublv & SUBLV_SUBSCRIPTION || sublv & SUBLV_LOGIN)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            LvStatements(lv), lex.PrevLine(), lex.PrevColumn());
	}
	const TOLLexem *l;
	RequireAtom(l);
	bool add = strcasecmp(l->m_pcoAtom->Identifier(), "add") == 0;
	RequireAtom(l);
	string tpl_file = l->m_pcoAtom->Identifier();
	RequireAtom(l);
	string button_name = l->m_pcoAtom->Identifier();
	int res, last_st;
	WaitForStatementEnd(true);
	TOLActUser *user = new TOLActUser(add, tpl_file, button_name);
	if ((res = LevelParser(user->block, lv, sublv | SUBLV_USER, last_st)))
	{
		delete user;
		return res;
	}
	if (last_st != TOL_ST_ENDUSER)
	{
		delete user;
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            ST_ENDUSER, lex.PrevLine(), lex.PrevColumn());
	}
	WaitForStatementEnd(true);
	al.insert(al.end(), user);
	return 0;
}

// HLogin: parse login statement; add TOLActLogin action to actions list (al)
// All statements between Login and EndLogin statements are parsed, added as actions
// in TOLActLogin's list of actions
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int TOLParser::HLogin(TOLPActionList& al, int lv, int sublv)
{
	if (sublv & SUBLV_USER || sublv & SUBLV_SUBSCRIPTION || sublv & SUBLV_LOGIN)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            LvStatements(lv), lex.PrevLine(), lex.PrevColumn());
	}
	const TOLLexem *l;
	RequireAtom(l);
	string tpl_file = l->m_pcoAtom->Identifier();
	RequireAtom(l);
	string button_name = l->m_pcoAtom->Identifier();
	int res, last_st;
	WaitForStatementEnd(true);
	TOLActLogin *login = new TOLActLogin(tpl_file, button_name);
	if ((res = LevelParser(login->block, lv, sublv | SUBLV_LOGIN, last_st)))
	{
		delete login;
		return res;
	}
	if (last_st != TOL_ST_ENDLOGIN)
	{
		delete login;
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            ST_ENDUSER, lex.PrevLine(), lex.PrevColumn());
	}
	WaitForStatementEnd(true);
	al.insert(al.end(), login);
	return 0;
}

// HSearch: parse search statement; add TOLActSearch action to actions list (al)
// All statements between Search and EndSearch statements are parsed, added as actions
// in TOLActSearch's list of actions
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int TOLParser::HSearch(TOLPActionList& al, int lv, int sublv)
{
	if (sublv & SUBLV_SEARCH)
	{
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            LvStatements(lv), lex.PrevLine(), lex.PrevColumn());
	}
	const TOLLexem *l;
	RequireAtom(l);
	string tpl_file = l->m_pcoAtom->Identifier();
	RequireAtom(l);
	string button_name = l->m_pcoAtom->Identifier();
	int res, last_st;
	WaitForStatementEnd(true);
	TOLActSearch *search = new TOLActSearch(tpl_file, button_name);
	if ((res = LevelParser(search->block, lv, sublv | SUBLV_SEARCH, last_st)))
	{
		delete search;
		return res;
	}
	if (last_st != TOL_ST_ENDSEARCH)
	{
		delete search;
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            ST_ENDSEARCH, lex.PrevLine(), lex.PrevColumn());
	}
	WaitForStatementEnd(true);
	al.insert(al.end(), search);
	return 0;
}

// HWith: parse with statement; add TOLActWith action to actions list (al)
// All statements between With and EndWith statements are parsed, added as actions
// in TOLActWith's list of actions
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int lv - current level
//		int sublv - current sublevel
inline int TOLParser::HWith(TOLPActionList& al, int lv, int sublv)
{
	int res, last_st;
	TOLStatement* ist = &(*lex.s_coStatements.find(ST_ARTICLE));
	const TOLLexem* l;
	RequireAtom(l);
	TOLTypeAttributes* ta = ist->FindType(l->m_pcoAtom->Identifier());
	if (ta == NULL)
	{
		delete ta;
		string what;
		ist->PrintTypes(what);
		FatalPError(parse_err, PERR_INV_TYPE_VAL, MODE_PARSE,
		            what.c_str(), lex.PrevLine(), lex.PrevColumn());
	}
	string art_type = l->m_pcoAtom->Identifier();
	RequireAtom(l);
	TOLAttribute* a = ist->FindTypeAttr(l->m_pcoAtom->Identifier(), art_type.c_str(),
	                        TOL_CT_WITH, &ta);
	delete ta;
	if (a == NULL)
	{
		delete a;
		string what;
		ist->PrintTAttrs(what, art_type.c_str(), TOL_CT_WITH);
		FatalPError(parse_err, PERR_INVALID_ATTRIBUTE, MODE_PARSE,
		            what.c_str(), lex.PrevLine(), lex.PrevColumn());
	}
	delete a;
	string field = l->m_pcoAtom->Identifier();
	WaitForStatementEnd(true);
	TOLActWith* aloc = new TOLActWith(art_type, field);
	if ((res = LevelParser(aloc->block, lv, sublv | SUBLV_WITH, last_st)))
	{
		delete aloc;
		return res;
	}
	if (last_st != TOL_ST_ENDWITH)
	{
		delete aloc;
		FatalPError(parse_err, PERR_WRONG_STATEMENT, MODE_PARSE,
		            ST_ENDWITH, lex.PrevLine(), lex.PrevColumn());
	}
	WaitForStatementEnd(true);
	fields.insert(string2string::value_type(field, art_type));
	al.insert(al.end(), aloc);
	return 0;
}

// LevelParser: read lexems until it finds a statement or reaches end of file
// Depending on read statement it calls on of HArticle, HDate, HEdit, HFormParameters,
// HIf, HInclude, HIssue, HLanguage, HList, HLocal, HLogin, HPrint, HPublication,
// HSearch, HSection, HSelect, HSubscription, HURLParameters, HUser, HWith methods.
// It does not add actions to action list (al)
// Parameters:
//		TOLPActionList& al - reference to actions list
//		int level - current level
//		int sublevel - current sublevel
//		int& last_st - last statement read
int TOLParser::LevelParser(TOLPActionList& al, int level, int sublevel, int& last_st)
{
	bool isEOF = false;
	int res;
	while (isEOF == false)
	{
		const TOLLexem *l = WaitForStatementStart(al);
		int err = level == LV_ROOT ? RES_OK : PERR_UNEXPECTED_EOF;
		CheckForEOF(l, err);
		RequireAtom(l);
		if (strcmp(l->m_pcoAtom->ClassName(), CL_TOLSTATEMENT))
		{
			SetPError(parse_err, PERR_ATOM_NOT_STATEMENT, MODE_PARSE,
			          LvStatements(level), lex.PrevLine(), lex.PrevColumn());
			WaitForStatementEnd(false);
			continue;
		}
		TOLStatement *st = (TOLStatement*)l->m_pcoAtom;
		last_st = st->statement;
		if (st->statement == TOL_ST_LANGUAGE)
		{
			HLanguage(al, level, sublevel);
		}
		else if (st->statement == TOL_ST_INCLUDE)
		{
			HInclude(al);
		}
		else if (st->statement == TOL_ST_PUBLICATION)
		{
			DEBUGLexem("hpublication 1", l);
			HPublication(al, level, sublevel, st);
		}
		else if (st->statement == TOL_ST_ISSUE)
		{
			HIssue(al, level, sublevel, l);
		}
		else if (st->statement == TOL_ST_SECTION)
		{
			HSection(al, level, sublevel, l);
		}
		else if (st->statement == TOL_ST_ARTICLE)
		{
			HArticle(al, level, sublevel, l);
		}
		else if (st->statement == TOL_ST_LIST)
		{
			if ((res = HList(al, level, sublevel, l)))
				return res;
		}
		else if (st->statement == TOL_ST_FOREMPTYLIST)
		{
			return 0;
		}
		else if (st->statement == TOL_ST_URLPARAMETERS)
		{
			HURLParameters(al, l);
		}
		else if (st->statement == TOL_ST_FORMPARAMETERS)
		{
			HFormParameters(al);
		}
		else if (st->statement == TOL_ST_PRINT)
		{
			HPrint(al, level, sublevel);
		}
		else if (st->statement == TOL_ST_IF)
		{
			if ((res = HIf(al, level, sublevel, l)))
				return res;
		}
		else if (st->statement == TOL_ST_DATE)
		{
			HDate(al, l);
		}
		else if (st->statement == TOL_ST_LOCAL)
		{
			if ((res = HLocal(al, level, sublevel)))
				return res;
		}
		else if (st->statement == TOL_ST_SUBSCRIPTION)
		{
			if ((res = HSubscription(al, level, sublevel)))
				return res;
		}
		else if (st->statement == TOL_ST_EDIT)
		{
			HEdit(al, level, sublevel);
		}
		else if (st->statement == TOL_ST_SELECT)
		{
			HSelect(al, level, sublevel);
		}
		else if (st->statement == TOL_ST_USER)
		{
			if ((res = HUser(al, level, sublevel)))
				return res;
		}
		else if (st->statement == TOL_ST_LOGIN)
		{
			if ((res = HLogin(al, level, sublevel)))
				return res;
		}
		else if (st->statement == TOL_ST_SEARCH)
		{
			if ((res = HSearch(al, level, sublevel)))
				return res;
		}
		else if (st->statement == TOL_ST_WITH)
		{
			if ((res = HWith(al, level, sublevel)))
				return res;
		}
		else if (st->statement == TOL_ST_ELSE)
		{
			return 0;
		}
		else if (st->statement == TOL_ST_ENDIF)
		{
			return 0;
		}
		else if (st->statement == TOL_ST_ENDLIST)
		{
			return 0;
		}
		else if (st->statement == TOL_ST_ENDLOCAL)
		{
			return 0;
		}
		else if (st->statement == TOL_ST_ENDSUBSCRIPTION)
		{
			return 0;
		}
		else if (st->statement == TOL_ST_ENDUSER)
		{
			return 0;
		}
		else if (st->statement == TOL_ST_ENDLOGIN)
		{
			return 0;
		}
		else if (st->statement == TOL_ST_ENDSEARCH)
		{
			return 0;
		}
		else if (st->statement == TOL_ST_ENDWITH)
		{
			return 0;
		}
		else
		{
			SetPError(parse_err, PERR_INVALID_STATEMENT, MODE_PARSE,
			          LvStatements(level), lex.PrevLine(), lex.PrevColumn());
			WaitForStatementEnd(false);
		}
	}
	return 0;
}

// TOLParser: constructor
// Parameters:
//		cpChar p_pchTpl - template path
//		string dr = "" - document root
TOLParser::TOLParser(cpChar p_pchTpl, string dr)
		: parent_tpl(4, cpCharHashFn, cpCharEqual, stringValue),
		child_tpl(4, cpCharHashFn, cpCharEqual, stringValue)
{
	m_coOpMutex.Lock();
	tpl = p_pchTpl != NULL ? strdup(p_pchTpl) : NULL;
	if (tpl)
		parent_tpl.insert_unique(tpl);
	document_root = dr;
	parsed = false;
	parse_err_printed = false;
	write_err_printed = false;
	m_nTplFileLen = 0;
	m_nTplFD = -1;
	m_pchTplBuf = NULL;
	pthread_once(&m_OpMapControl, OpMapInit);
	memset((void*)&tpl_stat, 0, sizeof(tpl_stat));
	MapTpl();
	UnMapTpl();
	m_coOpMutex.Unlock();
}

// copy-constructor
TOLParser::TOLParser(const TOLParser& p)
		: parent_tpl(4, cpCharHashFn, cpCharEqual, stringValue),
		child_tpl(4, cpCharHashFn, cpCharEqual, stringValue)
{
	m_coOpMutex.Lock();
	document_root = p.document_root;
	tpl = NULL;
	m_nTplFileLen = 0;
	m_nTplFD = -1;
	m_pchTplBuf = NULL;
	*this = p;
	pthread_once(&m_OpMapControl, OpMapInit);
	memset((void*)&tpl_stat, 0, sizeof(tpl_stat));
	MapTpl();
	UnMapTpl();
	m_coOpMutex.Unlock();
}

// destructor
TOLParser::~TOLParser()
{
	m_coOpMutex.Lock();
	Reset();
	UnMapTpl();
	m_coOpMutex.Unlock();
}

// SetMYSQL: set MySQL connection
// Parameters:
//		MYSQL* p_pMYSQL - pointer to MySQL server connection
void TOLParser::SetMYSQL(MYSQL* p_pMYSQL)
{
	s_MYSQL = p_pMYSQL;
	TOLAction::m_coSql = p_pMYSQL;
}

// assign operator
const TOLParser& TOLParser::operator =(const TOLParser& p)
{
	if (this == &p)
		return * this;
	Reset();
	m_coOpMutex.Lock();
	document_root = p.document_root;
	if (tpl != NULL)
		free(tpl);
	tpl = NULL;
	if (p.tpl != NULL)
		tpl = strdup(p.tpl);
	actions = p.actions;
	lex = p.lex;
	parent_tpl = p.parent_tpl;
	child_tpl = child_tpl;
	fields = p.fields;
	m_coOpMutex.Unlock();
	return *this;
}

// Reset: reset parser: clear actions tree, reset lex, clear errors list
void TOLParser::Reset()
{
	m_coOpMutex.Lock();
	clearParseErrors();
	clearWriteErrors();
	child_tpl.clear();
	lex.Reset(m_pchTplBuf, m_nTplFileLen);
	if (tpl != NULL)
		parent_tpl.insert_unique(tpl);
	parse_err_printed = false;
	write_err_printed = false;
	for (TOLPActionList::iterator al_i = actions.begin(); al_i != actions.end(); ++al_i)
	{
		delete *al_i;
		*al_i = NULL;
	}
	actions.clear();
	parsed = false;
	m_coOpMutex.Unlock();
}

// Reset: reset all the parsers in the hash
void TOLParser::ResetHash()
{
	LockHash();
	TOLParserHash::iterator coIt = m_coPHash.begin();
	for (; coIt != m_coPHash.end(); ++coIt)
	{
		(*coIt)->Reset();
	}
	UnlockHash();
}

// Parse: start the parser; return the parse result
// Parameters:
//		bool force = false - if true, force reparsing of template; if false, do not
//			parse the template again if already parsed
int TOLParser::Parse(bool force)
{
	m_coOpMutex.Lock();
	MapTpl();
	if (parsed && !force)
	{
		m_coOpMutex.Unlock();
		return 0;
	}
	Reset();
	MapTpl();
	if (m_pchTplBuf == NULL)
		FatalError(parse_err, EMAP_FAILED, MODE_PARSE);
	int last_st;
	parsed = true;
	int nRetVal = LevelParser(actions, LV_ROOT, SUBLV_NONE, last_st);
	m_coOpMutex.Unlock();
	return nRetVal;
}

// WriteOutput: write actions output to given file stream
// Parameters:
//		const TOLContext& c - context
//		fstream& fs - output file stream
int TOLParser::WriteOutput(const TOLContext& c, fstream& fs)
{
	m_coOpMutex.Lock();
	if (!parsed)
		Parse();
	TOLPActionList::iterator al_i;
	clearWriteErrors();
	TOLContext lc = c;
	lc.SetLevel(CLV_ROOT);
	string2string::iterator it;
	for (it = fields.begin(); it != fields.end(); ++it)
		lc.SetField((*it).first, (*it).second);
	if (DoDebug())
		fs << "<!-- start template " << tpl << " -->" << endl;
	int nRetVal = 0;
	for (al_i = actions.begin(); al_i != actions.end(); ++al_i)
		if ((*al_i) != 0)
		{
			int err;
			if (DoDebug())
				fs << "<!-- taking action " << (*al_i)->ClassName() << " -->\n";
			if ((err = (*al_i)->TakeAction(lc, fs)) != RES_OK)
				SetPError(write_err, err, MODE_WRITE, (*al_i)->ClassName(), 0, 0);
			if (DoDebug())
				fs << "<!-- action " << (*al_i)->ClassName() << " result: " << err << " -->\n";
			nRetVal |= err;
		}
	if (DoDebug())
		fs << "<!-- end template " << tpl << " -->" << endl;
	m_coOpMutex.Unlock();
	return RES_OK;
}

// PrintParseErrors: print parse errors to given output stream
// Parameters:
//		fstream& fs - output file stream
//		bool p_bMainTpl = false - true if this is the main template
void TOLParser::PrintParseErrors(fstream& fs, bool p_bMainTpl)
{
	m_coOpMutex.Lock();
	if (p_bMainTpl)
	{
		SetWriteErrors(true);
		fs << "<p>- on main template " << tpl << "<p>";
	}
	else
	{
		if (*parse_err_printed)
		{
			m_coOpMutex.Unlock();
			return ;
		}
		fs << "<p>- on included template " << tpl << "<p>";
	}
	TOLErrorList::iterator el_i;
	for (el_i = parse_err.begin(); el_i != parse_err.end(); ++el_i)
		(*el_i)->Print(fs);
	if (parse_err.empty())
		fs << "No errors found<p>";
	parse_err_printed = true;
	for (StringHash::iterator sh_i = child_tpl.begin(); sh_i != child_tpl.end(); ++sh_i)
	{
		if (*sh_i == tpl)
			continue;
		TOLParserHash::iterator ph_i;
		if ((ph_i = m_coPHash.find((*sh_i).c_str())) != m_coPHash.end())
			(*ph_i)->PrintParseErrors(fs);
	}
	m_coOpMutex.Unlock();
}

// PrintWriteErrors: print write errors to given output stream
// Parameters:
//		fstream& fs - output file stream
//		bool p_bMainTpl = false - true if this is the main template
void TOLParser::PrintWriteErrors(fstream& fs, bool p_bMainTpl)
{
	m_coOpMutex.Lock();
	if (p_bMainTpl)
	{
		SetWriteErrors(true);
		fs << "<p>- on main template " << tpl << "<p>";
	}
	else
	{
		if (*write_err_printed)
		{
			m_coOpMutex.Unlock();
			return ;
		}
		fs << "<p>- on included template " << tpl << "<p>";
	}
	TOLErrorList::iterator el_i;
	for (el_i = write_err.begin(); el_i != write_err.end(); ++el_i)
		(*el_i)->Print(fs);
	if (write_err.empty())
		fs << "No errors found<p>";
	write_err_printed = true;
	for (StringHash::iterator sh_i = child_tpl.begin(); sh_i != child_tpl.end(); ++sh_i)
	{
		if (*sh_i == tpl)
			continue;
		TOLParserHash::iterator ph_i;
		if ((ph_i = m_coPHash.find((*sh_i).c_str())) != m_coPHash.end())
			(*ph_i)->PrintWriteErrors(fs);
	}
	m_coOpMutex.Unlock();
}

// TestLex: test the lex; debug purposes only
void TOLParser::TestLex()
{
	MapTpl();
	cout << "START PARSED FILE\n";
	const TOLLexem *c_lexem;
	while (((c_lexem = lex.GetLexem())->m_Res) != TOL_ERR_EOF)
	{
		if (c_lexem->m_pcoAtom)
			cout << "*[" << c_lexem->m_pcoAtom->ClassName()
			<< " \"" << c_lexem->m_pcoAtom->Identifier()
			<< "\" " << (int)(c_lexem->m_DataType) << "]\n";
		else if (c_lexem->m_pchTextStart)
		{
			cout << "%";
			cout.write(c_lexem->m_pchTextStart, c_lexem->m_nTextLen);
			cout << "% text len: " << c_lexem->m_nTextLen << "\n";
		}
		else
			cout << "# received nothing #\n";
		cout << "$ lexem res: " << (int)(c_lexem->m_Res) << " $\n";
	}
	cout << "END PARSED FILE\n";
}
