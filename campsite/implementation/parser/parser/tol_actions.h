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
 
Define TOLParameter, TOLAction, TOLActLanguage, TOLActInclude, TOLActPublication,
TOLActIssue, TOLActSection, TOLActArticle, TOLActList, TOLActURLParameters,
TOLActFormParameters, TOLActPrint, TOLActIf, TOLActDate, TOLActText, TOLActLocal,
TOLActSubscription, TOLActEdit, TOLActSelect, TOLActUser, TOLActLogin,
TOLActSearch, TOLActWith classes. All these classes except TOLParameter and
TOLAction correspond to a certain "instruction"; they inherit TOLAction.
There is one important virtual method redefined by every action class: TakeAction.
This receives a context parameter and a stream to write output to. The context
can be modified by this method.
 
******************************************************************************/

#ifndef _TOL_ACTIONS_H
#define _TOL_ACTIONS_H

#include <mysql/mysql.h>
#include <iostream.h>
#include <fstream.h>
#include <string>

#include "tol_types.h"
#include "tol_context.h"
#include "tol_error.h"
#include "tol_atoms.h"
#include "tol_cgiparams.h"
#include "tol_cparser.h"

// TOLParameter: defines a parameter class: a parameter is an attribute characterised by
// a value on which a certain operator (is, not, greater, smaller) is applied
class TOLParameter
{
private:
	string m_coAttr;		// attribute
	string m_coValue;		// value
	TOperator m_Operator;	// operator to apply on value

public:
	// default constructor
	// Parameters:
	//		cpChar p_pchAttr = NULL - attribute
	//		cpChar p_pchValue = NULL - value
	//		TOperator p_Operator = TOL_NO_OP - operator to apply on value
	TOLParameter(cpChar p_pchAttr = NULL, cpChar p_pchValue = NULL,
	             TOperator p_Operator = TOL_NO_OP);
	
	// copy-constructor
	TOLParameter(const TOLParameter& s)
	{
		*this = s;
	}

	// Attribute: return attribute
	cpChar Attribute()
	{
		return m_coAttr.c_str();
	}
	
	// Value: return value
	cpChar Value()
	{
		return m_coValue.c_str();
	}
	
	// Operator: return operator
	TOperator Operator()
	{
		return m_Operator;
	}
	
	// assign operator
	inline const TOLParameter& operator =(const TOLParameter& p_rcoSrc);
};

// TOLAction: generic action; abstract class
// Important methods:
//		TakeAction: performs the action
class TOLAction
{
protected:
	static TK_MYSQL m_coSql;		// key variable: pointer to MySQL connection
	static TK_char m_coBuf;			// key variable: buffer for temporary use
	static Int2String m_coOpMap;	// operators map
	static TK_bool m_coDebug;		// key variable: print debug info(true/false)
	static pthread_once_t m_InitControl;	// control initialisation
	
	TAction m_Action;				// action identifier

	// Init: initialise operators map
	static void Init();
	
	// DEBUGAct: print debug information
	inline void DEBUGAct(cpChar, cpChar, fstream&);
	
	// SQLEscapeString: escape given string for sql query; returns escaped string
	// The returned string must be deallocated by the user using delete operator.
	// Parameters:
	//		cpChar src - source string
	//		UInt p_nLength - string length
	pChar SQLEscapeString(cpChar src, UInt p_nLength);

public:
	// constructor
	// Parameters:
	//		TAction p_Action = TOL_ACT_NONE - action identifier
	TOLAction(TAction p_Action = TOL_ACT_NONE);
	
	// copy-constructor
	TOLAction(const TOLAction& p_rcoAction)
	{
		*this = p_rcoAction;
	}
	
	// destructor
	virtual ~TOLAction()
	{}

	// InitTempMembers: init thread specific variables
	static void InitTempMembers();
	
	// assign operator
	const TOLAction& operator =(const TOLAction&);
	
	// SetDebug: set debug: true/false
	static void SetDebug(bool p_bDebug = false)
	{
		m_coDebug = p_bDebug;
	}
	
	// DoDebug: return debug value (true/false)
	static bool DoDebug()
	{
		return *(&m_coDebug);
	}
	
	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLAction";
	}
	
	// WhatAction: return action identifier
	TAction WhatAction() const
	{
		return m_Action;
	}
	
	// TakeAction: performs the action; virtual pure method
	// Parametes:
	//		TOLContext& c - current context (may be modified by action)
	//		fstream& fs - write the result to output stream; some actions may not write
	//			anything to ouput stream
	virtual int TakeAction(TOLContext& c, fstream& fs) = 0;
	
	// DateFormat: format the given date according to the given format in given language
	// Returns string containing formated date
	// Parameters:
	//		cpChar p_pchDate - date to format
	//		cpChar p_pchFormat - format of the date
	//		long int p_nLanguageId - language to use
	string DateFormat(cpChar p_pchDate, cpChar p_pchFormat, long int p_nLanguageId);

	friend class TOLParser;
};

// TOLActLanguage: language action - corresponding to Language statement (see manual)
class TOLActLanguage : public TOLAction
{
protected:
	pChar m_pchLang;		// language name

public:
	// constructor
	// Parameters:
	//		cpChar p_pchLang - language name
	TOLActLanguage(cpChar p_pchLang) : TOLAction(TOL_ACT_LANGUAGE)
	{
		if (p_pchLang != NULL)
			m_pchLang = strdup(p_pchLang);
		else
			m_pchLang = NULL;
	}
	
	// copy-constructor
	TOLActLanguage(const TOLActLanguage& s) : TOLAction(TOL_ACT_LANGUAGE)
	{
		m_pchLang = NULL;
		*this = s;
	}

	// destructor
	virtual ~TOLActLanguage()
	{
		if (m_pchLang != NULL)
			free(m_pchLang);
	}
	
	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActLanguage";
	}
	
	// assign operator
	const TOLActLanguage& operator =(const TOLActLanguage&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context (modified by action)
	//		fstream& fs - output stream (not used)
	virtual int TakeAction(TOLContext& c, fstream& fs);
};

// TOLActInclude: include action - corresponding to Include statement (see manual)
class TOLActInclude : public TOLAction
{
protected:
	pChar tpl_path;				// template to include
	TOLParserHash* parser_hash;	// pointer to parsers hash

public:
	// constructor
	// Parameters:
	//		cpChar p - path to included template
	//		TOLParserHash* ph - pointer to parsers hash
	TOLActInclude(cpChar p, TOLParserHash* ph) : TOLAction(TOL_ACT_INCLUDE)
	{
		if (p != NULL)
			tpl_path = strdup(p);
		else
			tpl_path = NULL;
		parser_hash = ph;
	}
	
	// copy-constructor
	TOLActInclude(const TOLActInclude& s)
			: TOLAction(TOL_ACT_INCLUDE)
	{
		tpl_path = NULL;
		*this = s;
	}
	
	// destructor
	virtual ~TOLActInclude()
	{
		if (tpl_path != NULL)
			free(tpl_path);
	}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActInclude";
	}
	
	// assign operator
	const TOLActInclude& operator =(const TOLActInclude&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context (may be modified by action)
	//		fstream& fs - output stream
	virtual int TakeAction(TOLContext& c, fstream& fs);
};

// TOLActPublication: Publication action - corresponding to Publication statement (see manual)
class TOLActPublication : public TOLAction
{
protected:
	TOLParameter param;		// parameter

public:
	// constructor
	// Parameters:
	//		const TOLParameter& p - parameter
	TOLActPublication(const TOLParameter& p) : TOLAction(TOL_ACT_PUBLICATION), param(p)
	{}
	
	// copy-constructor
	TOLActPublication(const TOLActPublication& s) : TOLAction(TOL_ACT_PUBLICATION)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActPublication()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActPublication";
	}
	
	// assign operator
	const TOLActPublication& operator =(const TOLActPublication&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context (modified by action)
	//		fstream& fs - output stream (not used)
	virtual int TakeAction(TOLContext& c, fstream& fs);
};

// TOLActIssue: Issue action - corresponding to Issue statement (see manual)
class TOLActIssue : public TOLAction
{
protected:
	TOLParameter param;		// parameter

public:
	// constructor
	// Parameters:
	//		const TOLParameter& p - parameter
	TOLActIssue(const TOLParameter& p) : TOLAction(TOL_ACT_ISSUE), param(p)
	{}
	
	// copy-constructor
	TOLActIssue(const TOLActIssue& s) : TOLAction(TOL_ACT_ISSUE)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActIssue()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActIssue";
	}
	
	// assign operator
	const TOLActIssue& operator =(const TOLActIssue&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context (modified by action)
	//		fstream& fs - output stream (not used)
	virtual int TakeAction(TOLContext& c, fstream& fs);
};

// TOLActSection: Section action - corresponding to Section statement (see manual)
class TOLActSection : public TOLAction
{
protected:
	TOLParameter param;		// parameter

public:
	// constructor
	// Parameters:
	//		const TOLParameter& p - parameter
	TOLActSection(const TOLParameter& p) : TOLAction(TOL_ACT_SECTION), param(p)
	{}
	
	// copy-constructor
	TOLActSection(const TOLActSection& s) : TOLAction(TOL_ACT_SECTION)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActSection()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActSection";
	}
	
	// assign operator
	const TOLActSection& operator =(const TOLActSection&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context (modified by action)
	//		fstream& fs - output stream (not used)
	virtual int TakeAction(TOLContext& c, fstream& fs);
};

// TOLActArticle: Article action - corresponding to Article statement (see manual)
class TOLActArticle : public TOLAction
{
protected:
	TOLParameter param;		// parameter

public:
	// constructor
	// Parameters:
	//		const TOLParameter& p - parameter
	TOLActArticle(const TOLParameter& p) : TOLAction(TOL_ACT_ARTICLE), param(p)
	{}
	
	// copy-constructor
	TOLActArticle(const TOLActArticle& s) : TOLAction(TOL_ACT_ARTICLE)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActArticle()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActArticle";
	}
	
	// assign operator
	const TOLActArticle& operator =(const TOLActArticle&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context (modified by action)
	//		fstream& fs - output stream (not used)
	virtual int TakeAction(TOLContext& c, fstream& fs);
};

// TOLActList: List action - corresponding to List statement (see manual)
class TOLActList : public TOLAction
{
protected:
	long int length;				// list length
	long int columns;				// columns (used to build tables)
	TOLParameterList mod_param;		// modifier parameters
	TOLParameterList ord_param;		// order parameters
	TOLPActionList first_block;		// first list of actions (list is not empty)
	TOLPActionList second_block;	// second list of action (empty list)
	TListModifier modifier;			// modifier

	// WriteModParam: add conditions - corresponding to modifier parameters -
	// to where clause of the query. Used for Issue and Section modifiers.
	// Parameters:
	//		string& s - string to add conditions to (where clause)
	//		TOLContext& c - current context
	//		string& table - string containig tables used in query
	int WriteModParam(string& s, TOLContext& c, string& table);
	
	// WriteArtParam: add conditions - corresponding to modifier parameters -
	// to where clause of the query. Used for Article modifier.
	// Parameters:
	//		string& s - string to add conditions to (where clause)
	//		TOLContext& c - current context
	//		string& table - string containig tables used in query
	int WriteArtParam(string& s, TOLContext& c, string& table);
	
	// WriteSrcParam: add conditions - corresponding to modifier parameters -
	// to where clause of the query. Used for SearchResult modifier.
	// Parameters:
	//		string& s - string to add conditions to (where clause)
	//		TOLContext& c - current context
	//		string& table - string containig tables used in query
	int WriteSrcParam(string& s, TOLContext& c, string& table);
	
	// WriteOrdParam: add conditions - corresponding to order parameters -
	// to order clause of the query.
	// Parameters:
	//		string& s - string to add conditions to (order clause)
	int WriteOrdParam(string& s);
	
	// WriteLimit: add conditions to limit clause of the query.
	// Parameters:
	//		string& s - string to add conditions to (limit clause)
	//		TOLContext& c - current context
	int WriteLimit(string& s, TOLContext& c);
	
	// RunBlock: run actions in a list of actions
	// Parameters:
	//		TOLPActionList& al - list of actions
	//		TOLContext& c - current context
	//		fstream& fs - output stream
	int RunBlock(TOLPActionList& al, TOLContext& c, fstream& fs);
	
	// SetContext: set the context current Issue, Section or Article depending of list
	// modifier
	// Parameters:
	//		TOLContext& c - current context
	// 		long int value - value to be set
	void SetContext(TOLContext& c, long int value);
	
	// IMod2Level: convert from list modifier to level identifier; return level identifier
	// Parameters:
	//		TListModifier m - list modifier
	CLevel IMod2Level(TListModifier);

public:
	// constructor
	// Parameters:
	//		TListModifier m - list modifier
	//		long int l - list length
	//		long int c - list columns
	//		TOLParameterList& mp - modifier parameter list
	//		TOLParameterList& op - order parameter list
	TOLActList(TListModifier m, long int l, long int c, TOLParameterList& mp,
	           TOLParameterList& op);
	
	// copy-constructor
	TOLActList(const TOLActList& s) : TOLAction(TOL_ACT_LIST)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActList();

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActList";
	}
	
	// assign operator
	const TOLActList& operator =(const TOLActList&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context (not modified by action)
	//		fstream& fs - output stream
	virtual int TakeAction(TOLContext& c, fstream& fs);

	friend class TOLParser;
};

// TOLActURLParameters: URLParameters action - corresponding to URLParameters
// statement (see manual)
class TOLActURLParameters : public TOLAction
{
protected:
	long int image_nr;		// if not -1, print url parameters for image nr.
	bool fromstart;			// if true, print url parameters using template start parameters
	bool allsubtitles;		// if true, print all subtitles parameter
	CLevel reset_from_list;	// level from which to reset list start

	// PrintSubtitlesURL: print url parameters for subtitle list/printing
	// Parameters:
	//		TOLContext& c - current context
	//		fstream& fs - output stream
	//		bool& first - used to signal if first parameter in list (for printing separators)
	void PrintSubtitlesURL(TOLContext& c, fstream& fs, bool& first);

public:
	// constructor
	TOLActURLParameters(bool fs = false, bool as = false, long int i = -1,
	                    CLevel r_fl = CLV_ROOT)
			: TOLAction(TOL_ACT_URLPARAMETERS)
	{
		fromstart = fs;
		allsubtitles = as;
		image_nr = i;
		reset_from_list = r_fl;
	}
	
	// copy-constructor
	TOLActURLParameters(const TOLActURLParameters& s) : TOLAction(TOL_ACT_URLPARAMETERS)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActURLParameters()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActURLParameters";
	}
	
	// assign operator
	const TOLActURLParameters& operator =(const TOLActURLParameters&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context
	//		fstream& fs - output stream
	virtual int TakeAction(TOLContext& c, fstream& fs);
};

// TOLActFormParameters: FormParameters action - corresponding to FormParameters
// statement (see manual)
class TOLActFormParameters : public TOLAction
{
protected:
	bool fromstart;		// if true, print url parameters using template start parameters

public:
	// constructor
	TOLActFormParameters(bool fs = false) : TOLAction(TOL_ACT_FORMPARAMETERS)
	{
		fromstart = fs;
	}
	
	// copy-constructor
	TOLActFormParameters(const TOLActFormParameters& s) : TOLAction(TOL_ACT_FORMPARAMETERS)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActFormParameters()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActFormParameters";
	}
	
	// assign operator
	const TOLActFormParameters& operator =(const TOLActFormParameters&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context
	//		fstream& fs - output stream
	virtual int TakeAction(TOLContext& c, fstream& fs);
};

// TOLActPrint: Print action - corresponding to Print statement (see manual)
class TOLActPrint : public TOLAction
{
protected:
	char attr[ID_MAXLEN + 1];	// attribute to print
	char type[ID_MAXLEN + 1];	// attribute type (for special type attributes)
	string format;				// if attribute is of date type, format to use for printing
	TPrintModifier modifier;	// print modifier
	TOLCParser cparser;			// article content parser

	// BlobField: return 0 if field of table is blob type
	// Parameters:
	//		cpChar table - table
	//		cpChar field - table field
	int BlobField(cpChar table, cpChar field);
	
	// DateField: return 0 if field of table is date type
	// Parameters:
	//		cpChar table - table
	//		cpChar field - table field
	int DateField(cpChar table, cpChar field);

public:
	// constructor
	// Parameters:
	//		cpChar a - attribute to print
	//		TPrintModifier m - print modifier
	//		cpChar t = NULL - special type (may be NULL)
	//		string f = "" - format (for date type attributes)
	TOLActPrint(cpChar a, TPrintModifier m, cpChar t = NULL, string f = "");
	
	// copy-constructor
	TOLActPrint(const TOLActPrint& s) : TOLAction(TOL_ACT_PRINT)
	{
		attr[0] = 0;
		*this = s;
	}
	
	// destructor
	virtual ~TOLActPrint()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActPrint";
	}
	
	// assign operator
	const TOLActPrint& operator =(const TOLActPrint&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context
	//		fstream& fs - output stream
	virtual int TakeAction(TOLContext& c, fstream& fs);
};

// TOLActIf: If action - corresponding to If statement (see manual)
class TOLActIf : public TOLAction
{
protected:
	TOLParameter param;			// parameter used to decide which path to choose
	intHash rc_hash;			// stores values of list parameters (used with list modifier)
	TIfModifier modifier;		// if modifier
	TOLPActionList block;		// first list of actions (condition is verified)
	TOLPActionList sec_block;	// second list of actions (condition is not verified)

	// RunBlock: run actions in a list of actions
	// Parameters:
	//		TOLPActionList& al - list of actions
	//		TOLContext& c - current context
	//		fstream& fs - output stream
	int RunBlock(TOLPActionList& al, TOLContext& c, fstream& fs);
	
	// AccessAllowed: return true if access to hidden content is allowed
	// Parameters:
	//		TOLContext& c - current context
	//		fstream& fs - output stream
	bool AccessAllowed(TOLContext& c, fstream& fs);

public:
	// constructor
	TOLActIf(TIfModifier m, const TOLParameter& p)
			: TOLAction(TOL_ACT_IF), param(p), rc_hash(4, intHashFn, intEqual, intValue)
	{
		modifier = m;
	}
	
	// copy-constructor
	TOLActIf(const TOLActIf& s)
		: TOLAction(TOL_ACT_IF), rc_hash(4, intHashFn, intEqual, intValue)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActIf();

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActIf";
	}
	
	// assign operator
	const TOLActIf& operator =(const TOLActIf&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context
	//		fstream& fs - output stream
	virtual int TakeAction(TOLContext& c, fstream& fs);

	friend class TOLParser;
};

// TOLActDate: Date action - corresponding to Date statement (see manual)
class TOLActDate : public TOLAction
{
protected:
	char attr[ID_MAXLEN + 1];	// date attribute: special (year, month etc.) or date format

public:
	// constructor
	// Parameters:
	//		cpChar d - date attribute
	TOLActDate(cpChar d);
	
	// copy-constructor
	TOLActDate(const TOLActDate& s) : TOLAction(TOL_ACT_DATE)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActDate()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActDate";
	}
	
	// assign operator
	const TOLActDate& operator =(const TOLActDate&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context
	//		fstream& fs - output stream
	virtual int TakeAction(TOLContext& c, fstream& fs);
};

// TOLActText: Text action - corresponding to html text from template (see manual)
class TOLActText : public TOLAction
{
protected:
	cpChar text;		// text to print
	ULInt text_len;		// text length

public:
	// constructor
	TOLActText(cpChar t, ULInt tl) : TOLAction(TOL_ACT_TEXT)
	{
		text = t;
		text_len = tl;
	}
	
	// copy-constructor
	TOLActText(const TOLActText& s) : TOLAction(TOL_ACT_TEXT)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActText()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActText";
	}
	
	// assign operator
	const TOLActText& operator =(const TOLActText&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context
	//		fstream& fs - output stream
	virtual int TakeAction(TOLContext& c, fstream& fs);
};

// TOLActLocal: Local action - corresponding to Local statement (see manual)
class TOLActLocal : public TOLAction
{
protected:
	TOLPActionList block;	// list of actions to execute

public:
	// default constructor
	TOLActLocal() : TOLAction(TOL_ACT_LOCAL)
	{}
	
	// copy-constructor
	TOLActLocal(const TOLActLocal& s) : TOLAction(TOL_ACT_LOCAL)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActLocal()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActLocal";
	}
	
	// assign operator
	const TOLActLocal& operator =(const TOLActLocal&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context
	//		fstream& fs - output stream
	virtual int TakeAction(TOLContext& c, fstream& fs);

	friend class TOLParser;
};

// TOLActSubscription: Subscription action - corresponding to Subscription
// statement (see manual)
class TOLActSubscription : public TOLAction
{
protected:
	bool by_publication;	// if true, subscribe on the whole publication
	string tpl_file;		// template file to load on submit action
	string button_name;		// submit button name
	string total;			// total field name
	string evaluate;		// evaluate button name
	TOLPActionList block;	// list of actions between Subscription - EndSubscription

public:
	// constructor
	TOLActSubscription(bool bp, string tf, string bn, string t, string ev)
			: TOLAction(TOL_ACT_SUBSCRIPTION), by_publication(bp), tpl_file(tf),
			button_name(bn), total(t), evaluate(ev)
	{}
	
	// copy-constructor
	TOLActSubscription(const TOLActSubscription& s) : TOLAction(TOL_ACT_SUBSCRIPTION)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActSubscription()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActSubscription";
	}
	
	// assign operator
	const TOLActSubscription& operator =(const TOLActSubscription&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context
	//		fstream& fs - output stream	
	virtual int TakeAction(TOLContext& c, fstream& fs);

	friend class TOLParser;
};

// TOLActEdit: Edit action - corresponding to Edit statement (see manual)
class TOLActEdit : public TOLAction
{
protected:
	TEditModifier modifier;		// edit modifier
	string field;				// field to edit
	int size;					// field size

public:
	// constructor
	TOLActEdit(TEditModifier m, const string& f, int s) : TOLAction(TOL_ACT_EDIT), field(f)
	{
		modifier = m;
		size = (s == 0 ? 10 : s);
	}
	
	// copy-constructor
	TOLActEdit(const TOLActEdit& s) : TOLAction(TOL_ACT_EDIT)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActEdit()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActEdit";
	}
	
	// assign operator
	const TOLActEdit& operator =(const TOLActEdit&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context
	//		fstream& fs - output stream	
	virtual int TakeAction(TOLContext& c, fstream& fs);

	friend class TOLParser;
};

// TOLActSelect: Select action - corresponding to Select statement (see manual)
class TOLActSelect : public TOLAction
{
protected:
	TSelectModifier modifier;	// select modifier
	string field;				// field name (used for selection)
	string male_name;			// male noun in current language
	string female_name;			// female noun in current language
	bool checked;				// true if field is checked

public:
	// constructor
	TOLActSelect(TSelectModifier m, const string& f, string mn = "", string fn = "", bool ck = false)
			: TOLAction(TOL_ACT_SELECT), field(f), male_name(mn), female_name(fn)
	{
		modifier = m;
		checked = ck;
	}
	
	// copy-constructor
	TOLActSelect(const TOLActSelect& s) : TOLAction(TOL_ACT_SELECT)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActSelect()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActSelect";
	}
	
	// assign operator
	const TOLActSelect& operator =(const TOLActSelect&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context
	//		fstream& fs - output stream	
	virtual int TakeAction(TOLContext& c, fstream& fs);

	friend class TOLParser;
};

// TOLActUser: User action - corresponding to User statement (see manual)
class TOLActUser : public TOLAction
{
protected:
	TOLPActionList block;		// list of action between User - EndUser
	bool add;					// if true, perform user add action
	string tpl_file;			// template file to load on submit
	string button_name;			// submit button name

public:
	// constructor
	TOLActUser(bool a, string &tf, string &bn)
			: TOLAction(TOL_ACT_USER), add(a), tpl_file(tf), button_name(bn)
	{}
	
	// copy-constructor
	TOLActUser(const TOLActUser& s) : TOLAction(TOL_ACT_USER)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActUser()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActUser";
	}
	
	// assign operator
	const TOLActUser& operator =(const TOLActUser&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context
	//		fstream& fs - output stream	
	virtual int TakeAction(TOLContext& c, fstream& fs);

	friend class TOLParser;
};

// TOLActLogin: Login action - corresponding to Login statement (see manual)
class TOLActLogin : public TOLAction
{
protected:
	TOLPActionList block;	// actions between Login - EndLogin statements
	string tpl_file;		// template file to load on submit
	string button_name;		// submit button name

public:
	// constructor
	TOLActLogin(string &tf, string &bn)
		: TOLAction(TOL_ACT_LOGIN), tpl_file(tf), button_name(bn)
	{}
	
	// copy-constructor
	TOLActLogin(const TOLActLogin& s) : TOLAction(TOL_ACT_LOGIN)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActLogin()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActLogin";
	}
	
	// assign operator
	const TOLActLogin& operator =(const TOLActLogin&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context
	//		fstream& fs - output stream	
	virtual int TakeAction(TOLContext& c, fstream& fs);

	friend class TOLParser;
};

// TOLActSearch: Search action - corresponding to Search statement (see manual)
class TOLActSearch : public TOLAction
{
protected:
	TOLPActionList block;		// actions between Search - EndSearch statements
	string tpl_file;			// template file to load on submit
	string button_name;			// submit button name

public:
	// constructor
	TOLActSearch(string &tf, string &bn)
		: TOLAction(TOL_ACT_SEARCH), tpl_file(tf), button_name(bn)
	{}
	
	// copy-constructor
	TOLActSearch(const TOLActSearch& s) : TOLAction(TOL_ACT_LOGIN)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActSearch()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActSearch";
	}
	
	// assign operator
	const TOLActSearch& operator =(const TOLActSearch&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context
	//		fstream& fs - output stream	
	virtual int TakeAction(TOLContext& c, fstream& fs);

	friend class TOLParser;
};

// TOLActWith: With action - corresponding to With statement (see manual)
class TOLActWith : public TOLAction
{
protected:
	TOLPActionList block;	// actions between With - EndWith statements
	string art_type;		// article type to use
	string field;			// field (article content) to use
	TOLCParser cparser;		// article content parser

public:
	// constructor
	TOLActWith(const string& art_t, const string& fld)
			: TOLAction(TOL_ACT_WITH), art_type(art_t), field(fld)
	{}
	
	// copy-constructor
	TOLActWith(const TOLActWith& s) : TOLAction(TOL_ACT_WITH)
	{
		*this = s;
	}
	
	// destructor
	virtual ~TOLActWith()
	{}

	// ClassName: return class name (action name)
	virtual cpChar ClassName() const
	{
		return "TOLActWith";
	}
	
	// assign operator
	const TOLActWith& operator =(const TOLActWith&);
	
	// TakeAction: performs the action
	// Parametes:
	//		TOLContext& c - current context
	//		fstream& fs - output stream	
	virtual int TakeAction(TOLContext& c, fstream& fs);

	friend class TOLParser;
};

#endif
