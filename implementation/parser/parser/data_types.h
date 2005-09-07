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

Declares data types: Integer, String, Switch, Date, Time, DateTime, Enum, Topic

******************************************************************************/

#ifndef _CMS_DATA_TYPES
#define _CMS_DATA_TYPES

#include <time.h>

#include <string>
#include <stdexcept>
#include <list>
#include <map>

#include "globals.h"
#include "mutex.h"
#include "cms_types.h"

using std::string;
using std::pair;
using std::list;
using std::bad_alloc;
using std::map;

// Integer data type; wrapper around int
class Integer
{
public:
	// default constructor
	Integer(lint p_nVal = 0) : m_nValue(p_nVal) {}

	// conversion from string
	Integer(const string& p_rcoVal) throw(InvalidValue) { m_nValue = string2int(p_rcoVal); }

	// string conversion operator
	operator string() const;

	// int conversion operator
	operator lint() const { return m_nValue; }

	// comparison operators
	bool operator ==(const Integer& p_rcoOther) const
	{ return m_nValue == p_rcoOther.m_nValue; }

	bool operator !=(const Integer& p_rcoOther) const
	{ return m_nValue != p_rcoOther.m_nValue; }

	bool operator >(const Integer& p_rcoOther) const
	{ return m_nValue > p_rcoOther.m_nValue; }

	bool operator >=(const Integer& p_rcoOther) const
	{ return m_nValue >= p_rcoOther.m_nValue; }

	bool operator <(const Integer& p_rcoOther) const
	{ return m_nValue < p_rcoOther.m_nValue; }

	bool operator <=(const Integer& p_rcoOther) const
	{ return m_nValue <= p_rcoOther.m_nValue; }

	// string2int: converts string to int; throws InvalidValue if unable to convert
	static lint string2int(const string&) throw(InvalidValue);

private:
	lint m_nValue;
};


// String data type; wrapper around string
class String
{
public:
	// default constructor
	String(const string& p_rcoVal = "") : m_coValue(p_rcoVal) {}

	// int conversion operator
	operator string() const { return m_coValue; }

	bool operator ==(const String& p_rcoOther) const
	{ return m_coValue == p_rcoOther.m_coValue; }

	bool operator !=(const String& p_rcoOther) const
	{ return m_coValue != p_rcoOther.m_coValue; }

	bool operator >(const String& p_rcoOther) const
	{ return m_coValue > p_rcoOther.m_coValue; }

	bool operator >=(const String& p_rcoOther) const
	{ return m_coValue >= p_rcoOther.m_coValue; }

	bool operator <(const String& p_rcoOther) const
	{ return m_coValue < p_rcoOther.m_coValue; }

	bool operator <=(const String& p_rcoOther) const
	{ return m_coValue <= p_rcoOther.m_coValue; }

	bool case_equal(const String& p_rcoOther) const
	{ return case_comp(m_coValue, p_rcoOther.m_coValue) == 0; }

	bool case_not_equal(const String& p_rcoOther) const
	{ return case_comp(m_coValue, p_rcoOther.m_coValue) != 0; }

	bool case_greater(const String& p_rcoOther) const
	{ return case_comp(m_coValue, p_rcoOther.m_coValue) > 0; }

	bool case_greater_equal(const String& p_rcoOther) const
	{ return case_comp(m_coValue, p_rcoOther.m_coValue) >= 0; }

	bool case_less(const String& p_rcoOther) const
	{ return case_comp(m_coValue, p_rcoOther.m_coValue) < 0; }

	bool case_less_equal(const String& p_rcoOther) const
	{ return case_comp(m_coValue, p_rcoOther.m_coValue) <= 0; }

public:
	const static string emptyString;

private:
	string m_coValue;
};


// Switch data type
class Switch
{
public:
	typedef enum { OFF = 0, ON = 1 } SwitchVal;

	// default constructor
	Switch(SwitchVal p_nVal = OFF) : m_nValue(p_nVal) {}

	// conversion from string
	Switch(const string& p_rcoVal) throw(InvalidValue) { m_nValue = string2SwitchVal(p_rcoVal); }

	// string conversion operator
	operator string() const { return valName(); }

	// SwitchVal conversion operator
	operator SwitchVal() const { return m_nValue; }

	bool operator ==(const Switch& p_rcoOther) const
	{ return m_nValue == p_rcoOther.m_nValue; }

	bool operator !=(const Switch& p_rcoOther) const
	{ return m_nValue != p_rcoOther.m_nValue; }

	static const string& valName(SwitchVal p_rnVal) { return s_coValName[(int)p_rnVal]; }

	const string& valName() const { return valName(m_nValue); }

	static SwitchVal string2SwitchVal(const string&) throw(InvalidValue);

private:
	SwitchVal m_nValue;

	static string s_coValName[2];
};


// Date data type
class Date
{
public:
	// instantiation from string; date must be of format: "yyyy" + p_coSep + "mm" + p_coSep + "dd"
	Date(const string&, string p_coSep = "-") throw(InvalidValue);

	// instantiation from struct tm
	Date(const struct tm& p_tm, string p_coSep = "-") throw(InvalidValue)
		: m_nYear(p_tm.tm_year+1900), m_nMon(p_tm.tm_mon + 1), m_nMDay(p_tm.tm_mday),
		  m_coSep(p_coSep) { Validate(); }

	Date(int p_nYear, int p_nMon, int p_nMDay, string p_coSep = "-") throw(InvalidValue)
		: m_nYear(p_nYear), m_nMon(p_nMon), m_nMDay(p_nMDay), m_coSep(p_coSep) { Validate(); }

	// virtual destructor
	virtual ~Date() {}

	// access members
	int year() const { return m_nYear; }
	int mon() const { return m_nMon; }
	int mday() const { return m_nMDay; }

	// string conversion operator; returns the date in format: "yyyy" + m_coSep + "mm"
	// + m_coSep + "dd"
	virtual operator string() const;

	// struct tm conversion operator
	virtual operator struct tm() const;

	bool operator ==(const Date& p_rcoOther) const;

	bool operator !=(const Date& p_rcoOther) const { return ! (*this == p_rcoOther); }

	bool operator >(const Date& p_rcoOther) const;

	bool operator >=(const Date& p_rcoOther) const
	{ return *this > p_rcoOther || *this == p_rcoOther; }

	bool operator <(const Date& p_rcoOther) const;

	bool operator <=(const Date& p_rcoOther) const
	{ return *this < p_rcoOther || *this == p_rcoOther; }

private:
	void Validate() throw(InvalidValue);

	int m_nYear;
	int m_nMon;
	int m_nMDay;

	mutable string m_coSep;
};

// some Date inline methods
inline bool Date::operator ==(const Date& p_rcoOther) const
{
	return m_nYear == p_rcoOther.m_nYear && m_nMon == p_rcoOther.m_nMon
	       && m_nMDay == p_rcoOther.m_nMDay;
}

inline bool Date::operator >(const Date& p_rcoOther) const
{
	return m_nYear > p_rcoOther.m_nYear
	       || (m_nYear == p_rcoOther.m_nYear && m_nMon > p_rcoOther.m_nMon)
	       || (m_nYear == p_rcoOther.m_nYear && m_nMon == p_rcoOther.m_nMon
	           && m_nMDay > p_rcoOther.m_nMDay);
}

inline bool Date::operator <(const Date& p_rcoOther) const
{
	return m_nYear < p_rcoOther.m_nYear
	       || (m_nYear == p_rcoOther.m_nYear && m_nMon < p_rcoOther.m_nMon)
	       || (m_nYear == p_rcoOther.m_nYear && m_nMon == p_rcoOther.m_nMon
	           && m_nMDay < p_rcoOther.m_nMDay);
}


// Time data type
class Time
{
public:
	// conversion from string; time must be of format: "hh" + p_coSep + "mm" + p_coSep + "ss"
	Time(const string&, string p_coSep = ":") throw(InvalidValue);

	// instantiation from struct tm
	Time(const struct tm& p_tm, string p_coSep = ":") throw(InvalidValue)
		: m_nHour(p_tm.tm_hour), m_nMin(p_tm.tm_min), m_nSec(p_tm.tm_sec), m_coSep(p_coSep)
	{ Validate(); }

	Time(int p_nHour, int p_nMin, int p_nSec, string p_coSep = ":") throw(InvalidValue)
		: m_nHour(p_nHour), m_nMin(p_nMin), m_nSec(p_nSec), m_coSep(p_coSep) { Validate(); }

	// virtual destructor
	virtual ~Time() {}

	// access members
	int hour() const { return m_nHour; }
	int min() const { return m_nMin; }
	int sec() const { return m_nSec; }

	// string conversion operator; returns the time in format: "hh" + m_coSep + "mm" + m_coSep
	// + "ss"
	virtual operator string() const;

	// struct tm conversion operator
	virtual operator struct tm() const;

	bool operator ==(const Time& p_rcoOther) const;

	bool operator !=(const Time& p_rcoOther) const { return ! (*this == p_rcoOther); }

	bool operator >(const Time& p_rcoOther) const;

	bool operator >=(const Time& p_rcoOther) const
	{ return *this > p_rcoOther || *this == p_rcoOther; }

	bool operator <(const Time& p_rcoOther) const;

	bool operator <=(const Time& p_rcoOther) const
	{ return *this < p_rcoOther || *this == p_rcoOther; }

private:
	void Validate() throw(InvalidValue);

	int m_nHour;
	int m_nMin;
	int m_nSec;

	mutable string m_coSep;
};

// some Time inline methods
inline bool Time::operator ==(const Time& p_rcoOther) const
{
	return m_nHour == p_rcoOther.m_nHour
	       && m_nMin == p_rcoOther.m_nMin
	       && m_nSec == p_rcoOther.m_nSec;
}

inline bool Time::operator >(const Time& p_rcoOther) const
{
	return m_nHour > p_rcoOther.m_nHour
	       || (m_nHour == p_rcoOther.m_nHour && m_nMin > p_rcoOther.m_nMin)
	       || (m_nHour == p_rcoOther.m_nHour && m_nMin == p_rcoOther.m_nMin
	           && m_nSec > p_rcoOther.m_nSec);
}

inline bool Time::operator <(const Time& p_rcoOther) const
{
	return m_nHour < p_rcoOther.m_nHour
	       || (m_nHour == p_rcoOther.m_nHour && m_nMin < p_rcoOther.m_nMin)
	       || (m_nHour == p_rcoOther.m_nHour && m_nMin == p_rcoOther.m_nMin
	           && m_nSec < p_rcoOther.m_nSec);
}


// DateTime data type
class DateTime : public Date, public Time
{
public:
	// conversion from string; date must be of format:
	// "yyyy" + p_coSep + "mm" + p_coSep + "dd" + " " + "hh" + m_coSep + "mm" + m_coSep + "ss"
	DateTime(const string&, string p_coDateSep = "-", string p_coTimeSep = ":")
		throw(InvalidValue);

	// instantiation from struct tm
	DateTime(const struct tm& p_tm, string p_coDateSep = "-", string p_coTimeSep = ":")
		throw(InvalidValue)
		: Date(p_tm, p_coDateSep), Time(p_tm, p_coTimeSep) {}

	DateTime(int p_nYear, int p_nMon, int p_nMDay, int p_nHour, int p_nMin, int p_nSec,
	         string p_coDateSep = "-", string p_coTimeSep = ":") throw(InvalidValue)
		: Date(p_nYear, p_nMon, p_nMDay, p_coDateSep), Time(p_nHour, p_nMin, p_nSec, p_coTimeSep) {}

	// virtual destructor
	virtual ~DateTime() {}

	// string conversion operator; returns the time in format:
	// "yyyy" + p_coSep + "mm" + p_coSep + "dd" + " " + "hh" + m_coSep + "mm" + m_coSep + "ss"
	virtual operator string() const
	{ return Date::operator string() + " " + Time::operator string(); }

	// struct tm conversion operator
	virtual operator struct tm() const;

	bool operator ==(const DateTime& p_rcoOther) const;

	bool operator !=(const DateTime& p_rcoOther) const { return ! (*this == p_rcoOther); }

	bool operator >(const DateTime& p_rcoOther) const;

	bool operator >=(const DateTime& p_rcoOther) const
	{ return *this > p_rcoOther || *this == p_rcoOther; }

	bool operator <(const DateTime& p_rcoOther) const;

	bool operator <=(const DateTime& p_rcoOther) const
	{ return *this < p_rcoOther || *this == p_rcoOther; }
};

// some DateTime inline methods
inline bool DateTime::operator ==(const DateTime& p_rcoOther) const
{
	return Date::operator ==(p_rcoOther)
	       && Time::operator ==(p_rcoOther);
}

inline bool DateTime::operator >(const DateTime& p_rcoOther) const
{
	return Date::operator >(p_rcoOther)
	       || (Date::operator ==(p_rcoOther) && Time::operator >(p_rcoOther));
}

inline bool DateTime::operator <(const DateTime& p_rcoOther) const
{
	return Date::operator <(p_rcoOther)
	       || (Date::operator ==(p_rcoOther) && Time::operator <(p_rcoOther));
}


class CEnumMap;

// Enum data type
class Enum
{
public:
	// Enum item
	class Item
	{
		friend class Enum;

	public:
		// instantiation from string
		Item(const string& p_rcoVal) throw(InvalidValue);

		// conversion to string
		operator string() const { return m_pcoEnum->name() + ":" + name(); }

		// item name
		const string& name() const { return m_coItem; }

		// item value
		lint value() const { return m_pcoEnum->itemValue(m_coItem); }

		// comparison operator
		bool operator ==(const Item& p_rcoVal) const
		{
			return m_coItem == p_rcoVal.m_coItem
			       && m_pcoEnum->name() == p_rcoVal.m_pcoEnum->name();
		}

		// comparison operator
		bool operator !=(const Item& p_rcoVal) const { return ! (*this == p_rcoVal); }

	private:
		// constructor
		Item(const string& p_coVal, const Enum& p_coEnum)
			: m_coItem(p_coVal), m_pcoEnum(&p_coEnum) {}

	private:
		string m_coItem;
		const Enum* m_pcoEnum;
	};

public:
	// Enum constructor: initialise it from a string (name) and a list of pair values; the list
	// must be ordered by the data type: lint; if the data value is -1 it is automatically
	// generated; throws InvalidValue if list contains two or more pairs having the same key or
	// is not ordered
	Enum(const string&, const list<pair<string, lint> >&) throw(InvalidValue, bad_alloc);

	// returns an item identified by name; throws InvalidValue if invalid item name
	Item item(const string& p_rcoVal) const throw(InvalidValue);

	// returns the value of an item identified by name; throws InvalidValue if invalid item name
	lint itemValue(const string& p_rcoVal) const throw(InvalidValue);

	// name: return enum type name
	const string& name() const { return m_coName; }

	// values: return enum values
	const string& values() const throw(InvalidValue);

	// values: return enum values of enum type
	static const string& values(const string& p_rcoEnum) throw(InvalidValue);

	// enumObj: return enum object of type p_rcoEnum
	static Enum* enumObj(const string& p_rcoEnum) throw(InvalidValue);

	// isValid: return true if enum type exists
	static bool isValid(const string& p_rcoEnum);

	// registerEnum: add enum to enum types
	static void registerEnum(const string&, const list<pair<string, lint> >&)
		throw(InvalidValue, bad_alloc);

private:
	Enum(const string& p_rcoEnum) : m_coName(p_rcoEnum) {}
	static void initMap();

private:
	string m_coName;
	static CEnumMap* s_pcoEnums;
};


typedef map<string, string> CStringMap;
class CTopicMap;
class CTopicIdTable;
class CTopicNameTable;

// Topic data type
class Topic
{
public:
	class Item
	{
		friend class Topic;

	public:
		// instantiation from string
		Item(const string& p_rcoVal) throw(InvalidValue) { *this = Topic::item(p_rcoVal); }

		// copy-constructor
		Item(const Item& o) : m_nId(o.m_nId), m_coLanguage(o.m_coLanguage) {}

		bool valid() const { return Topic::isValid(m_nId); }

		// name: return name of the topic
		const string& translation(const string& p_rcoLang) const
		{ return Topic::topic(m_nId)->name(p_rcoLang); }

		// id: return numerical identifier of the topic
		lint id() const
		{ return Topic::topic(m_nId)->id(); }

		// value: return string containing topic value: "name:language"
		string value() const
		{ return Topic::topic(m_nId)->strValue(m_coLanguage); }

		// parent: return name of parent topic (empty string if no parent)
		const string& parent() const
		{ return Topic::topic(m_nId)->parentName(m_coLanguage); }

		// children: return string containing children names
		string children(bool p_nLang = false) const
		{ return Topic::topic(m_nId)->childrenNames(p_nLang ? m_coLanguage : ""); }

		// operators

		// conversion to string
		operator string() const { return value(); }

		// assignment operator
		const Item& operator =(const Item& o)
		{
			m_nId = o.m_nId;
			m_coLanguage = o.m_coLanguage;
			return *this;
		}

		// equal operator
		bool operator ==(const Item& o) const
		{ return m_nId == o.m_nId && m_coLanguage == o.m_coLanguage; }

		// not equal operator
		bool operator !=(const Item& o) const { return !(*this == o); }

		bool isA(const Item& o) const { return topic(m_nId)->isA(o.m_nId); }

		bool isNotA(const Item& o) const { return (!isA(o)); }

	private:
		Item() {};

		Item(const Topic* p_pcoTopic, const string& p_rcoLang)
			: m_nId(p_pcoTopic->id()), m_coLanguage(p_rcoLang) {}

	private:
		lint m_nId;
		string m_coLanguage;
	};

	// destructor
	~Topic();

	// name: return name of the topic in the given language
	const string& name(const string& p_rcoLang) const;

	// value: return string containing topic value: "name:language"
	string strValue(const string& p_rcoLang) const;

	// id: return numerical identifier of the topic
	lint id() const { return m_nTopicId; }

	// parent: return pointer to parent const topic
	const Topic* parent() const { return m_pcoParent; }

	// parentName: return name of parent topic (empty string if no parent)
	const string& parentName(const string& p_rcoLang) const
	{ return m_pcoParent ? m_pcoParent->name(p_rcoLang) : empty_string; }

	// isRoot: return true if topic is root (has no parent)
	bool isRoot() const { return m_pcoParent == NULL; }

	bool isA(lint) const;

	// childrenNames: return string containing children names
	string childrenNames(const string&) const;

	bool updated() const { return m_bUpdated; }

	void updated(bool p_bUpdated) const { m_bUpdated = p_bUpdated; }

	// static methods

	// values: return all valid topic values
	static const string& values();

	// isValid: returns true if topic id is valid
	static bool isValid(lint p_nId);

	// isValid: returns true if topic value (name:lang) is valid
	static bool isValid(const string& p_rcoValue);

	// isValid: returns true if topic name:language is valid
	static bool isValid(const string& p_rcoName, const string& p_rcoLang);

	// item: returns Item object
	static Item item(const string& p_rcoValue) throw(InvalidValue);

	// topic: return pointer to const topic identified by id
	static const Topic* topic(lint p_nId);

	// topic: return pointer to const topic identified by value (name:lang)
	static const Topic* topic(const string& p_rcoValue);

	// topic: return pointer to const topic identified by name and name language
	static const Topic* topic(const string& p_rcoName, const string& p_rcoLang);

	static void setUpdated(bool p_bUpdated);

	static void clearInvalid();

	static void setNames(const CStringMap&, lint);

	static const Topic* setTopic(const string& p_rcoTopic, lint p_nId,
	                             lint p_nParentId = -1);

	static const Topic* setTopic(const string& p_rcoName, const string& p_rcoLang, lint p_nId,
	                             lint p_nParentId = -1);

	static bool valuesChanged() { return s_bValuesChanged; }

private:
	// static private methods
	static void addTranslation(Topic* p_pcoTopic, const string& p_rcoName,
	                           const string& p_rcoLang);

	static void delTranslation(Topic* p_pcoTopic, const string& p_rcoLang);

private:
	// constructor
	Topic(const string& p_rcoName, const string& p_rcoLang, lint p_nId, Topic* p_pcoParent);

	// forbid instantiation from other topic object
	Topic(const Topic&);

	// forbid assignment
	const Topic& operator =(const Topic& p_rcoOther);

private:
	CStringMap m_coNames;		// topic names (language:name)
	lint m_nTopicId;		// topic numeric identifier
	Topic* m_pcoParent;			// pointer to parent topic
	CTopicMap* m_pcoChildren;	// pointer to children topics map (subtopics)

	mutable bool m_bUpdated;
	mutable bool m_bValidNames;
	mutable string m_coNamesStr;

	static CMutex s_coOpMutex;
	static bool s_bValidValues;
	static string s_coValues;
	static bool s_bValuesChanged;
	static CTopicIdTable* s_pcoIdTopics;		// table of all the topics (search by id)
	static CTopicNameTable* s_pcoNameTopics;	// table of all the topics (search by name)
	static string empty_string;

	friend class CTopicMap;
	friend class CTopicIdTable;
	friend class CTopicNameTable;
};

// inline methods

// value: return string containing topic value: "name:language"
inline string Topic::strValue(const string& p_rcoLang) const
{
	string val;
	if (p_rcoLang == "")
		return val;
	val = name(p_rcoLang);
	if (val != "")
		val += ":" + p_rcoLang;
	return val;
}

inline bool Topic::isA(lint p_nId) const
{
	if (p_nId == m_nTopicId)
		return true;
	if (m_pcoParent)
		return m_pcoParent->isA(p_nId);
	return false;
}

inline const Topic* Topic::setTopic(const string& p_rcoTopic, lint p_nId,
                                    lint p_nParentId)
{
	string::size_type nSplit = p_rcoTopic.find(':');
	string coName = p_rcoTopic.substr(0, nSplit);
	string coLang = p_rcoTopic.substr(nSplit + 1, p_rcoTopic.length() - nSplit - 1);
	return setTopic(coName, coLang, p_nId, p_nParentId);
}

#endif
