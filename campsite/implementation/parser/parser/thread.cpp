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

Implementation of the classes defined in thread.h

******************************************************************************/

#include <signal.h>

#include "thread.h"

CThread::CThread()
{
	sem_init(&m_Semaphore, 0, 0);
	m_bRunning = false;
	pthread_attr_t threadAttr;
	pthread_attr_init(&threadAttr);
	pthread_attr_setdetachstate(&threadAttr, PTHREAD_CREATE_DETACHED);
	pthread_create(&m_nThreadId, &threadAttr, CThread::startRoutine, this);
	pthread_attr_destroy(&threadAttr);
}

void CThread::Cancel()
{
	pthread_kill(m_nThreadId, SIGTERM);
	sem_wait(&m_Semaphore);
	m_bRunning = false;
	sem_post(&m_Semaphore);
}
	
bool CThread::IsRunning() const
{
	sem_wait(&m_Semaphore);
	bool bIsRunning = m_bRunning;
	sem_post(&m_Semaphore);
	return bIsRunning;
}

void* CThread::startRoutine(void* p_pParam)
{
	if (p_pParam == NULL)
		return NULL;
	CThread* pcoThread = (CThread*) p_pParam;
	pcoThread->m_bRunning = true;
	sem_post(&pcoThread->m_Semaphore);
	void* pResult = pcoThread->run();
	sem_wait(&pcoThread->m_Semaphore);
	pcoThread->m_bRunning = false;
	sem_post(&pcoThread->m_Semaphore);
	return pResult;
}
