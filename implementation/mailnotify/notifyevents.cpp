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
#include <stdlib.h>
#include <string.h>
#include <stdio.h>
#include <mysql/mysql.h>
#include <iostream>

#include "sql_macros.h"
#include "readconf.h"
#include "configure.h"

using std::cout;
using std::endl;
using std::string;

string SMTP_SERVER;
string SMTP_WRAPPER;
string SQL_SERVER;
string SQL_USER;
string SQL_PASSWORD;
string SQL_DATABASE;
int SQL_SRV_PORT = 0;

void ReadConf();
int SQLConnection(MYSQL **sql);

int main()
{
  char buf[2000], text[10000], command[200], *last_tstamp = 0;
  MYSQL *sql = 0;
  int result;
  ReadConf();
  if ((result = SQLConnection(&sql)) != RES_OK)
    return result;

  // read reply address
  sprintf(buf, "select EMail from Users where Id = 1");
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

  // read events
  sprintf(buf, "select LogTStamp from AutoId");
  if (mysql_query(sql, buf) != 0)
    return 1;
  res = mysql_store_result(sql);
  if (res == NULL)
    return 2;
  row = mysql_fetch_row(res);
  if (row == NULL)
    return 3;
  sprintf(buf, "select Users.Name, Users.EMail, Users.UName, Events.Name, "
          "Log.Text, Log.TStamp, now() from Log, Users, Events where IdEvent "
          "= Events.Id and Events.Notify = 'Y' and Log.User = Users.UName and "
          "TStamp > '%s' order by TStamp", row[0]);
  mysql_free_result(res);
  if (mysql_query(sql, buf) != 0)
    return 1;
  if ((res = mysql_store_result(sql)) == 0)
    return 2;
  int num_rows = mysql_num_rows(res);
  for (int i = 0; i < num_rows && (row = mysql_fetch_row(res)) != 0; i++) {
    if (i == 0)
      last_tstamp = strdup(row[6]);

    const char *name = row[0];
    const char *email = row[1];
    const char *uname = row[2];
    const char *event = row[3];
    const char *event_text = row[4];
    const char *event_tstamp = row[5];

    sprintf(text, "Performed by: %s (%s) - email: %s\n%s on %s\n", name, uname,
            email, event_text, event_tstamp);

    sprintf(buf, "select Users.EMail from Users, UserPerm where MailNotify = "
            "'Y' and Users.Id = UserPerm.IdUser");
    if (mysql_query(sql, buf) != 0)
      return 1;
    MYSQL_RES* res_usr = mysql_store_result(sql);
    if (res_usr == NULL)
      return 2;
    if (mysql_num_rows(res_usr) == 0)
      continue;
    MYSQL_ROW row_usr;
    while ((row_usr = mysql_fetch_row(res_usr))) {
      if (row_usr[0] == NULL || row_usr[0][0] == 0)
        continue;
      sprintf(command, "%s -s %s -r %s %s", SMTP_WRAPPER.c_str(),
              SMTP_SERVER.c_str(), reply, row_usr[0]);
      FILE *os = popen(command, "w");
      if (os == NULL)
        return -1;
      fprintf(os, "Subject: %s\n%s\n", event, text);
      pclose(os);
    }
  }
  mysql_free_result(res);
  if (last_tstamp != 0 && last_tstamp[0] != 0)
  {
    sprintf(buf, "update AutoId set LogTStamp = '%s'", last_tstamp);
    mysql_query(sql, buf);
    free(last_tstamp);
  }
  return 0;
}

int SQLConnection(MYSQL **sql)
{
  if (*sql)
    return RES_OK;
  for (int i = 0; *sql == 0 && i < MAX_TRIES; i++)
    *sql = mysql_init(*sql);
  if (*sql == 0)
    return ERR_NOMEM;
  if ((*sql = mysql_real_connect(*sql, SQL_SERVER.c_str(), SQL_USER.c_str(),
                                 SQL_PASSWORD.c_str(), SQL_DATABASE.c_str(),
                                 SQL_SRV_PORT, 0, 0)) == 0)
    return ERR_SQLCONNECT;
  return RES_OK;
}

void ReadConf()
{
  try
  {
    ConfAttrValue coConf(SMTP_CONF_FILE);
    SMTP_SERVER = coConf.ValueOf("SERVER");
    SMTP_WRAPPER = coConf.ValueOf("WRAPPER");
    ConfAttrValue coDBConf(DATABASE_CONF_FILE);
    SQL_SERVER = coDBConf.ValueOf("SERVER");
    SQL_SRV_PORT = atoi(coDBConf.ValueOf("PORT").c_str());
    SQL_USER = coDBConf.ValueOf("USER");
    SQL_PASSWORD = coDBConf.ValueOf("PASSWORD");
    SQL_DATABASE = coDBConf.ValueOf("NAME");
  }
  catch (ConfException& rcoEx)
  {
    cout << "Error reading configuration: " << rcoEx.what() << endl;
    exit(1);
  }
}
