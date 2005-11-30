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

#include <sys/time.h>
#include <sys/types.h>
#include <unistd.h>
#include <string.h>
#include <iostream>
#include <signal.h>
#include <sys/wait.h>
#include <exception>
#include <pwd.h>
#include <grp.h>

#include "lex.h"
#include "atoms.h"
#include "parser.h"
#include "util.h"
#include "threadpool.h"
#include "cms_types.h"
#include "configure.h"
#include "csocket.h"
#include "thread.h"
#include "process_req.h"
#include "cpublication.h"
#include "cpublicationsregister.h"
#include "curlshortnames.h"
#include "curltemplatepath.h"
#include "cmessagefactory.h"
#include "configure.h"
#include "readconf.h"

using std::cout;
using std::cerr;
using std::endl;
using std::flush;

#define MAX_THREADS 40

#ifdef _DEBUG_SOURCE
#warning *******************************************************************************
#warning This compilation option is for source code debugging, do not use in production!
#warning *******************************************************************************
#endif

CMessage* readMessage(CTCPSocket* p_pcoClSock, CMessageFactoryRegister& p_rcoMFReg)
{
	char pchContent[11];

	int nMsgLen = p_pcoClSock->Recv(pchContent, 10, 0);
	if (nMsgLen < 9)
		throw SocketErrorException("Receive error");

	pchContent[10] = 0;
	uint nDataSize = strtol(pchContent + 5, NULL, 16);
	char *pchMsg = new char[nDataSize + 11];
	memcpy(pchMsg, pchContent, 10);
	uint nReceived = p_pcoClSock->Recv(pchMsg + 10, nDataSize, 0);
	pchMsg[nReceived + 10] = 0;

	return p_rcoMFReg.createMessage(pchMsg);
}


void resetPublicationsCache(const CMsgResetCache* p_pcoMsg)
{
	string coOperation = p_pcoMsg->getParameter("operation")->asString();
	id_type nPublicationId = Integer(p_pcoMsg->getParameter(P_IDPUBL)->asString());

	if (coOperation == "delete" || coOperation == "modify")
		CPublicationsRegister::getInstance().erase(nPublicationId);
	if (coOperation == "create" || coOperation == "modify")
		new CPublication(nPublicationId, MYSQLConnection());
}


void resetTopicsCache(const CMsgResetCache* p_pcoMsg)
{
	bool bUpdated;
	UpdateTopics(bUpdated);
	if (bUpdated)
		CParser::resetMap();
}


void resetArticleTypesCache(const CMsgResetCache* p_pcoMsg)
{
	if (CLex::updateArticleTypes())
		CParser::resetMap();
}


void resetAllCache(const CMsgResetCache* p_pcoMsg)
{
	resetPublicationsCache(p_pcoMsg);
	resetTopicsCache(p_pcoMsg);
	resetArticleTypesCache(p_pcoMsg);
}


void resetCache(const CMsgResetCache* p_pcoMsg)
{
	string coType = p_pcoMsg->getType();
	if (coType == "all")
		resetAllCache(p_pcoMsg);
	if (coType == "publications")
		resetPublicationsCache(p_pcoMsg);
	if (coType == "topics")
		resetTopicsCache(p_pcoMsg);
	if (coType == "article_types")
		resetArticleTypesCache(p_pcoMsg);
}


int readPublications()
{
	MYSQL* pSQL = MYSQLConnection();
	SQLQuery(pSQL, "select Id from Publications");
	StoreResult(pSQL, coRes);
	MYSQL_ROW row;
	while ((row = mysql_fetch_row(*coRes)) != NULL)
		new CPublication(Integer(row[0]), pSQL);
	return RES_OK;
}


// MyThreadRoutine: thread routine; this is started on new thread start
// Parameters:
//		void* p_pArg - pointer to connection to client socket
void* MyThreadRoutine(void* p_pArg)
{
	if (p_pArg == 0)
	{
		cerr << "MyThreadRoutine: Invalid arg\n";
		return NULL;
	}

#ifndef _DEBUG_SOURCE
	// block all signals
	sigset_t nSigMask;
	sigfillset(&nSigMask);
	pthread_sigmask(SIG_SETMASK, &nSigMask, NULL);
#endif

	CAction::initTempMembers();
	CTCPSocket* pcoClSock = (CTCPSocket*)p_pArg;
	struct timeval tVal = { 0, 0 };
	tVal.tv_sec = 5;
	fd_set clSet;
	FD_ZERO(&clSet);
	FD_SET((SOCKET)*pcoClSock, &clSet);
	outbuf coOutBuf((SOCKET)*pcoClSock);
	sockstream coOs(&coOutBuf);
	string coErrorMsg;
	MYSQL* pSql = NULL;
	try
	{
		if (select(FD_SETSIZE, &clSet, NULL, NULL, &tVal) == -1
		    || !FD_ISSET((SOCKET)*pcoClSock, &clSet))
		{
			throw RunException("Error on select");
		}
#ifdef _DEBUG
		cout << "MyThreadRoutine: reading message" << endl;
#endif
		CMessage* pcoMessage = readMessage(pcoClSock, CMessageFactoryRegister::getInstance());
#ifdef _DEBUG
		cout << "received message " << pcoMessage->getMessageTypeId() << endl;
#endif
		if (pcoMessage->getMessageTypeId() == 2) {
			resetCache((CMsgResetCache*)pcoMessage);
			return NULL;
		}
		if (pcoMessage->getMessageTypeId() != 1)
			return NULL;
		string coAlias = ((CMsgURLRequest*)pcoMessage)->getHTTPHost();
#ifdef _DEBUG
		cout << "alias: " << coAlias << endl;
#endif
		const CPublication* pcoPub = CPublicationsRegister::getInstance().getPublication(coAlias);
		const CURLType* pcoURLType = pcoPub->getURLType();
		CURL* pcoURL = pcoURLType->getURL(*((CMsgURLRequest*)pcoMessage));
		string coRemoteAddress = ((CMsgURLRequest*)pcoMessage)->getRemoteAddress();
#ifdef _DEBUG
		cout << "url type: " << pcoURLType->getTypeName() << endl;
#endif

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
	}
	catch (RunException& coEx)
	{
		coErrorMsg = coEx.what();
#ifdef _DEBUG
		cerr << "MyThreadRoutine: " << coEx.what() << " (RunException)" << endl;
#endif
	}
	catch (SocketErrorException& coEx)
	{
		coErrorMsg = string("There was an error communicating with the template engine: ")
				+ coEx.Message() + "! Please restart the template engine.";
#ifdef _DEBUG
		cerr << "MyThreadRoutine: " << coEx.Message() << " (SocketErrorException)" << endl;
#endif
	}
	catch (out_of_range& coEx)
	{
		coErrorMsg = string("Internal out of range error: ") + coEx.what();
#ifdef _DEBUG
		cerr << "MyThreadRoutine: " << coEx.what() << " (out_of_range)" << endl;
#endif
	}
	catch (bad_alloc& coEx)
	{
		coErrorMsg = string("Internal memory allocation error: ") + coEx.what();
#ifdef _DEBUG
		cerr << "MyThreadRoutine: " << coEx.what() << " (bad_alloc)" << endl;
#endif
	}
	catch (exception& coEx)
	{
		coErrorMsg = string("Internal error: ") + coEx.what();
#ifdef _DEBUG
		cerr << "MyThreadRoutine: " << coEx.what() << " (exception)" << endl;
#endif
	}
	catch (...)
	{
		coErrorMsg = "Unknown internal error";
#ifdef _DEBUG
		cerr << "MyThreadRoutine: other exception" << endl;
#endif
	}
	if (coErrorMsg != "")
	{
		coOs << "<html><body><font color=red><h2>There were errors!</h2>" << endl
				<< "<pre>" << coErrorMsg << "</pre></font>" << endl << "</body></html>" << endl;
	}
	coOs.flush();
	delete pcoClSock;
	return NULL;
}

// StartDaemon: run in background
void StartDaemon()
{
	pid_t nChildPID = fork();
	if (nChildPID > 0)
	{
		usleep(100000);
		exit(0);
	}
	if (nChildPID < 0)
	{
		cerr << "Unable to start daemon" << endl;
		exit(1);
	}
	setsid();
}

// ProcessArgs: process command line arguments
//		int argc - arguments number
//		char** argv - arguments list
//		bool& p_rbRunAsDaemon - set by this function according to arguments
//		string& p_rcoConfDir - set by this function according to arguments
void ProcessArgs(int argc, char** argv, bool& p_rbRunAsDaemon, string& p_rcoConfDir,
				 string& p_rcoInstanceName)
{
	bool bError = false;
	for (int i = 1; i < argc; i++)
	{
		if (strcmp(argv[i], "-d") == 0)
			p_rbRunAsDaemon = false;
		if (strcmp(argv[i], "-c") == 0)
		{
			if (++i >= argc)
			{
				cerr << "ERROR: You did not specify the configuration directory." << endl;
				bError = true;
				break;
			}
			else
			{
				p_rcoConfDir = argv[i];
			}
		}
		if (strcmp(argv[i], "-i") == 0)
		{
			if (++i >= argc)
			{
				cerr << "ERROR: no instance name was specified. You must specify the name of the\n"
						<< "instance you want to start." << endl;
				bError = true;
				break;
			}
			else
			{
				p_rcoInstanceName = argv[i];
			}
		}
		if (strcmp(argv[i], "-h") == 0)
		{
			bError = true;
			break;
		}
	}
	if (p_rcoInstanceName == "")
	{
		cerr << "ERROR: no instance name was specified. You must specify the name of the\n"
				<< "instance you want to start." << endl;
		bError = true;
	}
	if (bError)
	{
		cout << "Usage: campsite_server -i <instance_name> [-c <conf_dir>|-d|-h]\n"
				"where:\t-i <instance name>: name of the instance to run\n"
				"\t-d: run in console (by default run as daemon)\n"
				"\t-c <conf_dir>: set the configuration directory\n"
				"\t-h: print this help message" << endl;
		exit(1);
	}
}

#if (__GNUC__ < 3)
void my_terminate()
{
	cerr << "uncought exception. terminate." << endl;
	abort();
}
#endif

CThreadPool* g_pcoThreadPool = NULL;

void sigterm_handler(int p_nSigNum)
{
#ifdef _DEBUG
	cerr << p_nSigNum << " signal received (child)" << endl;
#endif
	if (g_pcoThreadPool == NULL)
	{
#ifdef _DEBUG
		cerr << "pointer to thread pool object is null" << endl;
#endif
		exit(0);
	}
	uint nWorkingThreads = g_pcoThreadPool->workingThreads();
	if (nWorkingThreads == 0)
	{
#ifdef _DEBUG
		cerr << "there are no working threads" << endl << "closing all sockets" << endl;
#endif
		CSocket::closeAllSockets();
		exit(0);
	}
#ifdef _DEBUG
	cerr << "waiting for " << nWorkingThreads << " thread(s) to finish" << endl;
#endif
	for (int i = 1; i < 20 && nWorkingThreads > 0; i++)
	{
		usleep(300000);
		nWorkingThreads = g_pcoThreadPool->workingThreads();
	}
#ifdef _DEBUG
	cerr << "killing idle threads" << endl;
#endif
	g_pcoThreadPool->killIdleThreads();
#ifdef _DEBUG
	cerr << endl << "closing all sockets" << endl;
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


void set_signals(sig_t p_sigHandler, bool p_bSetTERM = true,
				 bool p_bSetHUP = true, bool p_bSetINT = true)
{
	// mask most signals
	sigset_t nSigMask;
	sigemptyset(&nSigMask);
	sigaddset(&nSigMask, SIGPIPE);
	sigaddset(&nSigMask, SIGALRM);
	sigaddset(&nSigMask, SIGUSR1);
	sigaddset(&nSigMask, SIGUSR2);
	pthread_sigmask(SIG_SETMASK, &nSigMask, NULL);

	// set the signal handlers
	if (p_bSetTERM)
		signal(SIGTERM, p_sigHandler);
	if (p_bSetHUP)
		signal(SIGHUP, p_sigHandler);
	if (p_bSetINT)
		signal(SIGINT, p_sigHandler);
}


// main: main function
// Return 0 if no error encountered; error code otherwise
// Parameters:
//		int argc - arguments number
//		char** argv - arguments list
int main(int argc, char** argv)
{
	bool bRunAsDaemon = true;
	string coConfDir, coInstanceName;
	ProcessArgs(argc, argv, bRunAsDaemon, coConfDir, coInstanceName);
	if (coConfDir == "")
		coConfDir = ETC_DIR;
	coConfDir += "/" + coInstanceName;

	int nMaxThreads;
	int nPort;
	try {
		// read parser configuration
		string coParserConfFile = coConfDir + "/parser_conf.php";
		ConfAttrValue coAttributes(coParserConfFile);
	
		// read database configuration
		string coDatabaseConfFile = coConfDir + "/database_conf.php";
		coAttributes.open(coDatabaseConfFile);
	
		nMaxThreads = atoi(coAttributes.valueOf("PARSER_MAX_THREADS").c_str());
		nPort = atoi(coAttributes.valueOf("PARSER_PORT").c_str());
		if (nPort == 0)
		{
			throw ConfException("Template engine port was not specified");
		}
	
		SQL_SERVER = coAttributes.valueOf("DATABASE_SERVER_ADDRESS");
		SQL_SRV_PORT = atoi(coAttributes.valueOf("DATABASE_SERVER_PORT").c_str());
		SQL_USER = coAttributes.valueOf("DATABASE_USER");
		SQL_PASSWORD = coAttributes.valueOf("DATABASE_PASSWORD");
		SQL_DATABASE = coAttributes.valueOf("DATABASE_NAME");
	}
	catch (ConfException& rcoEx)
	{
		cerr << "ERROR reading configuration: " << rcoEx.what() << endl;
		exit(1);
	}

	nMaxThreads = nMaxThreads > 0 ? nMaxThreads : MAX_THREADS;

#ifndef _DEBUG_SOURCE
	if (bRunAsDaemon)
	{
		StartDaemon();
	}
	set_signals(sigterm_handler, true, true, false);
#else
	cout << "max threads: " << nMaxThreads << ", port: " << nPort << endl;
	cout << "sql server: " << SQL_SERVER << ", sql port: " << SQL_SRV_PORT
			<< ", sql user: " << SQL_USER << ", sql password: " << SQL_PASSWORD
			<< ", db name: " << SQL_DATABASE << endl;
#endif

#if (__GNUC__ < 3)
	set_terminate(my_terminate);
#else
	// The __verbose_terminate_handler function obtains the name of the current exception, 
	// attempts to demangle it, and prints it to stderr. If the exception is derived from
	// std::exception then the output from what() will be included. 
	std::set_terminate (__gnu_cxx::__verbose_terminate_handler);
#endif
	try
	{
		// initialize topics cache
		bool nTopicsChanged = false;
		UpdateTopics(nTopicsChanged);

		// initialize article types cache
		CLex::updateArticleTypes();

		// initialize publications cache
		readPublications();

		// initilize URL types
		new CURLShortNamesType();
		new CURLTemplatePathType();

		// initialize message types
		CMessageFactoryRegister::getInstance().insert(new CURLRequestMessageFactory());
		CMessageFactoryRegister::getInstance().insert(new CResetCacheMessageFactory());
		CMessageFactoryRegister::getInstance().insert(new CRestartServerMessageFactory());

		CServerSocket coServer("0.0.0.0", nPort);
#ifdef _DEBUG
		cout << "finished initializations" << endl;
#endif
#ifndef _DEBUG_SOURCE
		g_pcoThreadPool = new CThreadPool(1, nMaxThreads, MyThreadRoutine, NULL);
#endif	
		CTCPSocket* pcoClSock = NULL;
		char pchHostName[1000];
		gethostname(pchHostName, 1000);
		struct hostent* ph = gethostbyname(pchHostName);
		StringSet coHostAddrs;
		coHostAddrs.insert("127.0.0.1");
		for(int nIndex = 0; ph->h_addr_list[nIndex] != 0; nIndex++)
		{
			struct in_addr in;
			memcpy(&in.s_addr, ph->h_addr_list[nIndex], sizeof(struct in_addr));
			coHostAddrs.insert(inet_ntoa(in));
		}
		for (; ; )
		{
			try
			{
				pcoClSock = coServer.Accept();
				char* pchRemoteIP = pcoClSock->RemoteIP();
#ifdef _DEBUG
				cout << endl << "**********************************************" << endl;
				cout << "*****   received request from " << pchRemoteIP << endl;
				cout << "**********************************************" << endl;
#endif
				if (coHostAddrs.find(pchRemoteIP) == coHostAddrs.end())
				{
					cerr << "Not allowed host (" << pchRemoteIP << ") connected" << endl;
					delete pcoClSock;
					continue;
				}
				if (pcoClSock == 0)
					throw SocketErrorException("Accept error");
#ifdef _DEBUG_SOURCE
				MyThreadRoutine((void*)pcoClSock);
#else
				g_pcoThreadPool->waitFreeThread();
				g_pcoThreadPool->startThread(true, (void*)pcoClSock);
#endif
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
