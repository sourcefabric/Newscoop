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
 * url_util.c
 */
#include <stdlib.h>
#include <string.h>

#include "url_util.h"

static char url_ok_chars[] = "*-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz";
static char hex[16] = "0123456789ABCDEF";

char *
escape_url(unsigned char *str)
{
	unsigned char *p, *q, *c;

	if (!str)
		return 0;
	
	p = malloc(strlen(str) * 2 + 1);
	if (!p)
		return 0;

	q = p;

	for (c = str; *c; c++, q++)
		if (*c == ' ')
			*q = '+';
		else if (strchr(url_ok_chars, *c))
			*q = *c;
		else {
			*q++ = '%';
			*q++ = hex[*c >> 4];
			*q = hex[*c & 0x0F];
		}
	*q = 0;
	
	return p;
}

char *
escape_html(unsigned char *str)
{
	unsigned char *p, *q, *c;
	
	if (!str)
		return 0;
		
	p = malloc(strlen(str) * 6 + 1);
	if (!p)
		return 0;
	q = p;

	for (c = str; *c; c++, q++)
		switch (*c) {
		case '<':
			strcpy(q, "&lt;");
			q += 3;
			break;
		case '>':
			strcpy(q, "&gt;");
			q += 3;
			break;
		case '"':
			strcpy(q, "&quot;");
			q += 5;
			break;
		case '&':
			strcpy(q, "&amp;");
			q += 4;
			break;
		case '\n':
			if (c != str && c[-1] == '\r') {
				break;
			}
		case '\r':
			strcpy(q, "<BR>\r");
			q += 4;
			break;
		default:
			*q = *c;
		}

	*q = 0;
	
	return p;
}
