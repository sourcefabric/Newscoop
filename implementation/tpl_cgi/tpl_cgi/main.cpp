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

#include <sys/time.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>
#include <stdlib.h>
#include <string.h>
#include <stdio.h>
#include <iostream.h>

#include "globals.h"
#include "csocket.h"
#include "srvdef.h"
#include "tpl_cgi.h"
#include "readconf.h"

char* ReadPOSTQuery();
int ReadParameters(char** p_ppchParams, int* p_pnSize, const char** p_ppchErrMsg);

void ReadConf(string& p_rcoIP, int& p_rnPort)
{
	try
	{
		// read tpl_cgi configuration
		ConfAttrValue coConf(TPL_CGI_CONF_FILE);
		p_rcoIP = coConf.ValueOf("PARSER_IP");
		p_rnPort = atoi(coConf.ValueOf("PARSER_PORT").c_str());
	}
	catch (ConfException& rcoEx)
	{
		cout << "Error reading configuration: " << rcoEx.what() << endl;
		exit(0);
	}
	catch (SocketException& rcoEx)
	{
		cout << "Error reading configuration: " << rcoEx.Message() << endl;
		exit(0);
	}
}

int main()
{
	cout << "Content-type: text/html\n\n";
	string coIP;
	int nPort;
	ReadConf(coIP, nPort);
	nPort = nPort != 0 ? nPort : TOL_SRV_PORT;
	int nErrNo;
	int nSize;
	char* pchParams;
	const char* pchErrMsg;
	if ((nErrNo = ReadParameters(&pchParams, &nSize, &pchErrMsg)) != 0)
	{
#ifdef _DEBUG
		if (pchErrMsg == 0)
			pchErrMsg = "Error reading parameters";
		cout << "<html>\n<head>\n<title>REQUEST ERROR</title>\n</head>\n"
			 << "<body>\n" << pchErrMsg << "\n</body>\n</html>\n";
#endif
		return 1;
	}
	struct timeval tVal = { 0, 0 };
	tVal.tv_sec = 60;
	fd_set clSet;
	FD_ZERO(&clSet);
	CTCPSocket coSock;
	try
	{
		coSock.Connect(coIP.c_str(), nPort);
		coSock.Send(pchParams, nSize);
		FD_SET((SOCKET)coSock, &clSet);
		for (;;)
		{
			if (select(FD_SETSIZE, &clSet, NULL, NULL, &tVal) == -1
				|| !FD_ISSET((SOCKET)*coSock, &clSet))
			{
				throw ConfException("Error on select");
			}
			char pchBuff[1000];
			int nReceived = coSock.Recv(pchBuff, 1000);
			if (nReceived == -1)
				throw ConfException("Error receiving packet");
			if (nReceived == 0)
				break;
			pchBuff[nReceived] = 0;
			cout << pchBuff;
		}
		coSock.Shutdown();
	}
	catch (ConfException& rcoEx)
	{
		cout << "<html>\n" << rcoEx.what() << "\n</html>" << endl;
		coSock.Shutdown();
	}
	catch (ConnectRefused& rcoEx)
	{
		cout << "<html>\n" << rcoEx.Message() << " " << rcoEx.Host() << "\n</html>" << endl;
		coSock.Shutdown();
	}
	catch (SocketErrorException& rcoEx)
	{
		cout << "<html>\n" << rcoEx.Message() << "\n</html>" << endl;
		coSock.Shutdown();
	}
	return 0;
}

class ExReadParams
{
public:
	ExReadParams(int p_nErrNo, const char* p_pchErrMsg)
		: m_nErrNo(p_nErrNo), m_pchErrMsg(p_pchErrMsg) {}
	~ExReadParams() {}
	
	int ErrNo() const { return m_nErrNo; }
	const char* ErrMsg() const { return m_pchErrMsg; }

private:
	int m_nErrNo;
	const char* m_pchErrMsg;
};

int ReadParameters(char** p_ppchParams, int* p_pnSize, const char** p_ppchErrMsg)
{
	char* pchDocumentRoot = 0;
	char* pchIP = 0;
	char* pchPathTranslated = 0;
	char* pchPathInfo = 0;
	char* pchRequestMethod = 0;
	char* pchQueryString = 0;
	char* pchHttpCookie = 0;
	char* pchParams = 0;
	int nParamsBufSize = 0;
	try
	{
		char* pchTmp;
		if ((pchTmp = getenv("DOCUMENT_ROOT")) == NULL)
		{
			throw ExReadParams(-1,"Can not get DOCUMENT ROOT");
		}
		pchDocumentRoot = strdup(pchTmp);
		if ((pchTmp = getenv("REMOTE_ADDR")) == NULL)
		{
			throw ExReadParams(-2, "Can not get REMOTE_ADDR");
		}
		pchIP = strdup(pchTmp);
		if ((pchTmp = getenv("PATH_TRANSLATED")) == NULL)
		{
			throw ExReadParams(-3, "Can not translate path");
		}
		pchPathTranslated = strdup(pchTmp);
		if (strcmp(pchTmp, "/dev/stdin") == 0 || strcmp(pchTmp, "-") == 0)
		{
			throw ExReadParams(-4, "Unable to parse from stdin");
		}
		if ((pchTmp = getenv("PATH_INFO")) == NULL)
		{
			throw ExReadParams(-6, "Can not obtain path info");
		}
		pchPathInfo = strdup(pchTmp);
		if ((pchTmp = getenv("REQUEST_METHOD")) == NULL)
		{
			throw ExReadParams(-7, "Can not get REQUEST_METHOD");
		}
		pchRequestMethod = strdup(pchTmp);
		if (strcmp(pchRequestMethod, "GET") == 0)
		{
			if ((pchTmp = getenv("QUERY_STRING")) == NULL)
			{
				throw ExReadParams(-8, "Can not get QUERY_STRING");
			}
			pchQueryString = strdup(pchTmp);
		}
		else if (strcmp(pchRequestMethod, "POST") == 0)
		{
			pchQueryString = ReadPOSTQuery();
			if (pchQueryString == NULL)
			{
				throw ExReadParams(-8, "Can not get QUERY_STRING");
			}
		}
		pchTmp = getenv("HTTP_COOKIE");
		pchHttpCookie = strdup(pchTmp != NULL ? pchTmp : "");
		nParamsBufSize = 4 + strlen(pchDocumentRoot) + 1 + strlen(pchIP) + 1
						 + strlen(pchPathTranslated) + 1 + strlen(pchPathInfo) + 1
						 + strlen(pchRequestMethod) + 1 + strlen(pchQueryString) + 1
						 + strlen(pchHttpCookie) + 1;
		if (pchDocumentRoot == NULL || pchIP == NULL || pchPathTranslated == NULL
			|| pchPathInfo == NULL || pchRequestMethod == NULL || pchQueryString == NULL
			|| pchHttpCookie == NULL || (pchParams = new char[nParamsBufSize]) == NULL)
		{
			throw ExReadParams(-10, "Internal error");
		}
	}
	catch (ExReadParams& rcoEx)
	{
		if (pchDocumentRoot != NULL)
			free(pchDocumentRoot);
		if (pchIP != NULL)
			free(pchIP);
		if (pchPathTranslated != NULL)
			free(pchPathTranslated);
		if (pchPathInfo != NULL)
			free(pchPathInfo);
		if (pchRequestMethod != NULL)
			free(pchRequestMethod);
		if (pchQueryString != NULL)
			free(pchQueryString);
		if (pchHttpCookie != NULL)
			free(pchHttpCookie);
		*p_ppchParams = NULL;
		*p_ppchErrMsg = rcoEx.ErrMsg();
		int nErrNo = rcoEx.ErrNo();
		return nErrNo;
	}
	int nIndex = 4;
	strcpy(pchParams + nIndex, pchDocumentRoot);
	nIndex += strlen(pchDocumentRoot) + 1;
	strcpy(pchParams + nIndex, pchIP);
	nIndex += strlen(pchIP) + 1;
	strcpy(pchParams + nIndex, pchPathTranslated);
	nIndex += strlen(pchPathTranslated) + 1;
	strcpy(pchParams + nIndex, pchPathInfo);
	nIndex += strlen(pchPathInfo) + 1;
	strcpy(pchParams + nIndex, pchRequestMethod);
	nIndex += strlen(pchRequestMethod) + 1;
	strcpy(pchParams + nIndex, pchQueryString);
	nIndex += strlen(pchQueryString) + 1;
	strcpy(pchParams + nIndex, pchHttpCookie);	
	int* pnSize = (int*)pchParams;
	*pnSize = nParamsBufSize - 4;
	*p_ppchParams = pchParams;
	*p_pnSize = nParamsBufSize;
	*p_ppchErrMsg = NULL;
	
	return 0;
}

char* ReadPOSTQuery()
{
	int nQueryAlloc = 1000;
	char* pchQuery = (char*) malloc(nQueryAlloc);
	if (pchQuery == NULL)
		return NULL;
	int nIndex = 0;
	for (;;)
	{
		if (feof(stdin) || (nIndex > 0 && pchQuery[nIndex-1] == 0))
		{
			pchQuery[nIndex] = 0;
			break;
		}
		if (nIndex >= nQueryAlloc)
		{
			nQueryAlloc += nQueryAlloc;
			char* pchNewQuery = (char*) realloc(pchQuery, nQueryAlloc);
			pchQuery = pchNewQuery;
		}
		char chIn = fgetc(stdin);
		if (chIn > 0)
		{
			pchQuery[nIndex++] = chIn;
		}
		else
		{
			pchQuery[nIndex] = 0;
			break;
		}
	}
	return pchQuery;
}
