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

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <errno.h>
#include <unistd.h>
#include <mysql/mysql.h>
#include <iostream>
#include <sys/types.h>
#include <sys/wait.h>

#include "kwd.h"
#include "readconf.h"
#include "configure.h"
#include "ccampsiteinstance.h"

using std::cout;
using std::cerr;
using std::endl;

#define debug if(0) printf

string SQL_SERVER;
string SQL_USER;
string SQL_PASSWORD;
string SQL_DATABASE;
int SQL_SRV_PORT = 0;
char *prog_name = 0;

struct article_t {
	unsigned IdPublication;
	unsigned NrIssue;
	unsigned NrSection;
	unsigned Number;
	unsigned IdLanguage;
	int Published;
	char *Type;
	char *Keywords;
	char *Name;
};

void die_usage()
{
	printf("Usage: %s [options]\n"
			"Options:\n"
			"\t--conf_dir [conf_dir]: Host name of the MySQL server.\n"
			"\t--help: Display this information.\n",
	prog_name);
	exit(0);
}

void die_mysql(MYSQL *mysql, const char *message)
{
	fprintf(stderr, "%s: instance %s: %s: %s\n",
			prog_name, SQL_DATABASE.c_str(), message, mysql_error(mysql));
	exit(1);
}

static void build_kwd_list(MYSQL *mysql, struct article_t *a)
{
	MYSQL_RES *res;
	MYSQL_ROW row;
	MYSQL_FIELD *fld;
	int nf, i;
	char query[1024];

	debug("kwd[%s]\n", a->Keywords);
	parse_kwd(a->Keywords);
	debug("name[%s]\n", a->Name);
	parse_kwd(a->Name);

	if (!a->Type)
		return;

	sprintf(query, "SELECT * FROM X%s WHERE NrArticle = %u AND IdLanguage = %u",
			a->Type, a->Number, a->IdLanguage);
	debug("QUERY [%s]\n", query);
	if (mysql_query(mysql, query) != 0)
		die_mysql(mysql, "Get article: query");

	res = mysql_store_result(mysql);
	if (!res)
		die_mysql(mysql, "Get article: store_result");

	row = mysql_fetch_row(res);
	if (row) {
		nf = mysql_num_fields(res);
		fld = mysql_fetch_fields(res);
		for (i = 0; i < nf; i++) {
			if (fld[i].name[0] == 'F') {
				debug("fdl[%s]\n", fld[i].name);
				if (row[i])
					parse_kwd(row[i]);
			}
		}
	}
	mysql_free_result(res);
}

int GatherFunc(const ConfAttrValue& p_rcoConfValues);


int main(int argc, char **argv)
{
	string coConfDir;

	/* Parse program name from command line */
	prog_name = strrchr(argv[0], '/');
	if (prog_name)
		prog_name++;
	else
		prog_name = argv[0];

	/* Parse parameters from command line */
	for (int i = 1; i < argc; i++) {
		if (strcmp(argv[i], "--conf_dir") == 0) {
			if (!argv[++i])
				die_usage();
			coConfDir = argv[i];
		} else
			die_usage();
	}

	if (coConfDir == "")
		coConfDir = ETC_DIR;
	const CCampsiteInstanceMap& rcoInstances =
			CCampsiteInstance::readFromDirectory(coConfDir, GatherFunc);

	CCampsiteInstanceMap::const_iterator coIt = rcoInstances.begin();
	for (; coIt != rcoInstances.end(); ++coIt)
	{
		(*coIt).second->run();
	}
	while (true)
	{
		waitpid(-1, 0, 0);
		for (coIt = rcoInstances.begin(); coIt != rcoInstances.end(); ++coIt)
		{
			if (!(*coIt).second->isRunning())
			{
				CCampsiteInstanceRegister::get().erase((*coIt).second->getName());
			}
		}
		if (CCampsiteInstanceRegister::get().isEmpty())
			break;
	}

	return 0;
}

int GatherFunc(const ConfAttrValue& p_rcoConfValues)
{
	MYSQL mysql;
	MYSQL_RES *res, *res1;
	MYSQL_ROW row, row1;

	unsigned nart, nword, nnew, kwd_id;
	int h;
	struct kwd_t *k;
	struct article_t a;
	char query[1024], *p;

	SQL_SERVER = p_rcoConfValues.valueOf("DATABASE_SERVER_ADDRESS");
	SQL_SRV_PORT = atoi(p_rcoConfValues.valueOf("DATABASE_SERVER_PORT").c_str());
	SQL_USER = p_rcoConfValues.valueOf("DATABASE_USER");
	SQL_PASSWORD = p_rcoConfValues.valueOf("DATABASE_PASSWORD");
	SQL_DATABASE = p_rcoConfValues.valueOf("DATABASE_NAME");

	/* Connect to the MySQL server */
	mysql_init(&mysql);
	if (!mysql_real_connect(&mysql, SQL_SERVER.c_str(), SQL_USER.c_str(), SQL_PASSWORD.c_str(),
			SQL_DATABASE.c_str(), SQL_SRV_PORT, 0, 0))
		die_mysql(&mysql, "Connecting to the database server");


	/* Select articles not yet indexed */
	if (mysql_query(&mysql, "SELECT IdPublication, NrIssue, NrSection, Number, IdLanguage, "
			"Published, Type, Keywords, Name FROM Articles WHERE IsIndexed = 'N'") != 0)
		die_mysql(&mysql, "Selecting articles not yet indexed: query");
	if ((res = mysql_store_result(&mysql)) == NULL)
	{
		die_mysql(&mysql, "Selecting articles not yet indexed: store_result");
	}
	
	nart = nword = nnew = 0;
	
	while ((row = mysql_fetch_row(res))) {
		a.IdPublication = row[0] ? atoi(row[0]) : 0;
		a.NrIssue = row[1] ? atoi(row[1]) : 0;
		a.NrSection = row[2] ? atoi(row[2]) : 0;
		a.Number = row[3] ? atoi(row[3]) : 0;
		a.IdLanguage = row[4] ? atoi(row[4]) : 0;
		a.Published = row[5] && (row[5][0] == 'Y');
		a.Type = strdup(row[6] ? row[6] : "");
		a.Keywords = strdup(row[7] ? row[7] : "");
		a.Name = strdup(row[8] ? row[8] : "");
		
		/* Delete from index */
		sprintf(query, "DELETE FROM ArticleIndex WHERE IdPublication = %u AND IdLanguage = %u "
						"AND NrIssue = %u AND NrSection = %u AND NrArticle = %u",
						a.IdPublication, a.IdLanguage, a.NrIssue, a.NrSection, a.Number);
		debug("QUERY [%s]\n", query);
		if (mysql_query(&mysql, query) != 0)
			die_mysql(&mysql, "Deleting old index: query");
		
		if (!a.Published)
			continue;
		
		nart++;
		
		init_hash();
		build_kwd_list(&mysql, &a);
		for (h = 0; h < 256; h++)
			for (k = kwd_hash[h]; k; k = k->next) {
			if (!k->k)
				continue;
			nword++;
			
			p = (char*)malloc(strlen(k->k) * 2 + 1);
			mysql_escape_string(p, k->k, mymin(strlen(k->k), MAX_KWD));
			
			sprintf(query, "SELECT Id FROM KeywordIndex WHERE Keyword = '%s'", p);
			debug("QUERY [%s]\n", query);
			if (mysql_query(&mysql, query) != 0)
				die_mysql(&mysql, "Get KeywordId: query");
			
			res1 = mysql_store_result(&mysql);
			if (!res1)
				die_mysql(&mysql, "Get KeywordId: store_result");
			
			row1 = mysql_fetch_row(res1);
			kwd_id = (row1 != NULL && row1[0] != NULL) ? atoi(row1[0]) : 0;
			
			mysql_free_result(res1);
			
			if (kwd_id == 0) {
				if (mysql_query(&mysql, "LOCK TABLE KeywordIndex WRITE"))
					die_mysql(&mysql, "Lock table KeywordIndex: query");
				
				if (mysql_query(&mysql, "SELECT MAX(Id) from KeywordIndex"))
					die_mysql(&mysql, "Reading the last id: query");
				
				res1 = mysql_store_result(&mysql);
				if (!res1)
					die_mysql(&mysql, "Read last id: store_result");
				row1 = mysql_fetch_row(res1);
				kwd_id = 1 + ((row1 != NULL && row1[0] != NULL) ? atoi(row1[0]) : 0);
				
				/* Insert in keyword list */
				sprintf(query, "INSERT INTO KeywordIndex SET Keyword = '%s', Id = %u", p, kwd_id);
				debug("QUERY [%s]\n", query);
				mysql_free_result(res1);
				if (mysql_query(&mysql, query) != 0)
					die_mysql(&mysql, "Adding keyword: query");
				
				if (mysql_query(&mysql, "UNLOCK TABLES"))
					die_mysql(&mysql, "Unlock table KeywordIndex: query");
				
				nnew++;
			}
			
			free(p);
			
			/* Insert in article index */
			sprintf(query, "INSERT IGNORE INTO ArticleIndex SET IdPublication = %u, "
						"IdLanguage = %u, IdKeyword = %u, NrIssue = %u, NrSection = %u, "
						"NrArticle = %u", a.IdPublication, a.IdLanguage, kwd_id,
						a.NrIssue, a.NrSection, a.Number);
			debug("QUERY [%s]\n", query);
			if (mysql_query(&mysql, query) != 0)
				die_mysql(&mysql, "Adding article to index: query");
			}
			
			del_kwd_list();
    
			free(a.Name);
			free(a.Keywords);
			free(a.Type);
    
			sprintf(query, "UPDATE Articles SET IsIndexed = 'Y' WHERE IdPublication = "
					"%u AND NrIssue = %u AND NrSection = %u AND Number = %u AND "
							"IdLanguage = %u", a.IdPublication, a.NrIssue, a.NrSection,
					a.Number, a.IdLanguage);
			debug("QUERY [%s]\n", query);
			if (mysql_query(&mysql, query) != 0)
				die_mysql(&mysql, "Updating article: query");
    
	}
	if (nart > 0 || nword > 0 || nnew > 0)
		printf("Instance %s: %u new articles, %u words processed, %u of them are new\n",
			SQL_DATABASE.c_str(), nart, nword, nnew);
	mysql_close(&mysql);
  
	return 0;
}
