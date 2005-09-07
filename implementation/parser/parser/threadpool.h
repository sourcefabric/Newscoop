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

Defines ThreadPool class and exception classes. ThreadPool implements a
container of threads. It manages thread start/stop events. StartThread will
create if necessary and start a new thread.

******************************************************************************/

#ifndef THREADPOOL_H
#define THREADPOOL_H

#include <pthread.h>
#include <semaphore.h>

#include "mutex.h"
#include "cms_types.h"
#include "globals.h"

#define ThreadSvLow 0
#define ThreadSvRetry 1
#define ThreadSvAbort -1

// ExThread class; exception thrown by ThreadPool class
class ExThread
{
public:
	ExThread(int p_nSeverity, const char* p_pchMessage)
		: m_nSeverity(p_nSeverity), m_pchMessage(p_pchMessage)
	{ m_nThreadId = pthread_self(); }

	virtual ~ExThread() {}

	pthread_t ThreadId() const { return m_nThreadId; }

	int Severity() const { return m_nSeverity; }

	const char* Message() const { return m_pchMessage; }

private:
	pthread_t m_nThreadId;
	int m_nSeverity;
	const char* m_pchMessage;
};

// ExThreadNotFree class; thrown as exception from ThreadPool class
class ExThreadNotFree : public ExThread
{
public:
	ExThreadNotFree()
		: ExThread(ThreadSvRetry, "All threads are occupied. Try again later.") {}

	virtual ~ExThreadNotFree() {}
};

// ExThreadErrCreate class; thrown as exception from ThreadPool class
class ExThreadErrCreate : public ExThread
{
public:
	ExThreadErrCreate()
		: ExThread(ThreadSvRetry, "Error creating thread. Try again later.") {}

	virtual ~ExThreadErrCreate() {}
};

typedef void* (*ThreadRoutine)(void*);

// ThreadPool; container of threads; handles thread creation and destruction
// It can create in advance as many as p_nMinThr threads and start one when a request arrives
class CThreadPool
{
	typedef struct ThreadInfo
	{
		pthread_t m_nThread;		// thread id
		bool m_bWorking;			// true if thread is working
		bool m_bCreated;			// true if thread is created
		sem_t m_Start;				// thread waits for semaphore to be greater than zero to start
		void* m_pArg;				// parameter supplied to thread fuction
	} ThreadInfo;

public:
	// Constructor; parameters: the number of threads to create in advance, number of maximum
	// threads to create, pointer to thread start routine, pointer to parameter to pass to
	// thread start routine
	// Throws ExThread exception on error
	CThreadPool(uint p_nMinThr, uint p_nMaxThr, void* (*p_pStartRoutine)(void*), void* p_pArg)
	throw (ExThread);
	
	// Destructor
	~CThreadPool();

	// Start a thread; throws ExThreadNotFree or ExThreadErrCreate all threads are occupied
	// or cannot create a new thread
	// Parameters:
	//		bool p_bUserDefArg - if true will supply the second parameter to thread routine;
	//			otherwise will supply p_pArg parameter from constructor to the thread routine
	//		void* p_pArg - parameter to supply to thread routine
	void startThread(bool p_bUserDefArg = true, void* p_pArg = 0)
	throw(ExThreadNotFree, ExThreadErrCreate);
	
	// Returns the number of created threads
	uint createdThreads() const
	{
		return m_nCreatedThreads;
	}
	
	// Returns the number of working threads
	uint workingThreads() const
	{
		return m_nWorkingThreads;
	}
	
	// Returns the minimun number of created threads
	uint minThreads() const
	{
		return m_nMinThreads;
	}
	
	// Returns the number of maximum threads
	uint maxThreads() const
	{
		return m_nMaxThreads;
	}
	
	// isFreeThread: returns true if there is at least one free (not working) thread
	bool isFreeThread() const;
	
	// waitFreeThread: returns when there is at least one free thread
	// Parameters:
	//		ulint p_nUSec - time out (microseconds); 0 if wait forever
	// Returns true if at least one thread is free, false otherwise
	bool waitFreeThread(ulint p_nUSec = 0) const;

	// killIdleThreads: kills idle threads
	void killIdleThreads() throw(ExThread);

	// killAllThreads: kills all threads
	void killAllThreads() throw(ExThread);

private:
	uint m_nMinThreads; 					// min threads to create
	uint m_nMaxThreads; 					// max threads to create
	void* (*m_pStartRoutine)(void*); 		// pointer to thread start routine
	void* m_pArg; 							// pointer to start routine argument
	uint m_nCreatedThreads; 				// number of created threads
	uint m_nWorkingThreads; 				// number of working threads
	ThreadInfo* m_pThreads; 				// array of threads
	mutable CMutex m_coMutex;				// mutex used to lock access to member variables

	void Debug(const char* p_pchArg1, bool p_bArg, const void* p_pchArg2, bool p_bIndex = false,
	           uint p_nIndex = 0);
	void LockMutex() const throw (ExThread);
	void UnlockMutex() const throw (ExThread);
	static void* ThreadRoutine(void* p_pThreadLocal);
	static void CleanRoutine(void* p_pThreadLocal);
	void CreateThread(uint p_nIndex) throw(ExThreadNotFree, ExThreadErrCreate);
};

// isFreeThread: returns true if there is at least one free (not working) thread
inline bool CThreadPool::isFreeThread() const
{
	CMutexHandler coMh(&m_coMutex);
	bool bIsFree = m_nWorkingThreads < m_nMaxThreads;
	return bIsFree;
}

#endif
