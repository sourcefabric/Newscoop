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

/*   cgi.h
 * A simple include file so other files can access the decoded CGI info.
 * Don't include it in cgi.c.
 */

typedef struct variable {
  char *name, *value;
  struct variable *next;
} VAR;

extern VAR *firstVar;

/* function prototypes */
void initCGI();

void setVar(const char *name, const char *value);
void setDefault(const char *name, const char *value);
char *valueOf(const char *);

/* returns a newly malloc'd, url encoded version of the string */
char *escape_string(char *);

