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

Implementation of the classes defined in mutex.h

******************************************************************************/

#include <iostream>
#include <map>
#include <queue>
#include <unistd.h>

#include "mutex.h"

using std::cout;
using std::endl;
using std::map;
using std::queue;
using std::pair;

const int g_nMaxTries = 1000;

// CMutex (default constructor)
CMutex::CMutex()
{
	sem_init(&m_Semaphore, 0, 0);
	m_bLocked = false;
	m_LockingThread = 0;
	m_nLockCnt = 0;
	m_bClosing = false;
	sem_post(&m_Semaphore);
}

// ~CMutex (destructor)
CMutex::~CMutex() throw()
{
	sem_wait(&m_Semaphore);
	m_bClosing = true;
	if (m_bLocked && m_LockingThread == pthread_self())
	{
		m_bLocked = false;
		m_nLockCnt = 0;
	}
	sem_post(&m_Semaphore);
}

// lock: locks mutex
int CMutex::lock() throw(ExMutex)
{
	sem_wait(&m_Semaphore);		// wait for semaphore in order to work with members
	if (m_bClosing)
	{
		sem_post(&m_Semaphore);
		throw ExMutex(MutexSvAbort, "Mutex is closing");
		return 1;
	}
	if (m_bLocked && m_LockingThread == pthread_self())
	{							// if locked by myself just increment the lock counter
		m_nLockCnt++;
		sem_post(&m_Semaphore);
		return 0;
	}
	sem_post(&m_Semaphore);		// release semaphore
	int i;
	for (i = 1; i < g_nMaxTries; i++)
	{
		sem_wait(&m_Semaphore);
		if (!m_bLocked)			// wait until mutex is not locked
			break;
		sem_post(&m_Semaphore);
		usleep(200);
	}
	if (i >= g_nMaxTries)
	{
		sem_post(&m_Semaphore);
		throw ExMutex(MutexSvAbort, "Error locking mutex");
		return 1;
	}
	m_bLocked = true;
	m_LockingThread = pthread_self();
	m_nLockCnt = 1;
	sem_post(&m_Semaphore);
	return 0;
}

// unlock: unlocks mutex
int CMutex::unlock() throw()
{
	sem_wait(&m_Semaphore);
	if (m_bClosing)
	{
		sem_post(&m_Semaphore);
		return 0;
	}
	if (m_bLocked && m_LockingThread == pthread_self())
	{							// if locked by myself decrement lock count
		m_nLockCnt--;
		if (m_nLockCnt == 0)	// when lock count reach 0 unlock mutex
		{
			m_bLocked = false;
		}
	}
	sem_post(&m_Semaphore);
	return 0;
}

/*  CRWMutex description

// CRWMutex is not an ordinary mutex for read/write locking; it doesn't allow one write lock
// and many read locks at the same time; instead it allows or one write lock or zero-many read
// locks at the same time but not both

CRWMutex is an automate with six states (S0-S6). It accepts 4 types of events:
- lock for read (lr)
- lock for write (lw)
- unlock for read (ur)
- unlock for write (uw)

The five states are:
- S0: unlocked for read, unlocked for write
- S1: locked for read, unlocked for write, no write locks scheduled, no read locks scheduled
- S2: unlocked for read, locked for write, no read locks scheduled, no write locks scheduled
- S3: locked for read, unlocked for write, write locks scheduled,
	0-many read locks scheduled (after scheduled write locks)
- S4: unlocked for read, locked for write, read locks scheduled,
	0-many write locks scheduled (after scheduled read locks)
- S5: unlocked for read, locked for write, write locks scheduled,
	0-many read locks scheduled (after scheduled write locks)
- S6: locked for read and write by one thread, 0-many read/write locks scheduled

Below is the table describing the automate behaviour; lr, lw, ur, uw are events (see the previous
paragraphs) and S0-S5 are the states.

+----+----+----------+----------+-----------+----------+----------+----------+
|    | S0 | S1       | S2       | S3        | S4       | S5       | S6       |
+----+----+----------+----------+-----------+----------+----------+----------+
| lr | S1 | S1       | S4 if C5 | S3        | S4 if C5 | S5 if C5 | S6       |
|    |    |          | S6 if C6 |           | S6 if C6 | S6 if C6 |          |
+----+----+----------+----------+-----------+----------+----------+----------+
| lw | S2 | S3 if C1 | S2 if C7 | S3 if C9  | S4       | S5       | S6       |
|    |    | S6 if C2 | S5 if C8 | S6 if C10 |          |          |          |
+----+----+----------+----------+-----------+----------+----------+----------+
| ur | S0 | S1 if C3 | S2       | S3 if C11 | S4       | S5       | S6 if C3 |
|    |    | S0 if C4 |          | S4 if C12 |          |          | S5 if C4 |
+----+----+----------+----------+-----------+----------+----------+----------+
| uw | S0 | S1       | S0       | S3        | S3       | S2       | S5 if C5 |
|    |    |          |          |           |          |          | S3 if C6 |
+----+----+----------+----------+-----------+----------+----------+----------+

C1: the thread requesting the write lock did not acquire a read lock previously
C2: the thread requesting the write lock acquired a read lock previously
C3: the thread performing unlock read is not the only one who acquired a read lock and/or it
	performed more consecutive read locks and less consecutive read unlocks
C4: the thread performing unlock read is the only one who acquired a read lock and it performed n+1
	consecutive read locks and n consecutive read unlocks
C5: the thread performing read lock has not a write lock on the mutex
C6: the thread performing read lock has a write lock on the mutex
C7: the thread performing write lock already has a write lock on the mutex
C8: the thread performing write lock doesn't have a write lock on the mutex
C9: the thread performing write lock doesn't have a write lock on the mutex
C10: the thread performing write lock has already ackuired a read lock
C11: the thread requesting read unlock doesn't realease the mutex (performed n+1
	consecutive read locks and less then n consecutive read unlocks)
C12: the thread requesting read unlock releases the mutex (the nth consecutive read unlock after
	n consecutive read locks)

	mutable sem_t m_Semaphore; - it is used to lock access to object members
	bool m_bReadLocked; - true if mutex is locked for read
	bool m_bWriteLocked; - true if mutex is locked for write
	bool m_bClosing; - true if mutex is closing
	CThreadMap* m_pcoReadLocks; - map of current read locks (empty if m_bReadLocked = false - not
		locked for read)
	pthread_t m_nWriteLock; - write lock thread identifier (0 if m_bWriteLocked = false - not
		locked for write)
	int m_nWriteLockCounter; - counter of the number of consecutive write lockes performed by the locking thread
	bool m_bRestoreReadLock; - true if in S6: one thread locked for read and the same thread locks for write
	int m_nReadLockCounter; - counter of the number of consecutive read lockes to be restored when unlocking
		for write
	CThreadQueue* m_pcoThreadQueue; - queue containing threads identifiers scheduled for locking
	CIntQueue* m_pcoScheduler; - queue containing pair of int, bool; if bool = false the next
		thread is scheduled for read locking, otherwise for write locking; the int in the pair
		is a counter of how many locks are scheduled (for read/write)
	mutable pthread_cond_t m_WaitCond; - threads scheduled for locking are in wait state; they
		are woken up when this condition is signaled
	mutable pthread_mutex_t m_CondMutex; - mutex used for m_WaitCond

*/

#ifdef _DEBUG
#define DEBUG_RW_MUTEX(msg) PrintState(msg);
#else
#define DEBUG_RW_MUTEX(msg)
#endif

class CThreadMap : public map<pthread_t, int> {};
class CThreadQueue : public queue<pthread_t> {};
class CIntQueue : public queue<pair<int, bool> > {};

// CRWMutex constructor; throws ExMutex exception if unable to initialise
CRWMutex::CRWMutex()
{
	sem_init(&m_Semaphore, 0, 0);
	m_bReadLocked = false;
	m_bWriteLocked = false;
	m_pcoReadLocks = new CThreadMap;
	m_nWriteLock = 0;
	m_nWriteLockCounter = 0;
	m_bRestoreReadLock = false;
	m_nReadLockCounter = 0;
	m_pcoThreadQueue = new CThreadQueue;
	m_pcoScheduler = new CIntQueue;
	pthread_mutex_init(&m_CondMutex, NULL);
	pthread_cond_init(&m_WaitCond, NULL);
	sem_post(&m_Semaphore);
}

// CRWMutex destructor; destroys the mutex
CRWMutex::~CRWMutex() throw()
{
	sem_wait(&m_Semaphore);
	m_bReadLocked = false;
	m_bWriteLocked = false;
	m_nWriteLock = 0;
	delete m_pcoReadLocks;
	delete m_pcoThreadQueue;
	delete m_pcoScheduler;
	pthread_mutex_destroy(&m_CondMutex);
	pthread_cond_destroy(&m_WaitCond);
	sem_post(&m_Semaphore);
}

// lockRead: lock mutex for read
int CRWMutex::lockRead() throw(ExMutex)
{
	sem_wait(&m_Semaphore);
	pthread_t nMyId = pthread_self();
	if (!m_bReadLocked && !m_bWriteLocked)
	{
		LockRead(nMyId);
		DEBUG_RW_MUTEX("lockRead-S0");
		sem_post(&m_Semaphore);
		return 0;
	}
	if (m_bReadLocked && !m_bWriteLocked)
	{
		if (m_pcoScheduler->empty())
		{
			LockRead(nMyId);
			DEBUG_RW_MUTEX("lockRead-S1");
		}
		else
		{
			Schedule(nMyId, false);
			DEBUG_RW_MUTEX("lockRead-S3-wait");
			sem_post(&m_Semaphore);
			WaitSchedule(nMyId, false);
			LockRead(nMyId);
			DEBUG_RW_MUTEX("lockRead-S3-endwait");
		}
		sem_post(&m_Semaphore);
		return 0;
	}
	if (!m_bReadLocked && m_bWriteLocked)
	{
		if (m_nWriteLock == nMyId)
		{
			m_bRestoreReadLock = true;
			m_nReadLockCounter++;
			DEBUG_RW_MUTEX("lockRead-S6");
			sem_post(&m_Semaphore);
			return 0;
		}
		Schedule(nMyId, false);
		DEBUG_RW_MUTEX("lockRead-S4-wait");
		sem_post(&m_Semaphore);
		WaitSchedule(nMyId, false);
		LockRead(nMyId);
		DEBUG_RW_MUTEX("lockRead-S4-endwait");
		sem_post(&m_Semaphore);
		return 0;
	}
	sem_post(&m_Semaphore);
	return 1;
}

// lockRead: lock mutex for write
int CRWMutex::lockWrite() throw(ExMutex)
{
	sem_wait(&m_Semaphore);
	pthread_t nMyId = pthread_self();
	if (!m_bReadLocked && !m_bWriteLocked)
	{
		LockWrite(nMyId);
		DEBUG_RW_MUTEX("lockWrite-S0");
		sem_post(&m_Semaphore);
		return 0;
	}
	if (m_bReadLocked && !m_bWriteLocked)
	{
		if (m_pcoReadLocks->find(nMyId) != m_pcoReadLocks->end())
		{
			DEBUG_RW_MUTEX("lockWrite-S6-wait");
			WaitReadUnlock(nMyId);
			m_bRestoreReadLock = true;
			m_nReadLockCounter = (*m_pcoReadLocks)[nMyId];
			UnlockRead(nMyId, m_nReadLockCounter);
			LockWrite(nMyId);
			DEBUG_RW_MUTEX("lockWrite-S6-endwait");
			sem_post(&m_Semaphore);
			return 0;
		}
		Schedule(nMyId, true);
		DEBUG_RW_MUTEX("lockWrite-S3-wait");
		sem_post(&m_Semaphore);
		WaitSchedule(nMyId, true);
		LockWrite(nMyId);
		DEBUG_RW_MUTEX("lockWrite-S3-endwait");
		sem_post(&m_Semaphore);
		return 0;
	}
	if (!m_bReadLocked && m_bWriteLocked)
	{
		if (m_nWriteLock != nMyId)
		{
			Schedule(nMyId, true);
			DEBUG_RW_MUTEX("lockWrite-S5-wait");
			sem_post(&m_Semaphore);
			WaitSchedule(nMyId, true);
			LockWrite(nMyId);
			DEBUG_RW_MUTEX("lockWrite-S5-endwait");
		}
		else
		{
			LockWrite(nMyId);
			DEBUG_RW_MUTEX("lockWrite-S2");
		}
		sem_post(&m_Semaphore);
		return 0;
	}
	sem_post(&m_Semaphore);
	return 1;
}

// unlockRead: unlock mutex for read
int CRWMutex::unlockRead() throw()
{
	sem_wait(&m_Semaphore);
	pthread_t nMyId = pthread_self();
	if (!m_bReadLocked && !m_bWriteLocked)
	{
		DEBUG_RW_MUTEX("unlockRead-S0");
		sem_post(&m_Semaphore);
		return 0;
	}
	if (m_bReadLocked && !m_bWriteLocked)
	{
		UnlockRead(nMyId);
		SignalWaitingThreads();
		DEBUG_RW_MUTEX("unlockRead-S1");
		sem_post(&m_Semaphore);
		return 0;
	}
	if (!m_bReadLocked && m_bWriteLocked)
	{
		if (m_nWriteLock == nMyId && (m_nReadLockCounter <= 0 || --m_nReadLockCounter <= 0))
			m_bRestoreReadLock = false;
		DEBUG_RW_MUTEX("unlockRead-S2");
		sem_post(&m_Semaphore);
		return 0;
	}
	sem_post(&m_Semaphore);
	return 1;
}

// unlockWrite: unlock mutex for write
int CRWMutex::unlockWrite() throw()
{
	sem_wait(&m_Semaphore);
	pthread_t nMyId = pthread_self();
	if (!m_bReadLocked && !m_bWriteLocked)
	{
		DEBUG_RW_MUTEX("unlockWrite-S0");
		sem_post(&m_Semaphore);
		return 0;
	}
	if (m_bReadLocked && !m_bWriteLocked)
	{
		DEBUG_RW_MUTEX("unlockWrite-S1");
		sem_post(&m_Semaphore);
		return 0;
	}
	if (!m_bReadLocked && m_bWriteLocked)
	{
		if (nMyId == m_nWriteLock)
		{
			UnlockWrite(nMyId);
			SignalWaitingThreads();
		}
		DEBUG_RW_MUTEX("unlockWrite-S2");
		sem_post(&m_Semaphore);
		return 0;
	}
	sem_post(&m_Semaphore);
	return 1;
}

inline void CRWMutex::Schedule(pthread_t p_nThreadId, bool p_bWrite)
{
	m_pcoThreadQueue->push(p_nThreadId);
	if (m_pcoScheduler->empty())
		m_pcoScheduler->push(pair<int, bool>(0, p_bWrite));
	pair<int, bool>& rcoSched = m_pcoScheduler->back();
	if (rcoSched.second != p_bWrite)
		m_pcoScheduler->push(pair<int, bool>(1, p_bWrite));
	else
		rcoSched.first++;
}

void CRWMutex::WaitSchedule(pthread_t p_nThreadId, bool p_bWrite) throw(ExMutex)
{
	while (true)
	{
		pthread_mutex_lock(&m_CondMutex);
		pthread_cond_wait(&m_WaitCond, &m_CondMutex);
		pthread_mutex_unlock(&m_CondMutex);
		sem_wait(&m_Semaphore);
		DEBUG_RW_MUTEX("WaitSchedule");
		if ((p_bWrite && m_bReadLocked && p_nThreadId != m_nWriteLock)
		    || (m_bWriteLocked && p_nThreadId != m_nWriteLock))
		{
			sem_post(&m_Semaphore);
			continue;
		}
		if (m_pcoScheduler->empty() || m_pcoThreadQueue->empty())
		{
			sem_post(&m_Semaphore);
			throw ExMutex(MutexSvAbort, "scheduler internal error");
		}
		pair<int, bool>& rcoSched = m_pcoScheduler->front();
		if (rcoSched.second != p_bWrite || m_pcoThreadQueue->front() != p_nThreadId)
		{
			sem_post(&m_Semaphore);
			continue;
		}
		if (--(rcoSched.first) <= 0)
			m_pcoScheduler->pop();
		m_pcoThreadQueue->pop();
		break;
	}
	DEBUG_RW_MUTEX("WaitSchedule-end");
	SignalWaitingThreads();
}

void CRWMutex::WaitReadUnlock(pthread_t p_nThreadId)
{
	int nReadLocks = 0;
	while (true)
	{
		CThreadMap::const_iterator coIt = m_pcoReadLocks->find(p_nThreadId);
		if (m_pcoReadLocks->size() == 1 && coIt != m_pcoReadLocks->end())
			break;
		if (coIt != m_pcoReadLocks->end())
		{
			nReadLocks = (*coIt).second;
			UnlockRead(p_nThreadId, nReadLocks);
		}
		sem_post(&m_Semaphore);
		pthread_mutex_lock(&m_CondMutex);
		pthread_cond_wait(&m_WaitCond, &m_CondMutex);
		pthread_mutex_unlock(&m_CondMutex);
		sem_wait(&m_Semaphore);
	}
	if (nReadLocks > 0)
		LockRead(p_nThreadId, nReadLocks);
}

inline void CRWMutex::SignalWaitingThreads() const
{
	pthread_mutex_lock(&m_CondMutex);
	pthread_cond_broadcast(&m_WaitCond);
	pthread_mutex_unlock(&m_CondMutex);
}

inline void CRWMutex::LockRead(pthread_t p_nThreadId, int p_nCounter)
{
	m_bReadLocked = true;
	if (m_pcoReadLocks->find(p_nThreadId) != m_pcoReadLocks->end())
		(*m_pcoReadLocks)[p_nThreadId] += p_nCounter;
	else
		(*m_pcoReadLocks)[p_nThreadId] = p_nCounter;
}

inline void CRWMutex::LockWrite(pthread_t p_nThreadId, int p_nCount)
{
	m_bWriteLocked = true;
	m_nWriteLock = p_nThreadId;
	m_nWriteLockCounter += p_nCount;
}

inline void CRWMutex::UnlockRead(pthread_t p_nThreadId, int p_nCount)
{
	if (m_pcoReadLocks->find(p_nThreadId) != m_pcoReadLocks->end())
	{
		(*m_pcoReadLocks)[p_nThreadId] -= p_nCount;
		if ((*m_pcoReadLocks)[p_nThreadId] <= 0)
			m_pcoReadLocks->erase(p_nThreadId);
	}
	if (m_pcoReadLocks->empty())
		m_bReadLocked = false;
}

inline void CRWMutex::UnlockWrite(pthread_t p_nThreadId, int p_nCount)
{
	if (m_nWriteLock != p_nThreadId)
		return;
	m_nWriteLockCounter -= p_nCount;
	if (m_nWriteLockCounter <= 0)
	{
		m_bWriteLocked = false;
		m_nWriteLock = 0;
		if (m_bRestoreReadLock)
		{
			LockRead(p_nThreadId, m_nReadLockCounter);
			m_bRestoreReadLock = false;
			m_nReadLockCounter = 0;
		}
	}
}

inline void CRWMutex::PrintState(const char* p_pchStartMsg) const
{
	cout << p_pchStartMsg << " ";
	int nSemValue;
	sem_getvalue(&m_Semaphore, &nSemValue);
	cout << pthread_self() << " sem: " << nSemValue << "; read: " << (int)m_bReadLocked
	     << "; write: " << (int)m_bWriteLocked
	     << "; write lock: " << m_nWriteLock << "; read locks: ";
	CThreadMap::const_iterator coIt = m_pcoReadLocks->begin();
	for (; coIt != m_pcoReadLocks->end(); ++coIt)
	{
		if (coIt != m_pcoReadLocks->begin())
			cout << ", ";
		cout << (*coIt).first << ":" << (*coIt).second;
	}
	cout << endl << "\t";
	if (!m_pcoThreadQueue->empty())
	{
		cout << " next sched: " << m_pcoThreadQueue->front();
	}
	if (!m_pcoScheduler->empty())
	{
		bool bWrite = m_pcoScheduler->front().second;
		int nNr = m_pcoScheduler->front().first;
		cout << " next sched: " << (bWrite ? "write" : "read") << ":" << nNr;
	}
	cout << " restore rd: " << (int)m_bRestoreReadLock << ":" << m_nReadLockCounter
	     << ", wl count: " << m_nWriteLockCounter;
	cout << endl;
}
