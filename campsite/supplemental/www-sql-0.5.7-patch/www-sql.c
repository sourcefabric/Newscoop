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
/*   www-sql.c
 * This file contains the main program.  Thats all.
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>


#ifdef HAVE_CONFIG_H
#include "config.h"
#endif
char *xstrdup(const char *str);

#include "cgi.h"
#include "if.h"

#define WWWSQL_VERSION "WWW-Sql Version " VERSION

/* clean up database stuff */
void dbCleanUp();

#ifdef RECURSIVE
int dirty = 0;
#endif

/* This is the function produced by the flex scanner 'scanner.l' */
void parse(FILE *, FILE *);

int main(int argc, char *argv[]) {
  FILE *in = NULL;
  char *tmp;
#ifdef RECURSIVE
  FILE *out = NULL;
  char buffer[1025];
#endif

  printf("Content-type: text/html\nGenerator: %s\n\n", WWWSQL_VERSION);

  /* Special code to check if www-sql was called by apache's action handler
   * code.  The REDIRECT_STATUS and REDIRECT_URL environment variables are
   * set when action handlers are called.  This code is only activated if
   * configure is passed the argument --enable-apache-action-check.
   * The reason you might want to do this is that by not using www-sql as
   * an action handler, you open up secured areas to those who know how to
   * exploit a certain bug in previous versions of www-sql.
   */
    tmp = getenv("REDIRECT_STATUS");
    if (!tmp) {
    printf("<html><head><title>WWW-Sql</title></head>\n");
    printf("<body>This version of www-sql must be called as an action\n");
    printf("handler.</body></html>\n");
    exit(0);
  }

  initIfStack();
  initCGI();

  if ((tmp = getenv("DOCUMENT_ROOT")) == NULL) {
    printf("<html><head><title>WWW-Sql</title></head>\n");
    printf("<body> Can not get document root path.</body></html>\n");
    exit(0);
  }
  if ((tmp = getenv("PATH_TRANSLATED")) == NULL) {
    printf("<html><head><title>WWW-Sql</title></head>\n");
    printf("<body> Can not translate path.</body></html>\n");
    exit(0);
  }

  if (!strcmp(tmp, "/dev/stdin") || !strcmp(tmp, "-"))
    in = stdin;
  else if ((in = fopen(tmp, "r")) == NULL) {
    tmp = getenv("PATH_INFO");
    printf("<html><head><title>WWW-Sql</title></head>\n");
    if (tmp == NULL)
      printf("<body> No path information given.</body></html>\n");
    else
      printf("<body> Source file %s not found.</body></html>\n", tmp);
    exit(0);
  }

#ifdef RECURSIVE
  /* code to recursively parse www-sql headers
   * This bit of code was by Simon Cocking <simon@ibs.com.au>
   */
  do {
    if ((out = tmpfile()) == NULL) {
      printf("<html><head><title>WWW-Sql</title></head>\n");
      printf("<body> Can not create temporary file.</body></html>\n");
      dbCleanUp();
      exit(0);
    }
    dirty = 0;
    parse(in, out);
    fclose(in);
    rewind(out);
    in = out;
  } while (dirty);
  rewind(out);
  while (!feof(out)) {
    if (!fgets(buffer, 1024, out))
      break;
    fputs(buffer, stdout);
  }
  fclose(out);
#else
  parse(in, stdout);
  fclose(in);
#endif
  /* just in case ... */
  dbCleanUp();
  return 0;
}

