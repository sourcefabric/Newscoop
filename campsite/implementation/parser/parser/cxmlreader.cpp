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


#include "cxmlreader.h"


// Moves the position of the current instance to the next node in the stream.
// Returns the qualified name of the next node.
const char* CXMLReader::nextElement(const char* p_pchName)
	throw (xml_parse_error, invalid_message_content)
{
	int nRes = xmlTextReaderRead(m_pReader);
	if (nRes == 0)
		return NULL;
	if (nRes == -1)
		throw xml_parse_error("unable to read next element");
	const char* pchName = (const char*)xmlTextReaderConstName(m_pReader);
	if (strcmp(pchName, "#text") == 0 && (p_pchName == NULL || strcmp(p_pchName, "#text") != 0))
		return nextElement(p_pchName);
	if (p_pchName != NULL && strcmp(pchName, p_pchName) != 0)
		throw invalid_message_content(string(p_pchName) + " element is missing");
	return pchName;
}

// Returns the next node content checking the following constraints:
// - the node name must be the one supplied
// - the node depth
string CXMLReader::nextElementContent(const char* p_pchName, int p_nDepth, bool p_bExpectEnd)
	throw (xml_parse_error, invalid_message_content)
{
	const char* pchName = nextElement();
	if (p_pchName != NULL && strcmp(pchName, p_pchName) != 0)
		throw invalid_message_content(string(p_pchName) + " element is missing");
	if (p_nDepth >= 0 && elementDepth() != p_nDepth)
		throw invalid_message_content("element depth must be " + int2string(p_nDepth));
	string coContent;
	try {
		pchName = nextElement("#text");
		coContent = elementContent();
		if (!p_bExpectEnd)
			return coContent;
		pchName = nextElement();
	}
	catch (invalid_message_content& rcoEx)
	{
	}
	if (p_pchName != NULL && strcmp(pchName, p_pchName) != 0)
		throw invalid_message_content("element end is missing");
	return coContent;
}
