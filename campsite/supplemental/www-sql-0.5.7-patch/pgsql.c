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
/*   pgsql.c
 * contains all the database specific functions for www-sql.  This one is for
 * PostgreSQL, and is just a modified version of mysql.c.  (Maybe I will do a
 * msql.c next)
 */

#include <libpq-fe.h>

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
  PGresult *res;
  int row;
  struct qh_struct *next;
} qHandle;

qHandle *firstQuery = NULL;
PGconn *conn = NULL;

char *connstr = NULL;

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
  qh->row = 0;
  return (PQntuples(qh->res) != 0);
}

int dbLoopNext(void *data) {
  qHandle *qh = (qHandle *)data;

  qh->row++;
  return (qh->row < PQntuples(qh->res));
}

/* This function is used as a hook to clean up any database related stuff
 * when www-sql closes */
void dbCleanUp() {
  if (conn != NULL)
    PQfinish(conn);
  conn = NULL;
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
	int field = PQnfields(qh->res), longest = 0;
	for (i = 0; i < PQnfields(qh->res); i++)
	  if (!strncmp(&inp[len], PQfname(qh->res, i),
		       len2 = strlen(PQfname(qh->res, i))) && len2 > longest) {
	    longest = len2;
	    field = i;
	  }
	len += longest;
	i = field;
      }
      if (i>=PQnfields(qh->res) || qh->row<0 || qh->row>=PQntuples(qh->res)) {
	*toklen = 0;
        return "Column or row out of range: ";
      }
      *toklen = len;
      if (PQgetisnull(qh->res, qh->row, i))
	return "";
      else
	return PQgetvalue(qh->res, qh->row, i);
    }
  *toklen = 0;
  return NULL;
}

void cmdConnect(int argc, char *argv[]) {
  int connstr_len = 0, h=0, u=0, p=0;
  char *host = NULL, *user = SQL_USER, *pass = SQL_PASS;

  /* for Postgress, connection to backend is not made till call to database */
  if (conn != NULL) {
    PQfinish(conn);
    conn = NULL;
  }
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

  if (host != NULL) connstr_len += 6 + strlen(host);
  if (user != NULL) connstr_len += 6 + strlen(user);
  if (pass != NULL) connstr_len += 10 + strlen(pass);
  connstr_len += 18; /* "authtype=passwd" */
  connstr = xmalloc(connstr_len);
  connstr[0] = '\0';
  if (host != NULL) {
    strcat(connstr, "host="); strcat(connstr, host); strcat(connstr, " ");
  }
  if (user != NULL) {
    strcat(connstr, "user="); strcat(connstr, user); strcat(connstr, " ");
  }
  if (pass != NULL) {
    strcat(connstr, "password="); strcat(connstr, pass); strcat(connstr, " ");
  }
  strcat(connstr, "authtype=passwd");
  if (h) free(host);
  if (u) free(user);
  if (p) free(pass);
}

void cmdClose(int argc, char *argv[]) {
  if (conn != NULL) {
    PQfinish(conn);
    conn = NULL;
  }
  if (connstr != NULL) free(connstr);
  connstr = NULL;
}

void cmdDatabase(int argc, char *argv[]) {
  char *tmp, *str;

  checkNumArgs(1,"database");
  if (connstr == NULL) {
    fprintf(yyout, "<p>Database: connect not called before database</p>\n");
    return;
  }
  /* this function now creates the connection to the backend */
  if (conn != NULL)
    PQfinish(conn);
  tmp = substVars(argv[0]);
  str = xmalloc(strlen(connstr) + 8 + strlen(tmp) + 1);
  str[0] = '\0';
  strcat(str, connstr);
  strcat(str, " dbname=");
  strcat(str, tmp);
  free(tmp);
  conn = PQconnectdb(str);
  free(str);

  if (PQstatus(conn) != CONNECTION_OK) {
    fprintf(yyout, "<p>Database: %s</p>\n", PQerrorMessage(conn));
    PQfinish(conn);
    conn = NULL;
  }
}

void cmdQuery(int argc, char *argv[]) {
  char *tmp, buf[21];
  qHandle *qh;
  PGresult *res;

  checkNumArgs(1,"query");
  checkConn("query");
  tmp = substVars(argv[0]);
  res = PQexec(conn, tmp);
  free(tmp);
  if (res == NULL) {
    fprintf(yyout, "<p>Query failed: %s</p>\n", PQerrorMessage(conn));
    setVar("AFFECTED_ROWS", "0");
    setVar("INSERT_ID", "-1");
    return;
  }
  switch (PQresultStatus(res)) {
    case PGRES_BAD_RESPONSE:
    case PGRES_NONFATAL_ERROR:
    case PGRES_FATAL_ERROR:
      fprintf(yyout, "<p>Query failed: %s</p>\n", PQerrorMessage(conn));
      PQclear(res);
      return;
    case PGRES_COPY_IN:
    case PGRES_COPY_OUT:
      fprintf(yyout, "<p>Qyery: copy in and copy out not supported</p>\n");
      PQclear(res);
      return;
    case PGRES_EMPTY_QUERY:
    case PGRES_TUPLES_OK:
      if (argc < 2) {
        PQclear(res);
        fprintf(yyout, "<p>Query: requires a query handle name</p>\n");
        return;
      }
      qh = xmalloc(sizeof(qHandle));
      qh->name = xstrdup(argv[1]);
      qh->res = res;
      qh->row = 0;
      qh->next = firstQuery;
      firstQuery = qh;
      setVar("AFFECTED_ROWS", "0");
      setVar("INSERT_ID",     "-1");
      sprintf(buf, "%d", PQnfields(qh->res));
      setVar("NUM_FIELDS", buf);
      sprintf(buf, "%d", PQntuples(qh->res));
      setVar("NUM_ROWS", buf);
      break;
    case PGRES_COMMAND_OK:
      setVar("AFFECTED_ROWS", PQcmdTuples(res));
      setVar("INSERT_ID",     PQoidStatus(res));
      PQclear(res);
      break;
  }
}

void cmdFree(int argc, char *argv[]) {
  qHandle *qh, *prev, phony = {"", NULL, 0, NULL};

  phony.next = firstQuery;
  checkNumArgs(1,"free");

  prev = &phony;
  qh = firstQuery;
  while (qh != NULL) {
    if (!strcasecmp(argv[0], qh->name)) {
      PQclear(qh->res);
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
    fprintf(yyout, "<p>print_rows: query handle not found</p>\n");
    return;
  }

  while (qh->row < PQntuples(qh->res)) {
    tmp = substVars(argv[1]);
    fprintf(yyout, "%s", tmp);
    free(tmp);
    qh->row++;
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
    fprintf(yyout, "<p>fetch: query handle not found</p>\n");
    return;
  }

  if (qh->row < PQntuples(qh->res))
    qh->row++;
}

void cmdSeek(int argc, char *argv[]) {
  qHandle *qh;
  int row;

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

  row = atoi(argv[1]);
  if (row >= 0 && row < PQntuples(qh->res))
    qh->row++;
  else
    fprintf(yyout, "<p>Seek: column out of range</p>\n");
}
/* this procedure was completely rewritten for the postgresql port */
void cmdQtable(int argc, char *argv[]) {
  qHandle *qh;
  PQprintOpt po;

  po.header    = 1;
  po.align     = 0;
  po.standard  = 0;
  po.html3     = 1;
  po.expanded  = 0;
  po.pager     = 0;
  po.fieldSep  = "|";
  po.tableOpt  = "";
  po.caption   = "";
  po.fieldName = NULL;
  checkNumArgs(1,"qtable");
  checkConn("qtable");

  qh = firstQuery;
  while (qh != NULL) {
    if (!strcmp(qh->name, argv[0])) break;
    qh = qh->next;
  }
  if (qh == NULL) {
    fprintf(yyout, "<p>qtable: query hadle not found</p>\n");
    return;
  }

  if (argc > 1 && !strcasecmp(argv[1], "borders"))
    po.tableOpt = "border";
  PQprint(yyout, qh->res, &po);
}

/* This command was contributed by Martin Maisey <M.J.Maisey@webname.com> */
void cmdQlongform(int argc, char *argv[]) {
  qHandle *qh;
  unsigned int i, j;

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

  /* Output rows */
  for (j = 0; j < PQntuples(qh->res); j++) {
    for (i = 0; i < PQnfields(qh->res); i++)
      fprintf(yyout, "<b>%s:</b> %s<br>\n", PQfname(qh->res, i),
        PQgetvalue(qh->res, j, i));
    fprintf(yyout, "<p>\n");
  }
}

/* print a <select> style list box from a query.  For use in forms.
   Requires that the first column contain values for the form variable,
   and the second have labels for the list. <james@daa.com.au> */
void cmdQselect(int argc, char *argv[]) {
  qHandle *qh;
  int j;
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
  if (argc >= 3) {
    defaultGiven = 1;
    default_v = argv[2];
  }
  if (PQnfields(qh->res) < 2) {
    fprintf(yyout, "<p>qselect: not enough fields in table</p>\n");
    return;
  }
  fprintf(yyout, "<select name=\"%s\">\n", argv[1]);
  for (j = 0; j < PQntuples(qh->res); j++) {
    if (defaultGiven && !strcmp(PQgetvalue(qh->res, j, 0), default_v))
      fprintf(yyout, "<option value=\"%s\" selected>%s\n",
	      PQgetvalue(qh->res, j, 0), PQgetvalue(qh->res, j, 1));
    else
      fprintf(yyout, "<option value=\"%s\">%s\n", PQgetvalue(qh->res, j, 0),
	      PQgetvalue(qh->res, j, 1));
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

