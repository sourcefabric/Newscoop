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

#ifndef _CMS_CAMPSITEINSTANCE
#define _CMS_CAMPSITEINSTANCE

#include <sys/types.h>
#include <map>

#include "readconf.h"
#include "mutex.h"


// some typedefs for future use

class CCampsiteInstance;

typedef int (*InstanceFunction) (const ConfAttrValue&);
typedef map <string, CCampsiteInstance*, less<string> > CCampsiteInstanceMap;


/**
 * class CCampsiteInstance declaration
 *
 */

class CCampsiteInstance
{
	public:
		CCampsiteInstance(const string& p_rcoConfDir, InstanceFunction p_pInstFunc)
				throw (ConfException)
		: m_nChildPID(0), m_coConfDir(p_rcoConfDir), m_pInstanceFunction(p_pInstFunc),
		m_coAttributes(""), m_bRunning(false)
		{
			m_coAttributes = ReadConf();
			RegisterInstance();
		}

		~CCampsiteInstance() { stop(); UnregisterInstance(); }

		void setInstanceFunction(InstanceFunction p_pInstFunc) throw (RunException);

		pid_t getPID() const throw (RunException);

		const string& getName() const { return m_coName; }

		bool isRunning() const;

		pid_t run() throw (RunException);

		void stop();

		static const CCampsiteInstanceMap& readFromDirectory(const string& p_rcoDir,
				InstanceFunction p_pInstFunc) throw (ConfException);

	private:
		CCampsiteInstance(const CCampsiteInstance&); // do not allow copy

		const ConfAttrValue& ReadConf() throw (ConfException);

		void RegisterInstance();

		void UnregisterInstance();

		static void VerifyDir(const string& p_rcoDir) throw (ConfException);

	private:
		mutable pid_t m_nChildPID;
		string m_coName;
		string m_coConfDir;
		InstanceFunction m_pInstanceFunction;
		ConfAttrValue m_coAttributes;
		mutable bool m_bRunning;
};


inline void CCampsiteInstance::setInstanceFunction(InstanceFunction p_pInstFunc)
		throw (RunException)
{
	if (m_bRunning)
		throw RunException("Campsite instance is running, can't change the instance function");
	m_pInstanceFunction = p_pInstFunc;
}

inline pid_t CCampsiteInstance::getPID() const throw (RunException)
{
	if (!m_bRunning)
		throw RunException("Campsite instance not running, unable to return PID");
	return m_nChildPID;
}



/**
 * class CCampsiteInstanceRegister declaration
 * stores all CCampsiteInstance objects; static object
 */

class CCampsiteInstanceRegister
{
	public:
		CCampsiteInstanceRegister() {}

		static CCampsiteInstanceRegister& get();

		const CCampsiteInstanceMap& getCampsiteInstances() const;

		void insert(CCampsiteInstance& p_rcoInstance);

		void erase(pid_t p_nInstancePID);

		void unsetPID(pid_t p_nInstancePID);

		void erase(const string& p_rcoInstanceName);

		bool has(pid_t p_nInstancePID) const;

		bool has(const string& p_rcoInstanceName) const;

		bool isEmpty() const { return m_coCCampsiteInstances.empty(); }

		CCampsiteInstance* getCampsiteInstance(pid_t p_rcoInstancePID) const
				throw (InvalidValue);

		CCampsiteInstance* getCampsiteInstance(const string& p_rcoInstanceName) const throw (InvalidValue);

		void debug() const;

	private:
		CCampsiteInstanceMap m_coCCampsiteInstances;
		map < pid_t, string, less<pid_t> > m_coInstancePIDs;

#ifdef _REENTRANT
		mutable CMutex m_coMutex;
#endif
};


// CCampsiteInstanceRegister inline methods

inline const CCampsiteInstanceMap& CCampsiteInstanceRegister::getCampsiteInstances() const
{
	return m_coCCampsiteInstances;
}

inline void CCampsiteInstanceRegister::unsetPID(pid_t p_nInstancePID)
{
#ifdef _REENTRANT
	CMutexHandler coLockHandler(&m_coMutex);
#endif
	m_coInstancePIDs.erase(p_nInstancePID);
}

inline bool CCampsiteInstanceRegister::has(pid_t p_nInstancePID) const
{
#ifdef _REENTRANT
	CMutexHandler coLockHandler(&m_coMutex);
#endif
	return m_coInstancePIDs.find(p_nInstancePID) != m_coInstancePIDs.end();
}

inline bool CCampsiteInstanceRegister::has(const string& p_rcoInstanceName) const
{
#ifdef _REENTRANT
	CMutexHandler coLockHandler(&m_coMutex);
#endif
	return m_coCCampsiteInstances.find(p_rcoInstanceName) != m_coCCampsiteInstances.end();
}

#endif
