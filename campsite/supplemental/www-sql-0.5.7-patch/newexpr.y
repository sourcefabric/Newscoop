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
/*
    newexpr.y - the new expression parser.  Unlike the old one (the expr
      shell command), this one can handle floating point numbers.
*/
#include <math.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <regex.h>

extern FILE *yyout;
void *xmalloc(int size);
%}

%union {
  double d;
  char *s;
}

%{
static YYSTYPE retval;
static enum {NUM_TYPE, STR_TYPE}  rettype;
%}

%token <d> NUM
%token <s> STR_TOK
%token <s> '-' '+' '*' '/' '%' OR AND '=' NE '>' '<' GE LE '!' '^' '(' ')' ':'
%type <d> exp
%type <s> STR
%left OR
%left AND
%left '!'
%left '=' '>' '<' GE LE NE
%left '-' '+'
%left '*' '/' '%'
%left NEG
%right '^'
':'

%%
input: exp { retval.d = $1; rettype = NUM_TYPE; }
     | STR { retval.s = $1; rettype = STR_TYPE; }
;

exp:   NUM               { $$ = $1; }
     | exp OR exp        { $$ = ($1 != 0.0) || ($3 != 0.0); }
     | exp AND exp       { $$ = ($1 != 0.0) && ($3 != 0.0); }
     | exp '=' exp       { $$ = $1 == $3; }
     | STR '=' STR       { $$ = strcmp($1, $3) == 0; }
     | exp '=' STR       { $$ = 0; }
     | STR '=' exp       { $$ = 0; }
     | exp NE exp        { $$ = $1 != $3; }
     | STR NE STR        { $$ = strcmp($1, $3) != 0; }
     | exp NE STR        { $$ = 1; }
     | STR NE exp        { $$ = 1; }
     | exp '>' exp       { $$ = $1 > $3; }
     | STR '>' STR       { $$ = strcmp($1, $3) > 0; }
     | exp '<' exp       { $$ = $1 < $3; }
     | STR '<' STR       { $$ = strcmp($1, $3) < 0; }
     | exp GE exp        { $$ = $1 >= $3; }
     | STR GE STR        { $$ = strcmp($1, $3) >= 0; }
     | exp LE exp        { $$ = $1 <= $3; }
     | STR LE STR        { $$ = strcmp($1, $3) <= 0; }
     | '!' exp           { $$ = $2 == 0.0; }
     | exp '+' exp       { $$ = $1 + $3; }
     | exp '-' exp       { $$ = $1 - $3; }
     | exp '*' exp       { $$ = $1 * $3; }
     | exp '/' exp       { $$ = $1 / $3; }
     | exp '%' exp       { $$ = fmod($1, $3); }
     | '-' exp %prec NEG { $$ = -$2; }
     | exp '^' exp       { $$ = pow($1, $3); }
     | '(' exp ')'       { $$ = $2; }
     | STR ':' STR       {
        char *errmsg;
        struct re_pattern_buffer re_buffer;
        struct re_registers re_regs;
        int len;

        len = strlen($3);
        re_buffer.allocated = 2 * len;
        re_buffer.buffer = (unsigned char *)xmalloc(re_buffer.allocated);
        re_buffer.translate = 0;
        errmsg = (char *)re_compile_pattern($3, len, &re_buffer);
        if (errmsg) {
          yyerror(errmsg);
          YYERROR;
        }
        len = re_match(&re_buffer, $1, strlen($1), 0, &re_regs);
        if (len >= 0) {
          $$ = len;
        } else
          $$ = 0.0;
        free(re_buffer.buffer);
     }
;

STR : '-' | '+' | '*' | '/' | '%' | OR | AND | '=' | NE | '>' | '<' |
      GE | LE | '!' | '^' | '(' | ')' | ':' | STR_TOK { $$ = $1; }
;
%%

static int ac, curarg;
static char **av;

static int yylex() {
  char *endptr, *str;
  int len;
  double val;

  if (curarg == ac)
    return 0;
  str = av[curarg];
  curarg++;
  val = strtod(str, &endptr);
  if (str != endptr && *endptr == '\0') {
    yylval.d = val;
    return NUM;
  }
  len = strlen(str);
  yylval.s = str;
  if (len == 1 && strchr("=<>!%+-*/^:()", str[0]))
    return str[0];
  if (!strcmp(str, "=="))
    return '=';
  if ((len == 1 && str[0] == '&') || !strcmp(str, "&&"))
    return AND;
  if ((len == 1 && str[0] == '|') || !strcmp(str, "||"))
    return OR;
  if (!strcmp(str, ">="))
    return GE;
  if (!strcmp(str, "<="))
    return LE;
  if (!strcmp(str, "!=") || !strcmp(str, "<>"))
    return NE;
  return STR_TOK;
}

void yyerror(char *str) { fprintf(yyout, "<b>expr:</b> %s<br>\n", str); }
int yyparse();

int evalExpr(int argc, char **argv) {
  ac = argc;
  av = argv;
  curarg = 0;
  if (yyparse()) return 0;
  if (rettype == NUM_TYPE)
    return retval.d != 0.0;
  else
    return retval.s[0] != '\0';
}

char *evalToString(int argc, char **argv) {
  static char *buf = NULL;
  if (buf == NULL) buf = xmalloc(100);
  ac = argc;
  av = argv;
  curarg = 0;
  if (yyparse()) return "";
  if (rettype == NUM_TYPE) {
    sprintf(buf, "%g", retval.d);
    return buf;
  }
    return retval.s;
}

void evalPrint(int argc, char **argv) {
  fprintf(yyout, "%s", evalToString(argc, argv));
  return;
}

