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

Define CMutex class; this is a C++ wrapper of POSIX mutex.

******************************************************************************/

#ifndef MUTEX_H
#define MUTEX_H

#include <pthread.h>
#include <semaphore.h>
#include <iostream>
#include <string>
#include <sstream>

using std::ostream;
using std::string;
using std::stringstream;

#define MutexSvLow 0
#define MutexSvRetry 1
#define MutexSvAbort -1

// ExMutex: mutex exception; thrown by mutex methods
class ExMutex
{
public:
	ExMutex(int p_nSeverity, const char* p_pchMessage)
			: m_nSeverity(p_nSeverity), m_pchMessage(p_pchMessage) {}

	virtual ~ExMutex() {}

	int Severity() const { return m_nSeverity; }

	const char* Message() const { return m_pchMessage; }

private:
	int m_nSeverity;
	const char* m_pchMessage;
};

// CMutex: wrapper around a POSIX mutex
class CMutex
{
public:
	// constructor; throws ExMutex exception if unable to initialise
	CMutex();

	// destructor; destroys the mutex
	~CMutex() throw();

	// lock: lock mutex
	int lock() throw(ExMutex);

	// unlock: unlock mutex
	int unlock() throw();

	void setDebug(bool p_bDebug) throw();

	bool getDebug() const throw()
	{
		return m_bDebug;
	}

	string debugHeaderStr(const string& p_rcoMethod)
	{
		stringstream coHeaderStr;
		coHeaderStr << "CMutex::" << p_rcoMethod << "(addr: " << this << ")";
		return coHeaderStr.str();
	}

private:
	sem_t m_Semaphore;	// semaphore used to lock access to members
	bool m_bLocked;
	pthread_t m_LockingThread;
	int m_nLockCnt;
	bool m_bClosing;
	bool m_bDebug;
	ostream *m_pcoDebug;
};

// CMutexHandler: handler for mutex class; when the object is instantiated it locks the mutex
// when the object is destroied it unlocks the mutex.
class CMutexHandler
{
public:
	// CMutexHandler constructor; locks the mutex
	CMutexHandler(CMutex* m, bool p_bDebug = false) : m_pcoMutex(m)
	{
		m_pcoMutex->setDebug(p_bDebug);
		if (m_pcoMutex)
		{
			m_pcoMutex->lock();
		}
	}

	//CMutexHandler destructor; unlocks the mutex
	~CMutexHandler() throw()
	{
		if (m_pcoMutex)
		{
			m_pcoMutex->unlock();
		}
	}

	//get: returns pointer to handled mutex
	CMutex* get() const { return m_pcoMutex; }

	//reset: unlocks the old mutex, set the mutex to the new one and locks it
	void reset(CMutex* m)
	{
		if (m_pcoMutex)
			m_pcoMutex->unlock();
		m_pcoMutex = m;
		if (m_pcoMutex)
			m_pcoMutex->lock();
	}

	//release: unlocks mutex, set the handled mutex to NULL and returns pointer to old mutex
	CMutex* release()
	{
		CMutex* m = m_pcoMutex;
		m_pcoMutex = NULL;
		return m;
	}

private:
	CMutex* m_pcoMutex;
};

class CThreadMap;
class CThreadQueue;
class CIntQueue;

// CRWMutex: specialised mutex for read and write locking
// CRWMutex is not an ordinary mutex for read/write locking; it doesn't allow one write lock
// and many read locks at the same time; instead it allows or one write lock or zero-many read
// locks at the same time but not both
// See mutex.cpp for detailed information about CRWMutex
class CRWMutex
{
public:
	// constructor; throws ExMutex exception if unable to initialise
	CRWMutex();

	// destructor; destroys the mutex
	~CRWMutex() throw();

	// lockRead: lock mutex for read
	int lockRead() throw(ExMutex);

	// lockRead: lock mutex for write
	int lockWrite() throw(ExMutex);

	// unlockRead: unlock mutex for read
	int unlockRead() throw();

	// unlockWrite: unlock mutex for write
	int unlockWrite() throw();

private:
	void Schedule(pthread_t p_nThreadId, bool p_bWrite);
	void WaitSchedule(pthread_t p_nThreadId, bool p_bWrite) throw(ExMutex);
	void WaitReadUnlock(pthread_t p_nThreadId);
	void SignalWaitingThreads() const;
	void LockRead(pthread_t p_nThreadId, int p_nCount = 1);
	void LockWrite(pthread_t p_nThreadId, int p_nCount = 1);
	void UnlockRead(pthread_t p_nThreadId, int p_nCount = 1);
	void UnlockWrite(pthread_t p_nThreadId, int p_nCount = 1);
	void PrintState(const char* p_pchStartMsg) const;

private:
	mutable sem_t m_Semaphore;	// semaphore used to lock access to members
	bool m_bReadLocked;
	bool m_bWriteLocked;
	CThreadMap* m_pcoReadLocks;
	pthread_t m_nWriteLock;
	int m_nWriteLockCounter;
	bool m_bRestoreReadLock;
	int m_nReadLockCounter;
	CThreadQueue* m_pcoThreadQueue;
	CIntQueue* m_pcoScheduler;
	mutable pthread_cond_t m_WaitCond;
	mutable pthread_mutex_t m_CondMutex;
};

// CRWMutexHandler: handler for mutex class; when the object is instantiated it locks the mutex
// when the object is destroied it unlocks the mutex.
class CRWMutexHandler
{
public:
	// CRWMutexHandler constructor; locks the mutex
	CRWMutexHandler(CRWMutex* m, bool p_bWrite = false) : m_pcoMutex(m), m_bWrite(p_bWrite)
	{
		if (m_pcoMutex)
		{
			if (!m_bWrite)
				m_pcoMutex->lockRead();
			else
				m_pcoMutex->lockWrite();
		}
	}

	//CMutexHandler destructor; unlocks the mutex
	~CRWMutexHandler() throw()
	{
		if (m_pcoMutex)
		{
			if (!m_bWrite)
				m_pcoMutex->unlockRead();
			else
				m_pcoMutex->unlockWrite();
		}
	}

	//get: returns pointer to handled mutex
	CRWMutex* get() const { return m_pcoMutex; }

	//reset: unlocks the old mutex, set the mutex to the new one and locks it
	void reset(CRWMutex* m)
	{
		if (m_pcoMutex)
		{
			if (!m_bWrite)
				m_pcoMutex->unlockRead();
			else
				m_pcoMutex->unlockWrite();
		}
		m_pcoMutex = m;
		if (m_pcoMutex)
		{
			if (!m_bWrite)
				m_pcoMutex->lockRead();
			else
				m_pcoMutex->lockWrite();
		}
	}

	//release: set the handled mutex to NULL and returns pointer to old mutex
	CRWMutex* release()
	{
		CRWMutex* m = m_pcoMutex;
		m_pcoMutex = NULL;
		return m;
	}

private:
	CRWMutex* m_pcoMutex;
	bool m_bWrite;
};

#endif
