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

#ifndef _SQL_MACROS_H
#define _SQL_MACROS_H

#define RES_OK 0
#define ERR_NOTYPE 1
#define ERR_NOMEM 2
#define ERR_SQLCONNECT 3
#define ERR_QUERY 4
#define ERR_NODATA 5
#define ERR_NOHASH 6

#define SQLConnect(sql)\
int c_res;\
if ((c_res = SQLConnection(sql)) != RES_OK)\
return c_res;

#define SQLQuery(sql, buf)\
if (sql == NULL)\
return ERR_SQLCONNECT;\
if (mysql_query(sql, buf))\
return ERR_QUERY;

#define SQLRealQuery(sql, buf, len)\
if (mysql_real_query(sql, buf, len))\
return ERR_QUERY;

#define StoreResult(sql, res)\
MYSQL_RES* res = mysql_store_result(sql);\
if (res == NULL)\
return ERR_NOMEM;

#define CheckForRows(res, num)\
if (mysql_num_rows(res) < num) {\
return ERR_NODATA;\
}

#define FetchRow(res, row)\
MYSQL_ROW row = mysql_fetch_row(res);\
if (row == NULL)\
return ERR_NOMEM;

#endif
