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

#include <string>
#include <iostream>
#include <string.h>
#include <sys/types.h>
#include <sys/time.h>
#include <unistd.h>
#include <stdio.h>

#include "csocket.h"

#define SMTP_PORT 25
#define SMTP_OK 250

using std::cout;
using std::cin;
using std::endl;

class Exception
{
public:
	Exception(const char* msg) : m_pchMsg(msg) {}

	const char* Message() const { return m_pchMsg; }

private:
	const char* m_pchMsg;
};

int Receive(CTCPSocket& p_rcoSock, char** p_ppchBuf, int& p_rnBufLen, int p_nSecTimeOut = 30,
            int p_nUSecTimeOut = 0, bool p_bLeaveOneByte = true)
		throw (Exception, SocketErrorException);
int CheckSMTPErrorCode(const char* p_pchBuf, int p_nExpectedCode = SMTP_OK)
		throw (Exception);

int main(int argc, char** argv)
{
	bool bTest = false;
	const char** ppchSendToList;
	const char* pchReplyAddress = NULL;
	const char* pchServerAddress = NULL;
	const char* pchMyHostName = NULL;
	int nListLen = 3;
	int nFirstFree = 0;
	ppchSendToList = new const char* [nListLen];
	for (int i = 0;;)
	{
		if (++i >= argc)
			break;
		if (strcmp(argv[i], "-r") == 0)
		{
			i++;
			pchReplyAddress = i >= argc ? "" : argv[i];
			continue;
		}
		if (strcmp(argv[i], "-s") == 0)
		{
			i++;
			pchServerAddress = i >= argc ? "" : argv[i];
			continue;
		}
		if (strcmp(argv[i], "--test") == 0)
		{
			bTest = true;
			continue;
		}
		if (nFirstFree >= nListLen)
		{
			const char** ppchNewSendToList = new const char* [nListLen * 2];
			for (int i = 0; i < nListLen; i++)
				ppchNewSendToList[i] = ppchSendToList[i];
			delete ppchSendToList;
			ppchSendToList = ppchNewSendToList;
			nFirstFree = nListLen;
			nListLen *= 2;
		}
		ppchSendToList[nFirstFree++] = argv[i];
	}
	if (pchReplyAddress == NULL || pchReplyAddress[0] == 0)
	{
		cout << "Reply address not specified" << endl;
		return 1;
	}
	if (pchServerAddress == NULL || pchServerAddress[0] == 0)
	{
		cout << "Server address not specified" << endl;
		return 1;
	}
	pchMyHostName = CSocket::LocalHostName();
	CTCPSocket coSock;
	string coStr;
	try
	{
		coSock.Connect(pchServerAddress, SMTP_PORT);
		char* pchBuf = NULL;
		int nBufLen = 0;
		int nRecLen = 0;
		nRecLen = Receive(coSock, &pchBuf, nBufLen);

		coStr = string("helo ") + pchMyHostName + "\r\n";
		coSock.Send(coStr.c_str(), strlen(coStr.c_str()));
		nRecLen = Receive(coSock, &pchBuf, nBufLen);
		CheckSMTPErrorCode(pchBuf);

		coStr = string("mail from: ") + pchReplyAddress + "\r\n";
		coSock.Send(coStr.c_str(), strlen(coStr.c_str()));
		nRecLen = Receive(coSock, &pchBuf, nBufLen);
		CheckSMTPErrorCode(pchBuf);
		for (int i = 0; i < nFirstFree; i++)
		{
			coStr = string("rcpt to: ") + ppchSendToList[i] + "\r\n";
			coSock.Send(coStr.c_str(), strlen(coStr.c_str()));
			nRecLen = Receive(coSock, &pchBuf, nBufLen);
			CheckSMTPErrorCode(pchBuf);
		}
		if (bTest)
			return 0;
		coSock.Send("data\r\n", 6);
		nRecLen = Receive(coSock, &pchBuf, nBufLen);
		CheckSMTPErrorCode(pchBuf, 354);
		while (!cin.eof())
		{
			getline(cin, coStr);
			coStr += "\r\n";
			coSock.Send(coStr.c_str(), strlen(coStr.c_str()));
		}
		coSock.Send("\r\n.\r\n", 5);
		nRecLen = Receive(coSock, &pchBuf, nBufLen);
		CheckSMTPErrorCode(pchBuf);
		coSock.Shutdown();
	}
	catch (Exception& rcoEx)
	{
		cout << "Error sending message: " << rcoEx.Message() << endl;
		coSock.Shutdown();
		return 1;
	}
	catch (ConnectRefused& rcoEx)
	{
		cout << "Error sending message: " << rcoEx.Message() << " " << rcoEx.Host() << endl;
		coSock.Shutdown();
		return 2;
	}
	catch (SocketErrorException& rcoEx)
	{
		cout << "Error sending message: " << rcoEx.Message() << endl;
		coSock.Shutdown();
		return 3;
	}
	return 0;
}

int CheckSMTPErrorCode(const char* p_pchBuf, int p_nExpectedCode) throw (Exception)
{
	char pchBuf[20];
	strncpy(pchBuf, p_pchBuf, 19);
	pchBuf[19] = 0;
	int nCode = strtol(pchBuf, 0, 10);
	if (nCode != p_nExpectedCode)
		throw Exception(p_pchBuf);
	return nCode;
}

int Receive(CTCPSocket& p_rcoSock, char** p_ppchBuf, int& p_rnBufLen, int p_nSecTimeOut,
            int p_nUSecTimeOut, bool p_bEndString)
		throw (Exception, SocketErrorException)
{
	if (*p_ppchBuf == NULL)
	{
		*p_ppchBuf = new char [1000];
		p_rnBufLen = 1000;
	}
	int nRemaining = p_rnBufLen - (p_bEndString ? 1 : 0);
	int nRecLen = 0;
	struct timeval tVal = { 0, 0 };
	tVal.tv_sec = p_nSecTimeOut;
	tVal.tv_usec = p_nUSecTimeOut;
	fd_set clSet;
	FD_ZERO(&clSet);
	FD_SET((SOCKET)p_rcoSock, &clSet);
	for (;;)
	{
		if (select(FD_SETSIZE, &clSet, NULL, NULL, &tVal) == -1
			|| !FD_ISSET((SOCKET)p_rcoSock, &clSet))
		{
			if (nRecLen == 0)
				throw Exception("Error on select");
			break;
		}
		int nReceived = p_rcoSock.Recv(*p_ppchBuf, nRemaining);
		if (nReceived == -1)
			throw Exception("Error receiving packet");
		nRecLen += nReceived;
		nRemaining -= nReceived;
		if (nReceived == 0 || nRemaining > 0)
			break;
		if (nRemaining <= 0)
		{
			char* pchNewBuf = new char [2 * p_rnBufLen];
			memcpy(pchNewBuf, *p_ppchBuf, p_rnBufLen);
			p_rnBufLen *= 2;
			delete *p_ppchBuf;
			*p_ppchBuf = pchNewBuf;
			nRemaining += p_rnBufLen;
		}
	}
	if (p_bEndString)
		(*p_ppchBuf)[nRecLen] = 0;
	return nRecLen;
}
