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
 * process_i.c
 */
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <mysql/mysql.h>
#include <stdlib.h>

char *strmov(char *s, char *x);
static char *tmp_path = 0;

#include "sql.h"
#include "parse_file.h"

static void
die()
{
	printf("<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=%s\">\n", getenv("HTTP_REFERER"));
	if (tmp_path)
		unlink(tmp_path);
	exit(1);
}

static char *
make_tmp_path(char *id)
{
	char *c;

	c = malloc(strlen(TMP_DIR) + strlen(id) + 6);
	if (!c)
		die();

	strcpy(c, TMP_DIR);
	strcat(c, "/img-");
	strcat(c, id);

	return c;
}

int
main(int argc, char **argv)
{
	char *		sql_host_name = SQL_HOST;
	char *		sql_user_name = SQL_USER;
	char *		sql_password = SQL_PASS;
	char *		sql_db = SQL_DB;
	unsigned int	sql_port = SQL_PORT;
	char *		sql_socket = SQL_SOCKET;
	unsigned int	sql_flags = SQL_FLAGS;

	MYSQL		mysql;

	int		IdPublication = 0;
	int		NrIssue = 0;
	int		NrSection = 0;
	int		NrArticle = 0;
	int		Number = 0;
	int		Language = 0;
	int		sLanguage = 0;
	char *		magic_id = 0;

	struct form_item_t *	fi;
	char *			description = 0;
	char *			photographer = 0;
	char *			place = 0;
	char *			date = 0;
	char *			c;
	char *			e;
	unsigned long		l = 0;
	char			buff[1024];

	if (argc < 2)
		die();

	magic_id = argv[1];
	tmp_path = make_tmp_path(magic_id);

	mysql_init(&mysql);
	if (!mysql_real_connect(&mysql, sql_host_name, sql_user_name, sql_password, sql_db, sql_port, sql_socket, sql_flags))
		die();

	if (!parse_file(tmp_path))
		die();

	fi = get_form_item("cDescription");
	if (fi && fi->content_p) {
		description = get_content(fi);
		l += 3 * fi->content_l;
	}
	if (!description || !*description) {
		description = "None";
		l += 4;
	}

	fi = get_form_item("cPhotographer");
	if (fi && fi->content_p) {
		photographer = get_content(fi);
		l += 3 * fi->content_l;
	}
	if (!photographer)
		photographer = "";

	fi = get_form_item("cPlace");
	if (fi && fi->content_p) {
		place = get_content(fi);
		l += 3 * fi->content_l;
	}
	if (!place)
		place = "";

	fi = get_form_item("cDate");
	if (fi && fi->content_p) {
		date = get_content(fi);
		l += 3 * fi->content_l;
	}
	if (!date || !*date) {
		date = "0000-00-00";
		l += 10;
	}

	fi = get_form_item("Pub");
	if (fi && fi->content_p)
		IdPublication = atoi(fi->content_p);

	fi = get_form_item("Issue");
	if (fi && fi->content_p)
		NrIssue = atoi(fi->content_p);

	fi = get_form_item("Section");
	if (fi && fi->content_p)
		NrSection = atoi(fi->content_p);

	fi = get_form_item("Article");
	if (fi && fi->content_p)
		NrArticle = atoi(fi->content_p);

	fi = get_form_item("cNumber");
	if (fi && fi->content_p)
		Number = atoi(fi->content_p);

	fi = get_form_item("Language");
	if (fi && fi->content_p)
		Language = atoi(fi->content_p);

	fi = get_form_item("sLanguage");
	if (fi && fi->content_p)
		sLanguage = atoi(fi->content_p);
		
		

	fi = get_form_item("cImage");
	if (!fi || !fi->content_type_p || !fi->content_p) {
		fprintf(stderr, "<LI>No content</LI>\n");
		die();
	}

	l += 3 * fi->content_l;
	l += 1024;

	if (!IdPublication || !NrIssue || !NrSection || !NrArticle || !Number || !Language) {
		fprintf(stderr, "<LI>Invalid parameters</LI>\n");
		die();
	}

	c = malloc(l);
	sprintf(buff, "%u", IdPublication);
	e = strmov(c, "INSERT IGNORE INTO Images SET IdPublication=");
	e = strmov(e, buff);
	e = strmov(e, ", NrIssue=");
	sprintf(buff, "%u", NrIssue);
	e = strmov(e, buff);
	e = strmov(e, ", NrSection=");
	sprintf(buff, "%u", NrSection);
	e = strmov(e, buff);
	e = strmov(e, ", NrArticle=");
	sprintf(buff, "%u", NrArticle);
	e = strmov(e, buff);
	e = strmov(e, ", Number=");
	sprintf(buff, "%u", Number);
	e = strmov(e, buff);
	e = strmov(e, ", Description=\"");
	e += mysql_escape_string(e, description, strlen(description));
	e = strmov(e, "\", Photographer=\"");
	e += mysql_escape_string(e, photographer, strlen(photographer));
	e = strmov(e, "\", Place=\"");
	e += mysql_escape_string(e, place, strlen(place));
	e = strmov(e, "\", Date=\"");
	e += mysql_escape_string(e, date, strlen(date));
	e = strmov(e, "\", ContentType=\"");
	e += mysql_escape_string(e, fi->content_type_p, fi->content_type_l);
	e = strmov(e, "\", Image=\"");
	e += mysql_escape_string(e, fi->content_p, fi->content_l);
	e = strmov(e, "\"");

	if (mysql_real_query(&mysql, c, e - c)) {
		fprintf(stderr, "<LI>Query error</LI>\n");
		die();
	}

	printf("<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL="ROOT_DIR"/pub/issues/sections/articles/images/?Pub=%u&Issue=%u&Section=%u&Article=%u&Language=%u&sLanguage=%u\">",
		IdPublication, NrIssue, NrSection, NrArticle, Language, sLanguage);

	fflush(stdout);
	mysql_close(&mysql);
	unlink(tmp_path);

	return 0;
}

char *strmov(char *s, char *x)
{
        if (s == 0)
                return 0;
        if (x == 0)
                return s;
        strcpy(s, x);
        return s + strlen(x);
}
