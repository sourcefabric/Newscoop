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
 * parse_file.c
 */
#include <sys/types.h>
#include <sys/stat.h>
#include <sys/mman.h>
#include <fcntl.h>
#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include "parse_file.h"

#define debug	if(0)printf

static struct form_item_t* 	form_items;
static char *			bound_p;
static off_t 			bound_l;

static char *
find_short_str(char *bp, off_t bl, char *sp, off_t sl)
{
	int	i, f;
	off_t	len = bl - sl;

	if (bl < sl)
		return 0;

	do {
		f = 1;
		for (i = 0; i < sl; i++)
			if (bp[i] != sp[i]) {
				f = 0;
				break;
			}
		if (f)
			break;
		bp++;
	} while (len-- > 0);
	
	return f ? bp : 0;
}

inline static char *
find_long_str(char *bp, off_t bl, char *sp, off_t sl)
{
	return find_short_str(bp, bl, sp, sl);
}

static char *
find_nlnl(char *bp, off_t bl)
{
	while (bl > 3) {
		if (bp[0] == 0x0d && bp[1] == 0x0a && bp[2] == 0x0d && bp[3] == 0x0a)
			return bp;
		bl--;
		bp++;
	}
	return 0;
}

static char *
find_nl(char *bp, off_t bl)
{
	while (bl > 1) {
		if (bp[0] == 0x0d && bp[1] == 0x0a)
			return bp;
		bl--;
		bp++;
	}
	return 0;
}

static int
find_boundary(char **bp, off_t *bl)
{
	char *p;

	debug("find_boundary: begin: buffer is <%.20s>, len=%lu\n", *bp, *bl);

	p = find_long_str(*bp, *bl, bound_p, bound_l);
	if (!p)
		return 0;

	*bl -= p - *bp;
	*bp = p;

	debug("find_boundary: end: buffer is <%.20s>, len=%lu\n", *bp, *bl);

	return 1;
}

/*
 * -1 = error
 *  0 = ok + eof 
 *  1 = ok + more
 */
static int
parse_entry(char **bp, off_t *bl)
{
	struct form_item_t *	fi;
	int 			e;
	char *			c;
	char *			d;
	off_t			l;
	char *			ep = *bp;
	off_t 			el;
	char *			cp;
	off_t			cl;
	char *			hp;
	off_t			hl;

	debug("parse_entry: begin: buffer is <%.20s>, len=%lu\n", *bp, *bl);

	e = find_boundary(bp, bl);
	if (e == -1)
		return -1;

	el = *bp - ep;

	debug("parse_entry: ENTRY: buffer is <%.20s>, len=%lu\n", ep, el);

	cp = find_nlnl(ep, el);
	if (cp) {
		cl = *bp - cp;
		hp = ep;
		hl = cp - hp;
		cp += 4;
		cl = *bp - cp;
		debug("parse_entry: HEAD: buffer is <%.20s>, len=%lu\n", hp, hl);
		debug("parse_entry: CONTENT: buffer is <%.20s>, len=%lu\n", cp, cl);

		fi = (form_item_t*)malloc(sizeof(struct form_item_t));
		if (!fi) {
			fprintf(stderr, "<LI>Out of memory</LI>\n");
			return -1;
		}

		fi->content_p = cp;
		fi->content_l = cl;
		fi->next = form_items;
		form_items = fi;

		l = 0;
		c = find_short_str(hp, hl, "name=\"", 6);
		if (c) {
			c += 6;
			d = find_short_str(c, hl - 6, "\"", 1);
			if (d)
				l = d - c;
			else
				c = 0;
		}
		if (c) {
			debug("parse_header: found name <%.20s>, len=%lu\n", c, l);
		} else {
			debug("parse_header: no name found\n");
		}
		fi->name_p = c;
		fi->name_l = l;

		l = 0;
		c = find_short_str(hp, hl, "filename=\"", 10);
		if (c) {
			c += 10;
			d = find_short_str(c, hl - 10, "\"", 1);
			if (d)
				l = d - c;
			else
				c = 0;
		}
		if (c) {
			debug("parse_header: found filename <%.20s>, len=%lu\n", c, l);
		} else {
			debug("parse_header: no filename found\n");
		}
		fi->file_name_p = c;
		fi->file_name_l = l;

		l = 0;
		c = find_short_str(hp, hl, "Content-Type: ", 14);
		if (c) {
			c += 14;
			d = find_nl(c, hl - 14);
			if (d)
				l = d - c;
			else
				c = 0;
		}
		if (c) {
			debug("parse_header: found content-type <%.20s>, len=%lu\n", c, l);
		} else {
			debug("parse_header: no content-type found\n");
		}
		fi->content_type_p = c;
		fi->content_type_l = l;
	}

	return e;
}

static int
parse_buffer(char *bp, off_t bl)
{
	char *	p;
	off_t	l;
	int	r;

	form_items = 0;

	p = find_nl(bp, bl);
	if (!p) {
		fprintf(stderr, "<LI>Parse error: Cound not find end of form</LI>\n");
		return 0;
	}

	l = p - bp;
	bound_p = (char*)malloc(l + 2);
	if (!bound_p) {
		fprintf(stderr, "<LI>Out of memory</LI>\n");
		return 0;
	}

	bound_l = l;
	bound_p[0] = '\r';
	bound_p[1] = '\n';
	memcpy(&bound_p[2], bp, bound_l);
	bound_l += 2;

	bp = p + 2;
	bl -= bound_l;

	debug("parse_buffer: boundary found <%s>, len=%lu\n", bound_p, bound_l);
	debug("parse_buffer: buffer is <%.20s>, len=%lu\n", bp, bl);

	do {
		while (bl && (*bp == 0x0a || *bp == 0x0d)) {
			bp++;
			bl--;
		}
		r = parse_entry(&bp, &bl);
		bp += bound_l;
		bl -= bound_l;
	} while (r == 1);
	return ! (-r);

	fprintf(stderr, "<LI>Parse error: Cound not find starting boundary</LI>\n");
	return 0;
}

int
parse_file(char *file_name)
{
	int	fd;
	char *	bp;
	off_t	bl;

	fd = open(file_name, O_RDONLY);
	if (fd == -1) {
		fprintf(stderr, "<LI>Could not open file %s: %m</LI>\n", file_name);
		return 0;
	}

	bl = lseek(fd, 0, SEEK_END);
	if (bl == (off_t)(-1)) {
		fprintf(stderr, "<LI>Could not seek file %s: %m</LI>\n", file_name);
		return 0;
	}

	if (!bl)
		return 1;

	bp = (char*)mmap(0, bl, PROT_READ, MAP_SHARED, fd, 0);
	if (bp == MAP_FAILED) {
		fprintf(stderr, "<LI>Could not mmap file %s: %m</LI>\n", file_name);
		return 0;
	}

	return parse_buffer(bp, bl);
}

char *
get_file_name(struct form_item_t *fi)
{
	char *c, *d;

	c = (char*)malloc(fi->file_name_l + 1);
	if (!c) {
		fprintf(stderr, "<LI>Out of memory</LI>\n");
		return 0;
	}

	memcpy(c, fi->file_name_p, fi->file_name_l);
	c[fi->file_name_l] = 0;

	d = strrchr(c, '/');
	if (d)
		c = d + 1;

	d = strrchr(c, '\\');
	if (d)
		c = d + 1;

	return c;
}

char *
get_content(struct form_item_t *fi)
{
	char *c = (char*)malloc(fi->content_l + 1);
	if (!c) {
		fprintf(stderr, "<LI>Out of memory</LI>\n");
		return 0;
	}

	memcpy(c, fi->content_p, fi->content_l);
	c[fi->content_l] = 0;

	return c;
}

struct form_item_t *
get_form_item(char *name)
{
	off_t l = strlen(name);
	struct form_item_t *fi;

	for (fi = form_items; fi; fi = fi->next)
		if (fi->name_l == l && fi->name_p && !memcmp(name, fi->name_p, l))
			return fi;

	return 0;
}

void
dump_info()
{
	struct form_item_t *fi;

	for (fi = form_items; fi; fi = fi->next) {
		printf("=========== FORM ITEM AT %p ===========\n", fi);
	
		if (fi->name_p)
			printf("        Name : %.*s (len = %lu)\n",
				(int)fi->name_l, fi->name_p, fi->name_l);
		else
			printf("        Name : <none>\n");

		if (fi->file_name_p)
			printf("   File-Name : %.*s (len = %lu)\n",
				(int)fi->file_name_l, fi->file_name_p, fi->file_name_l);
		else
			printf("   File-Name : <none>\n");

		if (fi->content_type_p)
			printf("Content-Type : %.*s (len = %lu)\n",
				(int)fi->content_type_l, fi->content_type_p, fi->content_type_l);
		else
			printf("Content-Type : <none>\n");

		if (fi->content_p)
			printf("     Content : (len = %lu)\n%.*s\n",
				fi->content_l, (int)fi->content_l, fi->content_p);
		else
			printf("    Content : <none>\n");
	}
}
