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

/* cgi.c - makes cgi information available to a program
 *
 * This file was designed for www-sql, but will probably work with other
 * programs.  Just include the file cgi.h in your program, and call
 * initCGI().
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif
char *xstrdup(const char *str);
void *xmalloc(int size);

typedef struct variable {
  char *name, *value;
  struct variable *next;
} VAR;

VAR *firstVar = NULL;

void setVar(const char *name, const char *value) {
  VAR *var;

  for (var = firstVar; var != NULL; var = var->next)
    if (!strcmp(name, var->name)) break;
  if (var == NULL) {
    var = xmalloc(sizeof(VAR));
    var->name = xstrdup(name);
    var->value = xstrdup(value);
    var->next = firstVar;
    firstVar = var;
  } else {
    free(var->value);
    var->value = xstrdup(value);
  }
}

void setDefault(const char *name, const char *value) {
  VAR *var;

  for (var = firstVar; var != NULL; var = var->next)
    if (!strcmp(name, var->name)) break;
  if (var == NULL) {
    var = xmalloc(sizeof(VAR));
    var->name = xstrdup(name);
    var->value = xstrdup(value);
    var->next = firstVar;
    firstVar = var;
  }
}

char *valueOf(const char *name) {
  VAR *var;

  for (var = firstVar; var != NULL; var = var->next)
    if (!strcmp(name, var->name)) break;
  if (var == NULL)
    return NULL;
  else
    return var->value;
}

char *sepword(char *line, char sep) {
  int x, y;
  char *ret;

  for (x = 0; line[x] && line[x] != sep; x++) ;
  ret = xmalloc(x+1);
  ret[x] = '\0';
  for (y = 0; y < x; y++)
    ret[y] = line[y];
  return ret;
}

char x2c(char *what) {
  register char digit;

  digit = (what[0] >= 'A' ? ((what[0] & 0xdf) - 'A')+10 : (what[0] - '0'));
  digit *= 16;
  digit += (what[1] >= 'A' ? ((what[1] & 0xdf) - 'A')+10 : (what[1] - '0'));
  return(digit);
}

void unescape_string(char *str) {
  register int x,y;

  /* change '+'s to ' 's */
  for (x = 0; str[x]; x++) if (str[x] == '+') str[x] = ' ';

  /* change '%XX' to its equivalent character */
  for(x=0,y=0;str[y];++x,++y)
    if((str[x] = str[y]) == '%') {
      str[x] = x2c(&str[y+1]);
      y+=2;
    }
  str[x] = '\0';
}

void initCGI() {
  int len, i;
  char *buf = NULL, *bufptr, *val;
  VAR *var;

  /* Give the user access to some CGI variables */
#define setenvvar(x) if ((val = getenv(x)) != NULL) setVar((x), val)
  setenvvar("DOCUMENT_ROOT");
  setenvvar("GATEWAY_INTERFACE");
  setenvvar("HOSTTYPE");
  setenvvar("HTTP_HOST");
  setenvvar("HTTP_REFERER");
  setenvvar("HTTP_USER_AGENT");
  setenvvar("HTTP_COOKIE");
  setenvvar("OSTYPE");
  setenvvar("PATH_INFO");
  setenvvar("PATH_TRANSLATED");
  setenvvar("QUERY_STRING");
  setenvvar("REMOTE_ADDR");
  setenvvar("REMOTE_HOST");
  setenvvar("REMOTE_USER");
  setenvvar("REQUEST_URI");
  setenvvar("SCRIPT_NAME");
  setenvvar("SCRIPT_FILENAME");
  setenvvar("SERVER_ADMIN");
  setenvvar("SERVER_NAME");
  setenvvar("SERVER_PORT");
  setenvvar("SERVER_PROTOCOL");
  setenvvar("SERVER_SOFTWARE");
#undef setenvvar
#ifdef VERSION
  setVar("WWW_SQL_VERSION", VERSION);
#endif

  /* This cookie code contributed by Lars Bensmann <lars@skynet.e.ruhr.de>
   * Thanks! */
  if ((buf = getenv("HTTP_COOKIE")) != NULL) {
    bufptr = buf;
    while (*bufptr) {
      var = xmalloc(sizeof(VAR));
      var->next = firstVar;
      firstVar = var;

      var->name = sepword(bufptr, '=');
      bufptr += strlen(var->name);
      if (*bufptr) bufptr++;

      var->value = sepword(bufptr, ';');
      bufptr += strlen(var->value);
      /* move forward past the ';' and ' ' */
      if (*bufptr) bufptr++;
      if (*bufptr && *bufptr == ' ') bufptr++;
      unescape_string(var->value);
    }
    var = NULL;
    buf = NULL;
  }

  if ((val = getenv("REQUEST_METHOD")) != NULL)
    if (!strcmp(val, "GET")) {
      buf = getenv("QUERY_STRING");
      if (buf) buf = xstrdup(buf);
    } else if (!strcmp(val, "POST")) { /* Post request */
      len = atoi(getenv("CONTENT_LENGTH"));
      buf = xmalloc(len + 1);
      for (i=0; i < len; i++) buf[i] = getchar();
      buf[len] = '\0';
    }

  if (buf != NULL) {
    bufptr = buf;
    while (*bufptr) {
      var = xmalloc(sizeof(VAR));
      var->next = firstVar;
      firstVar = var;    

      var->name = sepword(bufptr, '=');
      bufptr += strlen(var->name);
      if (*bufptr) bufptr++;

      var->value = sepword(bufptr, '&');
      bufptr += strlen(var->value);
      if (*bufptr) bufptr++;
      unescape_string(var->value);
    }
    bufptr = NULL;
    var = NULL;
    free(buf);
  }
}

/* characters that don't require encoding */
static char *goodchars = "*-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_"
                         "abcdefghijklmnopqrstuvwxyz";

char *escape_string(char *str) {
  int len = strlen(str);
  char *buf = xmalloc(3 * len + 1);  /* worst case */
  int i, j;

  memset(buf, 0, 3 * len + 1);
  for (i=j=0; str[i]; i++,j++)
    if (str[i] == ' ')
      buf[j] = '+';
    else if (strchr(goodchars, str[i]))
      buf[j] = str[i];
    else {
      sprintf(&(buf[j]), "%%%02X", (unsigned char) str[i]);
      j += 2;
    }
  return buf;
}

FILE *cgi_fopen(const char *name) {
  extern FILE *yyout;
  FILE *f;
  char *fname, *pathtrans, *slash;
  int pathlen;

#ifndef ENABLE_UNSAFE
  if (strchr(name, '/')) {
    fprintf(yyout, "<p><b>include</b> - not allowed to open file</p>\n");
    return NULL;
  }
#endif

  /* execution doesn't get this far if PATH_TRANSLATED isn't set */
  slash = NULL;
  if (name[0] == '/') {
    pathtrans = getenv("DOCUMENT_ROOT");
    pathlen = strlen(pathtrans);
  } else {
    pathtrans = getenv("PATH_TRANSLATED");
    slash = strrchr(pathtrans, '/');
    pathlen = slash == NULL ? strlen(pathtrans) : slash - pathtrans;
  }
  fname = (char*) malloc(pathlen + strlen(name) + 2);
  if (fname == NULL)
    return NULL;
  strncpy(fname, pathtrans, pathlen);
  if (name[0] == '/')
    sprintf(fname+pathlen, "%s", name);
  else
    sprintf(fname+pathlen, "/%s", name);
  f = fopen(fname, "r");
  if (f == NULL)
    fprintf(yyout, "<p><b>include</b> - can't open file %s</p>\n", fname);
  free(fname);
  return f;
}
