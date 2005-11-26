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


#ifndef CXMLTREE_H
#define CXMLTREE_H


#include <cstddef>
#include <iterator>


#include <libxml/xmlreader.h>
#include <libxml/xmlmemory.h>
#include <libxml/tree.h>


using std::output_iterator_tag;


struct _xmltree_iterator_base
{
	typedef size_t                  size_type;
	typedef ptrdiff_t               difference_type;
	typedef output_iterator_tag     iterator_category;

	// Points to the current node in the xml tree
	xmlNodePtr m_pNode;

	_xmltree_iterator_base(xmlNodePtr __x)
	: m_pNode(__x)
	{ }

	_xmltree_iterator_base()
	: m_pNode(NULL)
	{ }

	bool operator==(const _xmltree_iterator_base& __x) const
	{ return m_pNode == __x.m_pNode; }

	bool operator!=(const _xmltree_iterator_base& __x) const
	{ return m_pNode != __x.m_pNode; }
};


/**
*  @brief A CXMLTree::iterator.
*
*  In addition to being used externally, a list holds one of these
*  internally, pointing to the sequence of data.
*
*  @if maint
*  All the functions are op overloads.
*  @endif
*/
struct _xmltree_iterator : public _xmltree_iterator_base
{
	typedef _xmltree_iterator iterator;

	_xmltree_iterator(xmlNodePtr __x)
	: _xmltree_iterator_base(__x)
	{ }

	_xmltree_iterator()
	{ }

	_xmltree_iterator(const iterator& __x)
	: _xmltree_iterator_base(__x.m_pNode)
	{ }

	const xmlNodePtr operator*() const
	{ return (const xmlNodePtr) m_pNode; }

	xmlNodePtr* const operator->() const
	{ return (xmlNodePtr* const) &m_pNode; }
};


class CXMLTree
{
public:
	typedef _xmltree_iterator iterator;

public:
	CXMLTree(const char* p_pchRootNode);

	~CXMLTree();

	iterator getRootNode() const { return iterator(m_pRootNode); }

	iterator newChild(iterator& p_pParent, const char* p_pchName, const char* p_pchContent = NULL);

	iterator newNode(const char* p_pchName);

	iterator newContent(const char* p_pchContent);

	iterator addChild(iterator p_pParent, iterator p_pChild);

	void addAttribute(iterator p_pNode, const char* p_pchAttribute, const char* p_pchValue);

	int saveToFile(const char* p_pchFileName, const char* p_pchEncoding);

	void saveToMemory(char** p_ppchBuffer, int* p_pnSize);

	void freeBufferMemory(char* p_pchBuffer);

private:
	// pointer to the XML document
	xmlDocPtr m_pDoc;

	// pointer to the root node
	xmlNodePtr m_pRootNode;
};


// CXMLTree inline methods

inline CXMLTree::CXMLTree(const char* p_pchRootNode)
{
	m_pDoc = xmlNewDoc(BAD_CAST "1.0");
	m_pRootNode = xmlNewNode(NULL, BAD_CAST p_pchRootNode);
	xmlDocSetRootElement(m_pDoc, m_pRootNode);
}

inline CXMLTree::~CXMLTree()
{
	xmlFreeDoc(m_pDoc);
	xmlCleanupParser();
	xmlMemoryDump(); // this is to debug memory for regression tests
	m_pDoc = NULL;
	m_pRootNode = NULL;
}

inline CXMLTree::iterator CXMLTree::newChild(iterator& p_pParent, const char* p_pchName, const char* p_pchContent)
{
	return iterator(xmlNewTextChild(*p_pParent, NULL, BAD_CAST p_pchName, BAD_CAST p_pchContent));
}

inline CXMLTree::iterator CXMLTree::newNode(const char* p_pchName)
{
	return iterator(xmlNewNode(NULL, BAD_CAST p_pchName));
}

inline CXMLTree::iterator CXMLTree::newContent(const char* p_pchContent)
{
	return iterator(xmlNewText(BAD_CAST p_pchContent));
}

inline CXMLTree::iterator CXMLTree::addChild(iterator p_pParent, iterator p_pChild)
{
	return iterator(xmlAddChild(*p_pParent, *p_pChild));
}

inline void CXMLTree::addAttribute(iterator p_pNode, const char* p_pchAttribute, const char* p_pchValue)
{
	xmlNewProp(*p_pNode, BAD_CAST p_pchAttribute, BAD_CAST p_pchValue);
}

inline int CXMLTree::saveToFile(const char* p_pchFileName, const char* p_pchEncoding)
{
	return xmlSaveFormatFileEnc(p_pchFileName, m_pDoc, p_pchEncoding, 1);
}

inline void CXMLTree::saveToMemory(char** p_ppchBuffer, int* p_pnSize)
{
	xmlDocDumpFormatMemory(m_pDoc, (xmlChar**) p_ppchBuffer, p_pnSize, 1);
}

inline void CXMLTree::freeBufferMemory(char* p_pchBuffer)
{
	xmlFree(p_pchBuffer);
}


#endif
