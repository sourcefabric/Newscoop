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
#include <sstream>
#include <sys/mman.h>
#include <curl/curl.h>


#include "globals.h"
#include "readconf.h"
#include "cgi.h"
#include "cgi_common.h"


using std::cout;
using std::endl;
using std::stringstream;


void ReadFileFromURL(const string& p_rcoURL, const string& p_rcoContentType);
void PushFile(const string& p_rcoFilePath, const string& p_rcoContentType);
void ExitWithError(string p_coError);
MYSQL_RES* QueryResult(const string& p_rcoQuery, MYSQL p_MySQL);
void ReadConf(string& p_rcoServer, ulint& p_rcoPort, string& p_rcoUser,
	string& p_rcoPassword, string& p_rcoDatabaseName);
int ReadParameters(ulint& p_nArticle, ulint& p_nImage, const char** p_ppchErrMsg);
void die_mysql(MYSQL *mysql, const char *message);


int main()
{
	string coServer, coUser, coPassword, coDatabaseName;
	ulint nPort;
	ReadConf(coServer, nPort, coUser, coPassword, coDatabaseName);

	ulint nArticle, nImage;
	const char* pchErrMsg;
	if (ReadParameters(nArticle, nImage, &pchErrMsg) != 0)
	{
		if (pchErrMsg == 0)
			pchErrMsg = "Error reading parameters";
		ExitWithError(pchErrMsg);
	}

	MYSQL mysql;
	MYSQL_RES *res;
	MYSQL_ROW row;
	mysql_init(&mysql);
	if (!mysql_real_connect(&mysql, coServer.c_str(), coUser.c_str(),
			coPassword.c_str(), coDatabaseName.c_str(), nPort, 0, 0))
		die_mysql(&mysql, "connect");

	stringstream coQuery;
	coQuery << "SELECT IdImage FROM ArticleImages WHERE NrArticle = " << nArticle
		<< " AND Number = " << nImage;
	res = QueryResult(coQuery.str(), mysql);
	if (!(row = mysql_fetch_row(res)))
	{
		if (mysql_errno(&mysql))
			die_mysql(&mysql, "fetch_row");
		cout << "Content-type: text/html; charset=UTF-8\n\n";
		cout << "Could not find image " << nImage << " of article " << nArticle << endl;
		return 0;
	}
	ulint nImageId = atol(row[0]);

	coQuery.str("");
	coQuery << "SELECT ImageFileName, URL, ContentType FROM Images WHERE Id = " << nImageId;
	res = QueryResult(coQuery.str(), mysql);
	if (!(row = mysql_fetch_row(res)))
	{
		if (mysql_errno(&mysql))
			die_mysql(&mysql, "fetch_row");
		cout << "Content-type: text/html; charset=UTF-8\n\n";
		cout << "Could not find image with id " << nImageId << endl;
		return 0;
	}
	string coFileName = row[0];
	string coURL = row[1];
	string coContentType = row[2];
	mysql_close(&mysql);

	if (coURL != "")
	{
		ReadFileFromURL(coURL, coContentType);
	}
	else
	{
		string coFilePath = string(getenv("DOCUMENT_ROOT")) + "/images/" + coFileName;
		PushFile(coFilePath, coContentType);
	}

	return 0;
}


void ReadFileFromURL(const string& p_rcoURL, const string& p_rcoContentType)
{
	CURL *curl;

	curl = curl_easy_init();
	if(curl) {
		curl_easy_setopt(curl, CURLOPT_URL, p_rcoURL.c_str());
		write(1, "Content-type: ", strlen("Content-type: "));
		write(1, p_rcoContentType.c_str(), p_rcoContentType.size());
		write(1, "; Expires: now\n\n", strlen("; Expires: now\n\n"));
		curl_easy_perform(curl);
		curl_easy_cleanup(curl);
	}
}


void PushFile(const string& p_rcoFilePath, const string& p_rcoContentType)
{
	struct stat StatBuf;
	if (stat(p_rcoFilePath.c_str(), &StatBuf) == -1)
		ExitWithError(string("Unable to stat file ") + p_rcoFilePath);
	off_t nSize = StatBuf.st_size;
	int nFD = open(p_rcoFilePath.c_str(), O_RDONLY);
	if (nFD == -1)
		ExitWithError(string("Unable to open file ") + p_rcoFilePath);
	void* pMap = mmap(0, nSize, PROT_READ, MAP_SHARED, nFD, 0);
	write(1, "Content-type: ", strlen("Content-type: "));
	write(1, p_rcoContentType.c_str(), p_rcoContentType.size());
	write(1, "; Expires: now\n\n", strlen("; Expires: now\n\n"));
	write(1, pMap, nSize);
}


void ExitWithError(string p_coError)
{
	cout << "Content-type: text/html; charset=UTF-8\n\n";
	cout << "<p>" << p_coError << "</p>" << endl;
	exit(0);
}


MYSQL_RES* QueryResult(const string& p_rcoQuery, MYSQL p_MySQL)
{
	MYSQL_RES* pRes;
	if (mysql_real_query(&p_MySQL, p_rcoQuery.c_str(), p_rcoQuery.size()))
		die_mysql(&p_MySQL, "query");
	if (!(pRes = mysql_store_result(&p_MySQL)))
		die_mysql(&p_MySQL, "store_result");
	return pRes;
}


void ReadConf(string& p_rcoServer, ulint& p_nPort, string& p_rcoUser,
	string& p_rcoPassword, string& p_rcoDatabaseName)
{
	char* pchDocumentRoot = getenv("DOCUMENT_ROOT");
	try
	{
		// read database configuration
		string coDatabaseConfFile = string(pchDocumentRoot) + "/database_conf.php";
		ConfAttrValue m_coAttributes(coDatabaseConfFile);
		p_rcoServer = m_coAttributes.valueOf("DATABASE_SERVER_ADDRESS");
		p_nPort = atoi(m_coAttributes.valueOf("DATABASE_SERVER_PORT").c_str());
		p_rcoUser = m_coAttributes.valueOf("DATABASE_USER");
		p_rcoPassword = m_coAttributes.valueOf("DATABASE_PASSWORD");
		p_rcoDatabaseName = m_coAttributes.valueOf("DATABASE_NAME");
	}
	catch (ConfException& rcoEx)
	{
		cout << "Content-type: text/html; charset=UTF-8\n\n";
		cout << "Error reading configuration: " << rcoEx.what() << endl;
		exit(0);
	}
}


int ReadParameters(ulint& p_nArticle, ulint& p_nImage, const char** p_ppchErrMsg)
{
	char* pchRequestMethod = 0;
	char* pchQueryString = 0;
	try
	{
		char* pchTmp;
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
		if (strcasecmp(pchParam, "NrArticle") == 0)
			p_nArticle = atol(pchValue);
		if (strcasecmp(pchParam, "NrImage") == 0)
			p_nImage = atol(pchValue);
	}

	return 0;
}

void die_mysql(MYSQL *mysql, const char *message)
{
	cout << "Content-type: text/html; charset=UTF-8\n\n";
	cout << "get_img: " << message << mysql_error(mysql) << endl;
	exit(1);
}
