#include "newscan.h"
#include "if.h"

#include <ctype.h>
#include <stdlib.h>
#include <string.h>

#define BUFSIZE 8192

static inline void scanner_read_cmd(Scanner *self);

void *xmalloc(int size);
char *xstrdup(const char *str);

Scanner *scanner_new(FILE *in, FILE *out, ExecFunc exec) {
  Scanner *self;

  self = xmalloc(sizeof(Scanner));

  self->in = in;
  self->out = out;
  self->exec = exec;
  self->argc = 0;
  self->argv[0] = NULL;
  return self;
}

void scanner_destroy(Scanner *self) {
  free(self);
}

static char cmd_lead[] = "<! sql ";
static int cmd_lead_length = 7 /* = strlen(cmd_lead) */;

void scanner_scan(Scanner *self) {
  char buf[64];
  int ch, lead_pos = 0, pos = 0, i;
  
  while ((ch = getc(self->in)) != EOF) {
    buf[pos++] = ch;
    if (cmd_lead[lead_pos] == ' ') {
      pos--;
      do {
	if (ch == ' ' || ch == '\t' || ch == '\n')
	  buf[pos++] = ch;
	else {
	  ungetc(ch, self->in);
	  break;
	}
      } while ((ch = getc(self->in)) != EOF);
      lead_pos++;
      /* also allow <!SQL command lead.  Also, some systems have weird
       * implementations of tolower that give unexpected results on non
       * alpha characters */
    } else if (cmd_lead[lead_pos] == ch ||
	       (isalpha(ch) && cmd_lead[lead_pos] == tolower(ch))) {
      if (lead_pos == 0)
	self->cmd_pos = ftell(self->in) - 1; /* possible start of command */
      lead_pos++;
    } else {
      if (checkIf)
	for (i = 0; i < pos; i++)
	  putc(buf[i], self->out);
      pos = 0;
      lead_pos = 0;
    }

    if (lead_pos == 1) { /* we just read the start of the lead ... */
      self->cmd_pos = ftell(self->in) - 1;
    }

    /* now check if we have got a correct command lead ... */
    if (lead_pos == cmd_lead_length) {
      scanner_read_cmd(self);
      if (self->exec)
	self->exec(self);
      pos = 0;
      lead_pos = 0;
      for (i = 0; i < self->argc; i++)
	free(self->argv[i]);
      self->argc = 0;
    }
  }
}

static inline char *scanner_read_word(Scanner *self) {
  static char buf[BUFSIZE];  /* static so it can be return'd */
  int ch, pos = 0;
  int in_quote = 0, escaped = 0;

  /*eat white space */
  while ((ch = getc(self->in)) != EOF && (ch == ' ' || ch == '\t' ||
					  ch == '\n'))
    ;
  if (ch == EOF || ch == '>')
    return NULL;
  ungetc(ch, self->in);
  while ((ch = getc(self->in)) != EOF && pos < BUFSIZE) {
    if (!in_quote && !escaped && (ch==' '||ch=='\t'||ch=='\n'||ch=='>')) {
      ungetc(ch, self->in);
      break;
    }
    if (escaped) {
      if (ch == '"')
	buf[pos-1] = ch;
      else
	buf[pos++] = ch;
      escaped = 0;
    } else if (ch == '"')
      in_quote = ! in_quote;
    else if (ch == '\\') {
      escaped = 1;
      buf[pos++] = ch;
    } else
      buf[pos++] = ch;
  }
  buf[pos] = '\0';
  return buf;
}

static inline char *scanner_read_expr(Scanner *self) {
  static char buf[BUFSIZE];
  int ch, i = 0;
  int escaped = 0;

  /* consume whitespace */
  while ((ch = getc(self->in)) != EOF && (ch == ' ' || ch == '\t' ||
					  ch == '\n'))
    ;
  if (ch == EOF || ch == '>')
    return NULL;

  switch (ch) {
  case ')':
  case '(':
  case '%':
  case '+':
  case '-':
  case '*':
  case '/':
  case ':':
    buf[0] = ch; buf[1] = '\0';
    return buf;
  case '<': /* make sense on its own, and with an '=' after */
  case '!':
  case '=':
    buf[0] = ch; buf[1] = buf[2] = '\0';
    ch = getc(self->in);
    if (ch == EOF) return buf;
    if (ch != '=') { ungetc(ch, self->in); return buf; }
    buf[1] = ch; /* '=' */
    return buf;
  case '&':
  case '|':
    buf[0] = ch; buf[1] = buf[2] = '\0';
    ch = getc(self->in);
    if (ch == EOF) return buf;
    if (ch != buf[0]) { ungetc(ch, self->in); return buf; }
    buf[1] = ch;
    return buf;
  case '$': /* a variable */
  case '@':
  case '#':
  case '?':
  case '~':
    i = 0;
    do {
      buf[i++] = ch;
      ch = getc(self->in);
    } while (ch != EOF && ((ch>='a' && ch<='z') || (ch>='A' && ch<='Z') ||
			   (ch>='0' && ch<='9') || ch=='.' || ch=='_'));
    if (ch != EOF) ungetc(ch, self->in);
    buf[i] = '\0';
    return buf;
  case '0':
  case '1':
  case '2':
  case '3':
  case '4':
  case '5':
  case '6':
  case '7':
  case '8':
  case '9':
    i = 0;
    do {
      buf[i++] = ch;
      ch = getc(self->in);
    } while (ch != EOF && ((ch >= '0' && ch <= '9') || ch == '.'));
    if (ch != EOF) ungetc(ch, self->in);
    buf[i] = '\0';
    return buf;
  case '"':
    i = escaped = 0;
    while ((ch = getc(self->in)) != EOF && i < BUFSIZE) {
      if (ch == '"' && ! escaped)
	break;
      if (escaped) {
	if (ch == '"')
	  buf[i-1] = ch;
	else
	  buf[i++] = ch;
	escaped = 0;
      } else if (ch == '\\') {
	buf[i++] = ch;
	escaped = 1;
      } else
	buf[i++] = ch;
    }
    buf[i] = '\0';
    return buf;
  case '\\': /* special handling for > -- because it also closes a command */
    buf[0] = ch;
    buf[1] = buf[2] = '\0';
    ch = getc(self->in);
    if (ch != '>') return buf;
    buf[0] = ch;
    ch = getc(self->in);
    if (ch == '=') buf[1] = ch;
    else if (ch != EOF) ungetc(ch, self->in);
    return buf;
  default:
    buf[0] = ch; buf[1] = '\0';
    return buf;
  }
}

static inline void scanner_read_cmd(Scanner *self) {
  char *tmp;
  int is_expr = 0;

  tmp = scanner_read_word(self);
  if (tmp == NULL)
    return;
  self->argv[0] = xstrdup(tmp);
  self->argc = 1;

  if (!strcasecmp(self->argv[0], "setexpr")) {
    tmp = scanner_read_word(self); /* var name */
    if (tmp == NULL)
      return;
    self->argv[self->argc++] = xstrdup(tmp);
    is_expr = 1;
  } else if (!strcasecmp(self->argv[0], "if") ||
	     !strcasecmp(self->argv[0], "elsif") ||
	     !strcasecmp(self->argv[0], "eval") ||
	     !strcasecmp(self->argv[0], "while"))
    is_expr = 1;
  else
    is_expr = 0;

  if (is_expr)
    while ((tmp = scanner_read_expr(self)) != NULL && self->argc < MAXARGS) {
      self->argv[self->argc++] = xstrdup(tmp);
    }
  else
    while ((tmp = scanner_read_word(self)) != NULL && self->argc < MAXARGS)
      self->argv[self->argc++] = xstrdup(tmp);
  /* this holds the position of the end of command */
  self->cur_pos = ftell(self->in);
}

FILE *yyout;
Scanner *cur_scan;

void exec_f(Scanner *s) {
  void executeSql(int argc, char *argv[]);

  yyout = s->out;
  cur_scan = s;
  executeSql(s->argc, s->argv);
}

void parse(FILE *is, FILE *os) {
  Scanner *s = scanner_new(is, os, exec_f);
  scanner_scan(s);
  scanner_destroy(s);
}
