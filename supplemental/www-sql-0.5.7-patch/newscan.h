#include <stdio.h>

#define MAXARGS 60

typedef struct _Scanner Scanner;
typedef void (*ExecFunc)(Scanner *scanner);

struct _Scanner {
  FILE *in;
  FILE *out;
  long cur_pos;
  long cmd_pos;
  int argc;
  char *argv[MAXARGS];
  ExecFunc exec;
};

Scanner *scanner_new(FILE *in, FILE *out, ExecFunc exec);
void scanner_destroy(Scanner *scanner);

void scanner_scan(Scanner *scanner);
