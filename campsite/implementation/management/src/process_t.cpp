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
 * process_t.c
 */
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <stdlib.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>

static char *tmp_path = 0;

#include "parse_file.h"
#include "url_util.h"
#include "dir_conf.h"

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
	char *c = (char*)malloc(strlen(TMP_DIR) + strlen(id) + 6);
	if (!c)
		die();

	strcpy(c, TMP_DIR);
	strcat(c, "/tpl-");
	strcat(c, id);

	return c;
}

int
main(int argc, char **argv)
{

	struct form_item_t *	fi;
	char *			path = 0;
	char *			fname = 0;
	char *			doc_root;
	char *			fn = 0;
	char *			magic_id = 0;
	int			fd;

	if (argc < 2)
		die();

	magic_id = argv[1];
	tmp_path = make_tmp_path(magic_id);

	if (!parse_file(tmp_path))
		die();

	fi = get_form_item("Path");
	if (fi && fi->content_p) {
		path = get_content(fi);
	}

	if (!path || !*path) {
		fprintf(stderr, "<LI>No path given.</LI>\n");
		die();
	}

	fi = get_form_item("File");
	if (!fi) {
		fprintf(stderr, "<LI>No content.</LI>\n");
		die();
	}

	fname = get_file_name(fi);
	if (!fname || !*fname) {
		fprintf(stderr, "<LI>No file name given.</LI>\n");
		die();
	}

	doc_root = getenv("DOCUMENT_ROOT");
	if (!doc_root) {
		fprintf(stderr, "<LI>No DOCUMENT_ROOT.</LI>\n");
		die();
	}

	fn = (char*)malloc(strlen(doc_root) + strlen(path) + strlen(fname) + 3);
	if (!fn) {
		fprintf(stderr, "<LI>Out of memory.</LI>\n");
		die();
	}

	strcpy(fn, doc_root);
	strcat(fn, "/");
	strcat(fn, path);
	strcat(fn, "/");
	strcat(fn, fname);


	fd = open(fn, O_WRONLY | O_CREAT | O_TRUNC, S_IRUSR | S_IWUSR | S_IRGRP | S_IWGRP);
	if (fd == -1) {
		fprintf(stderr, "<LI>Cannot create file %s: %m</LI>\n", fn);
		die();
	}

	if (write(fd, fi->content_p, fi->content_l) == -1) {
		fprintf(stderr, "<LI>Cannot write file %s: %m</LI>\n", fn);
		die();
	}

	close(fd);

	printf("<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL="ROOT_DIR"/templates/?Path=%s\">",
               escape_url((unsigned char*)path));
	unlink(tmp_path);

	fflush(stdout);

	return 0;
}
