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

#include <pwd.h>
#include <grp.h>
#include <sys/types.h>
#include <dirent.h>
#include <sys/stat.h> 
#include <unistd.h>
#include <signal.h>
#include <sys/wait.h>

#include "ccampsiteinstance.h"

using std::cout;
using std::endl;


/**
 * class CCampsiteInstance implementation
 *
 */

bool CCampsiteInstance::isRunning() const
{
	if (m_bRunning)
	{
		int nStatus;
		int nRes = waitpid(m_nChildPID, &nStatus, WNOHANG);
		m_bRunning = nRes == 0;
		g_coDebug << "***" << m_coName << "* (" << m_nChildPID << ") isRunning res: "
			<< nRes << ", running: " << m_bRunning << ", status: " << nStatus << endl;
		if (!m_bRunning)
		{
			CCampsiteInstanceRegister::get().unsetPID(m_nChildPID);
			m_nChildPID = 0;
		}
	}
	return m_bRunning;
}

pid_t CCampsiteInstance::run() throw (RunException)
{
#ifdef _DEBUG_SOURCE
#warning *******************************************************************************
#warning This compilation option is for source code debugging, do not use in production!
#warning *******************************************************************************
	m_pInstanceFunction(m_coAttributes);
	return 0;
#else
	if (m_bRunning)
		return m_nChildPID;
	pid_t nPid = fork();
	if (nPid == 0) // this is the child - call InstanceFunction
	{
		int nRes = m_pInstanceFunction(m_coAttributes);
		g_coDebug << "* child " << m_coName << "(" << getpid()
			<< ") exited with the code: " << nRes << endl;
		exit(nRes);
	}
	else  // this is the parent, register PID
	{
		m_nChildPID = nPid;
		m_bRunning = true;
		CCampsiteInstanceRegister::get().insert(*this);
	}
	return nPid;
#endif
}

void CCampsiteInstance::stop()
{
	if (!m_bRunning)
		return;
	for (int nIt = 1; nIt <= 10; nIt++)
	{
		kill(m_nChildPID, 15);
		usleep(100);
		if (!isRunning())
			break;
	}
	if (isRunning())
	{
		kill(m_nChildPID, 9);
	}
	m_nChildPID = 0;
	m_bRunning = false;
}

const CCampsiteInstanceMap& CCampsiteInstance::readFromDirectory(const string& p_rcoDir,
		InstanceFunction p_pInstFunc) throw (ConfException)
{
	DIR* pDir = opendir(p_rcoDir.c_str());
	if (pDir == NULL)
		throw ConfException(string("Invalid configuration directory ") + p_rcoDir);

	for (struct dirent* pFile = readdir(pDir); pFile != NULL; pFile = readdir(pDir))
	{
		if (strcmp(pFile->d_name, ".") == 0 || strcmp(pFile->d_name, "..") == 0)
			continue;

		string coFileName = p_rcoDir + "/" + pFile->d_name;
		struct stat FileStat;
		if (stat(coFileName.c_str(), &FileStat) != 0)
			continue;
		if (!S_ISDIR(FileStat.st_mode))
			continue;

		new CCampsiteInstance(coFileName, p_pInstFunc);
	}

	closedir(pDir);

	return CCampsiteInstanceRegister::get().getCampsiteInstances();
}

const ConfAttrValue& CCampsiteInstance::ReadConf() throw (ConfException)
{
	VerifyDir(m_coConfDir);

	string::size_type nSlashPos = m_coConfDir.rfind('/');
	while ((nSlashPos + 1) == m_coConfDir.length() && nSlashPos != string::npos)
	{
		m_coConfDir.erase(nSlashPos, 1);
		nSlashPos = m_coConfDir.rfind('/');
	}
	m_coName = m_coConfDir.substr(nSlashPos != string::npos ? nSlashPos + 1 : 0);

	// read parser configuration
	string coParserConfFile = m_coConfDir + "/parser_conf.php";
	m_coAttributes.open(coParserConfFile);

	// read apache configuration
	string coApacheConfFile = m_coConfDir + "/apache_conf.php";
	m_coAttributes.open(coApacheConfFile);
	struct passwd* pPwEnt = getpwnam(m_coAttributes.valueOf("APACHE_USER").c_str());
	if (pPwEnt == NULL)
		throw ConfException("Invalid user name in conf file");
	struct group* pGrEnt = getgrnam(m_coAttributes.valueOf("APACHE_GROUP").c_str());
	if (pGrEnt == NULL)
		throw ConfException("Invalid group name in conf file");

	// read database configuration
	string coDatabaseConfFile = m_coConfDir + "/database_conf.php";
	m_coAttributes.open(coDatabaseConfFile);

	// read email server configuration
	string coSMTPConfFile = m_coConfDir + "/smtp_conf.php";
	m_coAttributes.open(coSMTPConfFile);

	return m_coAttributes;
}

void CCampsiteInstance::RegisterInstance()
{
	CCampsiteInstanceRegister::get().insert(*this);
}

void CCampsiteInstance::UnregisterInstance()
{
	CCampsiteInstanceRegister::get().erase(this->getName());
}

void CCampsiteInstance::VerifyDir(const string& p_rcoDir) throw (ConfException)
{
	DIR* pDir = opendir(p_rcoDir.c_str());
	if (pDir == NULL)
		throw ConfException(string("Invalid configuration directory ") + p_rcoDir);
	closedir(pDir);
}


/**
 * class CCampsiteInstanceRegister implementation
 *
 */

CCampsiteInstanceRegister g_coCampsiteInstanceRegister;


CCampsiteInstanceRegister& CCampsiteInstanceRegister::get()
{
	return g_coCampsiteInstanceRegister;
}

void CCampsiteInstanceRegister::insert(CCampsiteInstance& p_rcoCampsiteInstance)
{
#ifdef _REENTRANT
	CMutexHandler coLockHandler(&m_coMutex);
#endif
	const string& rcoName = p_rcoCampsiteInstance.getName();
	if (has(rcoName) && getCampsiteInstance(rcoName) != &p_rcoCampsiteInstance)
	{
		erase(rcoName);
	}
	m_coCCampsiteInstances[rcoName] = &p_rcoCampsiteInstance;
	if (p_rcoCampsiteInstance.isRunning())
		m_coInstancePIDs[p_rcoCampsiteInstance.getPID()] = rcoName;
}

void CCampsiteInstanceRegister::erase(pid_t p_nInstancePID)
{
#ifdef _REENTRANT
	CMutexHandler coLockHandler(&m_coMutex);
#endif
	map < pid_t, string, less<pid_t> >::iterator coIt;
	coIt = m_coInstancePIDs.find(p_nInstancePID);
	if (coIt != m_coInstancePIDs.end())
		erase((*coIt).second);
}

void CCampsiteInstanceRegister::erase(const string& p_rcoInstanceName)
{
#ifdef _REENTRANT
	CMutexHandler coLockHandler(&m_coMutex);
#endif
	CCampsiteInstanceMap::const_iterator coIt;
	coIt = m_coCCampsiteInstances.find(p_rcoInstanceName);
	if (coIt == m_coCCampsiteInstances.end())
		return;
	CCampsiteInstance* pcoInstance = (*coIt).second;
	if (pcoInstance->isRunning())
		m_coInstancePIDs.erase(pcoInstance->getPID());
	m_coCCampsiteInstances.erase(p_rcoInstanceName);
	delete pcoInstance;
}

CCampsiteInstance* CCampsiteInstanceRegister::getCampsiteInstance(pid_t p_nInstancePID) const
		throw (InvalidValue)
{
#ifdef _REENTRANT
	CMutexHandler coLockHandler(&m_coMutex);
#endif
	map < pid_t, string, less<pid_t> >::const_iterator coIt;
	coIt = m_coInstancePIDs.find(p_nInstancePID);
	if (coIt == m_coInstancePIDs.end())
		throw InvalidValue("Campsite instance name");
	return getCampsiteInstance((*coIt).second);
}

CCampsiteInstance* CCampsiteInstanceRegister::getCampsiteInstance(const string& p_rcoInstanceName) const
		throw (InvalidValue)
{
#ifdef _REENTRANT
	CMutexHandler coLockHandler(&m_coMutex);
#endif
	CCampsiteInstanceMap::const_iterator coIt;
	coIt = m_coCCampsiteInstances.find(p_rcoInstanceName);
	if (coIt == m_coCCampsiteInstances.end())
		throw InvalidValue("Campsite instance PID");
	return (*coIt).second;
}

void CCampsiteInstanceRegister::debug() const
{
	CCampsiteInstanceMap::const_iterator coIt = m_coCCampsiteInstances.begin();
	for (; coIt != m_coCCampsiteInstances.end(); ++coIt)
	{
		CCampsiteInstance* pcoInstance = (*coIt).second;
		int isRunning = (int)pcoInstance->isRunning();
		g_coDebug << "[CCampsiteInstanceRegister::debug] instance: "
			<< pcoInstance->getName() << ", running: " << isRunning;
		if (isRunning)
			cout << ", pid: " << pcoInstance->getPID();
		cout << endl;
	}
	map < pid_t, string, less<pid_t> >::const_iterator coIt2 = m_coInstancePIDs.begin();
	for (; coIt2 != m_coInstancePIDs.end(); ++coIt2)
	{
		g_coDebug << "[CCampsiteInstanceRegister::debug] * instance: " << (*coIt2).second
			<< ", pid: " << (*coIt2).first << endl;
	}
}
