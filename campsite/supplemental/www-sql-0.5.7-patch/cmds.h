#ifndef _CMDS_H
#define _CMDS_H

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
/*   cmds.h
 * contains definitions that will be useful for files containing www-sql
 * functions.
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

/* cmds.h -- definitions of structures to hold information about available
     www-sql commands. */

typedef void (*sqlfunc)(int argc, char *argv[]);

/* definition of one command */
typedef struct command {
  char *name;
  sqlfunc func;
} command;

/* one of these should be defined for each command file */
/* array should be terminated by {NULL, NULL} */
typedef command commands[];


/* stuff that is useful for writing commands */
char *xstrdup(const char *str);
void *xmalloc(int size);

/* output file handle -- from the output of (f)lex */
extern FILE *yyout;

char *substVars(char *str);

#define checkNumArgs(n,f) if (argc < n) {\
                         fprintf(yyout, "<p>%s: too few arguments</p>\n", f);\
                         return; }

#endif

