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
#include "threadpool.h"
#include "cms_types.h"
#include "srvdef.h"
#include "csocket.h"
#include "readconf.h"
#include "thread.h"
#include "process_req.h"
#include "cpublication.h"
#include "cpublicationsregister.h"
#include "curlshortnames.h"
#include "curltemplatepath.h"
#include "cmessagefactory.h"

using std::cout;
using std::cerr;
using std::endl;
using std::flush;

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


CMessage* readMessage(CTCPSocket* p_pcoClSock, CMessageFactoryRegister& p_rcoMFReg)
{
	char pchContent[11];

	int nMsgLen = p_pcoClSock->Recv(pchContent, 10, 0);
	if (nMsgLen < 9)
		throw SocketErrorException("Receive error");

	pchContent[10] = 0;
	uint nDataSize = strtol(pchContent + 5, NULL, 16);
	char *pchMsg = new char[nDataSize + 10];
	memcpy(pchMsg, pchContent, 10);
	p_pcoClSock->Recv(pchMsg + 10, nDataSize, 0);
	pchMsg[nDataSize + 10] = 0;

	return p_rcoMFReg.createMessage(pchMsg);
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

	// block all signals
	sigset_t nSigMask;
	sigfillset(&nSigMask);
	pthread_sigmask(SIG_SETMASK, &nSigMask, NULL);

	new CPublication(6, MYSQLConnection());
	new CURLShortNamesType();
	new CURLTemplatePathType();
	CMessageFactoryRegister coMFReg;
	coMFReg.insert(new CURLRequestMessageFactory());
	const CPublicationsRegister& rcoPubReg = CPublicationsRegister::getInstance();

	CAction::initTempMembers();
	CTCPSocket* pcoClSock = (CTCPSocket*)p_pArg;
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
		CMessage* pcoMessage = readMessage(pcoClSock, coMFReg);
		string coAlias = ((CMsgURLRequest*)pcoMessage)->getHTTPHost();
		const CPublication* pcoPub = rcoPubReg.getPublication(coAlias);
		const CURLType* pcoURLType = pcoPub->getURLType();
		CURL* pcoURL = pcoURLType->getURL(*((CMsgURLRequest*)pcoMessage));
		string coRemoteAddress = ((CMsgURLRequest*)pcoMessage)->getRemoteAddress();

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
			RunParser(MYSQLConnection(), pcoURL, coRemoteAddress.c_str(), coOs);
		}
		coOs.flush();
		delete pcoClSock;
	}
	catch (RunException& coEx)
	{
		delete pcoClSock;
#ifdef _DEBUG
		cout << "MyThreadRoutine: " << coEx.what() << endl;
#endif
	}
	catch (SocketErrorException& coEx)
	{
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

CThreadPool* g_pcoThreadPool = NULL;

void sigterm_handler(int p_nSigNum)
{
#ifdef _DEBUG
	cout << "TERM signal received" << endl;
#endif
	if (g_pcoThreadPool == NULL)
	{
#ifdef _DEBUG
		cout << "pointer to thread pool object is null" << endl;
#endif
		exit(0);
	}
	UInt nWorkingThreads = g_pcoThreadPool->workingThreads();
	if (nWorkingThreads == 0)
	{
#ifdef _DEBUG
		cout << "there are no working threads" << endl << "closing all sockets" << endl;
#endif
		CSocket::closeAllSockets();
		exit(0);
	}
#ifdef _DEBUG
	cout << "waiting for " << nWorkingThreads << " thread(s) to finish" << endl;
#endif
	for (int i = 1; i < 20 && nWorkingThreads > 0; i++)
	{
		usleep(300000);
		nWorkingThreads = g_pcoThreadPool->workingThreads();
	}
#ifdef _DEBUG
	cout << "killing idle threads" << endl;
#endif
	g_pcoThreadPool->killIdleThreads();
#ifdef _DEBUG
	cout << endl << "closing all sockets" << endl;
#endif
	CSocket::closeAllSockets();
	if (nWorkingThreads > 0)
	{
#ifdef _DEBUG
		cerr << "killing all threads" << endl;
#endif
		g_pcoThreadPool->killAllThreads();
	}
	exit(0);
}

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

	// mask all signals except TERM signal
	sigset_t nSigMask;
	sigfillset(&nSigMask);
	sigdelset(&nSigMask, SIGTERM);
	pthread_sigmask(SIG_SETMASK, &nSigMask, NULL);

	// set the signal handler TERM
	signal(SIGTERM, sigterm_handler);

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
		g_pcoThreadPool = new CThreadPool(1, nMaxThreads, MyThreadRoutine, NULL);
		CTCPSocket* pcoClSock = NULL;
		for (; ; )
		{
			try
			{
				pcoClSock = coServer.Accept();
				char* pchRemoteIP = pcoClSock->RemoteIP();
				if (coAllowedHosts.find(pchRemoteIP) == coAllowedHosts.end())
				{
					cerr << "Not allowed host (" << pchRemoteIP << ") connected" << endl;
					delete pcoClSock;
					continue;
				}
				if (pcoClSock == 0)
					throw SocketErrorException("Accept error");
				g_pcoThreadPool->waitFreeThread();
				g_pcoThreadPool->startThread(true, (void*)pcoClSock);
			}
			catch (ExThread& coEx)
			{
				pcoClSock->Shutdown();
				delete pcoClSock;
				cerr << "Error starting thread: " << coEx.Message() << endl;
			}
			catch (SocketErrorException& coEx)
			{
				cerr << "Socket (" << (SOCKET)*pcoClSock << ") error: " << coEx.Message() << endl;
				pcoClSock->Shutdown();
				delete pcoClSock;
			}
		}
	}
	catch (ExMutex& rcoEx)
	{
		cerr << rcoEx.Message() << endl;
		return 1;
	}
	catch (ExThread& rcoEx)
	{
		cerr << "Thread: " << rcoEx.ThreadId() << ", Severity: " << rcoEx.Severity()
		     << "; " << rcoEx.Message() << endl;
		return 2;
	}
	catch (SocketErrorException& rcoEx)
	{
		cerr << rcoEx.Message() << endl;
		return 3;
	}
	catch (exception& rcoEx)
	{
		cerr << "exception: " << rcoEx.what() << endl;
		return 4;
	}
	catch (...)
	{
		cerr << "unknown exception" << endl;
		return 5;
	}
	return 0;
}
