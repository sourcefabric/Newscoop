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

#include <string.h>
#include <string>
#include <stdlib.h>
#include <stdio.h>
#include <mysql/mysql.h>
#include <iostream>
#include <sys/types.h>
#include <sys/wait.h>
#include <unistd.h>

#include "sql_macros.h"
#include "readconf.h"
#include "configure.h"
#include "ccampsiteinstance.h"

using std::string;
using std::cout;
using std::cerr;
using std::endl;

string coConfDir;
string SMTP_SERVER;
string SMTP_WRAPPER;
string SQL_SERVER;
string SQL_USER;
string SQL_PASSWORD;
string SQL_DATABASE;
int SQL_SRV_PORT = 0;
#define MAX_TRIES 5

#ifdef _DEBUG
#define DEBUG_FD 2
#else
#define DEBUG_FD -1
#endif
outbuf g_coDebugBuf(DEBUG_FD);
ostream g_coDebug(&g_coDebugBuf);

outbuf g_coNoDebugBuf(-1);
ostream g_coNoDebug(&g_coNoDebugBuf);

int SQLConnection(MYSQL **sql);
int NotifyEndSubsFunc(const ConfAttrValue& p_rcoConfValues);

int main()
{
	if (coConfDir == "")
		coConfDir = ETC_DIR;
	const CCampsiteInstanceMap& rcoInstances =
			CCampsiteInstance::readFromDirectory(coConfDir, NotifyEndSubsFunc);

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

int NotifyEndSubsFunc(const ConfAttrValue& p_rcoConfValues)
{
	char buf[2000], text[10000], command[200], sections[2000];
	MYSQL *sql = 0;
	int result;

	SMTP_SERVER = p_rcoConfValues.valueOf("SMTP_SERVER_ADDRESS");
	SQL_SERVER = p_rcoConfValues.valueOf("DATABASE_SERVER_ADDRESS");
	SQL_SRV_PORT = atoi(p_rcoConfValues.valueOf("DATABASE_SERVER_PORT").c_str());
	SQL_USER = p_rcoConfValues.valueOf("DATABASE_USER");
	SQL_PASSWORD = p_rcoConfValues.valueOf("DATABASE_PASSWORD");
	SQL_DATABASE = p_rcoConfValues.valueOf("DATABASE_NAME");

	string coInstallConfFile = coConfDir + "/install_conf.php";
	ConfAttrValue coInstallConf(coInstallConfFile);
	SMTP_WRAPPER = coInstallConf.valueOf("BIN_DIR") + "/smtp_wrapper";

	if ((result = SQLConnection(&sql)) != RES_OK)
		return result;

	// read reply address
	sprintf(buf, "select EMail from Users where UName = 'admin'");
	SQLQuery(sql, buf);
	StoreResult(sql, res);
	CheckForRows(res, 1);
	MYSQL_ROW row = mysql_fetch_row(res);
	if (row == 0)
	{
		printf("Not enough memory");
		exit(1);
	}
	char* reply = strdup(row[0]);
	mysql_free_result(res);

	long int notifiedIndex = 0;
	// read ending subscriptions
	sprintf(buf, "select p.Name, p.IdDefaultLanguage, u.Title, u.Name, u.EMail, s.Id, "
				"s.Type, p.Id, a.Name "
			"from Subscriptions as s, Publications as p, Aliases as a, Users as u "
			"where u.Id = s.IdUser and p.Id = s.IdPublication and s.Active = 'Y' "
				"and p.IdDefaultAlias = a.Id and s.ToPay = \"0.00\"");
	SQLQuery(sql, buf);
	if ((res = mysql_store_result(sql)) == 0)
	{
		printf("Not enough memory");
		exit(1);
	}
	while ((row = mysql_fetch_row(res))) {
		const char *pub_name = row[0];
		long int id_lang = atol(row[1]);
		const char *user_title = row[2];
		const char *user_name = row[3];
		const char *user_email = row[4];
		long int id_subs = atol(row[5]);
		const char *subs_type = row[6];
		long int id_pub = atol(row[7]);
		const char *site = row[8];

		sprintf(buf, "select Max(Number) from Issues where IdPublication = %ld and"
				" IdLanguage = %ld and Published = 'Y'", id_pub, id_lang);
		SQLQuery(sql, buf);
		StoreResult(sql, res_is);
		if (mysql_num_rows(res_is) <= 0)
			continue;
		MYSQL_ROW row_is = mysql_fetch_row(res_is);
		long int issue_nr = atol(row_is[0]);
		mysql_free_result(res_is);

		sprintf(buf, "select count(*) from Sections where IdPublication = %ld and "
				"NrIssue = %ld and IdLanguage = %ld", id_pub, issue_nr, id_lang);
		SQLQuery(sql, buf);
		StoreResult(sql, res_pub);
		MYSQL_ROW row_pub = mysql_fetch_row(res_pub);
		long int pub_sections = atol(row_pub[0]);
		mysql_free_result(res_pub);

		sprintf(buf, "select StartDate, DATE_FORMAT(StartDate, '%%M %%D, %%Y'), "
				"PaidDays, TO_DAYS(StartDate), TO_DAYS(now()), DATE_FORMAT("
				"ADDDATE(StartDate, INTERVAL PaidDays DAY), '%%M %%D, %%Y') from "
				"SubsSections where IdSubscription = %ld and NoticeSent = 'N' "
				"group by StartDate, PaidDays", id_subs);
		SQLQuery(sql, buf);
		StoreResult(sql, res_sec);
		int num_rows = mysql_num_rows(res_sec);
		if (num_rows <= 0)
			continue;
		MYSQL_ROW row_sec;
		text[0] = 0;
		bool notify = false;
		long int subs_sections = 0;
		int counter = 0;
		sections[0] = 0;
		while ((row_sec = mysql_fetch_row(res_sec))) {
			const char *sd = row_sec[0];
			const char *sdf = row_sec[1];
			long int paid_days = atol(row_sec[2]);
			long int sd_days = atol(row_sec[3]);
			long int now_days = atol(row_sec[4]);
			const char *edf = row_sec[5];
			if (now_days > (paid_days + sd_days))
				continue;
			long int remained_days = paid_days + sd_days - now_days;
			if (remained_days > 14 || remained_days <= 0)
				continue;
			notify = true;
			if (num_rows == 1) {
				sprintf(buf, "select count(*) from SubsSections where IdSubscription ="
						" %ld and NoticeSent = 'N' and StartDate = '%s' and PaidDays "
						"= %ld", id_subs, sd, paid_days);
				SQLQuery(sql, buf);
				StoreResult(sql, res_sec_nr);
				MYSQL_ROW row_sec_nr = mysql_fetch_row(res_sec_nr);
				subs_sections = atol(row_sec_nr[0]);
				mysql_free_result(res_sec_nr);
			}
			if (counter == 0)
				sprintf(text, "Dear %s %s,\n\nThis is an automatically generated mail message.\n\n"
						"Your %s subscription (started on %s) to publication %s",
						user_title, user_name, subs_type[0] == 'P' ? "paid" : "trial",
						sdf, pub_name);
			if (subs_sections == pub_sections && num_rows == 1) {
				sprintf(text+strlen(text), " will expire on %s (in %ld days).\n",
						edf, remained_days);
			} else {
				sprintf(buf, "select Sections.Name, Sections.Number from Sections, "
						"SubsSections where IdSubscription = %ld and NoticeSent = 'N' "
						"and StartDate = '%s' and PaidDays = %ld and IdPublication = "
						"%ld and NrIssue = %ld and IdLanguage = %ld and Number = "
						"SectionNumber", id_subs, sd, paid_days, id_pub, issue_nr, id_lang);
				SQLQuery(sql, buf);
				StoreResult(sql, res_date_sec);
				MYSQL_ROW row_date_sec;
				if (counter == 0)
					sprintf(text+strlen(text), " will expire as follows:\n");
				sprintf(text+strlen(text), "\t- ");
				bool is_first = true;
				while ((row_date_sec = mysql_fetch_row(res_date_sec))) {
					if (!is_first)
						strcat(text, ", ");
					else
						is_first = false;
					if (strlen(sections))
						strcat(sections, " or ");
					sprintf(text+strlen(text), "\"%s\"", row_date_sec[0]);
					sprintf(sections+strlen(sections), "SectionNumber = %s",
							row_date_sec[1]);
				}
				sprintf(text+strlen(text), " on %s (remained %ld days) - started on %s"
						"\n", edf, remained_days, sdf);
				mysql_free_result(res_date_sec);
				counter++;
			}
		}
		sprintf(text+strlen(text), "\nPlease enter the site http://%s to update "
				"subscription.\n", site);
		if (!notify)
			continue;
		sprintf(command, "%s -s %s -r %s %s", SMTP_WRAPPER.c_str(),
				SMTP_SERVER.c_str(), reply, user_email);
		FILE *os = popen(command, "w");
		if (os == NULL)
			return -1;
		fprintf(os, "Subject: Subscription to %s\n%s\n", pub_name, text);
		if (pclose(os) == -1)
			continue;
		sprintf(buf, "update SubsSections set NoticeSent='Y' where IdSubscription"
				" = %ld", id_subs);
		if (strlen(sections))
			sprintf(buf+strlen(buf), " and (%s)", sections);
		SQLQuery(sql, buf);
		mysql_free_result(res_sec);
		notifiedIndex++;
	}
	if (notifiedIndex > 0) {
		printf("%s: %ld user(s) notified\n", SQL_DATABASE.c_str(), notifiedIndex);
	}
	return 0;
}

int SQLConnection(MYSQL **sql)
{
	if (*sql)
		return RES_OK;
	for (int i = 0; *sql == 0 && i < MAX_TRIES; i++) {
		*sql = mysql_init(*sql);
		sleep(10);
	}
	if (*sql == 0)
		return ERR_NOMEM;
	if ((*sql = mysql_real_connect(*sql, SQL_SERVER.c_str(), SQL_USER.c_str(),
		  SQL_PASSWORD.c_str(), SQL_DATABASE.c_str(),
		  SQL_SRV_PORT, 0, 0)) == 0)
		return ERR_SQLCONNECT;
	return RES_OK;
}
