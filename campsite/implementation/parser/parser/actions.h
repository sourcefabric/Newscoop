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

Define CParameter, CAction, CActLanguage, CActInclude, CActPublication,
CActIssue, CActSection, CActArticle, CActList, CActURLParameters,
CActFormParameters, CActPrint, CActIf, CActDate, CActText, CActLocal,
CActSubscription, CActEdit, CActSelect, CActUser, CActLogin,
CActSearch, CActWith classes. All these classes except CParameter and
CAction correspond to a certain "instruction"; they inherit CAction.
There is one important virtual method redefined by every action class: takeAction.
This receives a context parameter and a stream to write output to. The context
can be modified by this method.

******************************************************************************/

#ifndef _CMS_ACTIONS
#define _CMS_ACTIONS

#include <mysql/mysql.h>
#include <string>
#include <set>
#include <typeinfo>

#include "cms_types.h"
#include "context.h"
#include "error.h"
#include "atoms.h"
#include "cgiparams.h"
#include "cparser.h"

typedef set<int> IntSet;

// exception thrown by CParameter class
class InvalidOperation : public exception
{
public:
	virtual const char* what () const throw() { return "invalid operation"; }
};

// exception thrown by action classes having modifiers (CActList, CActPrint, CActIf, CActEdit,
// CActSelect)
class InvalidModifier : public exception
{
public:
	virtual const char* what () const throw() { return "invalid operation"; }
};


// CParameter: defines a parameter class
class CParameter
{
public:
	// default constructor
	// Parameters:
	//		const string& p_rcoAttr - attribute
	//		CompOperation* p_pcoOperation - operation
	//		const string& p_rcoSpec - special value
	CParameter(const string& p_rcoAttr, const string& p_rcoType = string(""),
	           CompOperation* p_pcoOperation = NULL,
	           const string& p_rcoSpec = string(""))
		: m_coAttr(p_rcoAttr), m_coType(p_rcoType), m_pcoOperation(p_pcoOperation),
		m_coSpec(p_rcoSpec) {}

	// copy-constructor
	CParameter(const CParameter& s) : m_pcoOperation(NULL) { *this = s; }

	// virtual destructor
	virtual ~CParameter() { delete m_pcoOperation; m_pcoOperation = NULL; }

	// clone this object
	virtual CParameter* clone() const { return new CParameter(*this); }

	// attribute: return attribute
	const string& attribute() const { return m_coAttr; }

	// attrType: return attribute type
	const string& attrType() const { return m_coType; }

	// spec: return special value
	const string& spec() const { return m_coSpec; }

	// value: return value
	string value() const throw(InvalidOperation)
	{
		if (m_pcoOperation == NULL)
			return string("");
		return m_pcoOperation->second();
	}

	// opSymbol: return operator symbol
	const string& opSymbol() const throw(InvalidOperation)
	{
		if (m_pcoOperation == NULL)
			throw InvalidOperation();
		return m_pcoOperation->symbol();
	}

	// returns pointer to operation
	const CompOperation* operation() const { return m_pcoOperation; }

	// apply operation to the fist (parameter) and second (stored) operands
	bool applyOp(const string& p_rcoFirst) const throw(InvalidOperation, InvalidValue)
	{
		if (m_pcoOperation == NULL)
			throw InvalidOperation();
		return m_pcoOperation->apply(p_rcoFirst);
	}

	// apply operation fist and second operands (both given as parameters)
	bool applyOp(const string& p_rcoFirst, const string& p_rcoSecond) const
		throw(InvalidOperation, InvalidValue)
	{
		if (m_pcoOperation == NULL)
			throw InvalidOperation();
		return m_pcoOperation->apply(p_rcoFirst, p_rcoSecond);
	}

	// assign operator
	const CParameter& operator =(const CParameter& p_rcoSrc);

private:
	string m_coAttr;				// attribute name
	string m_coType;				// attribute type
	CompOperation* m_pcoOperation;	// operation to apply on attribute
	string m_coSpec;
};

class CParameterList : public list <CParameter*>
{
public:
	// default constructor
	CParameterList() {}

	// copy-constructor
	CParameterList(const CParameterList& o) { *this = o; }

	// virtual destructor
	virtual ~CParameterList() { clear(); }

	// assign operator
	const CParameterList& operator =(const CParameterList&);

	void clear();
};

class CActionList;

// CAction: generic action; abstract class
// Important methods:
//		takeAction: performs the action
class CAction
{
protected:
	static TK_MYSQL m_coSql;		// key variable: pointer to MySQL connection
	static TK_bool m_coDebug;		// key variable: print debug info(true/false)

protected:
	// DEBUGAct: print debug information
	inline void DEBUGAct(const char*, const char*, sockstream&);
	
	// SQLEscapeString: escape given string for sql query; returns escaped string
	// The returned string must be deallocated by the user using delete operator.
	// Parameters:
	//		const char* src - source string
	//		ulint p_nLength - string length
	char* SQLEscapeString(const char* src, ulint p_nLength);

public:
	// destructor
	virtual ~CAction() {}

	// actionType: return string containing action type name
	virtual string actionType() const { return typeid(*this).name(); }

	// initTempMembers: init thread specific variables
	static void initTempMembers();

	// setDebug: set debug: true/false
	static void setDebug(bool p_bDebug = false) { m_coDebug = p_bDebug; }

	// debug: return debug value (true/false)
	static bool debug() { return *(&m_coDebug); }

	// return pointer to sql connection
	static MYSQL* sqlConn() { return &m_coSql; }

	// set sql connection
	static void setSql(MYSQL* sql) { m_coSql = sql; }

	// runActions: run actions in a list of actions
	// Parameters:
	//		CActionList& al - list of actions
	//		CContext& c - current context
	//		sockstream& fs - output stream
	static int runActions(CActionList& al, CContext& c, sockstream& fs);
	
	// action: return action identifier
	virtual TAction action() const = 0;

	// clone this object
	virtual CAction* clone() const = 0;

	// takeAction: performs the action; virtual pure method
	// Parametes:
	//		CContext& c - current context (may be modified by action)
	//		sockstream& fs - write the result to output stream; some actions may not write
	//			anything to ouput stream
	virtual int takeAction(CContext& c, sockstream& fs) = 0;
	
	// dateFormat: format the given date according to the given format in given language
	// Returns string containing formated date
	// Parameters:
	//		const char* p_pchDate - date to format
	//		const char* p_pchFormat - format of the date
	//		id_type p_nLanguageId - language to use
	string dateFormat(const char* p_pchDate, const char* p_pchFormat, id_type p_nLanguageId);
};


class CActionList : public list <CAction*>
{
public:
	// default constructor
	CActionList() {}

	// copy-constructor
	CActionList(const CActionList& o) { *this = o; }

	// virtual destructor
	virtual ~CActionList() { clear(); }

	// assign operator
	const CActionList& operator =(const CActionList&);

	void clear();
};


// CActLanguage: language action - corresponding to Language statement (see manual)
class CActLanguage : public CAction
{
protected:
	string m_coLang;		// language name

public:
	// constructor
	// Parameters:
	//		const string& p_pchLang - language name
	CActLanguage(const string& p_pcoLang) : m_coLang(p_pcoLang) {}

	// destructor
	virtual ~CActLanguage() {}
	
	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_LANGUAGE; }

	// clone this object
	virtual CAction* clone() const { return new CActLanguage(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context (modified by action)
	//		sockstream& fs - output stream (not used)
	virtual int takeAction(CContext& c, sockstream& fs);
};

class CParserMap;

// CActInclude: include action - corresponding to Include statement (see manual)
class CActInclude : public CAction
{
protected:
	string tpl_path;				// template to include
	string document_root;
	CParserMap* parser_map;			// pointer to parsers hash

public:
	// constructor
	// Parameters:
	//		const string& p - path to included template
	//		CParserHash* ph - pointer to parsers hash
	CActInclude(const string& p, const string& dr, CParserMap* pm)
	: tpl_path(p), document_root(dr), parser_map(pm) {}
	
	// destructor
	virtual ~CActInclude() {}
	
	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_INCLUDE; }

	// clone this object
	virtual CAction* clone() const { return new CActInclude(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context (may be modified by action)
	//		sockstream& fs - output stream
	virtual int takeAction(CContext& c, sockstream& fs);
};

// CActPublication: Publication action - corresponding to Publication statement (see manual)
class CActPublication : public CAction
{
protected:
	CParameter param;		// parameter

public:
	// constructor
	// Parameters:
	//		const CParameter& p - parameter
	CActPublication(const CParameter& p) : param(p) {}

	// copy-constructor
	CActPublication(const CActPublication& s) : param("") { *this = s; }

	// destructor
	virtual ~CActPublication() {}

	// assign operator
	const CActPublication& operator =(const CActPublication& o)
	{
		param = o.param;
		return *this;
	}
	
	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_PUBLICATION; }

	// clone this object
	virtual CAction* clone() const { return new CActPublication(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context (modified by action)
	//		sockstream& fs - output stream (not used)
	virtual int takeAction(CContext& c, sockstream& fs);
};

// CActIssue: Issue action - corresponding to Issue statement (see manual)
class CActIssue : public CAction
{
protected:
	CParameter param;		// parameter

public:
	// constructor
	// Parameters:
	//		const CParameter& p - parameter
	CActIssue(const CParameter& p) : param(p) {}

	// copy-constructor
	CActIssue(const CActIssue& s) : param("") { *this = s; }
	
	// destructor
	virtual ~CActIssue() {}

	// assign operator
	const CActIssue& operator =(const CActIssue& o)
	{
		param = o.param;
		return *this;
	}
	
	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_ISSUE; }

	// clone this object
	virtual CAction* clone() const { return new CActIssue(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context (modified by action)
	//		sockstream& fs - output stream (not used)
	virtual int takeAction(CContext& c, sockstream& fs);
};

// CActSection: Section action - corresponding to Section statement (see manual)
class CActSection : public CAction
{
protected:
	CParameter param;		// parameter

public:
	// constructor
	// Parameters:
	//		const CParameter& p - parameter
	CActSection(const CParameter& p) : param(p) {}

	// copy-constructor
	CActSection(const CActSection& s) : param("") { *this = s; }
	
	// destructor
	virtual ~CActSection() {}

	// assign operator
	const CActSection& operator =(const CActSection& o)
	{
		param = o.param;
		return *this;
	}
	
	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_SECTION; }

	// clone this object
	virtual CAction* clone() const { return new CActSection(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context (modified by action)
	//		sockstream& fs - output stream (not used)
	virtual int takeAction(CContext& c, sockstream& fs);
};

// CActArticle: Article action - corresponding to Article statement (see manual)
class CActArticle : public CAction
{
protected:
	CParameter param;		// parameter

public:
	// constructor
	// Parameters:
	//		const CParameter& p - parameter
	CActArticle(const CParameter& p) : param(p) {}
	
	// copy-constructor
	CActArticle(const CActArticle& s) : param("") { *this = s; }
	
	// destructor
	virtual ~CActArticle() {}

	// assign operator
	const CActArticle& operator =(const CActArticle& o)
	{
		param = o.param;
		return *this;
	}
	
	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_ARTICLE; }

	// clone this object
	virtual CAction* clone() const { return new CActArticle(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context (modified by action)
	//		sockstream& fs - output stream (not used)
	virtual int takeAction(CContext& c, sockstream& fs);
};

// CActTopic: Topic action - corresponding to Topic statement (see manual)
class CActTopic : public CAction
{
protected:
	CParameter param;		// parameter

public:
	// constructor
	// Parameters:
	//		const CParameter& p - parameter
	CActTopic(const CParameter& p) : param(p) {}
	
	// copy-constructor
	CActTopic(const CActTopic& s) : param("") { *this = s; }

	// destructor
	virtual ~CActTopic() {}

	// assign operator
	const CActTopic& operator =(const CActTopic& o)
	{
		param = o.param;
		return *this;
	}
	
	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_TOPIC; }

	// clone this object
	virtual CAction* clone() const { return new CActTopic(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context (modified by action)
	//		sockstream& fs - output stream (not used)
	virtual int takeAction(CContext& c, sockstream& fs);
};

class CListModifiers : public set<int>
{
public:
	CListModifiers();
	bool validModifier(int m) const { return find(m) != end(); }
};

// CActList: List action - corresponding to List statement (see manual)
class CActList : public CAction
{
	friend class CParser;

private:
	static CListModifiers s_coModifiers;

protected:
	lint length;					// list length
	lint columns;					// columns (used to build tables)
	CParameterList mod_param;		// modifier parameters
	CParameterList ord_param;		// order parameters
	CActionList first_block;		// first list of actions (list is not empty)
	CActionList second_block;		// second list of action (empty list)
	int modifier;					// modifier

	// WriteModParam: add conditions - corresponding to modifier parameters -
	// to where clause of the query. Used for Issue and Section modifiers.
	// Parameters:
	//		string& s - string to add conditions to (where clause)
	//		CContext& c - current context
	//		string& table - string containig tables used in query
	int WriteModParam(string& s, CContext& c, string& table);
	
	// WriteArtParam: add conditions - corresponding to modifier parameters -
	// to where clause of the query. Used for Article modifier.
	// Parameters:
	//		string& s - string to add conditions to (where clause)
	//		CContext& c - current context
	//		string& table - string containig tables used in query
	int WriteArtParam(string& s, CContext& c, string& table);
	
	// WriteSrcParam: add conditions - corresponding to modifier parameters -
	// to where clause of the query. Used for SearchResult modifier.
	// Parameters:
	//		string& s - string to add conditions to (where clause)
	//		CContext& c - current context
	//		string& table - string containig tables used in query
	int WriteSrcParam(string& s, CContext& c, string& table);
	
	// WriteOrdParam: add conditions - corresponding to order parameters -
	// to order clause of the query.
	// Parameters:
	//		string& s - string to add conditions to (order clause)
	int WriteOrdParam(string& s);
	
	// WriteLimit: add conditions to limit clause of the query.
	// Parameters:
	//		string& s - string to add conditions to (limit clause)
	//		CContext& c - current context
	int WriteLimit(string& s, CContext& c);
	
	// SetContext: set the context current Issue, Section or Article depending of list
	// modifier
	// Parameters:
	//		CContext& c - current context
	// 		id_type value - value to be set
	void SetContext(CContext& c, id_type value);
	
	// IMod2Level: convert from list modifier to level identifier; return level identifier
	// Parameters:
	//		int m - list modifier
	CListLevel IMod2Level(int);

public:
	// constructor
	// Parameters:
	//		int m - list modifier
	//		lint l - list length
	//		lint c - list columns
	//		CParameterList& mp - modifier parameter list
	//		CParameterList& op - order parameter list
	CActList(int m, lint l, lint c, CParameterList& mp, CParameterList& op)
		throw (InvalidModifier)
		: length(l), columns(c), mod_param(mp), ord_param(op), modifier(m)
	{
		if (!s_coModifiers.validModifier(m))
			throw InvalidModifier();
	}

	// destructor
	virtual ~CActList() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_LIST; }

	// clone this object
	virtual CAction* clone() const { return new CActList(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context (not modified by action)
	//		sockstream& fs - output stream
	virtual int takeAction(CContext& c, sockstream& fs);

	// validModifier: return true if modifier is valid; false otherwise
	static bool validModifier(int m) { return s_coModifiers.validModifier(m); }
};

// CActURLParameters: URLParameters action - corresponding to URLParameters
// statement (see manual)
class CActURLParameters : public CAction
{
protected:
	id_type image_nr;		// if not -1, print url parameters for image nr.
	bool fromstart;			// if true, print url parameters using template start parameters
	bool allsubtitles;		// if true, print all subtitles parameter
	CListLevel reset_from_list;	// level from which to reset list start
	id_type m_coTemplate;	// specified a certain template to be used
	TPubLevel m_nPubLevel;	// identifies the level in the publication structure; parameters
							// above this level are cut
	bool m_bArticleAttachment;

	// PrintSubtitlesURL: print url parameters for subtitle list/printing
	// Parameters:
	//		CContext& c - current context
	//		sockstream& fs - output stream
	//		bool& first - used to signal if first parameter in list (for printing separators)
	void PrintSubtitlesURL(CContext& c, sockstream& fs, bool& first);

public:
	// constructor
	CActURLParameters(bool fs = false, bool as = false, id_type i = -1, CListLevel r_fl = CLV_ROOT,
	                  id_type tpl = -1, TPubLevel lvl = CMS_PL_ARTICLE,
					  bool p_bArticleAttachment = false)
		: image_nr(i), fromstart(fs), allsubtitles(as), reset_from_list(r_fl), m_coTemplate(tpl),
	m_nPubLevel(lvl), m_bArticleAttachment(p_bArticleAttachment) {}

	// destructor
	virtual ~CActURLParameters() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_URLPARAMETERS; }

	// clone this object
	virtual CAction* clone() const { return new CActURLParameters(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream
	virtual int takeAction(CContext& c, sockstream& fs);
};

// CActFormParameters: FormParameters action - corresponding to FormParameters
// statement (see manual)
class CActFormParameters : public CAction
{
protected:
	bool fromstart;		// if true, print url parameters using template start parameters

public:
	// constructor
	CActFormParameters(bool fs = false) : fromstart(fs) {}
	
	// destructor
	virtual ~CActFormParameters() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_FORMPARAMETERS; }

	// clone this object
	virtual CAction* clone() const { return new CActFormParameters(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream
	virtual int takeAction(CContext& c, sockstream& fs);
};

class CPrintModifiers : public set<int>
{
public:
	CPrintModifiers();
	bool validModifier(int m) const { return find(m) != end(); }
};

// CActPrint: Print action - corresponding to Print statement (see manual)
class CActPrint : public CAction
{
	friend class CParser;

private:
	static CPrintModifiers s_coModifiers;

protected:
	string attr;		// attribute to print
	string type;		// attribute type (for special type attributes)
	bool strictType;	// if true print only if type member matches the current article type
	string format;		// if attribute is of date type, format to use for printing
	int modifier;		// print modifier
	int image;			// image number for printing image attributes
	CCParser cparser;	// article content parser

	// BlobField: return 0 if field of table is blob type
	// Parameters:
	//		const char* table - table
	//		const char* field - table field
	int BlobField(const char* table, const char* field);

	// DateField: return 0 if field of table is date type
	// Parameters:
	//		const char* table - table
	//		const char* field - table field
	int DateField(const char* table, const char* field);

public:
	// constructor
	// Parameters:
	//		const string& a - attribute to print
	//		int m - print modifier
	//		const string& t = "" - special type (may be empty)
	//		string f = "" - format (for date type attributes)
	CActPrint(const string& a, int m, const string& t = string(""), bool st = false,
	          const string& f = string(""), int i = 1) throw(InvalidModifier)
		: attr(a), type(t), strictType(st), format(f), modifier(m), image(i)
	{
		if (!s_coModifiers.validModifier(m))
			throw InvalidModifier();
	}

	// destructor
	virtual ~CActPrint() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_PRINT; }

	// clone this object
	virtual CAction* clone() const { return new CActPrint(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream
	virtual int takeAction(CContext& c, sockstream& fs);

	// validModifier: return true if modifier is valid; false otherwise
	static bool validModifier(int m) { return s_coModifiers.validModifier(m); }
};

class CIfModifiers : public set<int>
{
public:
	CIfModifiers();
	bool validModifier(int m) const { return find(m) != end(); }
};

// CActIf: If action - corresponding to If statement (see manual)
class CActIf : public CAction
{
	friend class CParser;

private:
	static CIfModifiers s_coModifiers;

protected:
	CParameter param;		// parameter used to decide which path to choose
	IntSet rc_hash;			// stores values of list parameters (used with list modifier)
	int modifier;			// if modifier
	CActionList block;		// first list of actions (condition is verified)
	CActionList sec_block;	// second list of actions (condition is not verified)
	bool m_bNegated;
	bool m_bStrictType;

	// AccessAllowed: return true if access to hidden content is allowed
	// Parameters:
	//		CContext& c - current context
	//		sockstream& fs - output stream
	bool AccessAllowed(CContext& c, sockstream& fs);

public:
	// constructor
	CActIf(int m, const CParameter& p, bool p_bNegated = false, bool p_bStrictType = false)
		throw(InvalidModifier) : param(p), modifier(m), m_bNegated(p_bNegated),
			m_bStrictType(p_bStrictType)
	{
		if (!s_coModifiers.validModifier(m))
			throw InvalidModifier();
	}

	// copy-constructor
	CActIf(const CActIf& s) : param("") { *this = s; }
	
	// destructor
	virtual ~CActIf() {}

	// assign operator
	const CActIf& operator =(const CActIf&);
	
	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_IF; }

	// clone this object
	virtual CAction* clone() const { return new CActIf(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream
	virtual int takeAction(CContext& c, sockstream& fs);

	// validModifier: return true if modifier is valid; false otherwise
	static bool validModifier(int m) { return s_coModifiers.validModifier(m); }
};

// CActDate: Date action - corresponding to Date statement (see manual)
class CActDate : public CAction
{
protected:
	string attr;		// date attribute: special (year, month etc.) or date format

public:
	// constructor
	// Parameters:
	//		const string& d - date attribute
	CActDate(const string& d) : attr(d) {}

	// destructor
	virtual ~CActDate() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_DATE; }

	// clone this object
	virtual CAction* clone() const { return new CActDate(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream
	virtual int takeAction(CContext& c, sockstream& fs);
};

// CActText: Text action - corresponding to html text from template (see manual)
class CActText : public CAction
{
protected:
	const char* text;		// text to print
	ulint text_len;		// text length

public:
	// constructor
	CActText(const char* t, ulint tl)
	{
		text = t;
		text_len = tl;
	}

	// destructor
	virtual ~CActText() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_TEXT; }

	// clone this object
	virtual CAction* clone() const { return new CActText(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream
	virtual int takeAction(CContext& c, sockstream& fs)
	{
		fs.write(text, text_len);
		return RES_OK;
	}
};

// CActLocal: Local action - corresponding to Local statement (see manual)
class CActLocal : public CAction
{
	friend class CParser;

protected:
	CActionList block;	// list of actions to execute

public:
	// destructor
	virtual ~CActLocal() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_LOCAL; }

	// clone this object
	virtual CAction* clone() const { return new CActLocal(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream
	virtual int takeAction(CContext& c, sockstream& fs)
	{
		CContext lc(c);
		return runActions(block, lc, fs);
	}
};

// CActSubscription: Subscription action - corresponding to Subscription
// statement (see manual)
class CActSubscription : public CAction
{
	friend class CParser;

protected:
	bool by_publication;	// if true, subscribe on the whole publication
	id_type m_nTemplateId;		// identifier of the template to load on submit
	string button_name;		// submit button name
	string total;			// total field name
	string evaluate;		// evaluate button name
	CActionList block;	// list of actions between Subscription - EndSubscription

public:
	// constructor
	CActSubscription(bool bp, id_type p_nTemplateId, string bn, string t, string ev)
		: by_publication(bp), m_nTemplateId(p_nTemplateId), button_name(bn), total(t), evaluate(ev)
	{}

	// destructor
	virtual ~CActSubscription() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_SUBSCRIPTION; }

	// clone this object
	virtual CAction* clone() const { return new CActSubscription(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream	
	virtual int takeAction(CContext& c, sockstream& fs);
};

class CEditModifiers : public set<int>
{
public:
	CEditModifiers();
	bool validModifier(int m) const { return find(m) != end(); }
};

// CActEdit: Edit action - corresponding to Edit statement (see manual)
class CActEdit : public CAction
{
	friend class CParser;

private:
	static CEditModifiers s_coModifiers;

protected:
	int modifier;				// edit modifier
	string field;				// field to edit
	int size;					// field size

public:
	// constructor
	CActEdit(int m, const string& f, int s) throw(InvalidModifier)
		: modifier(m), field(f)
	{
		if (!s_coModifiers.validModifier(m))
			throw InvalidModifier();
		size = (s == 0 ? 10 : s);
	}

	// destructor
	virtual ~CActEdit() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_EDIT; }

	// clone this object
	virtual CAction* clone() const { return new CActEdit(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream	
	virtual int takeAction(CContext& c, sockstream& fs);

	// validModifier: return true if modifier is valid; false otherwise
	static bool validModifier(int m) { return s_coModifiers.validModifier(m); }
};

class CSelectModifiers : public set<int>
{
public:
	CSelectModifiers();
	bool validModifier(int m) const { return find(m) != end(); }
};

// CActSelect: Select action - corresponding to Select statement (see manual)
class CActSelect : public CAction
{
	friend class CParser;

private:
	static CSelectModifiers s_coModifiers;

protected:
	int modifier;				// select modifier
	string field;				// field name (used for selection)
	string male_name;			// male noun in current language
	string female_name;			// female noun in current language
	bool checked;				// true if field is checked

public:
	// constructor
	CActSelect(int m, const string& f, string mn = "", string fn = "", bool ck = false)
		throw(InvalidModifier)
		: modifier(m), field(f), male_name(mn), female_name(fn), checked(ck)
	{
		if (!s_coModifiers.validModifier(m))
			throw InvalidModifier();
	}

	// destructor
	virtual ~CActSelect() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_SELECT; }

	// clone this object
	virtual CAction* clone() const { return new CActSelect(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream	
	virtual int takeAction(CContext& c, sockstream& fs);

	// validModifier: return true if modifier is valid; false otherwise
	static bool validModifier(int m) { return s_coModifiers.validModifier(m); }
};

// CActUser: User action - corresponding to User statement (see manual)
class CActUser : public CAction
{
	friend class CParser;

protected:
	CActionList block;			// list of action between User - EndUser
	bool add;					// if true, perform user add action
	id_type m_nTemplateId;		// identifier of the template to load on submit
	string button_name;			// submit button name

public:
	// constructor
	CActUser(bool a, id_type p_nTemplateId, string &bn)
		: add(a), m_nTemplateId(p_nTemplateId), button_name(bn) {}

	// destructor
	virtual ~CActUser() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_USER; }

	// clone this object
	virtual CAction* clone() const { return new CActUser(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream	
	virtual int takeAction(CContext& c, sockstream& fs);
};

// CActLogin: Login action - corresponding to Login statement (see manual)
class CActLogin : public CAction
{
	friend class CParser;

protected:
	CActionList block;	// actions between Login - EndLogin statements
	id_type m_nTemplateId;		// identifier of the template to load on submit
	string button_name;		// submit button name

public:
	// constructor
	CActLogin(id_type p_nTemplateId, const string &bn)
		: m_nTemplateId(p_nTemplateId), button_name(bn) {}

	// destructor
	virtual ~CActLogin() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_LOGIN; }

	// clone this object
	virtual CAction* clone() const { return new CActLogin(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream	
	virtual int takeAction(CContext& c, sockstream& fs);
};

// CActSearch: Search action - corresponding to Search statement (see manual)
class CActSearch : public CAction
{
	friend class CParser;

protected:
	CActionList block;			// actions between Search - EndSearch statements
	id_type m_nTemplateId;		// identifier of the template to load on submit
	string button_name;			// submit button name

public:
	// constructor
	CActSearch(id_type p_nTemplateId, const string& bn)
		: m_nTemplateId(p_nTemplateId), button_name(bn) {}

	// destructor
	virtual ~CActSearch() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_SEARCH; }

	// clone this object
	virtual CAction* clone() const { return new CActSearch(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream	
	virtual int takeAction(CContext& c, sockstream& fs);
};

// CActWith: With action - corresponding to With statement (see manual)
class CActWith : public CAction
{
	friend class CParser;

protected:
	CActionList block;		// actions between With - EndWith statements
	string art_type;		// article type to use
	string field;			// field (article content) to use
	CCParser cparser;		// article content parser

public:
	// constructor
	CActWith(const string& art_t, const string& fld) : art_type(art_t), field(fld) {}

	// destructor
	virtual ~CActWith() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_WITH; }

	// clone this object
	virtual CAction* clone() const { return new CActWith(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream	
	virtual int takeAction(CContext& c, sockstream& fs);
};

// CActURIPath: URIPath action - corresponding to URIPath
// statement (see manual)
class CActURIPath : public CAction
{
protected:
	id_type m_nTemplate;
	TPubLevel m_nPubLevel;
	bool m_bArticleAttachment;

public:
	// constructor
	CActURIPath(id_type p_nTemplate = -1, TPubLevel p_nPubLevel = CMS_PL_ARTICLE,
				bool p_bArticleAttachment = false)
	: m_nTemplate(p_nTemplate), m_nPubLevel(p_nPubLevel),
	m_bArticleAttachment(p_bArticleAttachment) {}

	// destructor
	virtual ~CActURIPath() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_URIPATH; }

	// clone this object
	virtual CAction* clone() const { return new CActURIPath(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream
	virtual int takeAction(CContext& c, sockstream& fs);
};

// CActURI: URI action - corresponding to URI statement (see manual)
class CActURI : public CAction
{
protected:
	CActURIPath m_coURIPath;
	CActURLParameters m_coURLParameters;
	id_type m_nImageNr;		// if not -1, print URI of image nr.

public:
	// constructor
	CActURI(bool fs = false, bool as = false, id_type i = -1, CListLevel r_fl = CLV_ROOT,
			id_type tpl = -1, TPubLevel lvl = CMS_PL_ARTICLE, bool p_bArticleAttachment = false)
	: m_coURIPath(tpl, lvl, p_bArticleAttachment),
	m_coURLParameters(fs, as, i, r_fl, tpl, lvl, p_bArticleAttachment), m_nImageNr(i) {}

	// destructor
	virtual ~CActURI() {}

	// action: return action identifier
	virtual TAction action() const { return CMS_ACT_URI; }

	// clone this object
	virtual CAction* clone() const { return new CActURI(*this); }

	// takeAction: performs the action
	// Parametes:
	//		CContext& c - current context
	//		sockstream& fs - output stream
	virtual int takeAction(CContext& c, sockstream& fs);
};

#endif
