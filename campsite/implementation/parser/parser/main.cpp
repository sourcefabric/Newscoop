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

Contains the main function, initialisation functions and functions performing
certain operations against database: subscription, login, change user
information, search articles.

The main function builds the context from cgi parameters and from database,
calls the initialisation functions, eventually the functions performing
operations against database, creates a parser hash, creates a parser object
for the requested template and calls Parse and WriteOutput methods of parser
object.

******************************************************************************/

#include <pwd.h>
#include <grp.h>
#include <sys/time.h>
#include <sys/types.h>
#include <unistd.h>
#include <string.h>
#include <iostream>
#include <signal.h>
#include <sys/wait.h>
#include <exception>

#include "lex.h"
#include "atoms.h"
#include "parser.h"
#include "util.h"
#include "cgi.h"
#include "threadpool.h"
#include "cms_types.h"
#include "srvdef.h"
#include "csocket.h"
#include "readconf.h"
#include "thread.h"
#include "process_req.h"

using std::cout;
using std::endl;

class CUpdateThread : public CThread
{
protected:
	virtual void* Run();
};

void* CUpdateThread::Run()
{
	while (true)
	{
		bool nTopicsChanged = false;
		UpdateTopics(nTopicsChanged);
		if (CLex::updateArticleTypes() || nTopicsChanged)
			CParser::resetMap();
		sleep(5);
	}
	return NULL;
}

// NextParam: read next parameter from string of parameters; return pointer to parameter
// Read parameter is dynamically allocated and it must be deallocated using delete operator.
// Parameters:
//		const char* p_pchParams - string of parameters
//		int* p_pnIndex - current index in the string
//		int p_nMax - string length
char* NextParam(const char* p_pchParams, int* p_pnIndex, int p_nMax) throw(RunException)
{
	if (p_pchParams == NULL)
		throw RunException("Invalid params");
	if (*p_pnIndex >= p_nMax)
		throw RunException("Mising parameter");
	int nIndexNext = *p_pnIndex + strlen(p_pchParams + *p_pnIndex) + 1;
	char* pchParam = new char[nIndexNext - *p_pnIndex];
	if (pchParam == NULL)
		throw RunException("Alloc error");
	strcpy(pchParam, p_pchParams + *p_pnIndex);
	*p_pnIndex = nIndexNext;
	return pchParam;
}

// ReadCGIParams: read cgi environment from string into cgi environment structure
// Parameters:
//		const char* p_pchParams - string of parameters
CGIParams* ReadCGIParams(const char* p_pchParams) throw(RunException)
{
	if (p_pchParams == 0)
		throw RunException("NULL Params");
	CGIParams* pParams = new CGIParams;
	if (pParams == 0)
		throw RunException("Can not alloc memory");
	const int* pnSize = (const int*)p_pchParams;
	int nIndex = 4;
	try
	{
		pParams->m_pchDocumentRoot = NextParam(p_pchParams, &nIndex, *pnSize);
		pParams->m_pchIP = NextParam(p_pchParams, &nIndex, *pnSize);
		pParams->m_pchPathTranslated = NextParam(p_pchParams, &nIndex, *pnSize);
		pParams->m_pchPathInfo = NextParam(p_pchParams, &nIndex, *pnSize);
		pParams->m_pchRequestMethod = NextParam(p_pchParams, &nIndex, *pnSize);
		pParams->m_pchQueryString = NextParam(p_pchParams, &nIndex, *pnSize);
		try
		{
			pParams->m_pchHttpCookie = NextParam(p_pchParams, &nIndex, *pnSize);
		}
		catch (RunException& rcoEx)
		{
			pParams->m_pchHttpCookie = NULL;
		}
	}
	catch (RunException& rcoEx)
	{
		delete pParams;
		throw rcoEx;
		return NULL;
	}
	return pParams;
}

// MyThreadRoutine: thread routine; this is started on new thread start
// Parameters:
//		void* p_pArg - pointer to connection to client socket
void* MyThreadRoutine(void* p_pArg)
{
	if (p_pArg == 0)
	{
		cout << "MyThreadRoutine: Invalid arg\n";
		return NULL;
	}
	CAction::initTempMembers();
	CTCPSocket* pcoClSock = (CTCPSocket*)p_pArg;
	char pchBuff[4];
	char* pchMsg = 0;
	CGIParams* pParams = NULL;
	struct timeval tVal = { 0, 0 };
	tVal.tv_sec = 5;
	fd_set clSet;
	FD_ZERO(&clSet);
	FD_SET((SOCKET)*pcoClSock, &clSet);
	MYSQL* pSql = NULL;
	try
	{
		if (select(FD_SETSIZE, &clSet, NULL, NULL, &tVal) == -1
		        || !FD_ISSET((SOCKET)*pcoClSock, &clSet))
		{
			throw RunException("Error on select");
		}
		if (pcoClSock->Recv(pchBuff, 4) < 4)
			throw RunException("Error receiving packet");
		int* pnMsgLen = (int*)pchBuff;
		pchMsg = new char[*pnMsgLen + 1];
		if (pchMsg == 0)
			throw RunException("Out of memory");
		int nCnt = 0;
		while (nCnt < *pnMsgLen)
		{
			if (select(FD_SETSIZE, &clSet, NULL, NULL, &tVal) == -1
			        || !FD_ISSET((SOCKET)*pcoClSock, &clSet))
			{
				throw RunException("Error on select");
			}
			nCnt += pcoClSock->Recv(pchMsg + nCnt, *pnMsgLen - nCnt);
		}
		pchMsg[*pnMsgLen] = 0;
		pParams = ReadCGIParams(pchMsg);
		outbuf coOutBuf((SOCKET)*pcoClSock);
		sockstream coOs(&coOutBuf);
		pSql = MYSQLConnection();
		if (pSql == NULL)		// unable to connect to server
		{
			coOs << "<html><head><title>REQUEST ERROR</title></head>\n"
			"<body>Unable to connect to database server.</body></html>\n";
		}
		else
		{
			RunParser(pSql, pParams, coOs);
		}
		coOs.flush();
		delete pParams;
		delete []pchMsg;
		pcoClSock->Shutdown();
		delete pcoClSock;
	}
	catch (RunException& coEx)
	{
		delete pParams;
		delete pchMsg;
		pcoClSock->Shutdown();
		delete pcoClSock;
#ifdef _DEBUG
		cout << "MyThreadRoutine: " << coEx.what() << endl;
#endif
	}
	catch (SocketErrorException& coEx)
	{
		delete pParams;
		delete pchMsg;
		pcoClSock->Shutdown();
		delete pcoClSock;
#ifdef _DEBUG
		cout << "MyThreadRoutine: " << coEx.Message() << endl;
#endif
	}
	return NULL;
}

// nMainThreadPid: pid of main thread
int nMainThreadPid;

// SigHandler: TERM signal handler
void SigHandler(int p_nSig)
{
	if (nMainThreadPid != 0)
	{
		kill(nMainThreadPid, SIGTERM);
		nMainThreadPid = 0;
	}
	exit(0);
}

// StartWatchDog: start watch dog
// Parameters:
//		bool p_bRunAsDaemon - if true, detach and run in background
void StartWatchDog(bool p_bRunAsDaemon)
{
	if (p_bRunAsDaemon)
	{
		if (fork() != 0)
			exit(0);
		setsid();
	}
	while (1)
	{
		nMainThreadPid = fork();
		if (nMainThreadPid == -1)
		{
			sleep(10);
			continue;
		}
		if (nMainThreadPid != 0)
		{
			signal(SIGTERM, SigHandler);
			waitpid(nMainThreadPid, NULL, 0);
			sleep(10);
			continue;
		}
		return ; // nMainThreadPid == 0
	}
}

// ProcessArgs: process command line arguments
//		int argc - arguments number
//		char** argv - arguments list
//		bool& p_rbRunAsDaemon - set by this function according to arguments
//		int& p_rnMaxThreads - set by this function according to arguments
void ProcessArgs(int argc, char** argv, bool& p_rbRunAsDaemon, int& p_rnMaxThreads)
{
	if (argc < 2)
		return ;
	for (int i = 1; i < argc; i++)
	{
		if (strcmp(argv[i], "-d") == 0)
			p_rbRunAsDaemon = false;
		if (strcmp(argv[i], "-t") == 0)
		{
			if (++i < argc)
			{
				int nReqMaxThreads = atoi(argv[i]);
				if (nReqMaxThreads < 1)
				{
					cout << "Number of maximum threads must be at least 1. Running with "
					<< p_rnMaxThreads << endl;
				}
				else
					p_rnMaxThreads = nReqMaxThreads;
			}
			else
			{
				cout << "You did not specify the number of maximum threads. Running with "
				<< p_rnMaxThreads << endl;
				break;
			}
		}
		if (strcmp(argv[i], "-h") == 0)
		{
			cout << "Usage: tol_server [-d|-t <threads_nr>|-h]\n"
			"where:\t-d: run in console (by default run as daemon)\n"
			"\t-t <threads_nr>: set the maximum number of threads to start "
			"(default: " << p_rnMaxThreads << ")\n"
			"\t-h: print this help message\n";
			exit(0);
		}
	}
}

// ResolveNames: resolve host names
// Return -
// Parameters:
//		string& p_rcoAllowedHosts - allowed hosts
//		string& p_rcoAllowedIPs - allowed ip addresses
void ResolveNames(string& p_rcoAllowedHosts, StringSet& p_rcoAllowedIPs) throw (RunException)
{
	string coWord;
	int nIndex = 0;
	while ((coWord = ConfAttrValue::ReadWord(p_rcoAllowedHosts, nIndex)) != "")
	{
		struct hostent* pHost = gethostbyname(coWord.c_str());
		if (pHost == NULL)
		{
			string errMsg = string("Unable to resolve name ") + coWord;
			throw RunException(errMsg.c_str());
		}
		for (char** ppIP = pHost->h_addr_list; *ppIP != 0; ppIP++)
		{
			struct in_addr in;
			memcpy(&in.s_addr, *ppIP, sizeof(struct in_addr));
			char* pIP = inet_ntoa(in);
			p_rcoAllowedIPs.insert(pIP);
		}
		nIndex++;
	}
}

// ReadConf: read configuration
// Return -
// Parameters:
//		int& p_rnThreads - maximum number of threads
//		int& p_rnPort - port to bind to
//		string& p_rcoAllowed - allowed host
//		int& p_rnUserId - user id to run with
//		int& p_rnGroupId - group id to run with
void ReadConf(int& p_rnThreads, int& p_rnPort, StringSet& p_rcoAllowed, int& p_rnUserId,
		int& p_rnGroupId)
{
	try
	{
		// read parser configuration
		ConfAttrValue coConf(PARSER_CONF_FILE);
		p_rnThreads = atoi(coConf.ValueOf("THREADS").c_str());
		p_rnPort = atoi(coConf.ValueOf("PORT").c_str());
		string coAllowed = coConf.ValueOf("ALLOWED_HOSTS");
		ResolveNames(coAllowed, p_rcoAllowed);
		if (p_rcoAllowed.empty())
			throw RunException("Allowed hosts list is empty");
		const char* pUser = coConf.ValueOf("USER").c_str();
		struct passwd* pPwEnt = getpwnam(pUser);
		if (pPwEnt == NULL)
			throw RunException("Invalid user name in conf file");
		p_rnUserId = pPwEnt->pw_uid;
		const char* pGroup = coConf.ValueOf("GROUP").c_str();
		struct group* pGrEnt = getgrnam(pGroup);
		if (pGrEnt == NULL)
			throw RunException("Invalid group name in conf file");
		p_rnGroupId = pGrEnt->gr_gid;
		// read database configuration
		ConfAttrValue coDBConf(DATABASE_CONF_FILE);
		SQL_SERVER = coDBConf.ValueOf("SERVER");
		SQL_SRV_PORT = atoi(coDBConf.ValueOf("PORT").c_str());
		SQL_USER = coDBConf.ValueOf("USER");
		SQL_PASSWORD = coDBConf.ValueOf("PASSWORD");
		SQL_DATABASE = coDBConf.ValueOf("NAME");
	}
	catch (ConfException& rcoEx)
	{
		cout << "Error starting server: " << rcoEx.what() << endl;
		exit(1);
	}
	catch (SocketException& rcoEx)
	{
		cout << "Error starting server: " << rcoEx.Message() << endl;
		exit(1);
	}
	catch (RunException& rcoEx)
	{
		cout << "Error starting server: " << rcoEx.what() << endl;
		exit(1);
	}
}

#if (__GNUC__ < 3)
void my_terminate()
{
	cout << "uncought exception. terminate." << endl;
	abort();
}
#endif

// main: main function
// Return 0 if no error encountered; error code otherwise
// Parameters:
//		int argc - arguments number
//		char** argv - arguments list
int main(int argc, char** argv)
{
	nMainThreadPid = 0;
	bool bRunAsDaemon = true;
	int nMaxThreads;
	int nPort;
	StringSet coAllowedHosts;
	int nUserId;
	int nGroupId;
	ReadConf(nMaxThreads, nPort, coAllowedHosts, nUserId, nGroupId);
	ProcessArgs(argc, argv, bRunAsDaemon, nMaxThreads);
	nPort = nPort > 0 ? nPort : TOL_SRV_PORT;
	nMaxThreads = nMaxThreads > 0 ? nMaxThreads : MAX_THREADS;
	if (setuid(nUserId) != 0)
	{
		cout << "Error setting user id " << nUserId << endl;
		exit (1);
	}
	if (setgid(nGroupId) != 0)
	{
		cout << "Error setting group id " << nGroupId << endl;
		exit (1);
	}
	StartWatchDog(bRunAsDaemon);
	signal(SIGTERM, SIG_DFL);
#if (__GNUC__ < 3)
	set_terminate(my_terminate);
#else
	// The __verbose_terminate_handler function obtains the name of the current exception, attempts to
	// demangle it, and prints it to stderr. If the exception is derived from std::exception then the
	// output from what() will be included. 
	std::set_terminate (__gnu_cxx::__verbose_terminate_handler);
#endif
	try
	{
		bool nTopicsChanged = false;
		UpdateTopics(nTopicsChanged);
		CServerSocket coServer("0.0.0.0", nPort);
		CUpdateThread coUpdateThread;
		coUpdateThread.run();
		if (!coUpdateThread.isRunning())
			throw ExThread(ThreadSvAbort, "Error starting update thread.");
		CThreadPool coThreadPool(1, nMaxThreads, MyThreadRoutine, NULL);
		CTCPSocket* pcoClSock = NULL;
		for (; ; )
		{
			try
			{
				pcoClSock = coServer.Accept();
				if (coAllowedHosts.find(pcoClSock->RemoteIP()) == coAllowedHosts.end())
				{
					cout << "Not allowed host (" << pcoClSock->RemoteIP() << ") connected" << endl;
					delete pcoClSock;
					continue;
				}
				if (pcoClSock == 0)
					throw SocketErrorException("Accept error");
				coThreadPool.waitFreeThread();
				coThreadPool.startThread(true, (void*)pcoClSock);
			}
			catch (ExThread& coEx)
			{
				pcoClSock->Shutdown();
				delete pcoClSock;
				cout << "Error starting thread: " << coEx.Message() << endl;
			}
			catch (SocketErrorException& coEx)
			{
				pcoClSock->Shutdown();
				delete pcoClSock;
				cout << "Socket error: " << coEx.Message() << endl;
			}
		}
	}
	catch (ExMutex& rcoEx)
	{
		cout << rcoEx.Message() << endl;
		return 1;
	}
	catch (ExThread& rcoEx)
	{
		cout << "Thread: " << rcoEx.ThreadId() << ", Severity: " << rcoEx.Severity()
		<< "; " << rcoEx.Message() << endl;
		return 2;
	}
	catch (SocketErrorException& rcoEx)
	{
		cout << rcoEx.Message() << endl;
		return 3;
	}
	catch (exception& rcoEx)
	{
		cout << "exception: " << rcoEx.what() << endl;
		return 4;
	}
	catch (...)
	{
		cout << "unknown exception" << endl;
		return 5;
	}
	return 0;
}
