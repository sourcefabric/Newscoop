#!/bin/sh

################################################################################
#
# CAMPSITE is a Unicode-enabled multilingual web content
# management system for news publications.
# CAMPFIRE is a Unicode-enabled java-based near WYSIWYG text editor.
# Copyright (C)2000,2001  Media Development Loan Fund
# contact: contact@campware.org - http://www.campware.org
# Campware encourages further development. Please let us know.
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
################################################################################

for i in `find /usr/doc -name "*.htm*"`
do
R=$RANDOM$RANDOM$RANDOM$RANDOM$i
echo .
mysql -u root campsite <<EOF
update AutoId set ArticleId=LAST_INSERT_ID(ArticleId + 1);
insert into Articles set IdPublication=1, NrIssue=1, NrSection=1, Number=LAST_INSERT_ID(), IdLanguage=1, Name='testam$R', Type='art', Published='Y';
insert into Xart set NrArticle=LAST_INSERT_ID(), IdLanguage=1, Ftitle='title', Fbody='`cat $i | tr \' x`';
EOF
done
