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


#ifndef CMESSAGE_H
#define CMESSAGE_H


#include <typeinfo>
#include <exception>
#include <stdexcept>
#include <string>


#include "cms_types.h"
#include "cxmlreader.h"


using std::multimap;
using std::exception;
using std::bad_cast;
using std::string;
using std::pair;
using std::out_of_range;


class CValue
{
public:
	virtual ~CValue() {}

	typedef enum { string_type, binary_string_type } value_type;

	virtual const string& asString() const throw(bad_cast) = 0;

	virtual pair<lint, const char*> asBinaryString() const throw(bad_cast) = 0;
};


class CStringValue : public CValue
{
public:
	CStringValue(const string& p_rcoValue) { m_coValue = p_rcoValue; }

	CStringValue(const CStringValue& p_rcoSource) : m_coValue(p_rcoSource.m_coValue) {}

	virtual ~CStringValue() {}

	const CStringValue& operator =(const CStringValue& p_rcoSource)
		{ m_coValue = p_rcoSource.m_coValue; return *this; }

	const string& operator()() const { return m_coValue; }

	lint size() const { return m_coValue.size(); }

	virtual const string& asString() const throw(bad_cast) { return m_coValue; }

	virtual pair<lint, const char*> asBinaryString() const throw(bad_cast)
		{ return pair<lint, const char*>(m_coValue.size(), m_coValue.c_str()); }

	CStringValue* clone() const { return new CStringValue(*this); }

private:
	string m_coValue;
};


class CBinaryStringValue : public CValue
{
public:
	CBinaryStringValue(lint p_nSize, const char* p_pchValue);

	CBinaryStringValue(const CBinaryStringValue& p_rcoSource);

	virtual ~CBinaryStringValue() {}

	const CBinaryStringValue& operator = (const CBinaryStringValue& p_rcoSource);

	char& operator [] (lint p_nIndex) const;

	lint size() const { return m_nSize; }

	virtual const string& asString() const throw(bad_cast) { throw bad_cast(); }

	virtual pair<lint, const char*> asBinaryString() const throw(bad_cast)
		{ return pair<lint, const char*>(m_nSize, m_pchValue); }

	CBinaryStringValue* clone() const { return new CBinaryStringValue(*this); }

private:
	lint m_nSize;
	char* m_pchValue;
};


typedef multimap <string, CValue*, less<string> > String2Value;


class CParameterMap
{
public:
	CParameterMap() {}

	~CParameterMap();

	void insert(const string& p_rcoParameter, const string& p_rcoValue);

	void insert(const string& p_rcoParameter, pair<lint, const char*> p_Value);

	void erase(const string& p_rcoParameter) { m_coParameters.erase(p_rcoParameter); }

	bool has(const string& p_rcoParameter)
		{ return m_coParameters.find(p_rcoParameter) != m_coParameters.end(); }

	const CValue* valueOf(const string& p_rcoParameter) const throw(out_of_range);

	const String2Value& getMap() const { return m_coParameters; }

private:
	String2Value m_coParameters;
};


class CMessage
{
public:
	virtual ~CMessage() {}

	virtual void setContent(char* p_pchContent)
		throw (out_of_range, xml_parse_error, invalid_message_content) = 0;

	virtual pair<lint, const char*> getContent() const = 0;

	const CValue* getParameter(const string& p_rcoParameter) const throw(out_of_range);

	void setParameter(const string& p_rcoParameter, const string& p_rcoValue);

	void setParameter(const string& p_rcoParameter, pair<lint, const char*> p_Value);

	const CParameterMap& getParameters() const { return m_coParameters; }

	virtual string getMessageType() const = 0;

	virtual uint getMessageTypeId() const = 0;

protected:
	mutable bool m_bValidContent;

private:
	CParameterMap m_coParameters;
};


// CParameterMap inline methods

inline void CParameterMap::insert(const string& p_rcoParameter, const string& p_rcoValue)
{
	m_coParameters.insert(pair<string, CValue*>(p_rcoParameter, new CStringValue(p_rcoValue)));
}

inline void CParameterMap::insert(const string& p_rcoParameter, pair<lint, const char*> p_Value)
{
	m_coParameters.insert(pair<string, CValue*>(p_rcoParameter,
	                      new CBinaryStringValue(p_Value.first, p_Value.second)));
}

// CMessage inline methods

inline const CValue* CMessage::getParameter(const string& p_rcoParameter) const throw(out_of_range)
{
	return m_coParameters.valueOf(p_rcoParameter);
}

inline void CMessage::setParameter(const string& p_rcoParameter, const string& p_rcoValue)
{
	m_bValidContent = false;
	m_coParameters.insert(p_rcoParameter, p_rcoValue);
}

inline void CMessage::setParameter(const string& p_rcoParameter, pair<lint, const char*> p_Value)
{
	m_bValidContent = false;
	m_coParameters.insert(p_rcoParameter, p_Value);
}


class CMsgURLRequest : public CMessage
{
public:
	CMsgURLRequest(char* p_pchContent)
		throw (out_of_range, xml_parse_error, invalid_message_content)
		: m_pchContent(NULL) { setContent(p_pchContent); }

	~CMsgURLRequest() { delete m_pchContent; }

	void setContent(char* p_pchContent)
		throw (out_of_range, xml_parse_error, invalid_message_content);

	virtual pair<lint, const char*> getContent() const;

	virtual string getMessageType() const { return s_coMessageType; }

	virtual uint getMessageTypeId() const { return s_nMessageTypeId; }

	static const string& messageType() { return s_coMessageType; }

	static uint messageTypeId() { return s_nMessageTypeId; }

	const string& getHTTPHost() const { return m_coHTTPHost; }

	const string& getDocumentRoot() const { return m_coDocumentRoot; }

	const string& getRemoteAddress() const { return m_coRemoteAddress; }

	const string& getPathTranslated() const { return m_coPathTranslated; }

	const string& getReqestMethod() const { return m_coRequestMethod; }

	const string& getReqestURI() const { return m_coRequestURI; }

	const string& getCookie(const string& p_coCookie) const throw (out_of_range);

	const String2String& getCookies() const { return m_coCookies; }

private:
	static const string s_coMessageType;
	static const uint s_nMessageTypeId;

private:
	char* m_pchContent;
	lint m_nContentSize;

	string m_coHTTPHost;
	string m_coDocumentRoot;
	string m_coRemoteAddress;
	string m_coPathTranslated;
	string m_coRequestMethod;
	string m_coRequestURI;

	String2String m_coCookies;
};


// CMsgURLRequest inline methods

inline const string& CMsgURLRequest::getCookie(const string& p_coCookie) const throw (out_of_range)
{
	String2String::const_iterator coIt = m_coCookies.find(p_coCookie);
	if (coIt == m_coCookies.end())
		throw out_of_range(string("Cookie ") + p_coCookie + " does not exist.");
	return (*coIt).second;
}


class CMsgResetCache : public CMessage
{
public:
	CMsgResetCache(char* p_pchContent)
		throw (out_of_range, xml_parse_error, invalid_message_content)
		: m_pchContent(NULL) { setContent(p_pchContent); }

	void setContent(char* p_pchContent)
		throw (out_of_range, xml_parse_error, invalid_message_content);

	virtual pair<lint, const char*> getContent() const;

	virtual string getMessageType() const { return s_coMessageType; }

	virtual uint getMessageTypeId() const { return s_nMessageTypeId; }

	static const string& messageType() { return s_coMessageType; }

	static uint messageTypeId() { return s_nMessageTypeId; }

	const string& getType() const { return m_coType; }

private:
	static const string s_coMessageType;
	static const uint s_nMessageTypeId;

private:
	char* m_pchContent;
	lint m_nContentSize;
	string m_coType;
};


class CMsgRestartServer : public CMessage
{
public:
	CMsgRestartServer(char* p_pchContent)
		throw (out_of_range, xml_parse_error, invalid_message_content)
		: m_pchContent(NULL) { setContent(p_pchContent); }

	void setContent(char* p_pchContent)
		throw (out_of_range, xml_parse_error, invalid_message_content);

	virtual pair<lint, const char*> getContent() const;

	virtual string getMessageType() const { return s_coMessageType; }

	virtual uint getMessageTypeId() const { return s_nMessageTypeId; }

	static const string& messageType() { return s_coMessageType; }

	static uint messageTypeId() { return s_nMessageTypeId; }

private:
	static const string s_coMessageType;
	static const uint s_nMessageTypeId;

private:
	char* m_pchContent;
	lint m_nContentSize;
};


#endif // CMESSAGE_H
