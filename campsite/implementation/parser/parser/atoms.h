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

Defines the classes used to build the lexems structure (see tol_lex.h):
CAtom, CAttribute, CStatementContext, CStatement

******************************************************************************/

#ifndef _CMS_ATOMS
#define _CMS_ATOMS

#include <string.h>
#include <string>
#include <utility>
#include <stdexcept>
#include <new>
#include <typeinfo>

#include "cms_types.h"
#include "operators.h"
#include "mutex.h"

using std::bad_alloc;
using std::pair;

// atom; a string of non separator characters; separators are: space, ", <, >,
// all characters before ' '(space): tab, end of line, end of file etc.
class CAtom
{
public:
	// constructor
	CAtom(const string& p_rcoId) : m_coId(p_rcoId) {}

	// virtual destructor
	virtual ~CAtom() {}

	// atomType: return string containing atom type name
	virtual string atomType() const { return typeid(*this).name(); }

	// return pointer to new object equal to this
	virtual CAtom* clone() const { return new CAtom(*this); }

	// Identifier: return pointer to string: atom identifier
	const string& identifier() const { return m_coId; }

	// append character to identifier
	void push_back(char c) { m_coId.push_back(c); }

	// setId: set atom identifier
	void setId(const string& id) { m_coId = id; }

	// returns the n'th character
	char& operator [](size_t n) { return m_coId[n]; }

	// returns the n'th character
	char operator [](size_t n) const { return m_coId[n]; }

	// comparison operator
	bool operator ==(const CAtom& p_rcoOther) const { return m_coId == p_rcoOther.m_coId; }

	// comparison operator
	bool operator !=(const CAtom& p_rcoOther) const { return m_coId != p_rcoOther.m_coId; }

protected:
	string m_coId;		// atom identifier
};

// CAttribute: an attribute is an atom that name a statement feature; it may require a
// certain data type
class CAttribute : public CAtom
{
public:
	// constructor
	CAttribute(const string& p_rcoAttr, const string& p_rcoDBFld = string(""),
	           TAttrClass p_nCls = CMS_NORMAL_ATTR)
		: CAtom(p_rcoAttr), m_coDBField(p_rcoDBFld), m_nClass(p_nCls) {}

	// virtual destructor
	virtual ~CAttribute() {}

	// return pointer to new object equal to this
	virtual CAtom* clone() const { return new CAttribute(*this); }

	// attribute: returns the attribute name: DBField if defined, Identifier otherwise
	const string& attribute() const { return m_coDBField != "" ? m_coDBField : identifier(); }

	// dbField: returns the database field
	const string& dbField() const { return m_coDBField; }

	// dataType: returns the data type of attribute
	virtual TDataType dataType() const { return CMS_DT_NONE; }

	// typeName: returns the attribute's data type name
	virtual const string& typeName() const;

	// typeValues: returns string containing valid values of the attribute type
	virtual const string& typeValues() const;

	// typeNameValues: returns string containing data type name and values
	string typeNameValues() const { return typeName() + " (" + typeValues() + ")"; }

	// operators: returns string containing valid operators
	const string& operators() const;

	// class: returns the attribute class
	TAttrClass attrClass() const { return m_nClass; }

	// comparison operator
	bool operator ==(const CAttribute& p_rcoOther) const;

	// comparison operator
	bool operator !=(const CAttribute& p_rcoOther) const { return ! (*this == p_rcoOther); }

	// validOperator: returns true if operator exists, false otherwise
	virtual bool validOperator(const string& p_rcoOp) const { return false; }

	// validValue: returns true if value is valid, false otherwise
	virtual bool validValue(const string& p_rcoVal) const { return false; }

	// compOperation: returns a CompOperation class for given operator, second operand;
	// throws InvalidOperator if operator not found
	// throws InvalidValue if value is invalid
	virtual CompOperation* compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
		throw(InvalidOperator, InvalidValue) { throw InvalidOperator(); }

private:
	string m_coDBField;			// corresponding database field
	TAttrClass m_nClass;		// attribute class - see cms_types.h, TAttrClass
	static const string s_coEmptyString;
};

// typeName: returns the attribute's data type name
inline const string& CAttribute::typeName() const
{
	return s_coEmptyString;
}

// typeValues: returns string containing valid values of the attribute type
inline const string& CAttribute::typeValues() const
{
	return s_coEmptyString;
}

// operators: returns string containing valid operators
inline const string& CAttribute::operators() const
{
	return s_coEmptyString;
}

// comparison operator
inline bool CAttribute::operator ==(const CAttribute& p_rcoOther) const
{
	return CAtom::operator ==(p_rcoOther) && m_coDBField == p_rcoOther.m_coDBField
	       && m_nClass == p_rcoOther.m_nClass;
}

const int g_nFIND_ALL = 3;
const int g_nFIND_NORMAL = 1;
const int g_nFIND_TYPE = 2;

class InvalidAttr : public exception
{
public:
	InvalidAttr(int p_nMode = g_nFIND_ALL, const string& p_rcoType = string(""))
		: m_nMode(p_nMode), m_coType(p_rcoType) {}

	virtual ~InvalidAttr() throw() {}

	virtual const char* what () const throw () { return "invalid attribute"; }

	int mode() const { return m_nMode; }

	const string& type() const { return m_coType; }

private:
	int m_nMode;
	string m_coType;
};

class CAttributeMap;

// CStatementContext: define context of statement usage and valid attributes
class CStatementContext
{
public:
	// constructor
	CStatementContext(int p_nCtx) throw(bad_alloc);

	// copy-constructor
	CStatementContext(const CStatementContext& p_rcoOther) : m_pcoAttributes(NULL)
	{ *this = p_rcoOther; }

	// virtual destructor
	virtual ~CStatementContext();

	// returns a string containing the list of attributes in context
	string attributes() const;

	// context: return context identifier
	int id() const { return m_nContext; }

	// attr: return pointer to attribute identified by p_rcoAttrId; throws InvalidAttr if not found
	const CAttribute* attr(const string& p_rcoAttrId) const throw(InvalidAttr);

	// findAttr: return pointer to attribute identified by parameter; NULL if not found
	const CAttribute* findAttr(const string& p_rcoAttrId) const throw();

	// insertAttr: insert attribute into set
	bool insertAttr(CAttribute* p_pcoAttr) throw();

	// assignment operator
	const CStatementContext& operator =(const CStatementContext& p_rcoOther) throw(bad_alloc);

	// compare operator
	bool operator ==(const CStatementContext& p_rcoOther) const;

	// compare operator
	bool operator !=(const CStatementContext& p_rcoOther) const
	{ return ! (*this == p_rcoOther); }

	// print context attributes
	void print(int p_nStartIndent = 1, const string& p_rcoIndent = string("\t")) const;

private:
	int m_nContext;			// context - see cms_types.h, int
	CAttributeMap* m_pcoAttributes;	// attributes
};

class CStatementContextMap;

// CTypeAttributes: special attributes; dynamically defined by article types defined
// in the database
class CTypeAttributes
{
public:
	// constructor
	CTypeAttributes(const string& p_rcoName) throw(bad_alloc);

	// copy-constructor
	CTypeAttributes(const CTypeAttributes& p_rcoOther) : m_pcoCtxAttributes(NULL)
	{ *this = p_rcoOther; }

	// virtual destructor
	virtual ~CTypeAttributes();

	// name: return type name
	const string& name() const { return m_coName; }

	// insertCtx: insert new context; the context is duplicated
	bool insertCtx(const CStatementContext& p_rcoCtxs);

	// insertCtx: insert new context; the context object must be dynamically allocated
	// throws InvalidValue if value is NULL
	bool insertCtx(CStatementContext* p_pcoCtx) throw(InvalidValue);

	// findCtx: returns context; NULL if not found
	const CStatementContext* findCtx(int p_nCtx) const throw();

	// assignment operator
	const CTypeAttributes& operator =(const CTypeAttributes& p_rcoOther) throw(bad_alloc);

	// compare operator
	bool operator ==(const CTypeAttributes& p_rcoOther) const;

	// compare operator
	bool operator !=(const CTypeAttributes& p_rcoOther) const { return ! (*this == p_rcoOther); }

	// print type attributes
	void print(int p_nStartIndent = 1, const string& p_rcoIndent = string("\t")) const;

private:
	string m_coName;								// type
	CStatementContextMap* m_pcoCtxAttributes;		// map of CStatementContext
};

class InvalidType : public exception
{
public:
	virtual ~InvalidType() throw() {}

	virtual const char* what () const throw() { return "invalid type"; }
};

typedef pair<const CAttribute*, const CTypeAttributes*> CPairAttrType;

class CTypeAttributesMap;

// CStatement: language statement
class CStatement : public CAtom
{
public:
	// constructor
	CStatement(int p_nId, const string& p_rcoName) throw(bad_alloc);

	// copy-constructor
	CStatement(const CStatement& p_rcoOther)
		: CAtom(p_rcoOther.identifier()), m_pcoContexts(NULL), m_pcoTypes(NULL)
	{ *this = p_rcoOther; }

	// virtual destructor
	virtual ~CStatement();

	// return pointer to new object equal to this
	virtual CAtom* clone() const { return new CStatement(*this); }

	// assignment operator
	const CStatement& operator =(const CStatement& p_rcoOther) throw(bad_alloc, ExMutex);

	// compare operator
	bool operator ==(const CStatement& p_rcoOther) const;

	// compare operator
	bool operator !=(const CStatement& p_rcoOther) const { return ! (*this == p_rcoOther); }

	// id: returns statement identifier
	int id() const { return m_nId; }	

	// insertCtx: insert context into contexts map
	bool insertCtx(const CStatementContext& p_rcoCtx);

	// insertCtx: insert context into contexts map; the pointer must be dynamically allocated
	// throws InvalidValue if NULL
	bool insertCtx(CStatementContext* p_pcoCtx) throw(InvalidValue, bad_alloc);

	// insertType: insert type into types map
	bool insertType(const CTypeAttributes& p_rcoType) throw(bad_alloc, ExMutex);

	// insertType: insert type into types map; the pointer must be dynamically allocated
	// throws InvalidValue if NULL
	bool insertType(CTypeAttributes* p_pcoType) throw(InvalidValue, bad_alloc, ExMutex);

	// findType: return pointer to statement context corresponding to the given identifier
	// returns NULL if not found
	// Parameters:
	//		int p_Context - context identifier
	const CStatementContext* findContext(int p_Context) const throw();

	// findType: return pointer to type (special) attributes valid for a given type
	// throws InvalidType if not found
	// Parameters:
	//		const string& p_rcoType - type name
	const CTypeAttributes* findType(const string& p_rcoType) const throw(ExMutex);

	// findAttr: return pointer to attribute valid in given context and identified by given name
	// throws InvalidAttr if not found
	// Parameters:
	//		const string& p_rcoAttr - attribute name
	//		int p_Context - context
	const CAttribute* findAttr(const string& p_rcoAttr, int p_Context) const throw(InvalidAttr);

	// findTypeAttr: return pointer to pair of attribute and type containig found attribute
	// throws InvalidAttr if not found, InvalidType if type specified is invalid
	// Parameters:
	//		const string& p_rcoAttr - attribute name
	//		const string& p_rcoType - type name
	//		int p_Context - context
	CPairAttrType* findTypeAttr(const string& p_rcoAttr, const string& p_rcoType, int p_Context)
		const throw(InvalidAttr, InvalidType, ExMutex);

	// findAnyAttr: return pointer to pair attribute-type valid in given context and identified by
	// given name; if type is not NULL attribute is of that type
	// throws InvalidAttr if not found
	// Parameters:
	//		const string& p_rcoAttr - attribute name
	//		int p_Context - context
	CPairAttrType* findAnyAttr(const string& p_rcoAttr, int p_Context) const
		throw(InvalidAttr, ExMutex);

	// contextAttrs: return string containing valid simple (not with type) attributes for a
	// given context
	// Parameters:
	//		int p_Context - context
	string contextAttrs(int p_Context) const throw();

	// typeAttrs: return string containing attributes for a given type/context; if attributes type
	//		is undefined perform the operation for all types
	// Parameters:
	//		const string& p_rcoType - attributes type
	//		int p_Context - context
	string typeAttrs(const string& p_rcoType, int p_Context) const throw(ExMutex);

	// allAttrs: return string containing valid attributes for a given context
	// Parameters:
	//		int p_Context - context
	string allAttrs(int p_Context) const throw();

	// types: return string containig all the types
	string types() const throw(ExMutex);

	// updateTypes: set the types hash
	// Parameters:
	//		const CTypeAttributesHash* p_pcoTypeAttributes - pointer to types hash
	bool updateTypes(CTypeAttributesMap* p_pcoTypeAttributes) throw(ExMutex);

	// print statement
	void print(int p_nStartIndent = 1, const string& p_rcoIndent = string("\t")) const;

private:
	int m_nId;								// statement identifier
	CStatementContextMap* m_pcoContexts;	// map of CStatementContext
	CTypeAttributesMap* m_pcoTypes;			// map of CTypeAttributes
	mutable CMutex m_coTypesOp;
};

#endif
