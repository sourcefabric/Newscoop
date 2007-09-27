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

Define CThread class.

******************************************************************************/

#ifndef _THREAD_H
#define _THREAD_H

#include <pthread.h>
#include <semaphore.h>

#include "mutex.h"

class CThread
{
public:
	CThread();

	virtual ~CThread() { cancel(); }

	void run();

	void cancel();

	bool isRunning() const;

protected:
	virtual void* Run() = 0;

private:
	static void* StartRoutine(void* p_pParam);

private:
	pthread_t m_nThreadId;
	mutable sem_t m_Semaphore;	// semaphore used to lock access to members
	mutable sem_t m_RunSem;		// semaphore used to syncronize thread start
	bool m_bRunning;
};

#endif
