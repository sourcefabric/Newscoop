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

#include <iostream>

#include "atoms_impl.h"
#include "auto_ptr.h"

using std::cout;
using std::endl;

const string CAttribute::s_coEmptyString = "";

// CAttributeMap implementation

const CAttributeMap& CAttributeMap::operator =(const CAttributeMap& o)
{
	if (this == &o)
		return *this;
	clear();
	for (const_iterator coIt = o.begin(); coIt != o.end(); ++coIt)
	{
		this->operator []((*coIt).second->identifier()) =
				(CAttribute*)(*coIt).second->clone();
	}
	return *this;
}

bool CAttributeMap::operator ==(const CAttributeMap& o) const
{
	if (this == &o)
		return true;
	if (this->size() != o.size())
		return false;
	const_iterator coIt1 = begin();
	const_iterator coIt2 = o.begin();
	for (; coIt1 != end(); ++coIt1, ++coIt2)
	{
		if (coIt2 == o.end())
			return false;
		if ((*coIt1).first != (*coIt2).first
		    || *(*coIt1).second != *(*coIt2).second)
		{
			return false;
		}
	}
	return true;
}

void CAttributeMap::clear()
{
	for (iterator coIt = begin(); coIt != end(); coIt = begin())
	{
		delete (*coIt).second;
		(*coIt).second = NULL;
		erase(coIt);
	}
}


// CStatementContextMap implementation

const CStatementContextMap& CStatementContextMap::operator =(const CStatementContextMap& o)
{
	if (this == &o)
		return *this;
	clear();
	for (const_iterator coIt = o.begin(); coIt != o.end(); ++coIt)
		this->operator []((*coIt).second->id()) = new CStatementContext(*(*coIt).second);
	return *this;
}

bool CStatementContextMap::operator ==(const CStatementContextMap& o) const
{
	if (this == &o)
		return true;
	if (this->size() != o.size())
		return false;
	const_iterator coIt1 = begin();
	const_iterator coIt2 = o.begin();
	for (; coIt1 != end(); ++coIt1, ++coIt2)
	{
		if (coIt2 == o.end())
			return false;
		if ((*coIt1).first != (*coIt2).first
		    || *(*coIt1).second != *(*coIt2).second)
		{
			return false;
		}
	}
	return true;
}

void CStatementContextMap::clear()
{
	for (iterator coIt = begin(); coIt != end(); coIt = begin())
	{
		delete (*coIt).second;
		(*coIt).second = NULL;
		erase(coIt);
	}
}


// CTypeAttributesMap implementation

const CTypeAttributesMap& CTypeAttributesMap::operator =(const CTypeAttributesMap& o)
{
	if (this == &o)
		return *this;
	clear();
	for (const_iterator coIt = o.begin(); coIt != o.end(); ++coIt)
		this->operator []((*coIt).second->name()) = new CTypeAttributes(*(*coIt).second);
	return *this;
}

bool CTypeAttributesMap::operator ==(const CTypeAttributesMap& o) const
{
	if (this == &o)
		return true;
	if (this->size() != o.size())
		return false;
	const_iterator coIt1 = begin();
	const_iterator coIt2 = o.begin();
	for (; coIt1 != end(); ++coIt1, ++coIt2)
	{
		if (coIt2 == o.end())
			return false;
		if ((*coIt1).first != (*coIt2).first
		    || *(*coIt1).second != *(*coIt2).second)
		{
			return false;
		}
	}
	return true;
}

void CTypeAttributesMap::clear()
{
	for (iterator coIt = begin(); coIt != end(); coIt = begin())
	{
		delete (*coIt).second;
		(*coIt).second = NULL;
		erase(coIt);
	}
}


//	CStatementContext implementation

// constructor
CStatementContext::CStatementContext(int p_nCtx) throw(bad_alloc)
	: m_nContext(p_nCtx), m_pcoAttributes(new CAttributeMap)
{}

// virtual destructor
CStatementContext::~CStatementContext()
{
	delete m_pcoAttributes;
	m_pcoAttributes = NULL;
}

// returns a string containing the list of attributes in context
string CStatementContext::attributes() const
{
	string coRes;
	CAttributeMap::const_iterator coIt;
	bool bFirst = true;
	for (coIt = m_pcoAttributes->begin(); coIt != m_pcoAttributes->end(); ++coIt)
	{
		if (bFirst)
			bFirst = false;
		else
			coRes += ", ";
		coRes += (*coIt).second->identifier();
	}
	return coRes;
}

// attr: return pointer to attribute identified by p_rcoAttrId; throws InvalidAttr if not found
const CAttribute* CStatementContext::attr(const string& p_rcoAttrId) const throw(InvalidAttr)
{
	CAttributeMap::const_iterator coIt = m_pcoAttributes->find(p_rcoAttrId);
	if (coIt == m_pcoAttributes->end())
		throw InvalidAttr();
	return (*coIt).second;
}

// findAttr: return pointer to attribute identified by parameter; NULL if not found
const CAttribute* CStatementContext::findAttr(const string& p_rcoAttrId) const throw()
{
	CAttributeMap::const_iterator coIt = m_pcoAttributes->find(p_rcoAttrId);
	if (coIt == m_pcoAttributes->end())
		return NULL;
	return (*coIt).second;
}

// insertAttr: insert attribute into set
bool CStatementContext::insertAttr(CAttribute* p_pcoAttr) throw()
{
	if (p_pcoAttr == NULL)
		return false;
	return m_pcoAttributes->insert(pair<string, CAttribute*>(p_pcoAttr->identifier(),
	                                                         p_pcoAttr)).second;
}

// assignment operator
const CStatementContext& CStatementContext::operator =(const CStatementContext& p_rcoOther)
	throw(bad_alloc)
{
	if (this != &p_rcoOther)
	{
		m_nContext = p_rcoOther.m_nContext;
		delete m_pcoAttributes;
		m_pcoAttributes = new CAttributeMap(*p_rcoOther.m_pcoAttributes);
	}
	return *this;
}

// compare operator
bool CStatementContext::operator ==(const CStatementContext& p_rcoOther) const
{
	return m_nContext == p_rcoOther.m_nContext
		&& *m_pcoAttributes == *p_rcoOther.m_pcoAttributes;
}

// print context attributes
void CStatementContext::print(int p_nStartIndent, const string& p_rcoIndent) const
{
	string coStartIndent;
	for (int i = 0; i < p_nStartIndent; i++)
		coStartIndent += p_rcoIndent;
	cout << coStartIndent << "Context: " << (int)m_nContext << endl;
	CAttributeMap::const_iterator coIt = m_pcoAttributes->begin();
	for (; coIt != m_pcoAttributes->end(); ++coIt)
	{
		cout << coStartIndent << p_rcoIndent
		     << (*coIt).first << " (" << (*coIt).second->dbField() << ", "
		     << (*coIt).second->dataType() << ", " << (*coIt).second->atomType()
		     << ")" << endl;
	}
}


//	CTypeAttributes implementation

// constructor
CTypeAttributes::CTypeAttributes(const string& p_rcoName) throw(bad_alloc)
	: m_coName(p_rcoName), m_pcoCtxAttributes(new CStatementContextMap)
{}

// virtual destructor
CTypeAttributes::~CTypeAttributes()
{
	delete m_pcoCtxAttributes;
	m_pcoCtxAttributes = NULL;
}

// insertCtx: insert new context; the context is duplicated
bool CTypeAttributes::insertCtx(const CStatementContext& p_rcoCtxs)
{
	return m_pcoCtxAttributes->insert(pair<int, CStatementContext*>
	                                (p_rcoCtxs.id(), new CStatementContext(p_rcoCtxs))).second;
}

// insertCtx: insert new context; the context object must be dynamically allocated
// throws InvalidValue if value is NULL
bool CTypeAttributes::insertCtx(CStatementContext* p_pcoCtx) throw(InvalidValue)
{
	if (p_pcoCtx == NULL)
		throw InvalidValue();
	return m_pcoCtxAttributes->insert(pair<int, CStatementContext*>
	                                (p_pcoCtx->id(), p_pcoCtx)).second;
}

// findCtx: returns context; NULL if not found
const CStatementContext* CTypeAttributes::findCtx(int p_nCtx) const throw()
{
	return (*m_pcoCtxAttributes->find(p_nCtx)).second;
}

// assignment operator
const CTypeAttributes& CTypeAttributes::operator =(const CTypeAttributes& p_rcoOther)
	throw(bad_alloc)
{
	if (this != &p_rcoOther)
	{
		m_coName = p_rcoOther.m_coName;
		delete m_pcoCtxAttributes;
		m_pcoCtxAttributes = new CStatementContextMap(*p_rcoOther.m_pcoCtxAttributes);
	}
	return *this;
}

// compare operator
bool CTypeAttributes::operator ==(const CTypeAttributes& p_rcoOther) const
{
	return m_coName == p_rcoOther.m_coName
		&& *m_pcoCtxAttributes == *p_rcoOther.m_pcoCtxAttributes;
}

// print type attributes
void CTypeAttributes::print(int p_nStartIndent, const string& p_rcoIndent) const
{
	string coStartIndent;
	for (int i = 0; i < p_nStartIndent; i++)
		coStartIndent += p_rcoIndent;
	cout << coStartIndent << "Type: " << m_coName << endl;
	CStatementContextMap::const_iterator coIt = m_pcoCtxAttributes->begin();
	for (; coIt != m_pcoCtxAttributes->end(); ++coIt)
	{
		(*coIt).second->print(2);
	}
}


//	CStatement implementation

// constructor
CStatement::CStatement(int p_nId, const string& p_rcoName) throw(bad_alloc)
	: CAtom(p_rcoName), m_nId(p_nId), m_pcoContexts(new CStatementContextMap),
	m_pcoTypes(new CTypeAttributesMap)
{}

// virtual destructor
CStatement::~CStatement()
{
	delete m_pcoContexts;
	m_pcoContexts = NULL;
	delete m_pcoTypes;
	m_pcoTypes = NULL;
}

// assignment operator
const CStatement& CStatement::operator =(const CStatement& p_rcoOther) throw(bad_alloc, ExMutex)
{
	if (this != &p_rcoOther)
	{
		CMutexHandler m(&m_coTypesOp);
		m_nId = p_rcoOther.m_nId;
		delete m_pcoContexts;
		m_pcoContexts = new CStatementContextMap(*p_rcoOther.m_pcoContexts);
		delete m_pcoTypes;
	    m_pcoTypes = new CTypeAttributesMap(*p_rcoOther.m_pcoTypes);
	}
	return *this;
}

// compare operator
bool CStatement::operator ==(const CStatement& p_rcoOther) const
{
	CMutexHandler m(&m_coTypesOp);
	return CAtom::operator ==(p_rcoOther)
	       && m_nId == p_rcoOther.m_nId
	       && *m_pcoContexts == *p_rcoOther.m_pcoContexts
	       && *m_pcoTypes == *p_rcoOther.m_pcoTypes;
}

// insertCtx: insert context into contexts map
bool CStatement::insertCtx(const CStatementContext& p_rcoCtx)
{
	return m_pcoContexts->insert(pair<int, CStatementContext*>
	                             (p_rcoCtx.id(), new CStatementContext(p_rcoCtx))).second;
}

// insertCtx: insert context into contexts map; the pointer must be dynamically allocated
// throws InvalidValue if NULL
bool CStatement::insertCtx(CStatementContext* p_pcoCtx) throw(InvalidValue, bad_alloc)
{
	if (p_pcoCtx == NULL)
		throw InvalidValue();
	return m_pcoContexts->insert(pair<int, CStatementContext*>(p_pcoCtx->id(), p_pcoCtx)).second;
}

// insertType: insert type into types map
bool CStatement::insertType(const CTypeAttributes& p_rcoType) throw(bad_alloc, ExMutex)
{
	CMutexHandler m(&m_coTypesOp);
	return m_pcoTypes->insert(pair<string, CTypeAttributes*>(p_rcoType.name(),
	                                                       new CTypeAttributes(p_rcoType))).second;
}

// insertType: insert type into types map; the pointer must be dynamically allocated
// throws InvalidValue if NULL
bool CStatement::insertType(CTypeAttributes* p_pcoType) throw(InvalidValue, bad_alloc, ExMutex)
{
	if (p_pcoType == NULL)
		throw InvalidValue();
	CMutexHandler m(&m_coTypesOp);
	return m_pcoTypes->insert(pair<string, CTypeAttributes*>(p_pcoType->name(), p_pcoType)).second;
}

// findType: return pointer to statement context corresponding to the given identifier
// returns NULL if not found
// Parameters:
//		int p_Context - context identifier
const CStatementContext* CStatement::findContext(int p_Context) const throw()
{
	CStatementContextMap::const_iterator coIt = m_pcoContexts->find(p_Context);
	if (coIt == m_pcoContexts->end())
		return NULL;
	return (*coIt).second;
}

// findType: return pointer to type (special) attributes valid for a given type
// throws InvalidType if not found
// Parameters:
//		const string& p_rcoType - type name
const CTypeAttributes* CStatement::findType(const string& p_rcoType) const throw(ExMutex)
{
	CMutexHandler m(&m_coTypesOp);
	CTypeAttributesMap::const_iterator coIt = m_pcoTypes->find(p_rcoType);
	if (coIt == m_pcoTypes->end())
		return NULL;
	return (*coIt).second;
}

// findAttr: return pointer to attribute valid in given context and identified by given name
// throws InvalidAttr if not found
// Parameters:
//		const string& p_rcoAttr - attribute name
//		int p_Context - context
const CAttribute* CStatement::findAttr(const string& p_rcoAttr, int p_Context) const
	throw(InvalidAttr)
{
	CStatementContextMap::const_iterator coIt = m_pcoContexts->find(p_Context);
	if (coIt == m_pcoContexts->end())
		throw InvalidAttr(g_nFIND_NORMAL);
	return (*coIt).second->attr(p_rcoAttr);
}

// findTypeAttr: return pointer to pair of attribute and type containig found attribute
// throws InvalidAttr if not found, InvalidType if type specified is invalid
// Parameters:
//		const string& p_rcoAttr - attribute name
//		const string& p_rcoType - type name
//		int p_Context - context
CPairAttrType* CStatement::findTypeAttr(const string& p_rcoAttr, const string& p_rcoType,
                                        int p_Context) const
	throw(InvalidAttr, InvalidType, ExMutex)
{
	CMutexHandler m(&m_coTypesOp);
	if (p_rcoType != "")
	{
		const CTypeAttributes* pcoType = findType(p_rcoType);
		if (pcoType == NULL)
			throw InvalidType();
		const CStatementContext* c = pcoType->findCtx(p_Context);
		if (c == NULL)
			throw InvalidAttr(g_nFIND_TYPE, p_rcoType);
		return new CPairAttrType(c->attr(p_rcoAttr), pcoType);
	}
	CTypeAttributesMap::const_iterator coTIt;
	for (coTIt = m_pcoTypes->begin(); coTIt != m_pcoTypes->end(); ++coTIt)
	{
		const CStatementContext* pcoCtx = (*coTIt).second->findCtx(p_Context);
		if (pcoCtx == NULL)
			continue;
		const CAttribute* pcoAttr = pcoCtx->findAttr(p_rcoAttr);
		if (pcoAttr == NULL)
			continue;
		return new CPairAttrType(pcoAttr, (*coTIt).second);
	}
	throw InvalidAttr(g_nFIND_TYPE, p_rcoType);
}
	
// findAnyAttr: return pointer to pair attribute-type valid in given context and identified by
// given name; if type is not NULL attribute is of that type
// throws InvalidAttr if not found
// Parameters:
//		const string& p_rcoAttr - attribute name
//		int p_Context - context
CPairAttrType* CStatement::findAnyAttr(const string& p_rcoAttr, int p_Context) const
	throw(InvalidAttr, ExMutex)
{
	string coRes;
	CStatementContextMap::const_iterator coCIt = m_pcoContexts->find(p_Context);
	if (coCIt != m_pcoContexts->end())
	{
		const CAttribute* pcoAttr = (*coCIt).second->findAttr(p_rcoAttr);
		if (pcoAttr != NULL)
			return new CPairAttrType(pcoAttr, NULL);
	}
	CMutexHandler m(&m_coTypesOp);
	CTypeAttributesMap::const_iterator coTIt;
	for (coTIt = m_pcoTypes->begin(); coTIt != m_pcoTypes->end(); ++coTIt)
	{
		const CStatementContext* pcoCtx = (*coTIt).second->findCtx(p_Context);
		if (pcoCtx == NULL)
			continue;
		const CAttribute* pcoAttr = pcoCtx->findAttr(p_rcoAttr);
		if (pcoAttr == NULL)
			continue;
		return new CPairAttrType(pcoAttr, (*coTIt).second);
	}
	throw InvalidAttr(g_nFIND_ALL);
}

// contextAttrs: return string containing valid simple (not with type) attributes for a
// given context
// Parameters:
//		int p_Context - context
string CStatement::contextAttrs(int p_Context) const throw()
{
	const CStatementContext* c = findContext(p_Context);
	if (c != NULL)
		return c->attributes();
	return string("");
}

// typeAttrs: return string containing attributes for a given type/context; if attributes type
//		is undefined perform the operation for all types
// Parameters:
//		const string& p_rcoType - attributes type
//		int p_Context - context
string CStatement::typeAttrs(const string& p_rcoType, int p_Context) const throw(ExMutex)
{
	if (p_rcoType != "")
	{
		try
		{
			const CTypeAttributes* t = findType(p_rcoType);
			if (t == NULL)
				return string("");
			const CStatementContext* c = t->findCtx(p_Context);
			if (c == NULL)
				return string("");
			return c->attributes();
		}
		catch (InvalidType& rcoEx) { return string(""); }
	}
	string coRes;
	bool bFirst = true;
	CMutexHandler m(&m_coTypesOp);
	CTypeAttributesMap::const_iterator coIt;
	for (coIt = m_pcoTypes->begin(); coIt != m_pcoTypes->end(); ++coIt)
	{
		const CStatementContext* pcoCtx = (*coIt).second->findCtx(p_Context);
		if (pcoCtx == NULL)
			continue;
		if (bFirst)
			bFirst = false;
		else
			coRes += ", ";
		coRes += pcoCtx->attributes();
	}
	return coRes;
}

// allAttrs: return string containing valid attributes for a given context
// Parameters:
//		int p_Context - context
string CStatement::allAttrs(int p_Context) const throw()
{
	string coRes1 = contextAttrs(p_Context);
	string coRes2 = typeAttrs("", p_Context);
	if (coRes1 != "" && coRes2 != "")
		coRes1 += ", ";
	return coRes1 + coRes2;
}

// types: return string containig all the types
string CStatement::types() const throw(ExMutex)
{
	string coRes;
	bool bFirst = true;
	CMutexHandler m(&m_coTypesOp);
	CTypeAttributesMap::const_iterator coIt;
	for (coIt = m_pcoTypes->begin(); coIt != m_pcoTypes->end(); ++coIt)
	{
		if (bFirst)
			bFirst = false;
		else
			coRes += ", ";
		coRes += (*coIt).first;
	}
	return coRes;
}

// updateTypes: set the types hash
// Parameters:
//		const CTypeAttributesHash* p_pcoTypeAttributes - pointer to types hash
bool CStatement::updateTypes(CTypeAttributesMap* p_pcoTypeAttributes) throw(ExMutex)
{
	SafeAutoPtr<CTypeAttributesMap> pcoTypeAttrs(p_pcoTypeAttributes);
	if (pcoTypeAttrs.get() == NULL)
		return false;
	CMutexHandler m(&m_coTypesOp);
	if (*pcoTypeAttrs == *m_pcoTypes)
		return false;
	CTypeAttributesMap* t = m_pcoTypes;
	m_pcoTypes = pcoTypeAttrs.release();
	delete t;
	return true;
}

// print statement
void CStatement::print(int p_nStartIndent, const string& p_rcoIndent) const
{
	cout << identifier() << endl;
	CStatementContextMap::const_iterator coCIt = m_pcoContexts->begin();
	for (; coCIt != m_pcoContexts->end(); ++coCIt)
	{
		(*coCIt).second->print();
	}
	CTypeAttributesMap::const_iterator coTIt = m_pcoTypes->begin();
	for (; coTIt != m_pcoTypes->end(); ++coTIt)
		(*coTIt).second->print();
}
