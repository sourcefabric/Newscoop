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
 * parse_file.h
 */
#ifndef TOL_PARSE_FILE_H__
#define TOL_PARSE_FILE_H__

struct form_item_t {
	char *			name_p;
	off_t			name_l;
	char *			file_name_p;
	off_t			file_name_l;
	char *			content_type_p;
	off_t			content_type_l;
	char *			content_p;
	off_t			content_l;
	struct form_item_t *	next;
};

extern int parse_file(char *file_name);
extern struct form_item_t *get_form_item(char *name);
extern char *get_file_name(struct form_item_t *fi);
extern char *get_content(struct form_item_t *fi);
extern void dump_info();

#endif
