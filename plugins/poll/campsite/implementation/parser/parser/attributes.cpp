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

Implementation of type of attributes classes

******************************************************************************/

#include "attributes_impl.h"

// OperatorsMap destructor
template <class Operator> OperatorsMap<Operator>::~OperatorsMap()
{
	typename map<string, Operator*>::iterator coIt;
	for (coIt = m_coOperators.begin(); coIt != m_coOperators.end(); ++coIt)
	{
		delete (*coIt).second;
		(*coIt).second = NULL;
		m_coOperators.erase(coIt);
	}
}


// Implementation of CIntegerAttr (Integer attribute)

const string CIntegerAttr::s_coTypeName = "integer";
const string CIntegerAttr::s_coTypeValues = "integers";

// OperatorsMap constructor for CIntegerCompOp (Integer operator) type
template <> OperatorsMap<CIntegerCompOp>::OperatorsMap()
{
	registerOp(new CIntegerCompOp(g_coEQUAL, g_coEQUAL_Symbol, &Integer::operator ==));
	registerOp(new CIntegerCompOp(g_coNOT_EQUAL, g_coNOT_EQUAL_Symbol, &Integer::operator !=));
	registerOp(new CIntegerCompOp(g_coGREATER, g_coGREATER_Symbol, &Integer::operator >));
	registerOp(new CIntegerCompOp(g_coGREATER_EQUAL, g_coGREATER_EQUAL_Symbol,
	                              &Integer::operator >=));
	registerOp(new CIntegerCompOp(g_coLESS, g_coLESS_Symbol, &Integer::operator <));
	registerOp(new CIntegerCompOp(g_coLESS_EQUAL, g_coLESS_EQUAL_Symbol, &Integer::operator <=));
	m_coOps = g_coEQUAL + ", " + g_coNOT_EQUAL + ", " + g_coGREATER + ", " + g_coGREATER_EQUAL
	        + ", " + g_coLESS + ", " + g_coLESS_EQUAL;
}

// map of integer operators
class CIntegerCompOpMap : public OperatorsMap<CIntegerCompOp> {};

// initialise static CIntegerAttr member (the map of operators)
CIntegerCompOpMap* CIntegerAttr::s_pcoOpMap = new CIntegerCompOpMap;

// operators: returns string containing valid operators
const string& CIntegerAttr::operators() const
{
	return s_pcoOpMap->operators();
}

// validOperator: returns true if operator exists, false otherwise
bool CIntegerAttr::validOperator(const string& p_rcoOp) const
{
	return s_pcoOpMap->validOp(p_rcoOp);
}

// compOperation: returns a CompOperation class for given operator, first operand;
// throws InvalidOperator if operator not found
// throws InvalidValue if value is invalid
CompOperation* CIntegerAttr::compOperation(const string& p_rcoOp, const string& p_rcoSecond)
	const throw(InvalidOperator, InvalidValue)
{
	return new CIntegerCompOperation(s_pcoOpMap->operator[](p_rcoOp), Integer(p_rcoSecond));
}


// Implementation of CStringAttr (String attribute)

const string CStringAttr::s_coTypeName = "string";
const string CStringAttr::s_coTypeValues = "string of characters";

// OperatorsMap constructor for CStringCompOp (String operator) type
template <> OperatorsMap<CStringCompOp>::OperatorsMap()
{
	registerOp(new CStringCompOp(g_coEQUAL, g_coEQUAL_Symbol, &String::case_equal));
	registerOp(new CStringCompOp(g_coNOT_EQUAL, g_coNOT_EQUAL_Symbol, &String::case_not_equal));
	registerOp(new CStringCompOp(g_coGREATER, g_coGREATER_Symbol, &String::case_greater));
	registerOp(new CStringCompOp(g_coGREATER_EQUAL, g_coGREATER_EQUAL_Symbol,
	                             &String::case_greater_equal));
	registerOp(new CStringCompOp(g_coLESS, g_coLESS_Symbol, &String::case_less));
	registerOp(new CStringCompOp(g_coLESS_EQUAL, g_coLESS_EQUAL_Symbol, &String::case_less_equal));
	m_coOps = g_coEQUAL + ", " + g_coNOT_EQUAL + ", " + g_coGREATER + ", " + g_coGREATER_EQUAL
	        + ", " + g_coLESS + ", " + g_coLESS_EQUAL;
}

// map of integer operators
class CStringCompOpMap : public OperatorsMap<CStringCompOp> {};

// initialise static CStringAttr member (the map of operators)
CStringCompOpMap* CStringAttr::s_pcoOpMap = new CStringCompOpMap;

// operators: returns string containing valid operators
const string& CStringAttr::operators() const
{
	return s_pcoOpMap->operators();
}

// validOperator: returns true if operator exists, false otherwise
bool CStringAttr::validOperator(const string& p_rcoOp) const
{
	return s_pcoOpMap->validOp(p_rcoOp);
}

// compOperation: returns a CompOperation class for given operator, first operand;
// throws InvalidOperator if operator not found
// throws InvalidValue if value is invalid
CompOperation* CStringAttr::compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
	throw(InvalidOperator, InvalidValue)
{
	return new CStringCompOperation(s_pcoOpMap->operator[](p_rcoOp), String(p_rcoSecond));
}


// Implementation of CSwitchAttr (Switch attribute)

const string CSwitchAttr::s_coTypeName = "switch";
const string CSwitchAttr::s_coTypeValues = "on, off";

// OperatorsMap constructor for CSwitchCompOp (Switch operator) type
template <> OperatorsMap<CSwitchCompOp>::OperatorsMap()
{
	registerOp(new CSwitchCompOp(g_coEQUAL, g_coEQUAL_Symbol, &Switch::operator ==));
	registerOp(new CSwitchCompOp(g_coNOT_EQUAL, g_coNOT_EQUAL_Symbol, &Switch::operator !=));
	m_coOps = g_coEQUAL + ", " + g_coNOT_EQUAL;
}

// map of integer operators
class CSwitchCompOpMap : public OperatorsMap<CSwitchCompOp> {};

// initialise static CSwitchAttr member (the map of operators)
CSwitchCompOpMap* CSwitchAttr::s_pcoOpMap = new CSwitchCompOpMap;

// operators: returns string containing valid operators
const string& CSwitchAttr::operators() const
{
	return s_pcoOpMap->operators();
}

// validOperator: returns true if operator exists, false otherwise
bool CSwitchAttr::validOperator(const string& p_rcoOp) const
{
	return s_pcoOpMap->validOp(p_rcoOp);
}

// compOperation: returns a CompOperation class for given operator, first operand;
// throws InvalidOperator if operator not found
// throws InvalidValue if value is invalid
CompOperation* CSwitchAttr::compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
	throw(InvalidOperator, InvalidValue)
{
	return new CSwitchCompOperation(s_pcoOpMap->operator[](p_rcoOp), Switch(p_rcoSecond));
}


// Implementation of CDateAttr (Date attribute)

const string CDateAttr::s_coTypeName = "date";
const string CDateAttr::s_coTypeValues = "year, month, day";

// OperatorsMap constructor for CDateCompOp (Date operator) type
template <> OperatorsMap<CDateCompOp>::OperatorsMap()
{
	registerOp(new CDateCompOp(g_coEQUAL, g_coEQUAL_Symbol, &Date::operator ==));
	registerOp(new CDateCompOp(g_coNOT_EQUAL, g_coNOT_EQUAL_Symbol, &Date::operator !=));
	registerOp(new CDateCompOp(g_coGREATER, g_coGREATER_Symbol, &Date::operator >));
	registerOp(new CDateCompOp(g_coGREATER_EQUAL, g_coGREATER_EQUAL_Symbol, &Date::operator >=));
	registerOp(new CDateCompOp(g_coLESS, g_coLESS_Symbol, &Date::operator <));
	registerOp(new CDateCompOp(g_coLESS_EQUAL, g_coLESS_EQUAL_Symbol, &Date::operator <=));
	m_coOps = g_coEQUAL + ", " + g_coNOT_EQUAL + ", " + g_coGREATER + ", " + g_coGREATER_EQUAL
	        + ", " + g_coLESS + ", " + g_coLESS_EQUAL;
}

// map of integer operators
class CDateCompOpMap : public OperatorsMap<CDateCompOp> {};

// initialise static CDateAttr member (the map of operators)
CDateCompOpMap* CDateAttr::s_pcoOpMap = new CDateCompOpMap;

// operators: returns string containing valid operators
const string& CDateAttr::operators() const
{
	return s_pcoOpMap->operators();
}

// validOperator: returns true if operator exists, false otherwise
bool CDateAttr::validOperator(const string& p_rcoOp) const
{
	return s_pcoOpMap->validOp(p_rcoOp);
}

// compOperation: returns a CompOperation class for given operator, first operand;
// throws InvalidOperator if operator not found
// throws InvalidValue if value is invalid
CompOperation* CDateAttr::compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
	throw(InvalidOperator, InvalidValue)
{
	return new CDateCompOperation(s_pcoOpMap->operator[](p_rcoOp), Date(p_rcoSecond));
}


// Implementation of CTimeAttr (Time attribute)

const string CTimeAttr::s_coTypeName = "time";
const string CTimeAttr::s_coTypeValues = "hour, minute, second";

// OperatorsMap constructor for CTimeCompOp (Time operator) type
template <> OperatorsMap<CTimeCompOp>::OperatorsMap()
{
	registerOp(new CTimeCompOp(g_coEQUAL, g_coEQUAL_Symbol, &Time::operator ==));
	registerOp(new CTimeCompOp(g_coNOT_EQUAL, g_coNOT_EQUAL_Symbol, &Time::operator !=));
	registerOp(new CTimeCompOp(g_coGREATER, g_coGREATER_Symbol, &Time::operator >));
	registerOp(new CTimeCompOp(g_coGREATER_EQUAL, g_coGREATER_EQUAL_Symbol, &Time::operator >=));
	registerOp(new CTimeCompOp(g_coLESS, g_coLESS_Symbol, &Time::operator <));
	registerOp(new CTimeCompOp(g_coLESS_EQUAL, g_coLESS_EQUAL_Symbol, &Time::operator <=));
	m_coOps = g_coEQUAL + ", " + g_coNOT_EQUAL + ", " + g_coGREATER + ", " + g_coGREATER_EQUAL
	        + ", " + g_coLESS + ", " + g_coLESS_EQUAL;
}

// map of integer operators
class CTimeCompOpMap : public OperatorsMap<CTimeCompOp> {};

// initialise static CTimeAttr member (the map of operators)
CTimeCompOpMap* CTimeAttr::s_pcoOpMap = new CTimeCompOpMap;

// operators: returns string containing valid operators
const string& CTimeAttr::operators() const
{
	return s_pcoOpMap->operators();
}

// validOperator: returns true if operator exists, false otherwise
bool CTimeAttr::validOperator(const string& p_rcoOp) const
{
	return s_pcoOpMap->validOp(p_rcoOp);
}

// compOperation: returns a CompOperation class for given operator, first operand;
// throws InvalidOperator if operator not found
// throws InvalidValue if value is invalid
CompOperation* CTimeAttr::compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
	throw(InvalidOperator, InvalidValue)
{
	return new CTimeCompOperation(s_pcoOpMap->operator[](p_rcoOp), Time(p_rcoSecond));
}


// Implementation of CDateTimeAttr (DateTime attribute)

const string CDateTimeAttr::s_coTypeName = "datetime";
const string CDateTimeAttr::s_coTypeValues = "year, month, day, hour, minute, second";

// OperatorsMap constructor for CDateTimeCompOp (DateTime operator) type
template <> OperatorsMap<CDateTimeCompOp>::OperatorsMap()
{
	registerOp(new CDateTimeCompOp(g_coEQUAL, g_coEQUAL_Symbol, &DateTime::operator ==));
	registerOp(new CDateTimeCompOp(g_coNOT_EQUAL, g_coNOT_EQUAL_Symbol, &DateTime::operator !=));
	registerOp(new CDateTimeCompOp(g_coGREATER, g_coGREATER_Symbol, &DateTime::operator >));
	registerOp(new CDateTimeCompOp(g_coGREATER_EQUAL, g_coGREATER_EQUAL_Symbol,
	                               &DateTime::operator >=));
	registerOp(new CDateTimeCompOp(g_coLESS, g_coLESS_Symbol, &DateTime::operator <));
	registerOp(new CDateTimeCompOp(g_coLESS_EQUAL, g_coLESS_EQUAL_Symbol, &DateTime::operator <=));
	m_coOps = g_coEQUAL + ", " + g_coNOT_EQUAL + ", " + g_coGREATER + ", " + g_coGREATER_EQUAL
	        + ", " + g_coLESS + ", " + g_coLESS_EQUAL;
}

// map of integer operators
class CDateTimeCompOpMap : public OperatorsMap<CDateTimeCompOp> {};

// initialise static CDateTimeAttr member (the map of operators)
CDateTimeCompOpMap* CDateTimeAttr::s_pcoOpMap = new CDateTimeCompOpMap;

// operators: returns string containing valid operators
const string& CDateTimeAttr::operators() const
{
	return s_pcoOpMap->operators();
}

// validOperator: returns true if operator exists, false otherwise
bool CDateTimeAttr::validOperator(const string& p_rcoOp) const
{
	return s_pcoOpMap->validOp(p_rcoOp);
}

// compOperation: returns a CompOperation class for given operator, first operand;
// throws InvalidOperator if operator not found
// throws InvalidValue if value is invalid
CompOperation* CDateTimeAttr::compOperation(const string& p_rcoOp, const string& p_rcoSecond)
	const throw(InvalidOperator, InvalidValue)
{
	return new CDateTimeCompOperation(s_pcoOpMap->operator[](p_rcoOp), DateTime(p_rcoSecond));
}


// Implementation of CEnumAttr (Enum attribute)

// OperatorsMap constructor for CEnumCompOp (Enum operator) type
template <> OperatorsMap<CEnumCompOp>::OperatorsMap()
{
	registerOp(new CEnumCompOp(g_coEQUAL, g_coEQUAL_Symbol, &Enum::Item::operator ==));
	registerOp(new CEnumCompOp(g_coNOT_EQUAL, g_coNOT_EQUAL_Symbol, &Enum::Item::operator !=));
	m_coOps = g_coEQUAL + ", " + g_coNOT_EQUAL;
}

// map of integer operators
class CEnumCompOpMap : public OperatorsMap<CEnumCompOp> {};

// initialise static CEnumAttr member (the map of operators)
CEnumCompOpMap* CEnumAttr::s_pcoOpMap = new CEnumCompOpMap;

// operators: returns string containing valid operators
const string& CEnumAttr::operators() const
{
	return s_pcoOpMap->operators();
}

// validOperator: returns true if operator exists, false otherwise
bool CEnumAttr::validOperator(const string& p_rcoOp) const
{
	return s_pcoOpMap->validOp(p_rcoOp);
}

// compOperation: returns a CompOperation class for given operator, first operand;
// throws InvalidOperator if operator not found
// throws InvalidValue if value is invalid
CompOperation* CEnumAttr::compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
	throw(InvalidOperator, InvalidValue)
{
	return new CEnumCompOperation(s_pcoOpMap->operator[](p_rcoOp), Enum::Item(p_rcoSecond));
}

// typeValues: returns string containing valid values of the attribute type
const string& CEnumAttr::typeValues() const
{
	static const string coEmpty = "";
	try
	{
		return Enum::values(m_coEnumType);
	}
	catch (InvalidValue& rcoEx)
	{
		return coEmpty;
	}
}


// Implementation of CTopicAttr (Topic attribute)

const string CTopicAttr::s_coTypeName = "topic";

// OperatorsMap constructor for CTopicCompOp (Topic operator) type
template <> OperatorsMap<CTopicCompOp>::OperatorsMap()
{
	registerOp(new CTopicCompOp(g_coEQUAL, g_coEQUAL_Symbol, &Topic::Item::isA));
	registerOp(new CTopicCompOp(g_coNOT_EQUAL, g_coNOT_EQUAL_Symbol, &Topic::Item::isNotA));
	m_coOps = g_coEQUAL + ", " + g_coNOT_EQUAL;
}

// map of integer operators
class CTopicCompOpMap : public OperatorsMap<CTopicCompOp> {};

// initialise static CTopicAttr member (the map of operators)
CTopicCompOpMap* CTopicAttr::s_pcoOpMap = new CTopicCompOpMap;

// operators: returns string containing valid operators
const string& CTopicAttr::operators() const
{
	return s_pcoOpMap->operators();
}

// validOperator: returns true if operator exists, false otherwise
bool CTopicAttr::validOperator(const string& p_rcoOp) const
{
	return s_pcoOpMap->validOp(p_rcoOp);
}

// compOperation: returns a CompOperation class for given operator, first operand;
// throws InvalidOperator if operator not found
// throws InvalidValue if value is invalid
CompOperation* CTopicAttr::compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
	throw(InvalidOperator, InvalidValue)
{
	return new CTopicCompOperation(s_pcoOpMap->operator[](p_rcoOp), Topic::Item(p_rcoSecond));
}
