/*
     WWW-SQL - parses HTML files and inserts information from MySQL databases
    Copyright (C) 1997  James Henstridge <james@daa.com.au>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 */
/*   mysql.c
 * contains all the database specific functions for www-sql.  At the moment,
 * this is the only such file, but in the future, there may be implementations
 * for other database systems.
 */

#include <mysql.h>

#include "cgi.h"
#include "cmds.h"

#define checkConn(f) if (conn == NULL) {\
                  fprintf(yyout, "<p>%s: no connection established</p>\n", f);\
                       return; }

/* Define the query info structure.  To enable multiple queries, we make
 * a linked list.
 */
typedef struct qh_struct {
  char *name;
  MYSQL_RES *res;
  MYSQL_ROW row;
  struct qh_struct *next;
} qHandle;

qHandle *firstQuery = NULL;
MYSQL *conn = NULL;

#define isNum(n) (n >= '0' && n <= '9')

/* These functions are helpers for print_loop */
int dbLoopSetup(char *name, void **data) {
  qHandle *qh;

  qh = firstQuery;
  while (qh != NULL) {
    if (!strcmp(qh->name, name)) break;
    qh = qh->next;
  }
  if (qh == NULL) {
    fprintf(yyout, "<p><b>print_loop</b>: couldn't find query handle %s</p>\n",
	    name);
    return -1;
  }
  *data = qh;
  mysql_data_seek(qh->res, 0);
  qh->row = mysql_fetch_row(qh->res);
  return (qh->row != NULL);
}

int dbLoopNext(void *data) {
  qHandle *qh = (qHandle *)data;

  qh->row = mysql_fetch_row(qh->res);
  return (qh->row != NULL);
}

/* This function is used as a hook to clean up any database related stuff
 * when www-sql closes */
void dbCleanUp() {
  if (conn != NULL)
    mysql_close(conn);
}

/* This function acts is used as a hook by substVars to do expansions of
 * query fields. */
char *substField(char *inp, int *toklen) {
  qHandle *qh;
  int len, len2, i;

  for (qh = firstQuery; qh != NULL; qh = qh->next)
    if (!strncmp(inp, qh->name, len = strlen(qh->name)) && inp[len] == '.') {
      len++;
      if (isNum(inp[len])) {
	i = atoi(&inp[len]);
	while (isNum(inp[len])) len++;
      } else {
	/* added a fix so that www-sql doesn't get it wrong if you have one
	 * variable that's name is a prefix of an other.
	 * Thanks Eduardo Trapani <etrapani@unesco.org.uy>
	 * I have implemented similar fixes in pgsql.c and cmds.c */
	int field = mysql_num_fields(qh->res), longest = 0; 
	for (i = 0; i < mysql_num_fields(qh->res); i++)
	  if (!strncmp(&inp[len], mysql_fetch_fields(qh->res)[i].name,
		       len2 = strlen(mysql_fetch_fields(qh->res)[i].name)) &&
	      len2 > longest) {
	    longest = len2;
	    field = i;
	  }
	len += longest;
	i = field;
      }
      if (i >= mysql_num_fields(qh->res) || qh->row == NULL) {
	*toklen = 0;
        return "Column out of range: ";
      }
      *toklen = len;
      if (qh->row[i] == NULL)
	return "";
      else
	return qh->row[i];
    }
  *toklen = 0;
  return NULL;
}

void cmdConnect(int argc, char *argv[]) {
  char *host = NULL, *user = SQL_USER, *pass = SQL_PASS;
  int h=0, u=0, p=0;

  if (conn != NULL)
    mysql_close(conn);
  if (argc > 2) {
    user = substVars(argv[1]);
    pass = substVars(argv[2]);
    u=1; p=1;
  } else if (argc > 1) {
    user = substVars(argv[1]);
    pass = NULL;
    u=1;
  }
  if (argc > 0) {
    host = substVars(argv[0]);
    h=1;
  }

  conn = xmalloc(sizeof(MYSQL));
  mysql_connect(conn, host, user, pass);
#ifdef mysql_errno
  if (mysql_errno(conn)) {
    fprintf(yyout, "<p>Connect: %s</p>\n", mysql_error(conn));
    mysql_close(conn);
    free(conn);
    conn = NULL;
  }
#endif
  if (h) free(host);
  if (u) free(user);
  if (p) free(pass);
}

void cmdClose(int argc, char *argv[]) {
  if (conn != NULL) {
    mysql_close(conn);
    free(conn);
    conn = NULL;
  }
}

void cmdDatabase(int argc, char *argv[]) {
  char *tmp;

  checkNumArgs(1,"database");
  checkConn("database");
  tmp = substVars(argv[0]);
  if (mysql_select_db(conn, tmp)) {
    fprintf(yyout, "<p>Database: %s</p>\n", mysql_error(conn));
  }
  free(tmp);
}

void cmdQuery(int argc, char *argv[]) {
  char *tmp, buf[21];
  qHandle *qh;
  MYSQL_RES *res;

  checkNumArgs(1,"query");
  checkConn("query");
  tmp = substVars(argv[0]);
  if (mysql_query(conn, tmp) < 0) {
    fprintf(yyout, "<p>Query failed: %s</p>\n", mysql_error(conn));
    free(tmp);
    setVar("AFFECTED_ROWS", "0");
    setVar("INSERT_ID", "-1");
    return;
  }
  free(tmp);
  sprintf(buf, "%ld", mysql_affected_rows(conn));
  setVar("AFFECTED_ROWS", buf);
  sprintf(buf, "%ld", mysql_insert_id(conn));
  setVar("INSERT_ID", buf);
  /* if the query has no body, don't set up query handle (ie for INSERT) */
  if ((res = mysql_store_result(conn)) == NULL) {
    return;
  }

  if (argc < 2) {
    mysql_free_result(res);
    fprintf(yyout, "<p>Query: query requires a handle name</p>\n");
    return;
  }
  qh = xmalloc(sizeof(qHandle));

  qh->name = xstrdup(argv[1]);
  qh->res = res;
  qh->row = mysql_fetch_row(qh->res);
  qh->next = firstQuery;
  firstQuery = qh;

  sprintf(buf, "%d", mysql_num_fields(qh->res));
  setVar("NUM_FIELDS", buf);
  sprintf(buf, "%ld", mysql_num_rows(qh->res));
  setVar("NUM_ROWS", buf);
}

void cmdFree(int argc, char *argv[]) {
  qHandle *qh, *prev, phony = {"", NULL, NULL, NULL};

  phony.next = firstQuery;
  checkNumArgs(1,"free");

  prev = &phony;
  qh = firstQuery;
  while (qh != NULL) {
    if (!strcasecmp(argv[0], qh->name)) {
      mysql_free_result(qh->res);
      free(qh->name);
      prev->next = qh->next;
      free(qh);
      qh = prev->next;
    } else {
      prev = qh;
      qh = qh->next;
    }
  }
  firstQuery = phony.next;
}

void cmdPrintRows(int argc, char *argv[]) {
  qHandle *qh;
  char *tmp;

  checkNumArgs(2,"print_rows");
  checkConn("print_rows");

  qh = firstQuery;
  while (qh != NULL) {
    if (!strcmp(qh->name, argv[0])) break;
    qh = qh->next;
  }
  if (qh == NULL) {
    fprintf(yyout, "<p>print_rows: query handle %s not found</p>\n", argv[0]);
    return;
  }

  while (qh->row) {
    tmp = substVars(argv[1]);
    fprintf(yyout, "%s", tmp);
    free(tmp);
    qh->row = mysql_fetch_row(qh->res);
  }
}

void cmdFetch(int argc, char *argv[]) {
  qHandle *qh;

  checkNumArgs(1,"fetch");
  checkConn("fetch");

  qh = firstQuery;
  while (qh != NULL) {
    if (!strcmp(qh->name, argv[0])) break;
    qh = qh->next;
  }
  if (qh == NULL) {
    fprintf(yyout, "<p>fetch: query handle not found\n</p>");
    return;
  }

  qh->row = mysql_fetch_row(qh->res);
}

void cmdSeek(int argc, char *argv[]) {
  qHandle *qh;

  checkNumArgs(2,"seek");
  checkConn("seek");

  qh = firstQuery;
  while (qh != NULL) {
    if (!strcmp(qh->name, argv[0])) break;
    qh = qh->next;
  }
  if (qh == NULL) {
    fprintf(yyout, "<p>seek: query handle not found</p>\n");
    return;
  }

  mysql_data_seek(qh->res, atoi(argv[1]));
  qh->row = mysql_fetch_row(qh->res);
}

#ifndef IS_NUM
/* some peoples mysql header files don't seem to define this... */
# define IS_NUM(t) ((t) <= FIELD_TYPE_INT24)
#endif

/* This command was contributed by Martin Maisey <M.J.Maisey@webname.com>
 * (with slight modifications by me (James). */
void cmdQtable(int argc, char *argv[]) {
  qHandle *qh;
  unsigned int i;

  checkNumArgs(1,"qtable");
  checkConn("qtable");

  qh = firstQuery;
  while (qh != NULL) {
    if (!strcmp(qh->name, argv[0])) break;
    qh = qh->next;
  }
  if (qh == NULL) {
    fprintf(yyout, "<p>qtable: query handle not found</p>\n");
    return;
  }

  if (argc > 1 && !strcasecmp(argv[1], "borders"))
    fprintf(yyout, "<table border>\n");
  else
    fprintf(yyout, "<table>\n");
  /* Output field names */
  fprintf(yyout, "<tr>");
  for (i = 0; i < mysql_num_fields(qh->res); i++)
    fprintf(yyout, "<th>%s</th> ", mysql_fetch_fields(qh->res)[i].name);
  fprintf(yyout, "</tr>\n");
  mysql_data_seek(qh->res, 0);
  qh->row = mysql_fetch_row(qh->res);

  /* Output rows */
  while (qh->row) {
    fprintf(yyout, "<tr>");
    for (i = 0; i < mysql_num_fields(qh->res); i++)
      if (IS_NUM(mysql_fetch_fields(qh->res)[i].type)) /*right align numbers*/
        fprintf(yyout, "<td align=right>%s</td> ", qh->row[i]);
      else
        fprintf(yyout, "<td>%s</td> ", qh->row[i]);
    fprintf(yyout, "</tr>\n");
    qh->row = mysql_fetch_row(qh->res);
  }
  fprintf(yyout, "</table>\n");
}

/* This command was contributed by Martin Maisey <M.J.Maisey@webname.com> */
void cmdQlongform(int argc, char *argv[]) {
  qHandle *qh;
  unsigned int i;

  checkNumArgs(1,"qlongform");
  checkConn("qlongform");

  qh = firstQuery;
  while (qh != NULL) {
    if (!strcmp(qh->name, argv[0])) break;
    qh = qh->next;
  }
  if (qh == NULL) {
    fprintf(yyout, "<p>qlongform: query handle not found</p>\n");
    return;
  }

  /* Move to first row */
  mysql_data_seek(qh->res, 0);
  qh->row = mysql_fetch_row(qh->res);

  /* Output rows */
  while (qh->row) {
    for (i = 0; i < mysql_num_fields(qh->res); i++)
      fprintf(yyout, "<b>%s:</b> %s<br>\n",mysql_fetch_fields(qh->res)[i].name,
                      qh->row[i]);
    fprintf(yyout, "<p>\n");
    qh->row = mysql_fetch_row(qh->res);
  }
}

/* print a <select> style list box from a query.  For use in forms.
   Requires that the first column contain values for the form variable,
   and the second have labels for the list. <james@daa.com.au> */
void cmdQselect(int argc, char *argv[]) {
  qHandle *qh;
  int defaultGiven = 0;
  char *default_v = NULL;

  checkNumArgs(2,"qselect");
  checkConn("qselect");

  qh = firstQuery;
  while (qh != NULL) {
    if (!strcmp(qh->name, argv[0])) break;
    qh = qh->next;
  }
  if (qh == NULL) {
    fprintf(yyout, "<p>qselect: query handle not found</p>\n");
    return;
  }
  if (mysql_num_fields(qh->res) < 2) {
    fprintf(yyout, "<p>qselect: not enough fields in table</p>\n");
    return;
  }
  if (argc >= 3) {
    defaultGiven = 1;
    default_v = argv[2];
  }
  mysql_data_seek(qh->res, 0);
  qh->row = mysql_fetch_row(qh->res);
  fprintf(yyout, "<select name=\"%s\">\n", argv[1]);
  while (qh->row) {
    if (defaultGiven && !strcmp(qh->row[0], default_v))
      fprintf(yyout, "<option value=\"%s\" selected>%s\n", qh->row[0],
	      qh->row[1]);
    else
      fprintf(yyout, "<option value=\"%s\">%s\n", qh->row[0], qh->row[1]);
    qh->row = mysql_fetch_row(qh->res);
  }
  fprintf(yyout, "</select>");
}

commands db_funcs = {
  {"connect",    cmdConnect},
  {"close",      cmdClose},
  {"database",   cmdDatabase},
  {"query",      cmdQuery},
  {"free",       cmdFree},
  {"print_rows", cmdPrintRows},
  {"fetch",      cmdFetch},
  {"seek",       cmdSeek},
  {"qtable",     cmdQtable},
  {"qlongform",  cmdQlongform},
  {"qselect",    cmdQselect},
  {NULL, NULL}
};

