%{
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
/*   scanner.l
 * This file contains a lex parser to extract www-sql commands from an
 * HTML document.
 */

#include <stdlib.h>
#include <string.h>

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif
void *xmalloc(int size);

#include "if.h"

FILE *cgi_fopen(const char *name);
void executeSql(int, char **);

#define MAXARGS 60
static int margc = 0;
static char *margv[MAXARGS];

#define MAX_INCLUDE_DEPTH 10
static YY_BUFFER_STATE include_stack[MAX_INCLUDE_DEPTH];
static int include_stack_ptr = 0;
%}

%{
/* make a case insensitive scanner with a minimum of extra functions */
%}
%option case-insensitive
%option input
%option nounput
%option yy_scan_buffer
%option yy_scan_bytes
%option yy_scan_string

%x SQL
%x EXPR
%x SETEXPR

QUOTED		\"(\\\"|[^"])*\"
UNQUOTED	[^> \t\n]*
WS		[ \t\n]*
NUM		[0-9.]+
PUNCTUATION	"<"|"<"=|">"=|==|=|"!"=|"|"|"&"|")"|"("|"%"|"+"|"-"|"*"|"/"|":"|"!"
VAR		[$@#?~][a-ZA-Z0-9._]+

%%
 /*[^<]*			ECHO;*/
"<""!"{WS}sql{WS}		BEGIN(SQL);

<EXPR>{NUM}|{PUNCTUATION}|{VAR}	{
  margv[margc] = xmalloc(strlen(yytext) + 1);
  strcpy(margv[margc], yytext);
  margc++;
}

<EXPR>"|""|"|"&""&"	{
  margv[margc] = xmalloc(2);
  *margv[margc] = *yytext;
  *(margv[margc]+1) = '\0';
  margc++;
}

<EXPR>{QUOTED}		{
  int i, j, k;

  k = strlen(yytext);
  margv[margc] = xmalloc(k - 1);
  *(margv[margc]+k-2) = '\0';
  strncpy(margv[margc], yytext+1, k-2);
  for (i=0, j=0; i <= k; i++) {
    if (*(margv[margc]+i) == '"') j++;
    *(margv[margc]+i-j) = *(margv[margc]+i);
  }
  margc++;
}

<SQL,EXPR>">"		{
  int i;
  margv[margc] = NULL;      /* mark end of arguments */
  if (checkIf && !strcasecmp(margv[0], "include")) { /* an include statement */
    /* based on code submitted by Malte John <malte@ddd.de>
     * (modified to fit into standard flex states, and check the IF state */
    if (include_stack_ptr >= MAX_INCLUDE_DEPTH)
      fprintf(yyout, "<p><b>include</b> - nesting too deep</p>\n");
    else {
      FILE *f;
      f = cgi_fopen(margv[1]);
      if (f) {
        include_stack[include_stack_ptr++] = YY_CURRENT_BUFFER;
        yy_switch_to_buffer(yy_create_buffer(f, YY_BUF_SIZE));
      }
    }
  } else
    executeSql(margc, margv);
  for (i = 0; i < margc; i++) free(margv[i]);
  margc=0;
  BEGIN(INITIAL);
}

"<""!"{WS}sql{WS}if	{
  margv[margc] = xmalloc(3);
  strcpy(margv[margc], "if");
  margc++;
  BEGIN(EXPR);
}

"<""!"{WS}sql{WS}elsif	{
  margv[margc] = xmalloc(6);
  strcpy(margv[margc], "elsif");
  margc++;
  BEGIN(EXPR);
}

"<""!"{WS}sql{WS}eval	{
  margv[margc] = xmalloc(5);
  strcpy(margv[margc], "eval");
  margc++;
  BEGIN(EXPR);
}

"<""!"{WS}sql{WS}setexpr	{
  margv[margc] = xmalloc(8);
  strcpy(margv[margc], "setexpr");
  margc++;
  BEGIN(SETEXPR);
}

<SETEXPR>{UNQUOTED}	{
  margv[margc] = xmalloc(strlen(yytext) + 1);
  strcpy(margv[margc], yytext);
  margc++;
  BEGIN(EXPR);
}

<SQL>{QUOTED}		{
  int i,j,k;
  k = strlen(yytext);
  margv[margc] = xmalloc(k - 1);
  *(margv[margc]+k-2) = '\0';
  strncpy(margv[margc], yytext+1, k-2);
  for (i=0, j=0; i <= k; i++) {
    if (*(margv[margc]+i) == '"') j++;
    *(margv[margc]+i-j) = *(margv[margc]+i);
  }
  margc++;
}
<SQL,EXPR>{WS}		/* nothing for white space */
<SQL>{UNQUOTED} 	{
  margv[margc] = xmalloc(strlen(yytext) + 1);
  strcpy(margv[margc], yytext);
  margc++;
}

<<EOF>> {
  /* this code is a modified version of code submitted
   * by Malte John <malte@ddd.de> */
  if (--include_stack_ptr < 0)
    yyterminate();
  else {
    yy_delete_buffer(YY_CURRENT_BUFFER);
    yy_switch_to_buffer(include_stack[include_stack_ptr]);
    BEGIN(INITIAL);
  }
}

.			if (checkIf) ECHO;

%%

/* only scan one file */
int yywrap() { return 1; }

void parse(FILE *is, FILE *os) {
  yyin = is;
  yyout = os;
  yylex();
}

