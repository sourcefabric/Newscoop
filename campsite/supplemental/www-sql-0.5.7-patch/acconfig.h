/*
     WWW-SQL - parses HTML files and inserts information from MySQL databases
    Copyright (C) 1997  James Henstridge <james@daa.com.au>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 */

#ifndef __CONFIG_H__
#define __CONFIG_H__
@TOP@

/* define this to the version string for this program */
#undef VERSION

/* define this if you want to enable www-sql's recursive extensions */
#undef RECURSIVE

/* define this if you are using apache web server, and you want to force
   people to use www-sql as an action handler (thus blocking a security
   hole) */
#undef ONLY_APACHE_ACTION

/* This define enables some unsafe functions such as exec in www-sql.  It
   also relaxes the checking for the include statement. */
#undef ENABLE_UNSAFE

/* define this if you are compiling with the new scanner module */
#undef NEW_SCANNER

@BOTTOM@
#endif /* __CONFIG_H__ */
