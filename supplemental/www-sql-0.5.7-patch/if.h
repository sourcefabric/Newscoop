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

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

/*   if.h - include file for if routines
 * Include this in all files which need to think about if statements
 */

typedef struct if_stack {
  int stat;               /* 0 for false, 1 for true */
  int active;             /* if the code inside the if statement shouldn't
                           * be executed because of a surounding if statement,
                           * set this to 0.  Set to 1 otherwise */

#ifdef NEW_SCANNER
  /* extra code to extend if stack to handle while and print_loops */
  /* for while and print loops, loop_start gives the file position for
   * the initial loop command, and stat is set to 1.  When the loop should
   * be broken from, stat is set to 0.
   */
  enum { LOOP_IF, LOOP_WHILE, LOOP_PRINT } loop_type;
  long loop_start;
  int looping;     /* set by 'done' to indicate that we are looping around */
  void *data;      /* for a print_loop, this holds the query handle */
#endif
  struct if_stack *prev;  /* The if statement, one level up */
} IFSTACK;

extern IFSTACK *curIf;

#define checkIf (curIf->stat && curIf->active)

void initIfStack();


