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

Defines the classes used in attribute classes implementation

******************************************************************************/

#ifndef _CMS_ATTRIBUTES_IMPL
#define _CMS_ATTRIBUTES_IMPL

#include <map>

#include "attributes.h"

// OperatorsMap
// Template defining a map of operators; the default constructor must be implemented by the user
// and must register all the valid operators
// New operators cannot be registerd after object instantiation
template <class Operator> class OperatorsMap
{
public:
	// default constructor
	OperatorsMap();

	// destructor
	~OperatorsMap();

	// validOp: return true if operator identified by name is valid
	bool validOp(const string& p_rcoName) const
	{ return (m_coOperators.find(p_rcoName) != m_coOperators.end()); }

	// operators: return string containing all valid operators names
	const string& operators() const { return m_coOps; }

	const Operator& operator [](const string& p_rcoName) const throw(InvalidOperator)
	{
		typename map<string, Operator*, str_case_less>::const_iterator coIt;
		if ((coIt = m_coOperators.find(p_rcoName)) == m_coOperators.end())
			throw InvalidOperator();
		return *(*coIt).second;
	}

private: // private methods
	// registerOp: insert operator into operators map
	void registerOp(const Operator& p_rcoOp)
	{ m_coOperators[p_rcoOp.name()] = new Operator(p_rcoOp); }

	// registerOp: insert operator into operators map
	void registerOp(Operator* p_pcoOp)
	{ m_coOperators[p_pcoOp->name()] = p_pcoOp; }

private: // private members
	map<string, Operator*, str_case_less> m_coOperators;	// map of operators
	string m_coOps;
};


// Definitions for Integer

// Integer operator
typedef CompOperator<Integer> CIntegerCompOp;

// Integer operation
class CIntegerCompOperation : public CompOperation
{
public:
	CIntegerCompOperation(const CIntegerCompOp& p_rcoOp, const string& p_rcoSecond)
		: m_rcoOp(p_rcoOp), m_coSecond(p_rcoSecond) {}

	virtual bool apply(const string& p_rcoFirst) const throw(InvalidValue)
	{ return m_rcoOp.apply(p_rcoFirst, m_coSecond); }

	virtual bool apply(const string& p_rcoFirst, const string& p_rcoSecond) const
		throw(InvalidValue)
	{ return m_rcoOp.apply(Integer(p_rcoFirst), p_rcoSecond); }

	virtual void setSecond(const string& p_rcoSecond) throw(InvalidValue)
	{ m_coSecond = p_rcoSecond; }

	virtual string second() const { return (string)m_coSecond; }

	virtual const string& symbol() const { return m_rcoOp.symbol(); }

	virtual CompOperation* clone() const { return new CIntegerCompOperation(*this); }

private:
	const CIntegerCompOp& m_rcoOp;
	Integer m_coSecond;
};


// Definitions for String

// String operator
typedef CompOperator<String> CStringCompOp;

// String operation
class CStringCompOperation : public CompOperation
{
public:
	CStringCompOperation(const CStringCompOp& p_rcoOp, const string& p_rcoSecond)
		: m_rcoOp(p_rcoOp), m_coSecond(p_rcoSecond) {}

	virtual bool apply(const string& p_rcoFirst) const throw(InvalidValue)
	{ return m_rcoOp.apply(p_rcoFirst, m_coSecond); }

	virtual bool apply(const string& p_rcoFirst, const string& p_rcoSecond) const
		throw(InvalidValue)
	{ return m_rcoOp.apply(String(p_rcoFirst), p_rcoSecond); }

	virtual void setSecond(const string& p_rcoSecond) throw(InvalidValue)
	{ m_coSecond = p_rcoSecond; }

	virtual string second() const { return (string)m_coSecond; }

	virtual const string& symbol() const { return m_rcoOp.symbol(); }

	virtual CompOperation* clone() const { return new CStringCompOperation(*this); }

private:
	const CStringCompOp& m_rcoOp;
	String m_coSecond;
};


// Definitions for Switch

// Switch operator
typedef CompOperator<Switch> CSwitchCompOp;

// Switch operation
class CSwitchCompOperation : public CompOperation
{
public:
	CSwitchCompOperation(const CSwitchCompOp& p_rcoOp, const string& p_rcoSecond)
		: m_rcoOp(p_rcoOp), m_coSecond(p_rcoSecond) {}

	virtual bool apply(const string& p_rcoFirst) const throw(InvalidValue)
	{ return m_rcoOp.apply(p_rcoFirst, m_coSecond); }

	virtual bool apply(const string& p_rcoFirst, const string& p_rcoSecond) const
		throw(InvalidValue)
	{ return m_rcoOp.apply(Switch(p_rcoFirst), p_rcoSecond); }

	virtual void setSecond(const string& p_rcoSecond) throw(InvalidValue)
	{ m_coSecond = p_rcoSecond; }

	virtual string second() const { return (string)m_coSecond; }

	virtual const string& symbol() const { return m_rcoOp.symbol(); }

	virtual CompOperation* clone() const { return new CSwitchCompOperation(*this); }

private:
	const CSwitchCompOp& m_rcoOp;
	Switch m_coSecond;
};


// Definitions for Date

// Date operator
typedef CompOperator<Date> CDateCompOp;

// Date operation
class CDateCompOperation : public CompOperation
{
public:
	CDateCompOperation(const CDateCompOp& p_rcoOp, const string& p_rcoSecond)
		: m_rcoOp(p_rcoOp), m_coSecond(p_rcoSecond) {}

	virtual bool apply(const string& p_rcoFirst) const throw(InvalidValue)
	{ return m_rcoOp.apply(p_rcoFirst, m_coSecond); }

	virtual bool apply(const string& p_rcoFirst, const string& p_rcoSecond) const
		throw(InvalidValue)
	{ return m_rcoOp.apply(Date(p_rcoFirst), p_rcoSecond); }

	virtual void setSecond(const string& p_rcoSecond) throw(InvalidValue)
	{ m_coSecond = p_rcoSecond; }

	virtual string second() const { return (string)m_coSecond; }

	virtual const string& symbol() const { return m_rcoOp.symbol(); }

	virtual CompOperation* clone() const { return new CDateCompOperation(*this); }

private:
	const CDateCompOp& m_rcoOp;
	Date m_coSecond;
};


// Time operator
typedef CompOperator<Time> CTimeCompOp;

// Time operation
class CTimeCompOperation : public CompOperation
{
public:
	CTimeCompOperation(const CTimeCompOp& p_rcoOp, const string& p_rcoSecond)
		: m_rcoOp(p_rcoOp), m_coSecond(p_rcoSecond) {}

	virtual bool apply(const string& p_rcoFirst) const throw(InvalidValue)
	{ return m_rcoOp.apply(p_rcoFirst, m_coSecond); }

	virtual bool apply(const string& p_rcoFirst, const string& p_rcoSecond) const
		throw(InvalidValue)
	{ return m_rcoOp.apply(Time(p_rcoFirst), p_rcoSecond); }

	virtual void setSecond(const string& p_rcoSecond) throw(InvalidValue)
	{ m_coSecond = p_rcoSecond; }

	virtual string second() const { return (string)m_coSecond; }

	virtual const string& symbol() const { return m_rcoOp.symbol(); }

	virtual CompOperation* clone() const { return new CTimeCompOperation(*this); }

private:
	const CTimeCompOp& m_rcoOp;
	Time m_coSecond;
};


// Definitions for DateTime

// DateTime operator
typedef CompOperator<DateTime> CDateTimeCompOp;

// DateTime operation
class CDateTimeCompOperation : public CompOperation
{
public:
	CDateTimeCompOperation(const CDateTimeCompOp& p_rcoOp, const string& p_rcoSecond)
		: m_rcoOp(p_rcoOp), m_coSecond(p_rcoSecond) {}

	virtual bool apply(const string& p_rcoFirst) const throw(InvalidValue)
	{ return m_rcoOp.apply(p_rcoFirst, m_coSecond); }

	virtual bool apply(const string& p_rcoFirst, const string& p_rcoSecond) const
		throw(InvalidValue)
	{ return m_rcoOp.apply(DateTime(p_rcoFirst), p_rcoSecond); }

	virtual void setSecond(const string& p_rcoSecond) throw(InvalidValue)
	{ m_coSecond = p_rcoSecond; }

	virtual string second() const { return (string)m_coSecond; }

	virtual const string& symbol() const { return m_rcoOp.symbol(); }

	virtual CompOperation* clone() const { return new CDateTimeCompOperation(*this); }

private:
	const CDateTimeCompOp& m_rcoOp;
	DateTime m_coSecond;
};


// Definitions for Enum

// Enum operator
typedef CompOperator<Enum::Item> CEnumCompOp;

// Enum operation
class CEnumCompOperation : public CompOperation
{
public:
	CEnumCompOperation(const CEnumCompOp& p_rcoOp, const string& p_rcoSecond)
		: m_rcoOp(p_rcoOp), m_coSecond(p_rcoSecond) {}

	virtual bool apply(const string& p_rcoFirst) const throw(InvalidValue)
	{ return m_rcoOp.apply(p_rcoFirst, m_coSecond); }

	virtual bool apply(const string& p_rcoFirst, const string& p_rcoSecond) const
		throw(InvalidValue)
	{ return m_rcoOp.apply(Enum::Item(p_rcoFirst), p_rcoSecond); }

	virtual void setSecond(const string& p_rcoSecond) throw(InvalidValue)
	{ m_coSecond = p_rcoSecond; }

	virtual string second() const { return (string)m_coSecond; }

	virtual const string& symbol() const { return m_rcoOp.symbol(); }

	virtual CompOperation* clone() const { return new CEnumCompOperation(*this); }

private:
	const CEnumCompOp& m_rcoOp;
	Enum::Item m_coSecond;
};


// Definitions for Topic

// Topic operator
typedef CompOperator<Topic::Item> CTopicCompOp;

// Topic operation
class CTopicCompOperation : public CompOperation
{
public:
	CTopicCompOperation(const CTopicCompOp& p_rcoOp, const string& p_rcoSecond)
		: m_rcoOp(p_rcoOp), m_coSecond(p_rcoSecond) {}

	virtual bool apply(const string& p_rcoFirst) const throw(InvalidValue)
	{ return m_rcoOp.apply(p_rcoFirst, m_coSecond); }

	virtual bool apply(const string& p_rcoFirst, const string& p_rcoSecond) const
		throw(InvalidValue)
	{ return m_rcoOp.apply(Topic::Item(p_rcoFirst), p_rcoSecond); }

	virtual void setSecond(const string& p_rcoSecond) throw(InvalidValue)
	{ m_coSecond = p_rcoSecond; }

	virtual string second() const { return (string)m_coSecond; }

	id_type secondId() const { return m_coSecond.id(); }

	virtual const string& symbol() const { return m_rcoOp.symbol(); }

	virtual CompOperation* clone() const { return new CTopicCompOperation(*this); }

private:
	const CTopicCompOp& m_rcoOp;
	Topic::Item m_coSecond;
};

#endif
