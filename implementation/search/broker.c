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
 * broker.c
 */
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "kwd.h"
#include "broker.h"

#define debug if(0) printf

static struct Article *
add_article(struct Article *art, unsigned IdPublication, unsigned NrIssue, unsigned NrSection, unsigned Number, unsigned IdLanguage, int do_add)
{
  struct Article *a;
  
  debug("add(idp=%u, nri=%u, nrs=%u, nr=%u, idl=%u, do_add=%u\n",
	IdPublication, NrIssue, NrSection, Number, IdLanguage, do_add);
  
  for (a = art; a; a = a->next)
    if (a->IdPublication == IdPublication && a->NrIssue == NrIssue && a->NrSection == NrSection
        && a->Number == Number && a->IdLanguage == IdLanguage) {
      a->h++;
      debug("found...\n");
      return art;
    }
  
  if (do_add) {
    a = malloc(sizeof(struct Article));
    a->IdPublication = IdPublication;
    a->NrIssue = NrIssue;
    a->NrSection = NrSection;
    a->Number = Number;
    a->IdLanguage = IdLanguage;
    a->h = 0;
    a->next = art;
    
    return a;
  } else
    return art;
}

static inline struct Article *
prune(struct Article *a, int kc)
{
  struct Article *p, *q, *o = 0;
  
  debug("pruning(%u)....\n", kc);
  for(p = a; p;)
    if (p->h != kc) {
      q = p->next;
      if (p == a)
        a = q;
      free(p);
      p = q;
      if (o)
        o->next = p;
    } else {
      o = p;
      p = p->next;
    }
  
  return a;
}

struct Article *
broker(MYSQL *mysql, const char *what, int op, unsigned IdPublication,
       unsigned NrIssue, unsigned NrSection, unsigned Number, unsigned IdLanguage)
{
  struct Article *a = 0;
  struct kwd_t *k;
  char *p;
  MYSQL_RES *res;
  MYSQL_ROW row;
  unsigned kwd_id;
  int kc = 0;
  int h;
  char query[1024], str[96];
  
  if (!what)
    return 0;
  
  init_hash();
  parse_kwd(what);
  for (h = 0; h < 255; h++)
    for (k = kwd_hash[h]; k; k = k->next) {
      if (!k->k)
        continue;
      
      /* Get KeywordId */
      p = malloc(strlen(k->k) * 2 + 1);
      mysql_escape_string(p, k->k, mymin(strlen(k->k), MAX_KWD));
      sprintf(query, "SELECT Id FROM KeywordIndex WHERE Keyword = '%s'", p);
      free(p);
      if (mysql_query(mysql, query) != 0)
        return 0;
      
      res = mysql_store_result(mysql);
      if (!res)
        return 0;
      
      row = mysql_fetch_row(res);
      if (!row)
        return 0;
      
      kwd_id = row[0] ? atoi(row[0]) : 0;
      mysql_free_result(res);
      
      if (!kwd_id) {
        if (op == OP_AND)
          return 0;
        continue;
      }
      
      strcpy(query, "SELECT IdPublication, IdLanguage, NrIssue, NrSection, NrArticle FROM ArticleIndex WHERE ");
      if (IdPublication) {
        sprintf(str, "IdPublication = %u AND ", IdPublication);
        strcat(query, str);
      }
      
      if (IdLanguage) {
        sprintf(str, "IdLanguage = %u AND ", IdLanguage);
        strcat(query, str);
      }
      
      sprintf(str, "IdKeyword = %u", kwd_id);
      strcat(query, str);
      
      if (NrIssue) {
        sprintf(str, " AND NrIssue = %u", NrIssue);
        strcat(query, str);
      }
      
      if (NrSection) {
        sprintf(str, " AND NrSection = %u", NrSection);
        strcat(query, str);
      }
      
      if (Number) {
        sprintf(str, " AND NrArticle = %u", Number);
        strcat(query, str);
      }
      if (mysql_query(mysql, query) != 0)
        return 0;
      
      res = mysql_store_result(mysql);
      if (!res)
        return 0;
      
      for(;;) {
        row = mysql_fetch_row(res);
        if (!row)
          break;
        
        debug("adding...\n");
        a = add_article(a, atoi(row[0]), atoi(row[2]), atoi(row[3]), atoi(row[4]), atoi(row[1]), (op == OP_OR) || !kc);
      }
      mysql_free_result(res);
      
      if ((op == OP_AND) && kc) {
        a = prune(a, kc);
        if (!a)
          return 0;
      }
      
      kc++;
    }
  
  return a;
}


int
main(int argc, char **argv)
{
  char *        sql_host_name = "localhost";
  char *        sql_user_name = "root";
  char *        sql_password = "";
  char *        sql_db = "campsite";
  unsigned int  sql_port = 0;
  char *        sql_socket = 0;
  unsigned int  sql_flags = 0;
  
  MYSQL         mysql;
  
  struct Article *a;
  
  if (argc != 2)
    return 1;
  
  mysql_init(&mysql);
  mysql_real_connect(&mysql, sql_host_name, sql_user_name, sql_password, sql_db, sql_port, sql_socket, sql_flags);
  
  for (a = broker(&mysql, argv[1], OP_AND, 1, 0, 0, 0, 1); a; a = a->next)
    printf("found pub=%u, iss=%u, sect=%u, nr=%u, lng=%u, h=%u\n",
           a->IdPublication, a->NrIssue, a->NrSection, a->Number, a->IdLanguage, a->h);
  
  mysql_close(&mysql);
  
  return 0;
}
