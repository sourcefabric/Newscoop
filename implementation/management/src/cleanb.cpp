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
 * clean.c		-- delete the blob files --
 */
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <stdlib.h>

#include "parse_file.h"
#include "dir_conf.h"

unsigned
get_qs_u(char *qs, char *name)
{
	char *p;
	int l;

	if (!qs || !name)
		return 0;

	l = strlen(name);
	p = strstr(qs, name);
	if (!p)
		return 0;

	p += l + 1;
	return atoi(p);
}


static void
die()
{
        printf("<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=%s\">\n", getenv("HTTP_REFERER"));
        exit(1);
}

int
main(int argc, char **argv)
{
	int             IdPublication = 0;
        int             NrIssue = 0;
        int             NrSection = 0;
        int             NrArticle = 0, Article=0;
        int             Number = 0;
        int             Language = 0;
        int             sLanguage = 0;

	char *                  c, *qs;

	printf("Content-type: text/html%c%c", 10,10);
	    qs = getenv("QUERY_STRING");
	            if (!qs) return 1;
	IdPublication = get_qs_u(qs, "Pub");
        NrIssue = get_qs_u(qs, "Issue");
        NrSection = get_qs_u(qs, "Section");
        Article = get_qs_u(qs, "Article");
        Number = get_qs_u(qs, "Number");
	Language = get_qs_u(qs, "Language");
	sLanguage = get_qs_u(qs, "sLanguage");
	NrArticle = get_qs_u(qs, "NrArticle");


	if (!IdPublication || !NrIssue || !NrSection || !NrArticle || !Number || !Language || !sLanguage || !Article) {
		fprintf(stderr, "<LI>Invalid parameters</LI>\n");
		die();
	}

	c = (char*)malloc(1024);
	sprintf(c, "/tmp/blob%u%u", NrArticle, Number);

	printf("<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL="ROOT_DIR"/pub/issues/sections/articles/images/?Pub=%u&Issue=%u&Section=%u&Article=%u&Language=%u&sLanguage=%u\">",
		IdPublication, NrIssue, NrSection, NrArticle, Language, sLanguage);
	fflush(stdout);
	unlink(c);
	return 0;
}
