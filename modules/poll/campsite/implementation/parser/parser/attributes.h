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

Defines the attribute classes of Integer, String, Switch, Date, Time, DateTime
and Enum::Item types

******************************************************************************/

#ifndef _CMS_ATTRIBUTES
#define _CMS_ATTRIBUTES

#include "data_types.h"
#include "atoms.h"

class CIntegerCompOpMap;

// CIntegerAttr: attribute of Integer type
class CIntegerAttr : public CAttribute
{
public:
	// constructor
	CIntegerAttr(const string& p_rcoAttr, const string& p_rcoDBFld = string(""),
	             TAttrClass p_nCls = CMS_NORMAL_ATTR)
		: CAttribute(p_rcoAttr, p_rcoDBFld, p_nCls) {}

	// dataType: returns the data type of attribute
	virtual TDataType dataType() const { return CMS_DT_INTEGER; }

	// typeName: returns the attribute's data type name
	virtual const string& typeName() const;

	// typeValues: returns string containing valid values of the attribute type
	virtual const string& typeValues() const;

	// operators: returns string containing valid operators
	const string& operators() const;

	// return pointer to new object equal to this
	virtual CIntegerAttr* clone() const { return new CIntegerAttr(*this); }

	// validOperator: returns true if operator exists, false otherwise
	virtual bool validOperator(const string& p_rcoOp) const;

	// validValue: returns true if value is valid, false otherwise
	virtual bool validValue(const string& p_rcoVal) const;

	// compOperation: returns a CompOperation class for given operator, second operand;
	// throws InvalidOperator if operator not found
	// throws InvalidValue if value is invalid
	virtual CompOperation* compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
		throw(InvalidOperator, InvalidValue);

private:
	static CIntegerCompOpMap* s_pcoOpMap;
	static const string s_coTypeName;
	static const string s_coTypeValues;
};

// typeName: returns the attribute's data type name
inline const string& CIntegerAttr::typeName() const
{
	return s_coTypeName;
}

// typeValues: returns string containing valid values of the attribute type
inline const string& CIntegerAttr::typeValues() const
{
	return s_coTypeValues;
}

// validValue: returns true if value is valid, false otherwise
inline bool CIntegerAttr::validValue(const string& p_rcoVal) const
{
	try
	{
		Integer i(p_rcoVal);
		i.operator long int();	// just to remove the unused variable warning
		return true;
	}
	catch (InvalidValue& rcoEx)
	{
		return false;
	}
}


class CStringCompOpMap;

// CStringAttr: attribute of String type
class CStringAttr : public CAttribute
{
public:
	// constructor
	CStringAttr(const string& p_rcoAttr, const string& p_rcoDBFld = string(""),
	             TAttrClass p_nCls = CMS_NORMAL_ATTR)
		: CAttribute(p_rcoAttr, p_rcoDBFld, p_nCls) {}

	// dataType: returns the data type of attribute
	virtual TDataType dataType() const { return CMS_DT_STRING; }

	// typeName: returns the attribute's data type name
	virtual const string& typeName() const;

	// typeValues: returns string containing valid values of the attribute type
	virtual const string& typeValues() const;

	// operators: returns string containing valid operators
	const string& operators() const;

	// return pointer to new object equal to this
	virtual CStringAttr* clone() const { return new CStringAttr(*this); }

	// validOperator: returns true if operator exists, false otherwise
	virtual bool validOperator(const string& p_rcoOp) const;

	// validValue: returns true if value is valid, false otherwise
	virtual bool validValue(const string& p_rcoVal) const { return true; }

	// compOperation: returns a CompOperation class for given operator, second operand;
	// throws InvalidOperator if operator not found
	// throws InvalidValue if value is invalid
	virtual CompOperation* compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
		throw(InvalidOperator, InvalidValue);

private:
	static CStringCompOpMap* s_pcoOpMap;
	static const string s_coTypeName;
	static const string s_coTypeValues;
};

// typeName: returns the attribute's data type name
inline const string& CStringAttr::typeName() const
{
	return s_coTypeName;
}

// typeValues: returns string containing valid values of the attribute type
inline const string& CStringAttr::typeValues() const
{
	return s_coTypeValues;
}


class CSwitchCompOpMap;

// CSwitchAttr: attribute of Switch type
class CSwitchAttr : public CAttribute
{
public:
	// constructor
	CSwitchAttr(const string& p_rcoAttr, const string& p_rcoDBFld = string(""),
	             TAttrClass p_nCls = CMS_NORMAL_ATTR)
		: CAttribute(p_rcoAttr, p_rcoDBFld, p_nCls) {}

	// dataType: returns the data type of attribute
	virtual TDataType dataType() const { return CMS_DT_SWITCH; }

	// typeName: returns the attribute's data type name
	virtual const string& typeName() const;

	// typeValues: returns string containing valid values of the attribute type
	virtual const string& typeValues() const;

	// operators: returns string containing valid operators
	const string& operators() const;

	// return pointer to new object equal to this
	virtual CSwitchAttr* clone() const { return new CSwitchAttr(*this); }

	// validOperator: returns true if operator exists, false otherwise
	virtual bool validOperator(const string& p_rcoOp) const;

	// validValue: returns true if value is valid, false otherwise
	virtual bool validValue(const string& p_rcoVal) const;

	// compOperation: returns a CompOperation class for given operator, second operand;
	// throws InvalidOperator if operator not found
	// throws InvalidValue if value is invalid
	virtual CompOperation* compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
		throw(InvalidOperator, InvalidValue);

private:
	static CSwitchCompOpMap* s_pcoOpMap;
	static const string s_coTypeName;
	static const string s_coTypeValues;
};

// typeName: returns the attribute's data type name
inline const string& CSwitchAttr::typeName() const
{
	return s_coTypeName;
}

// typeValues: returns string containing valid values of the attribute type
inline const string& CSwitchAttr::typeValues() const
{
	return s_coTypeValues;
}

// validValue: returns true if value is valid, false otherwise
inline bool CSwitchAttr::validValue(const string& p_rcoVal) const
{
	try
	{
		Switch sw(p_rcoVal);
		sw.operator Switch::SwitchVal();	// just to remove the unused variable warning
		return true;
	}
	catch (InvalidValue& rcoEx)
	{
		return false;
	}
}


class CDateCompOpMap;

// CDateAttr: attribute of Date type
class CDateAttr : public CAttribute
{
public:
	// constructor
	CDateAttr(const string& p_rcoAttr, const string& p_rcoDBFld = string(""),
	             TAttrClass p_nCls = CMS_NORMAL_ATTR)
		: CAttribute(p_rcoAttr, p_rcoDBFld, p_nCls) {}

	// dataType: returns the data type of attribute
	virtual TDataType dataType() const { return CMS_DT_DATE; }

	// typeName: returns the attribute's data type name
	virtual const string& typeName() const;

	// typeValues: returns string containing valid values of the attribute type
	virtual const string& typeValues() const;

	// operators: returns string containing valid operators
	const string& operators() const;

	// return pointer to new object equal to this
	virtual CDateAttr* clone() const { return new CDateAttr(*this); }

	// validOperator: returns true if operator exists, false otherwise
	virtual bool validOperator(const string& p_rcoOp) const;

	// validValue: returns true if value is valid, false otherwise
	virtual bool validValue(const string& p_rcoVal) const;

	// compOperation: returns a CompOperation class for given operator, second operand;
	// throws InvalidOperator if operator not found
	// throws InvalidValue if value is invalid
	virtual CompOperation* compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
		throw(InvalidOperator, InvalidValue);

private:
	static CDateCompOpMap* s_pcoOpMap;
	static const string s_coTypeName;
	static const string s_coTypeValues;
};

// typeName: returns the attribute's data type name
inline const string& CDateAttr::typeName() const
{
	return s_coTypeName;
}

// typeValues: returns string containing valid values of the attribute type
inline const string& CDateAttr::typeValues() const
{
	return s_coTypeValues;
}

// validValue: returns true if value is valid, false otherwise
inline bool CDateAttr::validValue(const string& p_rcoVal) const
{
	try
	{
		Date d(p_rcoVal);
		return true;
	}
	catch (InvalidValue& rcoEx)
	{
		return false;
	}
}


class CTimeCompOpMap;

// CTimeAttr: attribute of Time type
class CTimeAttr : public CAttribute
{
public:
	// constructor
	CTimeAttr(const string& p_rcoAttr, const string& p_rcoDBFld = string(""),
	             TAttrClass p_nCls = CMS_NORMAL_ATTR)
		: CAttribute(p_rcoAttr, p_rcoDBFld, p_nCls) {}

	// dataType: returns the data type of attribute
	virtual TDataType dataType() const { return CMS_DT_TIME; }

	// typeName: returns the attribute's data type name
	virtual const string& typeName() const;

	// typeValues: returns string containing valid values of the attribute type
	virtual const string& typeValues() const;

	// operators: returns string containing valid operators
	const string& operators() const;

	// return pointer to new object equal to this
	virtual CTimeAttr* clone() const { return new CTimeAttr(*this); }

	// validOperator: returns true if operator exists, false otherwise
	virtual bool validOperator(const string& p_rcoOp) const;

	// validValue: returns true if value is valid, false otherwise
	virtual bool validValue(const string& p_rcoVal) const;

	// compOperation: returns a CompOperation class for given operator, second operand;
	// throws InvalidOperator if operator not found
	// throws InvalidValue if value is invalid
	virtual CompOperation* compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
		throw(InvalidOperator, InvalidValue);

private:
	static CTimeCompOpMap* s_pcoOpMap;
	static const string s_coTypeName;
	static const string s_coTypeValues;
};

// typeName: returns the attribute's data type name
inline const string& CTimeAttr::typeName() const
{
	return s_coTypeName;
}

// typeValues: returns string containing valid values of the attribute type
inline const string& CTimeAttr::typeValues() const
{
	return s_coTypeValues;
}

// validValue: returns true if value is valid, false otherwise
inline bool CTimeAttr::validValue(const string& p_rcoVal) const
{
	try
	{
		Time t(p_rcoVal);
		return true;
	}
	catch (InvalidValue& rcoEx)
	{
		return false;
	}
}


class CDateTimeCompOpMap;

// CDateTimeAttr: attribute of DateTime type
class CDateTimeAttr : public CAttribute
{
public:
	// constructor
	CDateTimeAttr(const string& p_rcoAttr, const string& p_rcoDBFld = string(""),
	             TAttrClass p_nCls = CMS_NORMAL_ATTR)
		: CAttribute(p_rcoAttr, p_rcoDBFld, p_nCls) {}

	// dataType: returns the data type of attribute
	virtual TDataType dataType() const { return CMS_DT_DATETIME; }

	// typeName: returns the attribute's data type name
	virtual const string& typeName() const;

	// typeValues: returns string containing valid values of the attribute type
	virtual const string& typeValues() const;

	// operators: returns string containing valid operators
	const string& operators() const;

	// return pointer to new object equal to this
	virtual CDateTimeAttr* clone() const { return new CDateTimeAttr(*this); }

	// validOperator: returns true if operator exists, false otherwise
	virtual bool validOperator(const string& p_rcoOp) const;

	// validValue: returns true if value is valid, false otherwise
	virtual bool validValue(const string& p_rcoVal) const;

	// compOperation: returns a CompOperation class for given operator, second operand;
	// throws InvalidOperator if operator not found
	// throws InvalidValue if value is invalid
	virtual CompOperation* compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
		throw(InvalidOperator, InvalidValue);

private:
	static CDateTimeCompOpMap* s_pcoOpMap;
	static const string s_coTypeName;
	static const string s_coTypeValues;
};

// typeName: returns the attribute's data type name
inline const string& CDateTimeAttr::typeName() const
{
	return s_coTypeName;
}

// typeValues: returns string containing valid values of the attribute type
inline const string& CDateTimeAttr::typeValues() const
{
	return s_coTypeValues;
}

// validValue: returns true if value is valid, false otherwise
inline bool CDateTimeAttr::validValue(const string& p_rcoVal) const
{
	try
	{
		DateTime dt(p_rcoVal);
		return true;
	}
	catch (InvalidValue& rcoEx)
	{
		return false;
	}
}


class CEnumCompOpMap;

// CEnumAttr: attribute of DateTime type
class CEnumAttr : public CAttribute
{
public:
	// constructor
	CEnumAttr(const string& p_rcoAttr, const string& p_rcoEnumType,
	          const string& p_rcoDBFld = string(""), TAttrClass p_nCls = CMS_NORMAL_ATTR)
	          throw(InvalidValue)
		: CAttribute(p_rcoAttr, p_rcoDBFld,  p_nCls), m_coEnumType(p_rcoEnumType)
	{
		if (!Enum::isValid(p_rcoEnumType)) throw InvalidValue();
		m_coTypeName = p_rcoEnumType + " (enum)";
	}

	// dataType: returns the data type of attribute
	virtual TDataType dataType() const { return CMS_DT_ENUM; }

	// typeName: returns the attribute's data type name
	virtual const string& typeName() const { return m_coTypeName; }

	// typeValues: returns string containing valid values of the attribute type
	virtual const string& typeValues() const;

	// operators: returns string containing valid operators
	const string& operators() const;

	// return pointer to new object equal to this
	virtual CEnumAttr* clone() const { return new CEnumAttr(*this); }

	// validOperator: returns true if operator exists, false otherwise
	virtual bool validOperator(const string& p_rcoOp) const;

	// validValue: returns true if value is valid, false otherwise
	virtual bool validValue(const string& p_rcoVal) const;

	// compOperation: returns a CompOperation class for given operator, second operand;
	// throws InvalidOperator if operator not found
	// throws InvalidValue if value is invalid
	virtual CompOperation* compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
		throw(InvalidOperator, InvalidValue);

private:
	string m_coEnumType;
	string m_coTypeName;
	static CEnumCompOpMap* s_pcoOpMap;
};

// validValue: returns true if value is valid, false otherwise
inline bool CEnumAttr::validValue(const string& p_rcoVal) const
{
	try
	{
		Enum* pcoEnum = Enum::enumObj(m_coEnumType);
		pcoEnum->item(p_rcoVal);
		delete pcoEnum;
		return true;
	}
	catch (InvalidValue& rcoEx)
	{
		return false;
	}
}

class CTopicCompOpMap;

// CTopicAttr: attribute of DateTime type
class CTopicAttr : public CAttribute
{
public:
	// constructor
	CTopicAttr(const string& p_rcoAttr, const string& p_rcoDBFld = string(""),
	           TAttrClass p_nCls = CMS_NORMAL_ATTR) throw(InvalidValue)
		: CAttribute(p_rcoAttr, p_rcoDBFld,  p_nCls) {}

	// dataType: returns the data type of attribute
	virtual TDataType dataType() const { return CMS_DT_TOPIC; }

	// typeName: returns the attribute's data type name
	virtual const string& typeName() const { return s_coTypeName; }

	// typeValues: returns string containing valid values of the attribute type
	virtual const string& typeValues() const { return Topic::values(); }

	// operators: returns string containing valid operators
	const string& operators() const;

	// return pointer to new object equal to this
	virtual CTopicAttr* clone() const { return new CTopicAttr(*this); }

	// validOperator: returns true if operator exists, false otherwise
	virtual bool validOperator(const string& p_rcoOp) const;

	// validValue: returns true if value is valid, false otherwise
	virtual bool validValue(const string& p_rcoVal) const;

	// compOperation: returns a CompOperation class for given operator, second operand;
	// throws InvalidOperator if operator not found
	// throws InvalidValue if value is invalid
	virtual CompOperation* compOperation(const string& p_rcoOp, const string& p_rcoSecond) const
		throw(InvalidOperator, InvalidValue);

private:
	static CTopicCompOpMap* s_pcoOpMap;
	static const string s_coTypeName;
};

// validValue: returns true if value is valid, false otherwise
inline bool CTopicAttr::validValue(const string& p_rcoVal) const
{
	try
	{
		Topic::item(p_rcoVal);
		return true;
	}
	catch (InvalidValue& rcoEx)
	{
		return false;
	}
}

#endif
