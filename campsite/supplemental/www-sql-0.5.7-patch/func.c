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
/*   func.c
 * This file contains the implementations of all the www-sql commands
 * that are database related or related to if statement handling.
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#ifdef ENABLE_UNSAFE
#include <sys/types.h>
#include <unistd.h>
#include <sys/wait.h>
#endif

#ifdef HAVE_STRFTIME
#include <time.h>
#endif

#ifdef NEW_SCANNER
#include "newscan.h"
#endif

#include "cgi.h"
#include "cmds.h"

/* expression evaluator -- defined in newexpr.c */
char *evalToString(int, char **);
void evalPrint(int, char **);

void cmdPrint(int argc, char *argv[]) {
  int i;
  char *tmp;

  for (i = 0; i < argc; i++) {
    tmp = substVars(argv[i]);
    fprintf(yyout, "%s", tmp);
    if (i + 1 != argc)
      fprintf(yyout, " ");
    free(tmp);
  }
}

#ifdef ENABLE_UNSAFE
void cmdExec(int argc, char *argv[]) {
  pid_t pid;

  checkNumArgs(1, "exec");
  fflush(yyout);
  pid = fork();
  if (pid) {
    int status;
    char buf[5];
    waitpid(pid, &status, 0);
    sprintf(buf, "%d", (int)(char)WEXITSTATUS(status));
    setVar("RESULT", buf);
  } else {
    if (dup2(fileno(yyout), fileno(stdout)) != -1 &&
	dup2(fileno(yyout), fileno(stderr)) != -1) {
      int i;
      char *tmp;

      for (i = 0; i < argc; i++) {
	tmp = substVars(argv[i]);
	free(argv[i]);
	argv[i] = tmp;
      }
      fclose(stdin);
      execvp(argv[0], argv);
      fprintf(yyout, "<p><b>exec</b> - exec failed</p>\n");
    } else
      fprintf(yyout, "<p><b>exec</b> - dup2 failed|</p>\n");
    fflush(yyout);
    _exit(-1);
  }
}
#endif

void cmdEval(int argc, char *argv[]) {
  int i;
  char *tmp;

  for (i = 0; i < argc; i++) {   /* perform substitutions */
    tmp = substVars(argv[i]);
    free(argv[i]);
    argv[i] = tmp;
  }

  evalPrint(argc, argv);
}

void cmdSet(int argc, char *argv[]) {
  char *tmp;

  checkNumArgs(2,"set");
  tmp = substVars(argv[1]);
  setVar(argv[0], tmp);
  free(tmp);
}

void cmdSetDefault(int argc, char *argv[]) {
  char *tmp;

  checkNumArgs(2,"setdefault");
  tmp = substVars(argv[1]);
  setDefault(argv[0], tmp);
  free(tmp);
}

void cmdSetExpr(int argc, char *argv[]) {
  char *tmp;
  int i;

  checkNumArgs(2,"setexpr");
  for (i = 1; i < argc; i++) {
    tmp = substVars(argv[i]);
    free(argv[i]);
    argv[i] = tmp;
  }
  setVar(argv[0], evalToString(argc - 1, argv + 1));
}

void cmdDumpVars(int argc, char *argv[]) {
  VAR *var;

  fprintf(yyout, "<p>\n");
  for (var = firstVar; var != NULL; var = var->next)
    fprintf(yyout, "<b>%s</b> = %s<br>\n", var->name, var->value);
  fprintf(yyout, "</p>\n");
}

/* like the W3-mSQL convert command -- not really needed, because of ?var */
void cmdConvert(int argc, char *argv[]) {
  char *tmp;
  char *sqlEscape(char *);

  checkNumArgs(1, "convert");
  tmp = valueOf(argv[0]);
  if (tmp == NULL)
    fprintf(yyout, "<b>%s: no such variable", tmp);
  else {
    tmp = sqlEscape(tmp);
    setVar(argv[0], tmp);
    free(tmp);
  }
}

#ifdef HAVE_STRFTIME
void cmdFtime(int argc, char *argv[]) {
  time_t t;
  struct tm *timeptr;
  char buf[512], *tmp;
  checkNumArgs(1, "ftime");

  time(&t);
  if (argc >= 2) {
    /* second argument is considered to be an increment to the current time
     * (This should be useful for setting expire times on cookies) */
    char *endptr;
    long int val;
    tmp = substVars(argv[1]);
    free(argv[1]);
    argv[1] = tmp;
    val = strtol(argv[1], &endptr, 0);
    if (argv[1] == endptr) {
      fprintf(yyout, "<p><b>ftime</b> - invalid integer argument</p>\n");
      return;
    }
    t += val;
  }
  timeptr = gmtime(&t);
  buf[0] = '\0';
  tmp = substVars(argv[0]);
  free(argv[0]);
  argv[0] = tmp;
  strftime(buf, 511, argv[0], timeptr);
  fprintf(yyout, "%s", buf);
}
#endif

#ifdef NEW_SCANNER
void cmdInclude(int argc, char *argv[]) {
  FILE *in;
  Scanner *s;
  char *tmp;
  void exec_f(Scanner *); /* from newscan.c */
  FILE *cgi_fopen(const char *); /* from cgi.c */

  checkNumArgs(1, "include");
  tmp = substVars(argv[0]);
  in = cgi_fopen(tmp);
  free(tmp);
  if (in == NULL)
    return;
  s = scanner_new(in, yyout, exec_f);
  scanner_scan(s);
  scanner_destroy(s);
}
#endif

commands other_funcs = {
  {"print",      cmdPrint},
  {"eval",       cmdEval},
  {"set",        cmdSet},
  {"setdefault", cmdSetDefault},
  {"setexpr",    cmdSetExpr},
  {"dumpvars",   cmdDumpVars},
  {"convert",    cmdConvert},
#ifdef ENABLE_UNSAFE
  {"exec",       cmdExec},
#endif
#ifdef HAVE_STRFTIME
  {"ftime",      cmdFtime},
#endif
#ifdef NEW_SCANNER
  {"include",    cmdInclude},
#endif
  {NULL, NULL}
};


