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

# update Events table data
UPDATE Events SET IdLanguage = 1;
INSERT INTO Events VALUES (113,'Edit template','N',1);
INSERT INTO Events VALUES (114,'Create template','N',1);
INSERT INTO Events VALUES (115,'Duplicate template','N',1);
INSERT INTO Events VALUES (141,'Add topic','N',1);
INSERT INTO Events VALUES (142,'Delete topic','N',1);
INSERT INTO Events VALUES (143,'Update topic','N',1);
INSERT INTO Events VALUES (144,'Add topic to article','N',1);
INSERT INTO Events VALUES (145,'Delete topic from article','N',1);

# update UserPerm table data
UPDATE UserPerm SET ManageLocalizer = 'Y', ManageIndexer = 'N', Publish = 'Y', ManageTopics = 'Y' WHERE IdUser = 1;

# update UserTypes table data
UPDATE UserTypes SET ManageLocalizer = 'Y', ManageIndexer = 'N', Publish = 'Y', ManageTopics = 'Y' WHERE Name = 'Administrator';
UPDATE UserTypes SET ManageLocalizer = 'N', ManageIndexer = 'N', Publish = 'N', ManageTopics = 'N' WHERE Name = 'Editor';
