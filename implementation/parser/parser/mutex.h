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

#define MutexSvLow 0
#define MutexSvRetry 1
#define MutexSvAbort -1

// ExMutex: mutex exception; thrown by mutex methods
class ExMutex
{
public:
	ExMutex(int p_nSeverity, const char* p_pchMessage)
			: m_nSeverity(p_nSeverity), m_pchMessage(p_pchMessage)
	{}
	virtual ~ExMutex()
	{}

	int Severity() const
	{
		return m_nSeverity;
	}
	const char* Message() const
	{
		return m_pchMessage;
	}

private:
	int m_nSeverity;
	const char* m_pchMessage;
};

// CMutex: wrapper around a POSIX mutex
class CMutex
{
public:
	// constructor; throws ExMutex exception if unable to initialise
	CMutex() throw(ExMutex);
	// destructor; destroys the mutex
	~CMutex();

	// Lock: lock mutex
	int Lock() throw(ExMutex);
	// Unlock: unlock mutex
	int Unlock();

private:
	sem_t m_Semaphore;	// semaphore used to lock access to members
	pthread_mutex_t m_Mutex;
	bool m_bLocked;
	pthread_t m_LockingThread;
	int m_nLockCnt;
	bool m_bClosing;
};

#endif
