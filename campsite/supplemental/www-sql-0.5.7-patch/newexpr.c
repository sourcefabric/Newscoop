#ifndef lint
static char yysccsid[] = "@(#)yaccpar	1.9 (Berkeley) 02/21/93";
#endif
#define YYBYACC 1
#define YYMAJOR 1
#define YYMINOR 9
#define yyclearin (yychar=(-1))
#define yyerrok (yyerrflag=0)
#define YYRECOVERING (yyerrflag!=0)
#define YYPREFIX "yy"
#line 2 "newexpr.y"
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
#line 34 "newexpr.y"
typedef union {
  double d;
  char *s;
} YYSTYPE;
#line 40 "newexpr.y"
static YYSTYPE retval;
static enum {NUM_TYPE, STR_TYPE}  rettype;
#line 51 "y.tab.c"
#define NUM 257
#define STR_TOK 258
#define OR 259
#define AND 260
#define NE 261
#define GE 262
#define LE 263
#define NEG 264
#define YYERRCODE 256
short yylhs[] = {                                        -1,
    0,    0,    1,    1,    1,    1,    1,    1,    1,    1,
    1,    1,    1,    1,    1,    1,    1,    1,    1,    1,
    1,    1,    1,    1,    1,    1,    1,    1,    1,    1,
    1,    2,    2,    2,    2,    2,    2,    2,    2,    2,
    2,    2,    2,    2,    2,    2,    2,    2,    2,    2,
};
short yylen[] = {                                         2,
    1,    1,    1,    3,    3,    3,    3,    3,    3,    3,
    3,    3,    3,    3,    3,    3,    3,    3,    3,    3,
    3,    2,    3,    3,    3,    3,    3,    2,    3,    3,
    3,    1,    1,    1,    1,    1,    1,    1,    1,    1,
    1,    1,    1,    1,    1,    1,    1,    1,    1,    1,
};
short yydefred[] = {                                      0,
    3,   50,    0,   33,   34,   35,   36,   37,   38,   39,
   40,   41,   42,   43,   44,    0,   46,    0,   48,   49,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,   30,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,   32,
   45,   47,   15,   17,   19,   21,   31,
};
short yydgoto[] = {                                      21,
   22,   25,
};
short yysindex[] = {                                    478,
    0,    0,  599,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,  534,    0,  478,    0,    0,
    0,  541,   -4,  -94,   -4,  569,  420,  478,  478,  478,
  478,  478,  478,  478,  478,  478,  478,  478,  478,  478,
  478,  478,  478,  504,  504,  504,  504,  504,    0,  -11,
  -11,  -94,  -94,  -94,  562,  569,  -30,  -56,  -30,  -56,
  -30,  -30,  -30,  -30,  -94,  -30,  -56,  -30,  -56,    0,
    0,    0,    0,    0,    0,    0,    0,
};
short yyrindex[] = {                                      0,
    0,    0,  177,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,   14,    0,    3,    0,    0,
    0,    4,    5,   66,    0,   19,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,  151,
  156,   93,  100,  122,   24,   35,  144,    1,  168,    8,
  186,  193,  215,  224,  129,  241,   30,  272,   37,    0,
    0,    0,    0,    0,    0,    0,    0,
};
short yygindex[] = {                                      0,
  668,  322,
};
#define YYTABLESIZE 857
short yytable[] = {                                      41,
    8,   48,   47,    1,    2,    0,   32,   12,    0,    0,
    0,   30,   29,   45,   28,    0,   31,    0,   22,    0,
    0,    0,    0,    4,    0,   32,    0,    0,    0,    7,
   30,    0,    0,    0,    5,   31,   11,    8,    0,    0,
    0,    8,    8,    8,   12,    8,    0,    8,   12,   12,
   12,    0,   12,   48,   12,   45,   42,   44,    0,   22,
    8,    8,    8,   41,    4,   28,    7,   12,   12,   12,
    7,    7,    7,   11,    7,    5,    7,   11,   11,   11,
    0,   11,   41,   11,    0,    0,    0,    0,    0,    7,
    7,    7,   25,    0,    8,    0,   11,   11,   11,   26,
    0,   12,   28,    0,    0,    0,   28,   28,   28,    0,
   28,    0,   28,    0,    0,    0,    0,    0,    0,    0,
    0,   27,    0,    7,    0,   28,   28,   28,   29,   25,
   11,    0,    0,   25,   25,   25,   26,   25,    0,   25,
   26,   26,   26,    6,   26,    0,   26,    0,    0,    0,
   24,    0,   25,   25,   25,   23,    0,    0,   27,   26,
   26,   26,   27,   27,   27,   29,   27,   10,   27,   29,
   29,   29,    0,   29,    0,   29,   32,    0,    0,    0,
    0,   27,   27,   27,    6,   14,    0,    0,   29,   29,
   29,   24,   16,   24,    0,   24,   23,    0,   23,    0,
   23,    0,    0,    6,    6,    6,    0,    0,   10,    0,
   24,   24,   24,    0,   18,   23,   23,   23,    0,   32,
    0,   32,    0,   20,    0,    0,   14,   10,   10,   10,
    0,    0,    0,   16,    0,    0,   32,   32,   32,    0,
    9,    0,    0,    0,    0,   14,   14,   14,    0,    0,
    0,    0,   16,   16,   16,   18,   43,   46,   47,    8,
    8,    8,    8,    8,   20,    0,   12,   12,   12,   12,
   12,   13,   45,   45,   18,   18,   18,   22,   22,    0,
    0,    9,    4,   20,   20,   20,    0,    0,    7,    7,
    7,    7,    7,    5,    5,   11,   11,   11,   11,   11,
    9,    9,    9,    0,    0,    0,    0,    0,    0,    0,
    0,    0,   13,    0,    0,    0,    0,    0,    0,    0,
    0,   23,    0,    0,   28,   28,   28,   28,   28,    0,
    0,   13,   13,   13,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,   25,   25,   25,   25,   25,   58,   60,   26,   26,
   26,   26,   26,   67,   69,   73,   74,   75,   76,   77,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
   27,   27,   27,   27,   27,    0,    0,   29,   29,   29,
   29,   29,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    6,    6,    6,    6,    6,    0,    0,   24,
   24,   24,   24,   24,   23,   23,   23,   23,   23,    0,
    0,    0,    0,    0,    0,    0,   10,   10,   10,   10,
   10,    0,    0,    0,    0,   32,   32,   32,   32,   32,
    0,    0,    0,    0,   14,   14,   14,   14,   14,    0,
    0,   16,   16,   16,   16,   16,   32,    0,    0,    0,
   49,   30,   29,    0,   28,    0,   31,    0,    0,    0,
    0,    0,    0,   18,   18,   18,   18,   18,    0,   38,
   35,   37,   20,   20,   20,   20,   20,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    9,
    9,    9,    9,    9,    0,    0,    0,    0,    0,    0,
   16,    0,    0,   41,    7,    0,    0,   18,   19,    5,
    4,    0,    3,    0,    6,    0,    0,    0,    0,    0,
   13,   13,   13,   13,   13,   20,   71,   13,   10,   12,
    7,    0,    0,   72,   19,    5,    4,    0,   70,    0,
    6,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,   20,    0,   13,   10,   12,   16,    0,    0,    0,
    7,   17,    0,   18,   19,    5,    4,   32,    3,    0,
    6,    0,   30,   29,    0,   28,    0,   31,    0,    0,
    0,   20,    0,   13,   10,   12,    0,   17,   32,    0,
   38,   35,   37,   30,   29,   32,   28,    0,   31,    0,
   30,   29,    0,   28,    0,   31,    0,    0,    0,    0,
    0,   38,   35,   37,    0,    0,    0,   17,   38,   35,
   37,   16,    0,    0,   41,    7,    0,    0,   18,   19,
    5,    0,    0,    0,    0,    6,    0,    0,    0,    0,
    0,    0,    0,    0,    0,   41,   20,    0,    0,    0,
    0,    0,   41,    0,    0,    0,    0,    0,    0,    0,
   24,    0,    0,    0,    0,    0,    0,    0,   33,   34,
   36,   39,   40,   26,    0,   27,    0,    0,    0,    0,
    0,    0,   17,    0,    0,   50,   51,   52,   53,   54,
   55,   56,   57,   59,   61,   62,   63,   64,   65,   66,
   68,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    1,    2,    8,    9,   11,   14,
   15,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    2,    8,    9,   11,   14,   15,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    1,    2,    0,    0,   11,   14,   15,    0,    0,   33,
   34,   36,   39,   40,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,   34,   36,   39,   40,    0,    0,    0,    0,   36,
   39,   40,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    0,    0,    0,    0,    0,
    0,    0,    0,    0,    0,    1,    2,
};
short yycheck[] = {                                      94,
    0,   58,    0,    0,    0,   -1,   37,    0,   -1,   -1,
   -1,   42,   43,    0,   45,   -1,   47,   -1,    0,   -1,
   -1,   -1,   -1,    0,   -1,   37,   -1,   -1,   -1,    0,
   42,   -1,   -1,   -1,    0,   47,    0,   37,   -1,   -1,
   -1,   41,   42,   43,   37,   45,   -1,   47,   41,   42,
   43,   -1,   45,   58,   47,   60,   61,   62,   -1,   41,
   60,   61,   62,   94,   41,    0,   37,   60,   61,   62,
   41,   42,   43,   37,   45,   41,   47,   41,   42,   43,
   -1,   45,   94,   47,   -1,   -1,   -1,   -1,   -1,   60,
   61,   62,    0,   -1,   94,   -1,   60,   61,   62,    0,
   -1,   94,   37,   -1,   -1,   -1,   41,   42,   43,   -1,
   45,   -1,   47,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,    0,   -1,   94,   -1,   60,   61,   62,    0,   37,
   94,   -1,   -1,   41,   42,   43,   37,   45,   -1,   47,
   41,   42,   43,    0,   45,   -1,   47,   -1,   -1,   -1,
    0,   -1,   60,   61,   62,    0,   -1,   -1,   37,   60,
   61,   62,   41,   42,   43,   37,   45,    0,   47,   41,
   42,   43,   -1,   45,   -1,   47,    0,   -1,   -1,   -1,
   -1,   60,   61,   62,   41,    0,   -1,   -1,   60,   61,
   62,   41,    0,   43,   -1,   45,   41,   -1,   43,   -1,
   45,   -1,   -1,   60,   61,   62,   -1,   -1,   41,   -1,
   60,   61,   62,   -1,    0,   60,   61,   62,   -1,   43,
   -1,   45,   -1,    0,   -1,   -1,   41,   60,   61,   62,
   -1,   -1,   -1,   41,   -1,   -1,   60,   61,   62,   -1,
    0,   -1,   -1,   -1,   -1,   60,   61,   62,   -1,   -1,
   -1,   -1,   60,   61,   62,   41,  261,  262,  263,  259,
  260,  261,  262,  263,   41,   -1,  259,  260,  261,  262,
  263,    0,  259,  260,   60,   61,   62,  259,  260,   -1,
   -1,   41,  259,   60,   61,   62,   -1,   -1,  259,  260,
  261,  262,  263,  259,  260,  259,  260,  261,  262,  263,
   60,   61,   62,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   41,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,    0,   -1,   -1,  259,  260,  261,  262,  263,   -1,
   -1,   60,   61,   62,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,  259,  260,  261,  262,  263,   35,   36,  259,  260,
  261,  262,  263,   42,   43,   44,   45,   46,   47,   48,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
  259,  260,  261,  262,  263,   -1,   -1,  259,  260,  261,
  262,  263,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,  259,  260,  261,  262,  263,   -1,   -1,  259,
  260,  261,  262,  263,  259,  260,  261,  262,  263,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,  259,  260,  261,  262,
  263,   -1,   -1,   -1,   -1,  259,  260,  261,  262,  263,
   -1,   -1,   -1,   -1,  259,  260,  261,  262,  263,   -1,
   -1,  259,  260,  261,  262,  263,   37,   -1,   -1,   -1,
   41,   42,   43,   -1,   45,   -1,   47,   -1,   -1,   -1,
   -1,   -1,   -1,  259,  260,  261,  262,  263,   -1,   60,
   61,   62,  259,  260,  261,  262,  263,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,  259,
  260,  261,  262,  263,   -1,   -1,   -1,   -1,   -1,   -1,
   33,   -1,   -1,   94,   37,   -1,   -1,   40,   41,   42,
   43,   -1,   45,   -1,   47,   -1,   -1,   -1,   -1,   -1,
  259,  260,  261,  262,  263,   58,   33,   60,   61,   62,
   37,   -1,   -1,   40,   41,   42,   43,   -1,   45,   -1,
   47,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   58,   -1,   60,   61,   62,   33,   -1,   -1,   -1,
   37,   94,   -1,   40,   41,   42,   43,   37,   45,   -1,
   47,   -1,   42,   43,   -1,   45,   -1,   47,   -1,   -1,
   -1,   58,   -1,   60,   61,   62,   -1,   94,   37,   -1,
   60,   61,   62,   42,   43,   37,   45,   -1,   47,   -1,
   42,   43,   -1,   45,   -1,   47,   -1,   -1,   -1,   -1,
   -1,   60,   61,   62,   -1,   -1,   -1,   94,   60,   61,
   62,   33,   -1,   -1,   94,   37,   -1,   -1,   40,   41,
   42,   -1,   -1,   -1,   -1,   47,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   94,   58,   -1,   -1,   -1,
   -1,   -1,   94,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
    3,   -1,   -1,   -1,   -1,   -1,   -1,   -1,  259,  260,
  261,  262,  263,   16,   -1,   18,   -1,   -1,   -1,   -1,
   -1,   -1,   94,   -1,   -1,   28,   29,   30,   31,   32,
   33,   34,   35,   36,   37,   38,   39,   40,   41,   42,
   43,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,  257,  258,  259,  260,  261,  262,
  263,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,  258,  259,  260,  261,  262,  263,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
  257,  258,   -1,   -1,  261,  262,  263,   -1,   -1,  259,
  260,  261,  262,  263,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,  260,  261,  262,  263,   -1,   -1,   -1,   -1,  261,
  262,  263,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   -1,
   -1,   -1,   -1,   -1,   -1,  257,  258,
};
#define YYFINAL 21
#ifndef YYDEBUG
#define YYDEBUG 0
#endif
#define YYMAXTOKEN 264
#if YYDEBUG
char *yyname[] = {
"end-of-file",0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
"'!'",0,0,0,"'%'",0,0,"'('","')'","'*'","'+'",0,"'-'",0,"'/'",0,0,0,0,0,0,0,0,0,
0,"':'",0,"'<'","'='","'>'",0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,"'^'",0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
0,0,0,0,0,0,0,0,0,0,"NUM","STR_TOK","OR","AND","NE","GE","LE","NEG",
};
char *yyrule[] = {
"$accept : input",
"input : exp",
"input : STR",
"exp : NUM",
"exp : exp OR exp",
"exp : exp AND exp",
"exp : exp '=' exp",
"exp : STR '=' STR",
"exp : exp '=' STR",
"exp : STR '=' exp",
"exp : exp NE exp",
"exp : STR NE STR",
"exp : exp NE STR",
"exp : STR NE exp",
"exp : exp '>' exp",
"exp : STR '>' STR",
"exp : exp '<' exp",
"exp : STR '<' STR",
"exp : exp GE exp",
"exp : STR GE STR",
"exp : exp LE exp",
"exp : STR LE STR",
"exp : '!' exp",
"exp : exp '+' exp",
"exp : exp '-' exp",
"exp : exp '*' exp",
"exp : exp '/' exp",
"exp : exp '%' exp",
"exp : '-' exp",
"exp : exp '^' exp",
"exp : '(' exp ')'",
"exp : STR ':' STR",
"STR : '-'",
"STR : '+'",
"STR : '*'",
"STR : '/'",
"STR : '%'",
"STR : OR",
"STR : AND",
"STR : '='",
"STR : NE",
"STR : '>'",
"STR : '<'",
"STR : GE",
"STR : LE",
"STR : '!'",
"STR : '^'",
"STR : '('",
"STR : ')'",
"STR : ':'",
"STR : STR_TOK",
};
#endif
#ifdef YYSTACKSIZE
#undef YYMAXDEPTH
#define YYMAXDEPTH YYSTACKSIZE
#else
#ifdef YYMAXDEPTH
#define YYSTACKSIZE YYMAXDEPTH
#else
#define YYSTACKSIZE 500
#define YYMAXDEPTH 500
#endif
#endif
int yydebug;
int yynerrs;
int yyerrflag;
int yychar;
short *yyssp;
YYSTYPE *yyvsp;
YYSTYPE yyval;
YYSTYPE yylval;
short yyss[YYSTACKSIZE];
YYSTYPE yyvs[YYSTACKSIZE];
#define yystacksize YYSTACKSIZE
#line 120 "newexpr.y"

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

#line 451 "y.tab.c"
#define YYABORT goto yyabort
#define YYREJECT goto yyabort
#define YYACCEPT goto yyaccept
#define YYERROR goto yyerrlab
int
yyparse()
{
    register int yym, yyn, yystate;
#if YYDEBUG
    register char *yys;
    extern char *getenv();

    if (yys = getenv("YYDEBUG"))
    {
        yyn = *yys;
        if (yyn >= '0' && yyn <= '9')
            yydebug = yyn - '0';
    }
#endif

    yynerrs = 0;
    yyerrflag = 0;
    yychar = (-1);

    yyssp = yyss;
    yyvsp = yyvs;
    *yyssp = yystate = 0;

yyloop:
    if (yyn = yydefred[yystate]) goto yyreduce;
    if (yychar < 0)
    {
        if ((yychar = yylex()) < 0) yychar = 0;
#if YYDEBUG
        if (yydebug)
        {
            yys = 0;
            if (yychar <= YYMAXTOKEN) yys = yyname[yychar];
            if (!yys) yys = "illegal-symbol";
            printf("%sdebug: state %d, reading %d (%s)\n",
                    YYPREFIX, yystate, yychar, yys);
        }
#endif
    }
    if ((yyn = yysindex[yystate]) && (yyn += yychar) >= 0 &&
            yyn <= YYTABLESIZE && yycheck[yyn] == yychar)
    {
#if YYDEBUG
        if (yydebug)
            printf("%sdebug: state %d, shifting to state %d\n",
                    YYPREFIX, yystate, yytable[yyn]);
#endif
        if (yyssp >= yyss + yystacksize - 1)
        {
            goto yyoverflow;
        }
        *++yyssp = yystate = yytable[yyn];
        *++yyvsp = yylval;
        yychar = (-1);
        if (yyerrflag > 0)  --yyerrflag;
        goto yyloop;
    }
    if ((yyn = yyrindex[yystate]) && (yyn += yychar) >= 0 &&
            yyn <= YYTABLESIZE && yycheck[yyn] == yychar)
    {
        yyn = yytable[yyn];
        goto yyreduce;
    }
    if (yyerrflag) goto yyinrecovery;
#ifdef lint
    goto yynewerror;
#endif
yynewerror:
    yyerror("syntax error");
#ifdef lint
    goto yyerrlab;
#endif
yyerrlab:
    ++yynerrs;
yyinrecovery:
    if (yyerrflag < 3)
    {
        yyerrflag = 3;
        for (;;)
        {
            if ((yyn = yysindex[*yyssp]) && (yyn += YYERRCODE) >= 0 &&
                    yyn <= YYTABLESIZE && yycheck[yyn] == YYERRCODE)
            {
#if YYDEBUG
                if (yydebug)
                    printf("%sdebug: state %d, error recovery shifting\
 to state %d\n", YYPREFIX, *yyssp, yytable[yyn]);
#endif
                if (yyssp >= yyss + yystacksize - 1)
                {
                    goto yyoverflow;
                }
                *++yyssp = yystate = yytable[yyn];
                *++yyvsp = yylval;
                goto yyloop;
            }
            else
            {
#if YYDEBUG
                if (yydebug)
                    printf("%sdebug: error recovery discarding state %d\n",
                            YYPREFIX, *yyssp);
#endif
                if (yyssp <= yyss) goto yyabort;
                --yyssp;
                --yyvsp;
            }
        }
    }
    else
    {
        if (yychar == 0) goto yyabort;
#if YYDEBUG
        if (yydebug)
        {
            yys = 0;
            if (yychar <= YYMAXTOKEN) yys = yyname[yychar];
            if (!yys) yys = "illegal-symbol";
            printf("%sdebug: state %d, error recovery discards token %d (%s)\n",
                    YYPREFIX, yystate, yychar, yys);
        }
#endif
        yychar = (-1);
        goto yyloop;
    }
yyreduce:
#if YYDEBUG
    if (yydebug)
        printf("%sdebug: state %d, reducing by rule %d (%s)\n",
                YYPREFIX, yystate, yyn, yyrule[yyn]);
#endif
    yym = yylen[yyn];
    yyval = yyvsp[1-yym];
    switch (yyn)
    {
case 1:
#line 60 "newexpr.y"
{ retval.d = yyvsp[0].d; rettype = NUM_TYPE; }
break;
case 2:
#line 61 "newexpr.y"
{ retval.s = yyvsp[0].s; rettype = STR_TYPE; }
break;
case 3:
#line 64 "newexpr.y"
{ yyval.d = yyvsp[0].d; }
break;
case 4:
#line 65 "newexpr.y"
{ yyval.d = (yyvsp[-2].d != 0.0) || (yyvsp[0].d != 0.0); }
break;
case 5:
#line 66 "newexpr.y"
{ yyval.d = (yyvsp[-2].d != 0.0) && (yyvsp[0].d != 0.0); }
break;
case 6:
#line 67 "newexpr.y"
{ yyval.d = yyvsp[-2].d == yyvsp[0].d; }
break;
case 7:
#line 68 "newexpr.y"
{ yyval.d = strcmp(yyvsp[-2].s, yyvsp[0].s) == 0; }
break;
case 8:
#line 69 "newexpr.y"
{ yyval.d = 0; }
break;
case 9:
#line 70 "newexpr.y"
{ yyval.d = 0; }
break;
case 10:
#line 71 "newexpr.y"
{ yyval.d = yyvsp[-2].d != yyvsp[0].d; }
break;
case 11:
#line 72 "newexpr.y"
{ yyval.d = strcmp(yyvsp[-2].s, yyvsp[0].s) != 0; }
break;
case 12:
#line 73 "newexpr.y"
{ yyval.d = 1; }
break;
case 13:
#line 74 "newexpr.y"
{ yyval.d = 1; }
break;
case 14:
#line 75 "newexpr.y"
{ yyval.d = yyvsp[-2].d > yyvsp[0].d; }
break;
case 15:
#line 76 "newexpr.y"
{ yyval.d = strcmp(yyvsp[-2].s, yyvsp[0].s) > 0; }
break;
case 16:
#line 77 "newexpr.y"
{ yyval.d = yyvsp[-2].d < yyvsp[0].d; }
break;
case 17:
#line 78 "newexpr.y"
{ yyval.d = strcmp(yyvsp[-2].s, yyvsp[0].s) < 0; }
break;
case 18:
#line 79 "newexpr.y"
{ yyval.d = yyvsp[-2].d >= yyvsp[0].d; }
break;
case 19:
#line 80 "newexpr.y"
{ yyval.d = strcmp(yyvsp[-2].s, yyvsp[0].s) >= 0; }
break;
case 20:
#line 81 "newexpr.y"
{ yyval.d = yyvsp[-2].d <= yyvsp[0].d; }
break;
case 21:
#line 82 "newexpr.y"
{ yyval.d = strcmp(yyvsp[-2].s, yyvsp[0].s) <= 0; }
break;
case 22:
#line 83 "newexpr.y"
{ yyval.d = yyvsp[0].d == 0.0; }
break;
case 23:
#line 84 "newexpr.y"
{ yyval.d = yyvsp[-2].d + yyvsp[0].d; }
break;
case 24:
#line 85 "newexpr.y"
{ yyval.d = yyvsp[-2].d - yyvsp[0].d; }
break;
case 25:
#line 86 "newexpr.y"
{ yyval.d = yyvsp[-2].d * yyvsp[0].d; }
break;
case 26:
#line 87 "newexpr.y"
{ yyval.d = yyvsp[-2].d / yyvsp[0].d; }
break;
case 27:
#line 88 "newexpr.y"
{ yyval.d = fmod(yyvsp[-2].d, yyvsp[0].d); }
break;
case 28:
#line 89 "newexpr.y"
{ yyval.d = -yyvsp[0].d; }
break;
case 29:
#line 90 "newexpr.y"
{ yyval.d = pow(yyvsp[-2].d, yyvsp[0].d); }
break;
case 30:
#line 91 "newexpr.y"
{ yyval.d = yyvsp[-1].d; }
break;
case 31:
#line 92 "newexpr.y"
{
        char *errmsg;
        struct re_pattern_buffer re_buffer;
        struct re_registers re_regs;
        int len;

        len = strlen(yyvsp[0].s);
        re_buffer.allocated = 2 * len;
        re_buffer.buffer = (unsigned char *)xmalloc(re_buffer.allocated);
        re_buffer.translate = 0;
        errmsg = (char *)re_compile_pattern(yyvsp[0].s, len, &re_buffer);
        if (errmsg) {
          yyerror(errmsg);
          YYERROR;
        }
        len = re_match(&re_buffer, yyvsp[-2].s, strlen(yyvsp[-2].s), 0, &re_regs);
        if (len >= 0) {
          yyval.d = len;
        } else
          yyval.d = 0.0;
        free(re_buffer.buffer);
     }
break;
case 50:
#line 117 "newexpr.y"
{ yyval.s = yyvsp[0].s; }
break;
#line 741 "y.tab.c"
    }
    yyssp -= yym;
    yystate = *yyssp;
    yyvsp -= yym;
    yym = yylhs[yyn];
    if (yystate == 0 && yym == 0)
    {
#if YYDEBUG
        if (yydebug)
            printf("%sdebug: after reduction, shifting from state 0 to\
 state %d\n", YYPREFIX, YYFINAL);
#endif
        yystate = YYFINAL;
        *++yyssp = YYFINAL;
        *++yyvsp = yyval;
        if (yychar < 0)
        {
            if ((yychar = yylex()) < 0) yychar = 0;
#if YYDEBUG
            if (yydebug)
            {
                yys = 0;
                if (yychar <= YYMAXTOKEN) yys = yyname[yychar];
                if (!yys) yys = "illegal-symbol";
                printf("%sdebug: state %d, reading %d (%s)\n",
                        YYPREFIX, YYFINAL, yychar, yys);
            }
#endif
        }
        if (yychar == 0) goto yyaccept;
        goto yyloop;
    }
    if ((yyn = yygindex[yym]) && (yyn += yystate) >= 0 &&
            yyn <= YYTABLESIZE && yycheck[yyn] == yystate)
        yystate = yytable[yyn];
    else
        yystate = yydgoto[yym];
#if YYDEBUG
    if (yydebug)
        printf("%sdebug: after reduction, shifting from state %d \
to state %d\n", YYPREFIX, *yyssp, yystate);
#endif
    if (yyssp >= yyss + yystacksize - 1)
    {
        goto yyoverflow;
    }
    *++yyssp = yystate;
    *++yyvsp = yyval;
    goto yyloop;
yyoverflow:
    yyerror("yacc stack overflow");
yyabort:
    return (1);
yyaccept:
    return (0);
}
