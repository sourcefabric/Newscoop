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
TOLAtom, TOLAttribute, TOLStatementContext, TOLStatement
 
******************************************************************************/

#ifndef _TOL_ATOMS_H
#define _TOL_ATOMS_H

#include <hashtable.h>
#include <string>

#include "tol_types.h"

using namespace std;

// atom; a string of non separator characters; separators are: space, ", <, >,
// all characters before ' '(space): tab, end of line, end of file etc.
class TOLAtom
{
protected:
	char m_pchIdentifier[ID_MAXLEN + 1];		// atom identifier

public:
	// constructor
	TOLAtom(cpChar id = "")
	{
		strncpy(m_pchIdentifier, id, ID_MAXLEN);
		m_pchIdentifier[strlen(id) > ID_MAXLEN ? ID_MAXLEN : strlen(id)] = 0;
	}
	// copy-constructor
	TOLAtom(const TOLAtom& source)
	{
		*this = source;
	}
	// virtual destructor
	virtual ~TOLAtom()
	{}

	// ClassName: return pointer to a string: class name
	virtual cpChar ClassName() const
	{
		return "TOLAtom";
	}
	// Identifier: return pointer to string: atom identifier
	cpChar Identifier() const
	{
		return m_pchIdentifier;
	}
	// assign operator
	virtual const TOLAtom& operator =(const TOLAtom&);
	// compare operator
	virtual bool operator ==(const TOLAtom&) const;

	friend class TOLAttribute;
	friend class TOLStatement;
	friend class TOLLex;
};

// TOLAttribute: an attribute is an atom that name a statement feature; it may require a
// certain data type
class TOLAttribute : public TOLAtom
{
private:
	TDataType DType;				// data type required by attribute
	TOLAttrClass attr_class;		// attribute class - see tol_types.h, TOLAttrClass
	char DBField[ID_MAXLEN + 1];	// corresponding database field

public:
	// constructor
	TOLAttribute(cpChar at_name, TDataType dt = TOL_DT_NONE, cpChar = NULL,
	             TOLAttrClass = TOL_NORMAL_ATTR);
	// copy-constructor
	TOLAttribute(const TOLAttribute& source)
	{
		*this = source;
	}
	// virtual destructor
	virtual ~TOLAttribute()
	{}

	// Attribute: returns the attribute name: DBField if defined, Identifier otherwise
	cpChar Attribute() const
	{
		return DBField[0] != 0 ? DBField : m_pchIdentifier;
	}
	
	// DataType: returns the data type of attribute
	TDataType DataType() const
	{
		return DType;
	}
	
	// Class: returns the attribute class
	TOLAttrClass Class() const
	{
		return attr_class;
	}
	
	// ClassName: return pointer to a string: class name
	virtual cpChar ClassName() const
	{
		return "TOLAttribute";
	}
	// assign operator
	virtual const TOLAttribute& operator =(const TOLAttribute&);
	// compare operator
	virtual bool operator ==(const TOLAttribute&) const;

	friend class TOLStatement;
	friend class TOLLex;
	friend cpChar TOLAttributeValue(const TOLAttribute&);
};

inline cpChar TOLAttributeValue(const TOLAttribute& a)
{
	return a.m_pchIdentifier;
}

// TOLStatementContext: define context of statement usage and valid attributes
class TOLStatementContext
{
private:
	TContext context;				// context - see tol_types.h, TContext
	TOLAttributeHash attributes;	// attributes

public:
	// constructor
	TOLStatementContext(TContext c, const TOLAttributeHash& ah)
			: attributes(ah)
	{
		context = c;
	}
	// copy-constructor
	TOLStatementContext(const TOLStatementContext& source)
			: attributes(source.attributes)
	{
		*this = source;
	}
	// virtual destructor
	virtual ~TOLStatementContext()
	{}

	// assign operator
	virtual const TOLStatementContext& operator =(const TOLStatementContext&);
	// compare operator
	virtual bool operator ==(const TOLStatementContext&) const;

	friend class TOLStatement;
	friend class TOLLex;
	friend class TOLParser;
	friend TContext TOLStatementContextValue(const TOLStatementContext&);
};

inline bool TContextEqual(TContext c1, TContext c2)
{
	return c1 == c2;
}

inline TContext TOLStatementContextValue(const TOLStatementContext& c)
{
	return c.context;
}

inline unsigned int TContextHashFn(TContext c)
{
	return (unsigned int)c % 4;
}

// TOLTypeAttributes: special attributes; dynamically defined by article types defined
// in the database
class TOLTypeAttributes
{
private:
	pChar type_value;								// type
	TOLStatementContextHash context_attributes;		// hash of TOLStatementContext

public:
	// constructor
	TOLTypeAttributes(cpChar tv, const TOLStatementContextHash& ca)
			: context_attributes(ca)
	{
		type_value = tv ? strdup(tv) : NULL;
	}
	// copy-constructor
	TOLTypeAttributes(const TOLTypeAttributes& source)
			: context_attributes(source.context_attributes)
	{
		type_value = NULL;
		*this = source;
	}
	// virtual destructor
	virtual ~TOLTypeAttributes()
	{
		if (type_value != NULL)
			free(type_value);
	}

	// assign operator
	const TOLTypeAttributes& operator =(const TOLTypeAttributes&);
	// compare operator
	bool operator ==(const TOLTypeAttributes&) const;

	friend class TOLStatement;
	friend class TOLLex;
	friend class TOLParser;
	friend cpChar TOLTypeAttributesValue(const TOLTypeAttributes&);
};

inline cpChar TOLTypeAttributesValue(const TOLTypeAttributes& ta)
{
	return ta.type_value;
}

// TOLStatement: language statement
class TOLStatement : public TOLAtom
{
private:
	int statement;								// statement identifier
	TOLStatementContextHash statement_context;	// hash of TOLStatementContext
	TOLTypeAttributesHash type_attributes;		// hash of TOLTypeAttributes

public:
	// constructor
	TOLStatement(int st, cpChar st_name, const TOLStatementContextHash& ca,
	             const TOLTypeAttributesHash* p_pcoTypeAttributes = NULL)
			: TOLAtom(st_name), statement_context(ca),
			type_attributes(4, cpCharHashFn, cpCharEqual, TOLTypeAttributesValue)
	{
		if (p_pcoTypeAttributes != NULL)
			type_attributes = *p_pcoTypeAttributes;
		statement = st;
	}
	// copy-constructor
	TOLStatement(const TOLStatement& source)
			: statement_context(source.statement_context), type_attributes(source.type_attributes)
	{
		*this = source;
	}
	// virtual destructor
	virtual ~TOLStatement()
	{}

	// ClassName: return pointer to a string: class name
	virtual cpChar ClassName() const
	{
		return "TOLStatement";
	}
	// assign operator
	virtual const TOLStatement& operator =(const TOLStatement&);
	// compare operator
	virtual bool operator ==(const TOLStatement&) const;

	// Id: returns statement identifier
	int Id() const { return statement; }	
	// PrintAttrs: print valid attributes for a given context
	// Parameters:
	//		string& p_rcoOutString [out] - string to write attributes to
	//		TContext p_Context - context
	void PrintAttrs(string& p_rcoOutString, TContext p_Context);
	
	// PrintTAttrs: print valid type (special) attributes for a given context; if attributes type
	//		is undefined perform the operation for all types
	// Parameters:
	//		string& p_rcoOutString [out] - string to write attributes to
	//		cpChar p_chType - attributes type
	//		TContext p_Context - context
	void PrintTAttrs(string& p_rcoOutString, cpChar p_chType, TContext p_Context);
	
	// PrintTypes: print types of attributes
	// Parameters:
	//		string& p_rcoOutString - string to write types to
	void PrintTypes(string& p_rcoOutString);
	
	// FindAttr: return pointer to attribute valid in given context and identified by given name
	// Parameters:
	//		cpChar p_pchAttr - attribute name
	//		TContext p_Context - context
	const TOLAttribute* FindAttr(cpChar p_pchAttr, TContext p_Context);
	
	// FindType: return pointer to type (special) attributes valid for a given type
	// Parameters:
	//		cpChar p_chType - type name
	TOLTypeAttributes* FindType(cpChar p_chType);
	
	// FindTypeAttr: return pointer to attribute and pointer to type attributes containig found
	//		attribute
	// Parameters:
	//		cpChar p_pchAttr - attribute name
	//		cpChar p_chType - type name
	//		TContext p_Context - context
	//		const TOLTypeAttributes** p_ppcoTypeAttributes [out] - pointer to pointer to type
	//			(special) attributes
	TOLAttribute* FindTypeAttr(cpChar p_pchAttr, cpChar p_chType,
			TContext p_Context, TOLTypeAttributes** p_ppcoTypeAttributes);

	// UpdateTypes: set the types hash
	// Parameters:
	//		const TOLTypeAttributesHash* p_pcoTypeAttributes - pointer to types hash
	bool UpdateTypes(const TOLTypeAttributesHash* p_pcoTypeAttributes);
			
	friend class TOLLex;
	friend class TOLParser;
	friend cpChar TOLStatementValue(const TOLStatement&);
};

inline cpChar TOLStatementValue(const TOLStatement& s)
{
	return s.m_pchIdentifier;
}

#endif
