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
#include <iostream>


#include "globals.h"
#include "readconf.h"
#include "cgi.h"
#include "cgi_common.h"


using std::cout;
using std::endl;
//using std::ios_base;


void ReadConf();
int ReadParameters(char** p_ppchMsg, int* p_pnSize, const char** p_ppchErrMsg);


int main()
{
	ReadConf();
	char* pchMsg;
	int nSize;
	const char* pchErrMsg;
	if (ReadParameters(&pchMsg, &nSize, &pchErrMsg) != 0)
	{
		if (pchErrMsg == 0)
			pchErrMsg = "Error reading parameters";
		cout << "Content-type: text/html; charset=UTF-8\n\n";
		cout << "<html>\n<head>\n<title>REQUEST ERROR</title>\n</head>\n"
			 << "<body>\n" << pchErrMsg << "\n</body>\n</html>\n";
		return 0;
	}
	try
	{
	}
	catch (ConfException& rcoEx)
	{
		cout << "Content-type: text/html; charset=UTF-8\n\n";
		cout << "<html>\n" << rcoEx.what() << "\n</html>" << endl;
	}
	return 0;
}


void ReadConf()
{
	char* pchDocumentRoot = getenv("DOCUMENT_ROOT");
	try
	{
		// read database configuration
		string coDatabaseConfFile = string(pchDocumentRoot) + "/database_conf.php";
		ConfAttrValue m_coAttributes(coDatabaseConfFile);
	}
	catch (ConfException& rcoEx)
	{
		cout << "Content-type: text/html; charset=UTF-8\n\n";
		cout << "Error reading configuration: " << rcoEx.what() << endl;
		exit(0);
	}
}


int ReadParameters(char** p_ppchMsg, int* p_pnSize, const char** p_ppchErrMsg)
{
	char* pchDocumentRoot = 0;
	char* pchRequestMethod = 0;
	char* pchQueryString = 0;
	try
	{
		char* pchTmp;
		if ((pchTmp = getenv("DOCUMENT_ROOT")) == NULL)
		{
			throw ExReadParams(-1,"Can not get DOCUMENT ROOT");
		}
		pchDocumentRoot = strdup(pchTmp);
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
	}
	catch (ExReadParams& rcoEx)
	{
		if (pchDocumentRoot != NULL)
			free(pchDocumentRoot);
		if (pchRequestMethod != NULL)
			free(pchRequestMethod);
		if (pchQueryString != NULL)
			free(pchQueryString);
		*p_ppchErrMsg = rcoEx.ErrMsg();
		int nErrNo = rcoEx.ErrNo();
		return nErrNo;
	}
	CGI coCgi(pchRequestMethod, pchQueryString);
	coCgi.ResetIterator();
	const char* pchParam;
	const char* pchValue;
	while (coCgi.GetNextParameter(&pchParam, &pchValue))
	{
	}

	return 0;
}
