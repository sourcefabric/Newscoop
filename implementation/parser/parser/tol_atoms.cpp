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
 
Implementation of TOLAtom, TOLAttribute, TOLStatementContext and TOLStatement
methods
 
******************************************************************************/

#include <string.h>

#include "tol_atoms.h"

// TOLAtom assign operator
const TOLAtom& TOLAtom::operator = (const TOLAtom& source)
{
	if (this != &source)
		strcpy(m_pchIdentifier, source.m_pchIdentifier);
	return *this;
}

// TOLAtom compare operator
bool TOLAtom::operator ==(const TOLAtom& other) const
{
	return strcmp(m_pchIdentifier, other.m_pchIdentifier) == 0;
}

// TOLAttribute constructor
TOLAttribute::TOLAttribute(cpChar at_name, TDataType dt, const char* dbf, TOLAttrClass ac)
		: TOLAtom(at_name)
{
	DType = dt;
	attr_class = ac;
	if (dbf)
	{
		strncpy(DBField, dbf, ID_MAXLEN > strlen(dbf) ? strlen(dbf) : ID_MAXLEN);
		DBField[ID_MAXLEN > strlen(dbf) ? strlen(dbf) : ID_MAXLEN] = 0;
	}
	else
		DBField[0] = 0;
}

// TOLAttribute assign operator
const TOLAttribute& TOLAttribute::operator =(const TOLAttribute& source)
{
	if (this == &source)
		return * this;
	strcpy(m_pchIdentifier, source.m_pchIdentifier);
	DType = source.DType;
	attr_class = source.attr_class;
	strcpy(DBField, source.DBField);
	return *this;
}

// TOLAttribute compare operator
bool TOLAttribute::operator ==(const TOLAttribute& other) const
{
	return strcmp(m_pchIdentifier, other.m_pchIdentifier) == 0
	       && DType == other.DType
	       && attr_class == other.attr_class
	       && strcmp(DBField, other.DBField) == 0;
}

// TOLStatementContext assign operator
const TOLStatementContext& TOLStatementContext::operator =(const TOLStatementContext& s)
{
	if (this == &s)
		return * this;
	context = s.context;
	attributes = s.attributes;
	return *this;
}

// TOLStatementContext compare operator
bool TOLStatementContext::operator ==(const TOLStatementContext& o) const
{
	return context == o.context && attributes == o.attributes;
}

// TOLTypeAttributes assign operator
const TOLTypeAttributes& TOLTypeAttributes::operator =(const TOLTypeAttributes& s)
{
	if (this == &s)
		return * this;
	if (type_value != NULL)
		free(type_value);
	type_value = s.type_value != NULL ? strdup(s.type_value) : NULL;
	context_attributes = s.context_attributes;
	return *this;
}

// TOLTypeAttributes compare operator
bool TOLTypeAttributes::operator ==(const TOLTypeAttributes& o) const
{
	return strcmp(type_value, o.type_value) == 0
	       && context_attributes == o.context_attributes;
}

// TOLStatement assign operator
const TOLStatement& TOLStatement::operator =(const TOLStatement& source)
{
	if (this == &source)
		return * this;
	strcpy(m_pchIdentifier, source.m_pchIdentifier);
	statement = source.statement;
	statement_context = source.statement_context;
	type_attributes = source.type_attributes;
	return *this;
}

// TOLStatement compare operator
bool TOLStatement::operator ==(const TOLStatement& other) const
{
	return strcmp(m_pchIdentifier, other.m_pchIdentifier) == 0
	       && statement == ((const TOLStatement&)other).statement
	       && statement_context == ((const TOLStatement&)other).statement_context
	       && type_attributes == ((const TOLStatement&)other).type_attributes;
}

// PrintAttrs: print valid attributes for a given context
// Parameters:
//		string& p_rcoOutString [out] - string to write attributes to
//		TContext p_Context - context
void TOLStatement::PrintAttrs(string& p_rcoOutString, TContext p_Context)
{
	TOLStatementContextHash::iterator sc_i = statement_context.find(p_Context);
	if (sc_i == statement_context.end())
		return ;
	TOLAttributeHash::iterator a_i;
	for (a_i = (*sc_i).attributes.begin(); a_i != (*sc_i).attributes.end(); ++a_i)
		if (strlen(p_rcoOutString.c_str()) == 0)
			p_rcoOutString += (*a_i).m_pchIdentifier;
		else
			p_rcoOutString += string(", ") + (*a_i).m_pchIdentifier;
}

// PrintTAttrs: print type attributes for a given context; if attributes type
//		is undefined perform the operation for all types
// Parameters:
//		string& p_rcoOutString [out] - string to write attributes to
//		cpChar p_chType - attributes type
//		TContext p_Context - context
void TOLStatement::PrintTAttrs(string& p_rcoOutString, cpChar p_chType, TContext p_Context)
{
	if (p_chType == NULL)
		return ;
	TOLTypeAttributesHash::iterator ta_h;
	TOLStatementContextHash::iterator sc_i;
	TOLAttributeHash::iterator a_i;
	if (strlen(p_chType) != 0)			// type is define
	{									// print attributes having the given type
		ta_h = type_attributes.find(p_chType);
		if (ta_h == type_attributes.end())
			return ;
		sc_i = (*ta_h).context_attributes.find(p_Context);
		if (sc_i == (*ta_h).context_attributes.end())
			return;
		for (a_i = (*sc_i).attributes.begin(); a_i != (*sc_i).attributes.end(); ++a_i)
			if (strlen(p_rcoOutString.c_str()) == 0)
				p_rcoOutString += (*a_i).m_pchIdentifier;
			else
				p_rcoOutString += string(", ") + (*a_i).m_pchIdentifier;
	}
	else								// type is undefined
	{									// print attributes of all types
		for (ta_h = type_attributes.begin(); ta_h != type_attributes.end(); ++ta_h)
		{
			sc_i = (*ta_h).context_attributes.find(p_Context);
			if (sc_i == (*ta_h).context_attributes.end())
				return;
			for (a_i = (*sc_i).attributes.begin(); a_i != (*sc_i).attributes.end(); ++a_i)
				if (strlen(p_rcoOutString.c_str()) == 0)
					p_rcoOutString += (*a_i).m_pchIdentifier;
				else
					p_rcoOutString += string(", ") + (*a_i).m_pchIdentifier;
		}
	}
}

// PrintTypes: print types of attributes
// Parameters:
//		string& p_rcoOutString - string to write types to
void TOLStatement::PrintTypes(string& p_rcoOutString)
{
	TOLTypeAttributesHash::iterator ta_h;
	for (ta_h = type_attributes.begin(); ta_h != type_attributes.end(); ++ta_h)
		if (strlen(p_rcoOutString.c_str()) == 0)
			p_rcoOutString += (*ta_h).type_value;
		else
			p_rcoOutString += string(", ") + (*ta_h).type_value;
}

// FindAttr: return pointer to attribute valid in given context and identified by given name
// Parameters:
//		cpChar p_pchAttr - attribute name
//		TContext p_Context - context
const TOLAttribute* TOLStatement::FindAttr(cpChar p_pchAttr, TContext p_Context)
{
	TOLStatementContextHash::iterator sc_i = statement_context.find(p_Context);
	if (sc_i == statement_context.end())
		return NULL;
	TOLAttributeHash::iterator a_i = (*sc_i).attributes.find(p_pchAttr);
	if (a_i != (*sc_i).attributes.end())
		return &(*a_i);
	return NULL;
}

// FindType: return pointer to type (special) attributes valid for a given type
// Parameters:
//		cpChar p_chType - type name
TOLTypeAttributes* TOLStatement::FindType(cpChar p_chType)
{
	TOLTypeAttributesHash::iterator ta_h = type_attributes.find(p_chType);
	if (ta_h != type_attributes.end())
		return new TOLTypeAttributes(*ta_h);
	return NULL;
}

// FindTypeAttr: return pointer to attribute and pointer to type attributes containig found
//		attribute
// Parameters:
//		cpChar p_pchAttr - attribute name
//		cpChar p_chType - type name
//		TContext p_Context - context
//		const TOLTypeAttributes** p_ppcoTypeAttributes [out] - pointer to pointer to type (special)
//			attributes
TOLAttribute* TOLStatement::FindTypeAttr(cpChar p_pchAttr, cpChar p_chType,
		TContext p_Context, TOLTypeAttributes** p_ppcoTypeAttributes)
{
	*p_ppcoTypeAttributes = NULL;
	if (p_chType == NULL)
		return NULL;
	TOLTypeAttributesHash::iterator ta_h;
	TOLStatementContextHash::iterator sc_i;
	TOLAttributeHash::iterator a_i;
	if (strlen(p_chType) != 0)
	{
		ta_h = type_attributes.find(p_chType);
		if (ta_h == type_attributes.end())
			return NULL;
		sc_i = (*ta_h).context_attributes.find(p_Context);
		if (sc_i == (*ta_h).context_attributes.end())
			return NULL;
		a_i = (*sc_i).attributes.find(p_pchAttr);
		if (a_i != (*sc_i).attributes.end())
		{
			*p_ppcoTypeAttributes = new TOLTypeAttributes(*ta_h);
			return new TOLAttribute(*a_i);
		}
	}
	else
	{
		for (ta_h = type_attributes.begin(); ta_h != type_attributes.end(); ++ta_h)
		{
			sc_i = (*ta_h).context_attributes.find(p_Context);
			if (sc_i == (*ta_h).context_attributes.end())
				continue;
			a_i = (*sc_i).attributes.find(p_pchAttr);
			if (a_i != (*sc_i).attributes.end())
			{
				*p_ppcoTypeAttributes = new TOLTypeAttributes(*ta_h);
				return new TOLAttribute(*a_i);
			}
		}
	}
	return NULL;
}

// UpdateTypes: set the types hash
// Parameters:
//		const TOLTypeAttributesHash* p_pcoTypeAttributes - pointer to types hash
bool TOLStatement::UpdateTypes(const TOLTypeAttributesHash* p_pcoTypeAttributes)
{
	if (p_pcoTypeAttributes == NULL)
	{
		return false;
	}
	if (type_attributes != *p_pcoTypeAttributes)
	{
		type_attributes = *p_pcoTypeAttributes;
		return true;
	}
	return false;
}
