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

#include <stdlib.h>
#include <string.h>
#include <iostream.h>
#include <mysql/mysql.h>

int main(int argc, char** argv)
{
	if (argc < 4)
	{
		cout << "Not enough arguments" << endl;
		return 1;
	}
	int i = 1;
	const char* pchDatabaseName = NULL;
	if (strcmp(argv[i], "-d") == 0)
	{
		pchDatabaseName = argv[++i];
		i++;
	}
	const char* pchServerHost = argv[i++];
	unsigned int nServerPort = strtoul(argv[i++], 0, 10);
	const char* pchUserName = argv[i++];
	const char* pchPassword = argc == (i+1) ? argv[i] : "";
	MYSQL* pMySQL = NULL;
	pMySQL = mysql_init(pMySQL);
	if (pMySQL == NULL)
	{
		cout << "Unable to initialise MySQL connection" << endl;
		return 2;
	}
	pMySQL = mysql_real_connect(pMySQL, pchServerHost, pchUserName, pchPassword,
	                            NULL, nServerPort, NULL, 0);
	if (pMySQL == NULL)
	{
		cout << "Unable to connect to MySQL server" << endl;
		return 3;
	}
	if (pchDatabaseName == NULL)
	{
		char* pchServerInfo = mysql_get_server_info(pMySQL);
		if (pchServerInfo == NULL)
		{
			cout << "Error retreiving MySQL server info" << endl;
			return 4;
		}
		cout << pchServerInfo << endl;
		return 0;
	}
	mysql_close(pMySQL);
	pMySQL = NULL;
	pMySQL = mysql_init(pMySQL);
	if (pMySQL == NULL)
	{
		cout << "Unable to initialise MySQL connection" << endl;
		return 5;
	}
	pMySQL = mysql_real_connect(pMySQL, pchServerHost, pchUserName, pchPassword,
	                            pchDatabaseName, nServerPort, NULL, 0);
	if (pMySQL == NULL)
	{
		cout << "Invalid database name " << pchDatabaseName << endl;
		return 6;
	}
	char* pchServerInfo = mysql_get_server_info(pMySQL);
	if (pchServerInfo == NULL)
	{
		cout << "Error retreiving MySQL server info" << endl;
		return 7;
	}
	cout << pchServerInfo << endl;
	return 0;
}
