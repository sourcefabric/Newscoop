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
 * ls_url.c
 *
 * usage : ls_url d|f <path1> <path2>
 */
#include <stdio.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <dirent.h>
#include <unistd.h>
#include <stdlib.h>
#include <string.h>
#include <errno.h>
#include "url_util.h"


int
main(int argc, char **argv)
{
	char *path, *p;
	enum { FILES, DIRECTORIES } entry_type;
	struct dirent **de;
	struct stat s;
	int de_nr, index;

	if (argc < 4) {
		fprintf(stderr, "ls_usl: called with wrong arguments\n");
		return 1;
	}

	entry_type = (argv[1][0] == 'd') ? DIRECTORIES : FILES;
	path = malloc(strlen(argv[2]) + strlen(argv[3]) + 2);
	if (!path) {
		errno=ENOMEM;
		perror("malloc");
		return 1;
	}
	strcpy(path, argv[2]);
	strcat(path, "/");
	strcat(path, argv[3]);

	de_nr = scandir(path, &de, 0, alphasort);
	if (de_nr == -1) {
		perror("scandir");
		return 1;
	}

	index = -1;
	while (++index < de_nr) {
		if (de[index]->d_name[0] == '.')
			continue;

		p = malloc(strlen(path) + strlen(de[index]->d_name) + 2);
		if (!p) {
			errno=ENOMEM;
			perror("malloc");
			return 1;
		}

		strcpy(p, path);
		strcat(p, "/");
		strcat(p, de[index]->d_name);

		if (stat(p, &s) != 0) {
			perror("stat");
			return 1;
		}

		if (S_ISDIR(s.st_mode)) {
			if (entry_type == FILES)
				continue;
		} else
			if (entry_type == DIRECTORIES)
				continue;

		printf("%s\n%s\n", de[index]->d_name, escape_url(de[index]->d_name));
		fflush(stdout);
	}

	return 0;
}
