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

Defines

******************************************************************************/

#ifndef _CMS_OPERATORS
#define _CMS_OPERATORS

#include <string>
#include <stdexcept>

#include "globals.h"

extern const string g_coEQUAL;
extern const string g_coNOT_EQUAL;
extern const string g_coGREATER;
extern const string g_coGREATER_EQUAL;
extern const string g_coLESS;
extern const string g_coLESS_EQUAL;

extern const string g_coEQUAL_Symbol;
extern const string g_coNOT_EQUAL_Symbol;
extern const string g_coGREATER_Symbol;
extern const string g_coGREATER_EQUAL_Symbol;
extern const string g_coLESS_Symbol;
extern const string g_coLESS_EQUAL_Symbol;

// exception classes thrown by CompOperand template instantiations
class InvalidOperator : public exception
{
public:
	virtual const char* what () const throw() { return "invalid operator"; }
};

// CompOperator
// template defining the comparation operator
template <class DT> class CompOperator
{
	typedef bool (DT::*Operator)(const DT&) const;

public:
	// constructor (name, symbol and operator function must be defined)
	// throws InvalidOperator if pointer to method (operator) is null
	CompOperator(const string& p_rcoName, const string& p_rcoSymbol,
	             Operator p_pOperator) throw(InvalidOperator)
		: m_coName(p_rcoName), m_coSymbol(p_rcoSymbol), m_pOperator(p_pOperator)
	{
		if (m_pOperator == 0)
			throw InvalidOperator();
	}

	// name: returns operator name
	const string& name() const { return m_coName; }

	// symbol: returns operator symbol
	const string& symbol() const { return m_coSymbol; }
	
	// operatorFunction: returns pointer to operator method
	Operator operatorFunction() const { return m_pOperator; }

	// apply: applies operator to data
	// Parameters:
	//	- const DT& p_rcoVal1 : first operand
	//	- const string& p_rcoVal2: second operand; converted to DT type
	// Throws InvalidOperand if conversion to DT of the second operand is not possible
	bool apply(const DT& p_rcoVal1, const string& p_rcoVal2) const throw(InvalidValue)
	{ return (p_rcoVal1.*m_pOperator)(DT(p_rcoVal2)); }

	// apply: applies operator to data
	// Parameters:
	//	- const string& p_rcoVal1: first operand; converted to DT type
	//	- const DT& p_rcoVal2 : second operand
	// Throws InvalidOperand if conversion to DT of the first operand is not possible
	bool apply(const string& p_rcoVal1, const DT& p_rcoVal2) const throw(InvalidValue)
	{ return (DT(p_rcoVal1).*m_pOperator)(p_rcoVal2); }

	// apply: applies operator to data
	// Parameters:
	//	- const DT& p_rcoVal1 : first operand
	//	- const DT& p_rcoVal2 : second operand
	bool apply(const DT& p_rcoVal1, const DT& p_rcoVal2) const throw()
	{ return (p_rcoVal1.*m_pOperator)(p_rcoVal2); }

private:
	string m_coName;
	string m_coSymbol;
	Operator m_pOperator;
};

// CompOperation
// Abstract class declaring comparison operation interface
// Stores the operator and the first operand; used when the operator and the first operand
// are known at the object initialisation but the second operand is not known
class CompOperation
{
public:
	// need a virtual destructor
	virtual ~CompOperation() {}

	// set first operand
	virtual void setSecond(const string&) throw(InvalidValue) = 0;

	// virtual method apply: applies the operation to the first (received as a parameter)
	// operand and the second operand (stored)
	virtual bool apply(const string&) const throw(InvalidValue) = 0;

	// virtual method apply: applies the operation to the first operand and the second
	// operand (both received as a parameters)
	virtual bool apply(const string&, const string&) const throw(InvalidValue) = 0;

	// returns first operand
	virtual string second() const = 0;

	// returns operator symbol
	virtual const string& symbol() const = 0;

	// returns an operation object equal to this
	virtual CompOperation* clone() const = 0;
};

#endif
