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

// exception classes thrown by CompOperand template instantiations
class InvalidOperator : public exception
{
public:
	virtual const char* what () const throw() { return "invalid operator"; }
};


class CCompOperator
{
public:
	static string equal_op() { initStrings(); return *m_pcoEqual; }
	static string not_equal_op() { initStrings(); return *m_pcoNotEqual; }
	static string greater_op() { initStrings(); return *m_pcoGreater; }
	static string greater_equal_op() { initStrings(); return *m_pcoGreaterEqual; }
	static string less_op() { initStrings(); return *m_pcoLess; }
	static string less_equal_op() { initStrings(); return *m_pcoLessEqual; }

	static string equal_op_symbol() { initStrings(); return *m_pcoEqualSymbol; }
	static string not_equal_op_symbol() { initStrings(); return *m_pcoNotEqualSymbol; }
	static string greater_op_symbol() { initStrings(); return *m_pcoGreaterSymbol; }
	static string greater_equal_op_symbol() { initStrings(); return *m_pcoGreaterEqualSymbol; }
	static string less_op_symbol() { initStrings(); return *m_pcoLessSymbol; }
	static string less_equal_op_symbol() { initStrings(); return *m_pcoLessEqualSymbol; }

private:
	static void initStrings();

	static string* m_pcoEqual;
	static string* m_pcoNotEqual;
	static string* m_pcoGreater;
	static string* m_pcoGreaterEqual;
	static string* m_pcoLess;
	static string* m_pcoLessEqual;

	static string* m_pcoEqualSymbol;
	static string* m_pcoNotEqualSymbol;
	static string* m_pcoGreaterSymbol;
	static string* m_pcoGreaterEqualSymbol;
	static string* m_pcoLessSymbol;
	static string* m_pcoLessEqualSymbol;
};

inline void CCompOperator::initStrings()
{
	if (m_pcoEqual != NULL)
		return;

	m_pcoEqual = new string("is");
	m_pcoNotEqual = new string("not");
	m_pcoGreater = new string("greater");
	m_pcoGreaterEqual = new string("greater_equal");
	m_pcoLess = new string("smaller");
	m_pcoLessEqual = new string("smaller_equal");

	m_pcoEqualSymbol = new string("=");
	m_pcoNotEqualSymbol = new string("!=");
	m_pcoGreaterSymbol = new string(">");
	m_pcoGreaterEqualSymbol = new string(">=");
	m_pcoLessSymbol = new string("<");
	m_pcoLessEqualSymbol = new string("<=");
}

#define g_coEQUAL CCompOperator::equal_op()
#define g_coNOT_EQUAL CCompOperator::not_equal_op()
#define g_coGREATER CCompOperator::greater_op()
#define g_coGREATER_EQUAL CCompOperator::greater_equal_op()
#define g_coLESS CCompOperator::less_op()
#define g_coLESS_EQUAL CCompOperator::less_equal_op()

#define g_coEQUAL_Symbol CCompOperator::equal_op_symbol()
#define g_coNOT_EQUAL_Symbol CCompOperator::not_equal_op_symbol()
#define g_coGREATER_Symbol CCompOperator::greater_op_symbol()
#define g_coGREATER_EQUAL_Symbol CCompOperator::greater_equal_op_symbol()
#define g_coLESS_Symbol CCompOperator::less_op_symbol()
#define g_coLESS_EQUAL_Symbol CCompOperator::less_equal_op_symbol()


// CompOperator
// template defining the comparation operator
template <class DT> class CompOperator : public CCompOperator
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
