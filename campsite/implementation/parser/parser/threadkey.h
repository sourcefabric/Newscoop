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

Define CThreadKey and CThreadKeyConst classe templates. They are C++ wrappers
of POSIX key variables.

******************************************************************************/

#ifndef THREADKEY
#define THREADKEY
#include <pthread.h>

#define TK_TRY try {

#define TK_CATCH } catch(ExTK& rcoEx) { return; }

#define TK_CATCH_ERR } catch(ExTK& rcoEx) { return ERR_NOMEM; }

#define TK_CATCH_RES(res) } catch(ExTK& rcoEx) { return res; }

typedef enum { ERR_CREATE, ERR_ALLOC } TKErrNr;

// ExTK: used as exception thrown by classes instantiated from CThreadKey
// and CThreadKeyConst classes
class ExTK
{
public:
	ExTK(TKErrNr p_ErrNr) : m_ErrNr(p_ErrNr) {}

	~ExTK() {}

	TKErrNr ErrNr() const { return m_ErrNr; }

private:
	TKErrNr m_ErrNr;
};

// CThreadKey template; wrapper around POSIX key variables; hadles data destruction
template <class DataType>
class CThreadKey
{
public:
	CThreadKey(DataType* p_pData = NULL) throw (ExTK)
	{
		if (pthread_key_create(&m_Key, destroyData) != 0)
			throw ExTK(ERR_CREATE);
		*this = (DataType*)p_pData;
	}
	~CThreadKey()
	{
		Clear();
		pthread_key_delete(m_Key);
	}

	const CThreadKey<DataType>& operator =(DataType* p_pData)
	{
		Clear();
		pthread_setspecific(m_Key, (void*)p_pData);
		return *this;
	}
	const CThreadKey<DataType>& operator =(const DataType& p_rData) throw (ExTK)
	{
		if (pthread_getspecific(m_Key) == NULL)
		{
			pthread_setspecific(m_Key, (void*)new DataType(p_rData));
			if (pthread_getspecific(m_Key) == NULL)
				throw ExTK(ERR_ALLOC);
		}
		else
			*((DataType*)pthread_getspecific(m_Key)) = p_rData;
		return *this;
	}
	DataType& operator *() throw (ExTK)
	{
		if (pthread_getspecific(m_Key) == NULL)
			pthread_setspecific(m_Key, (void*)new DataType);
		if (pthread_getspecific(m_Key) == NULL)
			throw ExTK(ERR_ALLOC);
		return *((DataType*)pthread_getspecific(m_Key));
	}
	DataType* operator &() const
	{
		return (DataType*) pthread_getspecific(m_Key);
	}
	void Clear()
	{
		if ((DataType*) pthread_getspecific(m_Key) != NULL)
		{
			destroyData((DataType*) pthread_getspecific(m_Key));
			pthread_setspecific(m_Key, NULL);
		}
	}

private:
	static void destroyData(void* p_pData) throw();

	pthread_key_t m_Key;
};

// CThreadKeyConst template; wrapper around POSIX key variables; doesn't handle data destruction
template <class DataType>
class CThreadKeyConst
{
public:
	CThreadKeyConst(const DataType* p_pData = NULL) throw (ExTK)
	{
		if (pthread_key_create(&m_Key, destroyData) != 0)
			throw ExTK(ERR_CREATE);
		*this = (const DataType*)p_pData;
	}
	~CThreadKeyConst()
	{
		pthread_key_delete(m_Key);
	}

	const CThreadKeyConst<DataType>& operator =(const DataType* p_pData)
	{
		pthread_setspecific(m_Key, (void*)p_pData);
		return *this;
	}
	DataType* operator &() const
	{
		return (DataType*) pthread_getspecific(m_Key);
	}

private:
	static void destroyData(void* p_pData) {}

	pthread_key_t m_Key;
};

#endif
