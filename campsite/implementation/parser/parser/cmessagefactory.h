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


#ifndef CMESSAGEFACTORY_H
#define CMESSAGEFACTORY_H


#include <map>
#include <stdexcept>
#include <sstream>


using std::map;
using std::out_of_range;
using std::stringstream;


#include "cmessage.h"


/**
  * class CMessageFactory
  * 
  */

class CMessageFactory
{
public:
	virtual CMessage* createMessage(char* p_pchMsgContent) const = 0;

	virtual uint getMessageTypeId() const = 0;
};


class CURLRequestMessageFactory : public CMessageFactory
{
public:
	virtual CMessage* createMessage(char* p_pchMsgContent) const
		{ return new CMsgURLRequest(p_pchMsgContent); }

	virtual uint getMessageTypeId() const { return CMsgURLRequest::messageTypeId(); }
};


class CURLServeMessageFactory : public CMessageFactory
{
public:
	virtual CMessage* createMessage(char* p_pchMsgContent) const
		{ return new CMsgURLServe(p_pchMsgContent); }

	virtual uint getMessageTypeId() const { return CMsgURLServe::messageTypeId(); }
};


class CMessageFactoryRegister
{
public:
	void insert(CMessageFactory* p_pcoMessageFactory);

	void erase(uint p_nMessageFactoryType);

	CMessage* createMessage(char* p_pchMsgContent);

private:
	// private types
	typedef map <uint, CMessageFactory*> MessageFactoryMap;

private:
	MessageFactoryMap m_coMessageFactories;
};


// CMessageFactoryRegister inline methods

inline void CMessageFactoryRegister::erase(uint p_nMessageFactoryType)
{
	m_coMessageFactories.erase(p_nMessageFactoryType);
}


#endif // CMESSAGEFACTORY_H
