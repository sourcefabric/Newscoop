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

#include <unistd.h>

#include "mutex.h"

// CMutex (default constructor)
CMutex::CMutex() throw(ExMutex)
{
	sem_init(&m_Semaphore, 0, 0);
	if (pthread_mutex_init(&m_Mutex, NULL) != 0)
		throw ExMutex(MutexSvAbort, "Unable to initialize mutex.");
	m_bLocked = false;
	m_LockingThread = 0;
	m_nLockCnt = 0;
	m_bClosing = false;
	sem_post(&m_Semaphore);
}

// ~CMutex (destructor)
CMutex::~CMutex()
{
	sem_wait(&m_Semaphore);
	m_bClosing = true;
	sem_post(&m_Semaphore);
	for (; ; )
	{
		sem_wait(&m_Semaphore);
		if (m_bLocked && m_LockingThread == pthread_self())
		{
			for (; ; )
				if (pthread_mutex_unlock(&m_Mutex) == 0)
					break;
			m_bLocked = false;
			m_nLockCnt = 0;
		}
		if (!m_bLocked && sem_destroy(&m_Semaphore) == 0)
			break;
		sem_post(&m_Semaphore);
		usleep(10);
	}
	for (; ; )
	{
		if (pthread_mutex_destroy(&m_Mutex) == 0)
			break;
		usleep(10);
	}
}

int CMutex::Lock() throw(ExMutex)
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
	for (; ; )
	{
		sem_wait(&m_Semaphore);
		if (!m_bLocked)			// wait until mutex is not locked
			break;
		sem_post(&m_Semaphore);
		usleep(10);
	}
	if (pthread_mutex_lock(&m_Mutex) != 0)	// lock mutex
	{
		sem_post(&m_Semaphore);
		throw ExMutex(MutexSvRetry, "Error locking mutex");
		return 1;
	}
	m_bLocked = true;
	m_LockingThread = pthread_self();
	m_nLockCnt = 1;
	sem_post(&m_Semaphore);
	return 0;
}

int CMutex::Unlock()
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
			pthread_mutex_unlock(&m_Mutex);
			m_bLocked = false;
		}
	}
	sem_post(&m_Semaphore);
	return 0;
}
