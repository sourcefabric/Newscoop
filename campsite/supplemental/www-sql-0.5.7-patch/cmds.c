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
/*
 *   cmds.c
 * This file contains the code that does variable expansion, and picks which
 * commands to execute.
 */

#include <stdio.h>
#include <string.h>
#include "cmds.h"
#include "cgi.h"
#include "if.h"

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#ifndef HAVE_MEMCPY
#define memcpy(d,s,c) bcopy(s,d,c)
#endif

/* variable expansion code */
char *sqlEscape(char *str) {
  char *buf;
  int len, i, j;

  len = strlen(str);
  /* return buffer that is larger than needed -- it gets freed pretty soon
   * after being allocated */
  buf = xmalloc(len * 2 + 1); /* worst case */
  memset(buf, 0, len * 2 + 1);
  for (i=j=0; str[i] != '\0'; i++,j++)
    switch (str[i]) {
    case '\n':
      buf[j++] = '\\'; buf[j] = 'n';
      break;
    case '\t':
      buf[j++] = '\\'; buf[j] = 't';
      break;
    case '\r':
      buf[j++] = '\\'; buf[j] = 'r';
      break;
    case '\b':
      buf[j++] = '\\'; buf[j] = 'b';
      break;
    case '\'':
    case '\"':
    case '\\':
      buf[j++] = '\\';
    default:
      buf[j] = str[i];
    }
  return buf;
}

char *htmlEscape(char *str) {
  char *buf;
  int len, i, j;

  len = strlen(str);
  buf = xmalloc(6 * len + 1);
  memset(buf, 0, 6 * len + 1);
  for (i=j=0; str[i]; i++,j++)
    switch (str[i]) {
    case '&':
      strcpy(&buf[j], "&amp;");  j+= 4;
      break;
    case '>':
      strcpy(&buf[j], "&gt;");   j+= 3;
      break;
    case '<':
      strcpy(&buf[j], "&lt;");   j+= 3;
      break;
    case '"':
      strcpy(&buf[j], "&quot;"); j += 5;
      break;
    case '\r':
      strcpy(&buf[j], "<br>\r"); j += 4;
      break;
    case '\n':
      if (str[i-1] != '\r') {
	strcpy(&buf[j], "<br>\n"); j += 4;
	break;
      } /* else fall through to the next item ... */
    default:
      buf[j] = str[i];
    }
  return buf;
}

/* this function should be defined in the database specific functions file */
char *substField(char *imp, int *toklen);

char *substVars(char *s) {
  static char *buf = NULL; /* this buffer is grown as needed */
  static long buflen = 0;
  char *val, *val2 = NULL;
  int i, j, k, len, field_len, longest;
  VAR *var, *var2;
  enum {NORMAL, CGI_ESCAPE, SQL_ESCAPE, HTML_ESCAPE} esc_flag;

  if (buf == NULL) {
    buflen = 256;
    buf = xmalloc(buflen);
  }
  len = strlen(s);
  for (i=0,j=0; i<len; i++) {
    if (j >= buflen) {  /* time to grow the buffer */
      val = buf;
      buflen *= 2; /* double buffer size */
      buf = xmalloc(buflen);
      memcpy(buf, val, j);
      free(val);
    }
    esc_flag = NORMAL;
    switch (s[i]) {
      case '\\':
        switch(s[i+1]) {
          case 'n': buf[j] = '\n'; break;
          case 't': buf[j] = '\t'; break;
          case '$':
          case '@':
          case '#':
          case '?':
          case '~':
          case '\\': buf[j] = s[i+1]; break;
          default:  buf[j] = '\\'; i--; break;
        }
        i++; j++; break;

      case '#':
        esc_flag = CGI_ESCAPE;  /* fall through to the next */
      case '?':
        if (esc_flag == NORMAL) esc_flag = SQL_ESCAPE;
      case '~':
        if (esc_flag == NORMAL) esc_flag = HTML_ESCAPE;
      case '$':
      case '@':
        val = NULL;
	var2 = NULL;
	longest = 0;
        for (var = firstVar; var != NULL; var = var->next)
          if (!strncmp(s+i+1, var->name, field_len = strlen(var->name)) &&
	      field_len > longest) {
            var2 = var;
            longest = field_len;
          }
	if (var2 != NULL) {
	  i += longest;
	  val = var2->value;
	}
	if (val == NULL) {
	  val = substField(&s[i+1], &k);
	  if (val != NULL)
	    i+= k;
	}
        if (val == NULL) {
          buf[j] = s[i];
          j++;
        } else {
          switch (esc_flag) {
	  case CGI_ESCAPE:
	    val2 = escape_string(val);
	    break;
	  case SQL_ESCAPE:
	    val2 = sqlEscape(val);
	    break;
	  case HTML_ESCAPE:
	    val2 = htmlEscape(val);
	    break;
	  case NORMAL:
	    val2 = val;
          }
	  field_len = strlen(val2);
	  if (j + field_len >= buflen) {  /* time to grow the buffer */
	    val = buf;
	    buflen = (2*buflen > buflen + field_len)
	      ? 2 * buflen
	      : buflen + field_len + 1; /* double buffer size */
	    buf = xmalloc(buflen);
	    memcpy(buf, val, j);
	    free(val);
	  }
	  strcpy(&buf[j], val2);
	  j += field_len;
	  if (esc_flag != NORMAL) free(val2);
        }
        break;

      default:
        buf[j] = s[i];
        j++;
        break;
    }
  }
  buf[j] = '\0';
  return xstrdup(buf);
}


extern commands other_funcs, db_funcs, if_funcs;

#ifdef RECURSIVE
/* variable saying if any expansions have been done */
extern int dirty;
#endif

/* This function is the callback for the scanner */
void executeSql(int argc, char *argv[]) {
  int i;

#define pickCmd(cmds) \
          for (i = 0; cmds[i].name != NULL; i++) \
	    if (!strcasecmp(argv[0], cmds[i].name)) { \
	      cmds[i].func(argc-1, argv+1); \
	      return; \
	    }
  if (argc < 1) return;

#ifdef RECURSIVE
  /* code to allow recursive parsing of output */
  dirty = 1;
#endif

  pickCmd(if_funcs);
  if (checkIf) {
    pickCmd(other_funcs);
    pickCmd(db_funcs);
    /* else */
    fprintf(yyout, "<p><b>Unknown command:</b> ");
    for (i=0;i<argc;i++) fprintf(yyout, "%s ", argv[i]);
    fprintf(yyout, "</p>\n");
  }

#undef pickCmd
}
