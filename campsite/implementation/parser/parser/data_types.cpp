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

Implementation of data types: Integer, String, Switch, Date, Time, DateTime, Enum

******************************************************************************/

#include <stdio.h>
#include <stdlib.h>
#include <limits.h>

#include "data_types.h"
#include "auto_ptr.h"


// Integer implementation

// string conversion operator
Integer::operator string() const
{
	char pchBuf[20];
	
	sprintf(pchBuf, "%ld", m_nValue);
	return string(pchBuf);
}

// string2int: converts string to int; throws InvalidValue if unable to convert
lint Integer::string2int(const string& p_rcoVal) throw(InvalidValue)
{
	const char* pchStart = p_rcoVal.c_str();
	char* pchEnd;
	lint nRes = strtol(pchStart, &pchEnd, 10);
	if (nRes == LONG_MIN || nRes == LONG_MAX || pchEnd == pchStart || *pchEnd != '\0')
		throw InvalidValue();
	return nRes;
}


// String implementation
const string String::emptyString = "";


// Switch implementation

string Switch::s_coValName[2] = { "OFF", "ON" };

Switch::SwitchVal Switch::string2SwitchVal(const string& p_rcoVal) throw(InvalidValue)
{
	for (int i = 0; i < 2; i++)
		if (case_comp(p_rcoVal, s_coValName[i]) == 0)
			return (SwitchVal)i;
	throw InvalidValue();
}


// Date implementation

// instantiation from string; date must be of format: "yyyy" + p_coSep + "mm" + p_coSep + "dd"
Date::Date(const string& p_rcoDate, string p_coSep) throw(InvalidValue)
	: m_coSep(p_coSep)
{
	string::size_type nMon = p_rcoDate.find(p_coSep);
	string::size_type nMDay = p_rcoDate.find(p_coSep, nMon + 1);
	if (nMon == 0 || nMon == nMDay)
		throw InvalidValue();
	m_nYear = Integer::string2int(p_rcoDate.substr(0, nMon));
	m_nMon = Integer::string2int(p_rcoDate.substr(nMon + 1, nMDay - nMon - 1));
	m_nMDay = Integer::string2int(p_rcoDate.substr(nMDay + 1, p_rcoDate.length() - nMDay - 1));
	Validate();
}

// string conversion operator; returns the date in format: "yyyy" + m_coSep + "mm" + m_coSep + "dd"
Date::operator string() const
{
	return string(Integer(m_nYear))
	       + m_coSep + string(Integer(m_nMon))
	       + m_coSep + string(Integer(m_nMDay));
}

// struct tm conversion operator
Date::operator struct tm() const
{
	struct tm TM = { 0, 0, 0, m_nMDay, m_nMon - 1, m_nYear - 1900, 0, 0, 0 };
	return TM;
}

void Date::Validate() throw(InvalidValue)
{
	struct tm TM = { 0, 0, 0, m_nMDay, m_nMon - 1, m_nYear - 1900, 0, 0, 0 };
	if (mktime(&TM) == -1)
		throw InvalidValue();
	if (TM.tm_year != (m_nYear - 1900) || TM.tm_mon != (m_nMon - 1) || TM.tm_mday != m_nMDay)
		throw InvalidValue();
}


// Time implementation

// conversion from string
void Time::setTime(const string& p_rcoTime, string p_coSep) throw(InvalidValue)
{
	m_coSep = p_coSep;
	string::size_type nMin = p_rcoTime.find(p_coSep);
	string::size_type nSec = p_rcoTime.find(p_coSep, nMin + 1);
	if (nMin == string::npos || nMin == nSec)
	{
		throw InvalidValue();
	}
	m_nHour = Integer::string2int(p_rcoTime.substr(0, nMin));
	m_nMin = Integer::string2int(p_rcoTime.substr(nMin + 1, nSec - nMin - 1));
	m_nSec = Integer::string2int(p_rcoTime.substr(nSec + 1, p_rcoTime.length() - nSec - 1));
	Validate();
}

// string conversion operator
Time::operator string() const
{
	return string(Integer(m_nHour))
	       + m_coSep + string(Integer(m_nMin))
	       + m_coSep + string(Integer(m_nSec));
}

// struct tm conversion operator
Time::operator struct tm() const
{
	struct tm TM = { m_nSec, m_nMin, m_nHour, 1, 0, 0, 0, 0, 0 };
	return TM;
}

void Time::Validate() throw(InvalidValue)
{
	struct tm TM = { m_nSec, m_nMin, m_nHour, 1, 0, 2, 0, 0, 0 };
	if (mktime(&TM) == -1)
		throw InvalidValue();
	if (TM.tm_hour != m_nHour || TM.tm_min != m_nMin || TM.tm_sec != m_nSec)
		throw InvalidValue();
}


// DateTime implementation

// conversion from string
DateTime::DateTime(const string& p_rcoVal, string p_coDateSep, string p_coTimeSep)
	throw(InvalidValue)
	: Date(p_rcoVal.substr(0, p_rcoVal.find(" "))), Time("00:00:00")
{
	try {
		string::size_type nSpacePos = p_rcoVal.find(" ");
		if (nSpacePos != string::npos && p_rcoVal.find(p_coTimeSep) != string::npos)
		{
			Time::setTime(p_rcoVal.substr(nSpacePos + 1));
		}
	}
	catch (std::out_of_range &rcoEx) {
	}
}

// struct tm conversion operator
DateTime::operator struct tm() const
{
	struct tm TM = { sec(), min(), hour(), mday(), mon() - 1, year() + 1900, 0, 0, 0 };
	return TM;
}


// Enum implementation

// Enum::Item constructor: instantiation from string
Enum::Item::Item(const string& p_rcoVal) throw(InvalidValue)
{
	string::size_type nSep = p_rcoVal.find(":");
	if (m_pcoEnum->name() != p_rcoVal.substr(0, nSep))
		throw InvalidValue();
	m_coItem = p_rcoVal.substr(nSep + 1, p_rcoVal.length() - nSep - 1);
}

// CValuesMap: map used to store enum values
class CValuesMap : private map<string, lint>
{
public:
	// default constructor
	CValuesMap() : m_bValuesValid(true), m_coValues("") {}

	// insert: insert value in map
	void insert(const string& val, lint id);

	// erase: erase value from map
	void erase(const string& val);

	// id: return id associated to value
	lint id(const string& val) const throw(InvalidValue);

	// has: return true if value is in map
	bool has(const string& val) const;

	// values: return string containing values in the map
	const string& values() const;

private:
	mutable bool m_bValuesValid;
	mutable string m_coValues;
};

// insert: insert value in map
inline void CValuesMap::insert(const string& val, lint id)
{
	m_bValuesValid = false;
	this->operator [](val) = id;
}

// erase: erase value from map
inline void CValuesMap::erase(const string& val)
{
	iterator coIt = find(val);
	if (coIt == end())
		return;
	m_bValuesValid = false;
	map<string, lint>::erase(coIt);
}

// id: return id associated to value
inline lint CValuesMap::id(const string& val) const throw(InvalidValue)
{
	const_iterator coIt = find(val);
	if (coIt == end())
		throw InvalidValue();
	return (*coIt).second;
}

// has: return true if value is in map
inline bool CValuesMap::has(const string& val) const
{
	return find(val) != end();
}

// values: return string containing values in the map
const string& CValuesMap::values() const
{
	if (m_bValuesValid)
		return m_coValues;
	m_coValues = "";
	bool first = true;
	for (const_iterator coIt = begin(); coIt != end(); ++coIt)
	{
		if (first)
			first = false;
		else
			m_coValues += ", ";
		m_coValues += (*coIt).first;
	}
	m_bValuesValid = true;
	return m_coValues;
}


// CEnumMap: map of enum types; all enum types are registered here
class CEnumMap : private map<string, CValuesMap*>
{
public:
	// default constructor
	CEnumMap() {}

	// destructor
	~CEnumMap();

	// insert: insert enum type
	bool insert(const string&, CValuesMap*);

	// insert: insert enum type
	bool insert(const string& p_rcoEnum, const CValuesMap& p_rcoValMap)
	{ return insert(p_rcoEnum, new CValuesMap(p_rcoValMap)); }

	// erase: erase enum type
	void erase(const string& p_rcoEnum);

	// has: returns true if enum type is registered
	bool has(const string& p_rcoEnum) const;

	// valMap: returns values map of some enum type
	const CValuesMap* valMap(const string& p_rcoEnum) const throw(InvalidValue);

	// values: return string containig values of some enun type
	const string& values(const string&) const throw(InvalidValue);

private:
	// private copy-constructor, assign and compare operators: don't allow copying and
	// comparison
	CEnumMap(const CEnumMap&);

	const CEnumMap& operator =(const CEnumMap&);

	bool operator ==(const CEnumMap&);

	bool operator !=(const CEnumMap&);
};

// CEnumMap destructor
CEnumMap::~CEnumMap()
{
	for (iterator coIt = begin(); coIt != end(); coIt = begin())
	{
		delete (*coIt).second;
		(*coIt).second = NULL;
		map<string, CValuesMap*>::erase(coIt);
	}
}

// insert: insert enum type
bool CEnumMap::insert(const string& p_rcoEnum, CValuesMap* p_pcoValMap)
{
	if (p_pcoValMap == NULL)
		return false;
	iterator coIt = find(p_rcoEnum);
	if (coIt != end())
	{
		delete (*coIt).second;
		(*coIt).second = NULL;
	}
	this->operator[](p_rcoEnum) = p_pcoValMap;
	return true;
}

// erase: erase enum type
void CEnumMap::erase(const string& p_rcoEnum)
{
	iterator coIt = find(p_rcoEnum);
	if (coIt == end())
		return;
	delete (*coIt).second;
	(*coIt).second = NULL;
	map<string, CValuesMap*>::erase(coIt);
}

// has: returns true if enum type is registered
inline bool CEnumMap::has(const string& p_rcoEnum) const
{
	return find(p_rcoEnum) != end();
}

// valMap: returns values map of some enum type
inline const CValuesMap* CEnumMap::valMap(const string& p_rcoEnum) const throw(InvalidValue)
{
	const_iterator coIt = find(p_rcoEnum);
	if (coIt == end())
		throw InvalidValue();
	return (*coIt).second;
}

// values: return string containig values of some enun type
inline const string& CEnumMap::values(const string& p_rcoEnum) const throw(InvalidValue)
{
	const_iterator coIt = find(p_rcoEnum);
	if (coIt == end())
		throw InvalidValue();
	return (*coIt).second->values();
}

// initialise enums map
CEnumMap* Enum::s_pcoEnums = NULL;

void Enum::initMap()
{
	if (s_pcoEnums == NULL)
	{
		s_pcoEnums = new CEnumMap;
	}
}

// Enum constructor: initialise it from a string (name) and a list of pair values; the list
// must be ordered by the data type: lint; if the data value is -1 it is automatically
// generated; throws InvalidValue if list contains two or more pairs having the same key or
// is not ordered
Enum::Enum(const string& p_rcoName, const list<pair<string, lint> >& p_rcoValues)
	throw(InvalidValue, bad_alloc) : m_coName(p_rcoName)
{
	registerEnum(p_rcoName, p_rcoValues);
}

// returns an item identified by name; throws InvalidValue if invalid item name
Enum::Item Enum::item(const string& p_rcoVal) const throw(InvalidValue)
{
	const CValuesMap* pcoValMap = s_pcoEnums->valMap(m_coName);
	if (!pcoValMap->has(p_rcoVal))
		throw InvalidValue();
	return Item(p_rcoVal, *this);
}

// returns the value of an item identified by name; throws InvalidValue if invalid item name
lint Enum::itemValue(const string& p_rcoVal) const throw(InvalidValue)
{
	return s_pcoEnums->valMap(m_coName)->id(p_rcoVal);
}

// values: return enum values
const string& Enum::values() const throw(InvalidValue)
{
	return s_pcoEnums->valMap(m_coName)->values();
}

// values: return enum values of enum type
const string& Enum::values(const string& p_rcoEnum) throw(InvalidValue)
{
	return s_pcoEnums->valMap(p_rcoEnum)->values();
}

// enumObj: return enum object of type p_rcoEnum
Enum* Enum::enumObj(const string& p_rcoEnum) throw(InvalidValue)
{
	if (!s_pcoEnums->has(p_rcoEnum))
		throw InvalidValue();
	return new Enum(p_rcoEnum);
}

// isValid: return true if enum type exists
bool Enum::isValid(const string& p_rcoEnum)
{
	return s_pcoEnums->has(p_rcoEnum);
}

// registerEnum: add enum to enum types
void Enum::registerEnum(const string& p_rcoName,
	                    const list<pair<string, lint> >& p_rcoValues)
	throw(InvalidValue, bad_alloc)
{
	initMap();
	SafeAutoPtr<CValuesMap> pcoValues(new CValuesMap);
	list<pair<string, lint> >::const_iterator coIt;
	lint i = 1;
	lint nMax = 0;
	for (coIt = p_rcoValues.begin(); coIt != p_rcoValues.end(); ++coIt)
	{
		lint nVal = (*coIt).second;
		if (nVal < 0)
			nVal = i;
		if (nMax >= nVal)
			throw InvalidValue();
		nMax = nVal;
		string coName = (*coIt).first;
		if (pcoValues->has(coName))
			throw InvalidValue();
		pcoValues->insert(coName, nVal);
		i = nVal + 1;
	}
	s_pcoEnums->insert(p_rcoName, pcoValues.release());
}


// Topic implementation

// CTopicMap: map of topics
class CTopicMap : private map<lint, Topic*>
{
public:
	// constructor
	CTopicMap() : m_coValues("") {}

	// destructor
	~CTopicMap() { clear(); }

	// insert topic referred by pointer; topic must be dynamically allocated
	void insert(Topic*);

	// erase topic referred by name from the map
	void erase(lint);

	// names: return string containig values in the topic map
	const string& names(const string&) const;

	// clear: erase all topics from the map
	void clear();

	// reparent: reparent all the topics in the map
	void reparent(Topic*);

private:
	// forbid instantiation from another topic map
	CTopicMap(const CTopicMap&);

	// forbid assignment
	const CTopicMap& operator =(const CTopicMap&);

	mutable string m_coValues;
};

// insert topic referred by pointer; topic must be dynamically allocated
inline void CTopicMap::insert(Topic* p_pcoTopic)
{
	if (p_pcoTopic == NULL)
		return;
	(*this)[p_pcoTopic->id()] = p_pcoTopic;
}

// erase topic referred by name from the map
inline void CTopicMap::erase(lint p_nTopic)
{
	iterator coIt = find(p_nTopic);
	if (coIt == end())
		return;
	map<lint, Topic*>::erase(coIt);
}

// values: return string containig values in the topic map
const string& CTopicMap::names(const string& p_rcoLang) const
{
	string coLeft, coRight;
	if (p_rcoLang == "")
	{
		coLeft = "(";
		coRight = ")";
	}
	m_coValues = "";
	bool first = true;
	for (const_iterator coIt = begin(); coIt != end(); ++coIt)
	{
		if (!first)
			m_coValues += ", ";
		first = false;
		m_coValues += coLeft + (*coIt).second->name(p_rcoLang) + coRight;
	}
	return m_coValues;
}

// clear: erase all topics from the map
void CTopicMap::clear()
{
	for (iterator coIt = begin(); coIt != end(); coIt = begin())
		delete (*coIt).second;
}

// reparent: reparent all the topics in the map
void CTopicMap::reparent(Topic* p_pcoNewParent)
{
	for (iterator coIt = begin(); coIt != end(); ++coIt)
		(*coIt).second->m_pcoParent = p_pcoNewParent;
}


class CTopicIdTable : private map<lint, Topic*>
{
	friend class Topic;

public:
	// default constructor
	CTopicIdTable() {}

	// destructor
	~CTopicIdTable() { clear(); }

	Topic* find(lint p_nId) const;

	void insert(Topic*);

	void erase(lint);

	void clear() { map<lint, Topic*>::clear(); }

	void setUpdated(bool p_bUpdate) const;

	void clearInvalid();

private:
	// forbid instantiation from another topic map
	CTopicIdTable(const CTopicIdTable&);

	// forbid assignment
	const CTopicIdTable& operator =(const CTopicIdTable&);
};

inline Topic* CTopicIdTable::find(lint p_nId) const
{
	map<lint, Topic*>::const_iterator coIt = map<lint, Topic*>::find(p_nId);
	if (coIt != map<lint, Topic*>::end())
		return (*coIt).second;
	return NULL;
}

inline void CTopicIdTable::insert(Topic* p_pcoTopic)
{
	(*this)[p_pcoTopic->id()] = p_pcoTopic;
}

inline void CTopicIdTable::erase(lint p_nId)
{
	iterator coIt = map<lint, Topic*>::find(p_nId);
	if (coIt == end())
		return;
	map<lint, Topic*>::erase(coIt);
}

void CTopicIdTable::setUpdated(bool p_bUpdate) const
{
	for (const_iterator coIt = begin(); coIt != end(); ++coIt)
		(*coIt).second->updated(p_bUpdate);
	Topic::s_bValuesChanged = p_bUpdate;
}

void CTopicIdTable::clearInvalid()
{
	iterator coNextIt;
	for (iterator coIt = begin(); coIt != end(); coIt = coNextIt)
	{
		coNextIt = coIt;
		++coNextIt;
		if (!(*coIt).second->updated())
		{
			delete (*coIt).second;
			coNextIt = begin();
		}
	}
}


typedef pair<string, string> TopicItem;

class CTopicNameTable : private map<TopicItem, Topic*>
{
public:
	// default constructor
	CTopicNameTable() {}

	// destructor
	~CTopicNameTable() { clear(); }

	Topic* find(const string& p_rcoName, const string& p_rcoLang) const;

	void insert(Topic*, const string&);

	void insert(Topic*);

	void erase(const string&, const string&);

	void clear() { map<TopicItem, Topic*>::clear(); }

private:
	// forbid instantiation from another topic map
	CTopicNameTable(const CTopicNameTable&);

	// forbid assignment
	const CTopicNameTable& operator =(const CTopicNameTable&);
};

inline Topic* CTopicNameTable::find(const string& p_rcoName, const string& p_rcoLang) const
{
	map<TopicItem, Topic*>::const_iterator coIt;
	coIt = map<TopicItem, Topic*>::find(TopicItem(p_rcoName, p_rcoLang));
	if (coIt == map<TopicItem, Topic*>::end())
		return NULL;
	return (*coIt).second;
}

inline void CTopicNameTable::insert(Topic* p_pcoTopic, const string& p_rcoLang)
{
	if (p_rcoLang == "")
		insert(p_pcoTopic);
	else
		(*this)[TopicItem(p_pcoTopic->name(p_rcoLang), p_rcoLang)] = p_pcoTopic;
}

void CTopicNameTable::insert(Topic* p_pcoTopic)
{
	CStringMap::const_iterator coIt = p_pcoTopic->m_coNames.begin();
	for (; coIt != p_pcoTopic->m_coNames.begin(); ++coIt)
		(*this)[TopicItem((*coIt).second, (*coIt).first)] = p_pcoTopic;
}

inline void CTopicNameTable::erase(const string& p_rcoTopic, const string& p_rcoLang)
{
	iterator coIt = map<TopicItem, Topic*>::find(TopicItem(p_rcoTopic, p_rcoLang));
	if (coIt == end())
		return;
	map<TopicItem, Topic*>::erase(coIt);
}


CMutex Topic::s_coOpMutex;
bool Topic::s_bValidValues = true;
string Topic::s_coValues = "";
bool Topic::s_bValuesChanged = false;
CTopicIdTable* Topic::s_pcoIdTopics = new CTopicIdTable();
CTopicNameTable* Topic::s_pcoNameTopics = new CTopicNameTable();
string Topic::empty_string = "";

// name: return name of the topic in the given language
inline const string& Topic::name(const string& p_rcoLang) const
{
	CMutexHandler coH(&s_coOpMutex);
	if (p_rcoLang != "")
	{
		CStringMap::const_iterator coIt = m_coNames.find(p_rcoLang);
		if (coIt == m_coNames.end())
			return empty_string;
		return (*coIt).second;
	}
	if (m_bValidNames)
		return m_coNamesStr;
	m_coNamesStr = "";
	bool bFirst = true;
	CStringMap::const_iterator coIt = m_coNames.begin();
	for (; coIt != m_coNames.end(); ++coIt)
	{
		if (!bFirst)
			m_coNamesStr += ", ";
		bFirst = false;
		m_coNamesStr += (*coIt).second + ":" + (*coIt).first;
	}
	m_bValidNames = true;
	return m_coNamesStr;
}

// childrenNames: return string containing children names
string Topic::childrenNames(const string& p_rcoLang) const
{
	CMutexHandler coH(&s_coOpMutex);
	return m_pcoChildren->names(p_rcoLang);
}

// values: return all valid topic values
const string& Topic::values()
{
	CMutexHandler coH(&s_coOpMutex);
	if (s_bValidValues)
		return s_coValues;
	s_coValues = "";
	bool bFirst = true;
	CTopicIdTable::const_iterator coIt = s_pcoIdTopics->begin();
	for(; coIt != s_pcoIdTopics->end(); ++coIt)
	{
		if (!bFirst)
			s_coValues += ", ";
		bFirst = false;
		s_coValues += "(" + (*coIt).second->name("") + ")";
	}
	return s_coValues;
}

// isValid: returns true if topic id is valid
bool Topic::isValid(lint p_nId)
{
	CMutexHandler coH(&s_coOpMutex);
	return s_pcoIdTopics->find(p_nId) != NULL;
}

// isValid: returns true if topic value (name:lang) is valid
bool Topic::isValid(const string& p_rcoValue)
{
	string::size_type nSplit = p_rcoValue.find(':');
	string coName = p_rcoValue.substr(0, nSplit);
	string coLang = p_rcoValue.substr(nSplit + 1, p_rcoValue.length() - nSplit - 1);
	return isValid(coName, coLang);
}

// isValid: returns true if topic name:language is valid
bool Topic::isValid(const string& p_rcoName, const string& p_rcoLang)
{
	CMutexHandler coH(&s_coOpMutex);
	return s_pcoNameTopics->find(p_rcoName, p_rcoLang) != NULL;
}

// item: returns Item object
Topic::Item Topic::item(const string& p_rcoValue) throw(InvalidValue)
{
	CMutexHandler coH(&s_coOpMutex);
	string::size_type nSplit = p_rcoValue.find(':');
	string coName = p_rcoValue.substr(0, nSplit);
	string coLang = p_rcoValue.substr(nSplit + 1, p_rcoValue.length() - nSplit - 1);
	Topic* pcoTopic = s_pcoNameTopics->find(coName, coLang);
	if (pcoTopic == NULL)
		throw InvalidValue();
	return Item(pcoTopic, coLang);
}

// topic: return pointer to const topic identified by id
const Topic* Topic::topic(lint p_nId)
{
	CMutexHandler coH(&s_coOpMutex);
	return s_pcoIdTopics->find(p_nId);
}

// topic: return pointer to const topic identified by value (name:lang)
const Topic* Topic::topic(const string& p_rcoValue)
{
	string::size_type nSplit = p_rcoValue.find(':');
	string coName = p_rcoValue.substr(0, nSplit);
	string coLang = p_rcoValue.substr(nSplit + 1, p_rcoValue.length() - nSplit - 1);
	return topic(coName, coLang);
}

// topic: return pointer to const topic identified by name and name language
const Topic* Topic::topic(const string& p_rcoName, const string& p_rcoLang)
{
	CMutexHandler coH(&s_coOpMutex);
	return s_pcoNameTopics->find(p_rcoName, p_rcoLang);
}

void Topic::setUpdated(bool p_bUpdated)
{
	CMutexHandler coH(&s_coOpMutex);
	s_pcoIdTopics->setUpdated(p_bUpdated);
}

void Topic::clearInvalid()
{
	CMutexHandler coH(&s_coOpMutex);
	s_pcoIdTopics->clearInvalid();
}

void Topic::setNames(const CStringMap& p_rcoNames, lint p_nTopicId)
{
	CMutexHandler coH(&s_coOpMutex);
	Topic* pcoTopic = s_pcoIdTopics->find(p_nTopicId);
	if (pcoTopic == NULL)
		return;
	CStringMap::iterator coMyIt = pcoTopic->m_coNames.begin();
	CStringMap::const_iterator coOtherIt = p_rcoNames.begin();
	while (coOtherIt != p_rcoNames.end())
	{
		if (coMyIt == pcoTopic->m_coNames.end() || (*coMyIt).first > (*coOtherIt).first)
		{
			addTranslation(pcoTopic, (*coOtherIt).second, (*coOtherIt).first);
			++coOtherIt;
		}
		else if ((*coMyIt).first < (*coOtherIt).first)
		{
			CStringMap::iterator coTmpIt = coMyIt;
			++coTmpIt;
			delTranslation(pcoTopic, (*coMyIt).first);
			coMyIt = coTmpIt;
		}
		else
		{
			if ((*coMyIt).second != (*coOtherIt).second)
				addTranslation(pcoTopic, (*coOtherIt).second, (*coOtherIt).first);
			++coMyIt;
			++coOtherIt;
		}
	}
	CStringMap::iterator coTmpIt;
	for (; coMyIt != pcoTopic->m_coNames.end(); coMyIt = coTmpIt)
	{
		coTmpIt = coMyIt;
		++coTmpIt;
		delTranslation(pcoTopic, (*coMyIt).first);
	}
	pcoTopic->m_bUpdated = true;
}

const Topic* Topic::setTopic(const string& p_rcoName, const string& p_rcoLang, lint p_nId,
	                         lint p_nParentId)
{
	CMutexHandler coH(&s_coOpMutex);
	Topic* pcoCurr = s_pcoIdTopics->find(p_nId);
	Topic* pcoParent = p_nParentId < 0 ? NULL : s_pcoIdTopics->find(p_nParentId);
	if (!pcoCurr)
		pcoCurr = new Topic(p_rcoName, p_rcoLang, p_nId, pcoParent);
	else
		addTranslation(pcoCurr, p_rcoName, p_rcoLang);
	pcoCurr->m_bUpdated = true;
	return pcoCurr;
}

// constructor
Topic::Topic(const string& p_rcoName, const string& p_rcoLang, lint p_nId, Topic* p_pcoParent)
	: m_nTopicId(p_nId), m_pcoParent(p_pcoParent), m_pcoChildren(NULL),
	m_bUpdated(true), m_bValidNames(false), m_coNamesStr("")
{
	CMutexHandler coH(&s_coOpMutex);
	if (s_pcoIdTopics->find(p_nId) != NULL)
		throw InvalidValue();
	m_pcoChildren = new CTopicMap;
	s_pcoIdTopics->insert(this);
	addTranslation(this, p_rcoName, p_rcoLang);
	if (p_pcoParent)
		p_pcoParent->m_pcoChildren->insert(this);
}

// destructor
Topic::~Topic()
{
	CMutexHandler coH(&s_coOpMutex);
	if (m_pcoParent)
		m_pcoParent->m_pcoChildren->erase(m_nTopicId);
	s_pcoIdTopics->erase(m_nTopicId);
	for (CStringMap::iterator coIt = m_coNames.begin(); coIt != m_coNames.end();
	     coIt = m_coNames.begin())
		delTranslation(this, (*coIt).first);
	delete m_pcoChildren;
	m_pcoParent = NULL;
	m_pcoChildren = NULL;
}

void Topic::addTranslation(Topic* p_pcoTopic, const string& p_rcoName, const string& p_rcoLang)
{
	if (p_rcoLang == "" || p_pcoTopic == NULL)
		return;
	CMutexHandler coH(&s_coOpMutex);
	p_pcoTopic->m_coNames[p_rcoLang] = p_rcoName;
	s_pcoNameTopics->insert(p_pcoTopic, p_rcoLang);
	p_pcoTopic->m_bValidNames = false;
	s_bValidValues = false;
	s_bValuesChanged = true;
}

void Topic::delTranslation(Topic* p_pcoTopic, const string& p_rcoLang)
{
	if (p_rcoLang == "" || p_pcoTopic == NULL)
		return;
	CMutexHandler coH(&s_coOpMutex);
	CStringMap::iterator coIt = p_pcoTopic->m_coNames.find(p_rcoLang);
	if (coIt == p_pcoTopic->m_coNames.end())
		return;
	s_pcoNameTopics->erase((*coIt).second, p_rcoLang);
	p_pcoTopic->m_coNames.erase(coIt);
	p_pcoTopic->m_bValidNames = false;
	s_bValidValues = false;
	s_bValuesChanged = true;
}
