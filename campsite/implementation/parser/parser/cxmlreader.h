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

#ifndef CXMLREADER_H
#define CXMLREADER_H


#include <libxml/xmlreader.h>
#include <string>
#include <stdexcept>


using std::string;
using std::exception;
using std::invalid_argument;


#include "globals.h"


// Exception thrown by the XML parser in case of error
class xml_parse_error : public exception
{
public:
	xml_parse_error(const char* p_pchMessage) : m_coMessage(p_pchMessage) {}

	virtual const char* what() const throw () { return m_coMessage.c_str(); }

	~xml_parse_error() throw() {}

private:
	string m_coMessage;
};


// Exception thrown by the XML parser in case of error
class invalid_message_content : public exception
{
public:
	invalid_message_content(const char* p_pchMessage) throw() : m_coMessage(p_pchMessage) {}

	invalid_message_content(const string& p_rcoMessage) throw() : m_coMessage(p_rcoMessage) {}

	// This declaration is not useless:
	// http://gcc.gnu.org/onlinedocs/gcc-3.0.2/gcc_6.html#SEC118
	virtual ~invalid_message_content() throw() {}

	virtual const char* what() const throw() { return m_coMessage.c_str(); }

private:
	string m_coMessage;
};


class CXMLReader
{
public:
	// Create an xml reader fed with the resource from file name.
	CXMLReader(const char* p_pchFilename) throw (invalid_argument);

	// Create an xml reader for an XML in-memory document.
	// The parsing flags @options are a combination of xmlParserOption(s).
	CXMLReader(const char* p_pchBuffer, int p_nSize, const char* p_pchURL,
			   const char* p_pchEncoding, int p_nOptions) throw (invalid_argument);
	
	~CXMLReader();

	// Moves the position of the current instance to the next node in the stream.
	// Returns the qualified name of the next node.
	const char* nextElement(const char* p_pchName = NULL)
		throw (xml_parse_error, invalid_message_content);

	// Returns the depth of the node in the tree.
	int elementDepth() const throw (xml_parse_error);

	// Returns true if the node has attributes.
	bool hasAttributes() const;

	// Returns true if the current node is empty.
	bool isEmpty() const;

	// Retrieve the validity status from the parser context
	bool isValid() const;

	// Moves the position of the current instance to the attribute with the specified qualified name.
	bool moveToAttribute(const char* p_pchAttribute) throw (xml_parse_error, invalid_argument);

	// Returns the value of the current attribute.
	const char* attributeValue() const;

	// Returns the value of the requested attribute.
	const char* getAttributeValue(const char* p_pchAttribute) throw (xml_parse_error, invalid_argument);

	// Returns the current node content
	const char* elementContent() const;

	// Returns the next node content checking the following constraints:
	// - the node name must be the one supplied
	// - the node depth
	string nextElementContent(const char* p_pchName = NULL, int p_nDepth = -1,
	                          bool p_bExpectEnd = true)
		throw (xml_parse_error, invalid_message_content);

private:
	xmlTextReaderPtr m_pReader;
};


// CXMLReader inline methods

// Create an xml reader fed with the resource from file name.
inline CXMLReader::CXMLReader(const char* p_pchFilename) throw (invalid_argument)
{
	if (p_pchFilename == NULL)
		throw invalid_argument(string("Invalid file NULL"));
	m_pReader = xmlNewTextReaderFilename(p_pchFilename);
	if (m_pReader == NULL)
		throw invalid_argument(string("Invalid file ") + p_pchFilename);
}

// Create an xml reader for an XML in-memory document.
// The parsing flags @options are a combination of xmlParserOption(s).
inline CXMLReader::CXMLReader(const char* p_pchBuffer, int p_nSize, const char* p_pchURL,
                              const char* p_pchEncoding, int p_nOptions) throw (invalid_argument)
{
	m_pReader = xmlReaderForMemory(p_pchBuffer, p_nSize, p_pchURL, p_pchEncoding, p_nOptions);
	if (m_pReader == NULL)
		throw invalid_argument("Invalid arguments");
}

inline CXMLReader::~CXMLReader()
{
	xmlFreeTextReader(m_pReader);
}

// Returns the depth of the node in the tree.
inline int CXMLReader::elementDepth() const throw (xml_parse_error)
{
	int nRes = xmlTextReaderDepth(m_pReader);
	if (nRes == -1)
		throw xml_parse_error("");
	return nRes;
}

// Returns true if the node has attributes.
inline bool CXMLReader::hasAttributes() const
{
	int nRes = xmlTextReaderHasAttributes(m_pReader);
	if (nRes == -1)
		throw xml_parse_error("");
	return nRes;
}

// Returns true if the current node is empty.
inline bool CXMLReader::isEmpty() const
{
	int nRes = xmlTextReaderIsEmptyElement(m_pReader);
	if (nRes == -1)
		throw xml_parse_error("");
	return nRes;
}

// Retrieve the validity status from the parser context
inline bool CXMLReader::isValid() const
{
	int nRes = xmlTextReaderIsValid(m_pReader);
	if (nRes == -1)
		throw xml_parse_error("");
	return nRes;
}

// Moves the position of the current instance to the attribute with the specified qualified name.
inline bool CXMLReader::moveToAttribute(const char* p_pchAttribute)
	throw(xml_parse_error, invalid_argument)
{
	if (p_pchAttribute == NULL)
		throw invalid_argument(string("Invalid attribute NULL"));
	int nRes = xmlTextReaderMoveToAttribute(m_pReader, (const xmlChar*) p_pchAttribute);
	if (nRes == -1)
		throw xml_parse_error("");
	if (nRes == 0)
		throw invalid_argument(string("Attribute ") + p_pchAttribute + " does not exist.");
	return nRes;
}

// Returns the value of the current attribute.
inline const char* CXMLReader::attributeValue() const
{
	return (const char*) xmlTextReaderConstValue(m_pReader);
}

// Returns the value of the requested attribute.
inline const char* CXMLReader::getAttributeValue(const char* p_pchAttribute)
	throw (xml_parse_error, invalid_argument)
{
	moveToAttribute(p_pchAttribute);
	return attributeValue();
}

// Returns the current node content
inline const char* CXMLReader::elementContent() const
{
	const char* pchContent = (const char*) xmlTextReaderConstValue(m_pReader);
	if (pchContent == NULL)
		return "";
	return pchContent;
}

#endif // CXMLREADER_H
