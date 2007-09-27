/******************************************************************************

CAMPSITE is a Unicode-enabled multilingual web content
management system for news publications.
CAMPFIRE is a Unicode-enabled java-based near WYSIWYG text editor.
Copyright (C)2000,2001  Media Development Loan Fund
contact: contact@campware.org - http://www.campware.org
Campware encourages further development. Please let us know.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

******************************************************************************/

/*
 * kwd.cpp
 */
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "kwd.h"

#define debug if(0) printf

struct kwd_t *kwd_hash[256];

static inline unsigned char
make_hash(const char *c)
{
  char hash = *c + c[1];
  return hash;
}

void
init_hash()
{
  int h;

  for(h = 0; h < 256; h++)
    kwd_hash[h] = 0;
}

static inline void
add_keyword(const char *kwd, int l)
{
  struct kwd_t *p;
  unsigned h = make_hash(kwd);

  for (p = kwd_hash[h]; p; p = p->next)
    if (strcmp(kwd, p->k) == 0)
      return;

  p = (kwd_t*)malloc(sizeof(struct kwd_t));

  p->k = (char*)malloc(l + 1);
  memcpy(p->k, kwd, l);
  p->k[l] = 0;
  p->next = kwd_hash[h];
  kwd_hash[h] = p;
}

void
parse_kwd(const char *s)
{
  static const int t[256] = {
    0, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
    1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1,
    1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, 1, 1, 1,
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0,
    0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
    1, 1, 1, 1, 1, 1
  };
  /* " \t\n\r,./\\<>?:;\"'{}[]~`!%^&*()+=\\|" */
  const char *p, *q;
  int l;
  
  if (s) {
    for (q = s; *q;) {
      p = q;
      while (*q && t[(unsigned char)*q])
        q++;
      l = q - p;
      if (l > 1) {
        add_keyword(p, mymin(l, MAX_KWD));
      } else {
        while (*q && !t[(unsigned char)*q])
          q++;
      }
    }
  }
}

void
del_kwd_list()
{
  int h;
  struct kwd_t *k, *q;
  
  for (h = 0; h < 256; h++) {
    k = kwd_hash[h];
    while (k) {
      if (k->k)
        free(k->k);
      q = k;
      k = k->next;
      free(q);
    }
  }
}
