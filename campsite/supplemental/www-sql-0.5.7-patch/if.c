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

/*   if.c - routines to handle if statements
 * I put them here so they wouldn't be cluttered.  No expression parsing is
 * done in these functions.  If you want to find that, look at expr.c (A
 * hacked up version of the GNU sh-util expr)
 */

#include <stdio.h>
#include <stdlib.h>

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif
void *xmalloc(int size);

#include "cmds.h"
#include "if.h"
#ifdef NEW_SCANNER
#include "newscan.h"
#endif

/* Function prototypes */
char *substVars(char *);     /* from www-sql */
int evalExpr(int, char **);  /* from expr.c */

IFSTACK *curIf;

void initIfStack() {
  curIf = xmalloc(sizeof(IFSTACK));
  curIf->stat = 1;
  curIf->active = 1;
  curIf->prev = NULL;  /* mark bottom of stack */
#ifdef NEW_SCANNER
  curIf->loop_type = LOOP_IF;
#endif
}

void cmdIf(int argc, char *argv[]) {
  char *tmp;
  int i;
  IFSTACK *is;

  if (checkIf) {
    for (i=0; i < argc; i++) {   /* perform substitutions */
      tmp = substVars(argv[i]);
      free(argv[i]);
      argv[i] = tmp;
    }

    is = xmalloc(sizeof(IFSTACK));
    is->stat = evalExpr(argc, argv);
    is->active = 1;
#ifdef NEW_SCANNER
    is->loop_type = LOOP_IF;
    is->looping = 0;
#endif
    is->prev = curIf;
    curIf = is;
  } else {                        /* we must parse if statements in  */
    is = xmalloc(sizeof(IFSTACK)); /* sections of a document excluded */
    is->stat = 0;                 /* by other if statements to match */
    is->active = 0;               /* up the elses and endifs         */
#ifdef NEW_SCANNER
    is->loop_type = LOOP_IF;
    is->looping = 0;
#endif
    is->prev = curIf;
    curIf = is;
  }
}

/* This command was contributed by David J. N. Begley <d.begley@ieee.org>. */
void cmdElsIf(int argc, char *argv[]) {
  char *tmp;
  int i;

#ifdef NEW_SCANNER
  if (curIf->loop_type != LOOP_IF) {
    fprintf(yyout, "<p><b>elsif:</b> not in if loop</p>\n");
    return;
  }
#endif
  if (curIf->prev != NULL) {
    if (curIf->active && !curIf->stat) {
      for (i=0; i < argc; i++) {   /* perform substitutions */
        tmp = substVars(argv[i]);
        free(argv[i]);
        argv[i] = tmp;
      }
      curIf->stat = evalExpr(argc, argv);
      curIf->active = 1;
    } else {                        /* we must parse elsif statements to  */
      curIf->stat = 0;              /* match up the elses and endifs      */
      curIf->active = 0;
    }
  }
}

void cmdElse(int argc, char *argv[]) {
#ifdef NEW_SCANNER
  if (curIf->loop_type != LOOP_IF) {
    fprintf(yyout, "<p><b>else:</b> not in if loop</p>\n");
    return;
  }
#endif
  curIf->stat = ! curIf->stat;
}

void cmdEndIf(int argc, char *argv[]) {
  IFSTACK *is;

#ifdef NEW_SCANNER
  if (curIf->loop_type != LOOP_IF) {
    fprintf(yyout, "<p><b>endif:</b> not in if loop</p>\n");
    return;
  }
#endif
  is = curIf;
  if (curIf->prev != NULL) {   /* make sure the user doesn't remove the  */
    curIf = is->prev;          /* last if from the stack.  (I don't like */
    free(is);                  /* core dumps.)                           */
  }
}

#ifdef NEW_SCANNER
void cmdWhile(int argc, char *argv[]) {
  extern Scanner *cur_scan;
  char *tmp;
  int i;

  if (curIf->looping)
    curIf->looping = 0;
  else {
    IFSTACK *is;
    is = xmalloc(sizeof(IFSTACK));
    is->loop_type = LOOP_WHILE;
    is->prev = curIf;
    is->looping = 0;
    if (curIf->active && !curIf->stat) {
      is->active = 0;
      is->stat = 0;
    } else {
      is->active = 1;
      is->stat = 1;
      is->loop_start = cur_scan->cmd_pos;
    }
    curIf = is;
    if (!curIf->active)
      return;
  }
  /* check expr */
  for (i=0; i < argc; i++) {   /* perform substitutions */
    tmp = substVars(argv[i]);
    free(argv[i]);
    argv[i] = tmp;
  }
  curIf->stat = evalExpr(argc, argv);
}

void cmdPrintLoop(int argc, char *argv[]) {
  extern Scanner *cur_scan;

  /* defined in the database specific module */
  int dbLoopSetup(char *, void **); /* stores an argument to be passed to
				     * dbLoopNext and returns 1 if
				     * alright to continue, 0 if no rows
				     * in query, or -1 for error */
  int dbLoopNext(void *);   /* Increments row counter, or returns zero if
			     * there is no more rows */

  checkNumArgs(1, "print_loop");

  if (curIf->looping) {
    curIf->looping = 0;
    curIf->stat = dbLoopNext(curIf->data);
  } else {
    IFSTACK *is;
    is = xmalloc(sizeof(IFSTACK));
    is->loop_type = LOOP_PRINT;
    is->prev = curIf;
    is->looping = 0;
    if (curIf->active && !curIf->stat) {
      is->active = 0;
      is->stat = 0;
    } else {
      is->active = 1;
      is->loop_start = cur_scan->cmd_pos;
      is->stat = dbLoopSetup(argv[0], &(is->data));
      if (is->stat == -1) {
	free(is);
	return;
      }
    }
    curIf = is;
  }
}

void cmdDone(int argc, char *argv[]) {
  IFSTACK *is;
  extern Scanner *cur_scan;

  if (curIf->loop_type != LOOP_WHILE && curIf->loop_type != LOOP_PRINT) {
    fprintf(yyout, "<p><b>Done:</b> not in a while/print loop</p>\n");
    return;
  }
  if (checkIf) { /* loop again */
    fseek(cur_scan->in, curIf->loop_start, SEEK_SET);
    curIf->looping = 1;
  } else {       /* remove from stack */
    is = curIf;
    curIf = curIf->prev;
    free(is);
  }
}

void cmdBreak(int argc, char *argv[]) {
  IFSTACK *is;

  if (!checkIf) return;
  /* if break was in an if statement, deactivate them. */
  is = curIf;
  while (is->loop_type == LOOP_IF && is->prev != NULL) {
    is->active = 0;
    is = is->prev;
  }
  if (is->loop_type != LOOP_WHILE && is->loop_type != LOOP_PRINT) {
    fprintf(yyout, "<p><b>Break:</b> not in a while/print loop</p>\n");
    return;
  }
  is->stat = 0;
}

void cmdContinue(int argc, char *argv[]) {
  extern Scanner *cur_scan;

  if (!checkIf) return;
  /* if continue was in an if statement */
  while (curIf->loop_type == LOOP_IF && curIf->prev != NULL) {
    IFSTACK *is = curIf;
    curIf = curIf->prev;
    free(is);
  }
  if (curIf->loop_type != LOOP_WHILE && curIf->loop_type != LOOP_PRINT) {
    fprintf(yyout, "<p><b>Continue:</b> not in a while/print loop</p>\n");
    return;
  }
  fseek(cur_scan->in, curIf->loop_start, SEEK_SET);
  curIf->looping = 1;
}
#endif

#ifdef NEW_SCANNER
void cmdInclude(int argc, char *argv[]);
#endif

commands if_funcs = {
  {"else",  cmdElse},
  {"elsif", cmdElsIf},
  {"endif", cmdEndIf},
  {"if",    cmdIf},
#ifdef NEW_SCANNER
  {"while",      cmdWhile},
  {"print_loop", cmdPrintLoop},
  {"done",       cmdDone},
  {"break",      cmdBreak},
  {"continue",   cmdContinue},
  {"uinclude",   cmdInclude},
#endif
  {NULL, NULL}
};



