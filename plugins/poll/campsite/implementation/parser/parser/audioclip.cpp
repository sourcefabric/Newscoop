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

#include "audioclip.h"
#include "util.h"


CNamespaces::CNamespaces()
{
	insert("dc");
	insert("ls");
	insert("dcterms");
}


bool CNamespaces::validNamespace(string& p_rcoNamespace) const
{
	string::size_type nSeparator = p_rcoNamespace.find(':');
	if (nSeparator != string::npos)
	{
		p_rcoNamespace = p_rcoNamespace.substr(0, nSeparator);
	}
	return find(p_rcoNamespace) != end();
}


CMetatagNames::CMetatagNames()
{
	insert(value_type("Title", "dc:title"));
	insert(value_type("Creator", "dc:creator"));
	insert(value_type("Genre", "dc:type"));
	insert(value_type("Length", "dcterms:extent"));
	insert(value_type("Year", "ls:year"));
	insert(value_type("Bitrate", "ls:bitrate"));
	insert(value_type("Samplerate", "ls:samplerate"));
	insert(value_type("Album", "dc:source"));
	insert(value_type("Description", "dc:description"));
	insert(value_type("Format", "dc:format"));
	insert(value_type("Label", "dc:publisher"));
	insert(value_type("Composer", "ls:composer"));
	insert(value_type("Channels", "ls:channels"));
	insert(value_type("Rating", "ls:rating"));
	insert(value_type("TrackNum", "ls:track_num"));
	insert(value_type("DiskNum", "ls:disc_num"));
	insert(value_type("Lyrics", "ls:lyrics"));
	insert(value_type("Copyright", "dc:rights"));
}


bool CMetatagNames::validMetatagName(const string& p_rcoMetatagName) const
{
	return find(p_rcoMetatagName) != end();
}


string CAudioclip::s_coEmptyValue = "";
CNamespaces CAudioclip::s_coValidNamespaces;
CMetatagNames CAudioclip::s_coMetadataTagNames;


bool CAudioclip::fetch(const string& p_rcoGunId)
{
	if (p_rcoGunId == "")
	{
		return false;
	}
	char* pchVal = SQLEscapeString(p_rcoGunId.c_str(), p_rcoGunId.length());
	if (pchVal == NULL)
	{
		return false;
	}
	string coSql = string("select predicate_ns, predicate, object ")
			+ "from AudioclipMetadata where gunid = '" + pchVal + "'";
	delete []pchVal;
	CMYSQL_RES coRes;
	MYSQL_ROW pRow = QueryFetchRow(MYSQLConnection(), coSql, coRes);
	if (pRow == NULL)
	{
		return false;
	}
	m_coMetadata.clear();
	m_coGunId = p_rcoGunId;
	m_bExists = true;
	while (pRow != NULL)
	{
		string coTag = string(pRow[0]) + ":" + pRow[1];
		m_coMetadata[coTag] = pRow[2];
		pRow = mysql_fetch_row(*coRes);
	}
	return true;
}

const string& CAudioclip::getMetatagValue(const string& p_rcoTagName) const
		throw(InvalidValue)
{
	CMetatagNames::const_iterator coIt1 = s_coMetadataTagNames.find(p_rcoTagName);
	if (coIt1 == s_coMetadataTagNames.end())
	{
		throw InvalidValue("meta tag", p_rcoTagName);
	}
	String2String::const_iterator coIt2 = m_coMetadata.find((*coIt1).second);
	if (coIt2 != m_coMetadata.end())
	{
		return (*coIt2).second;
	}
	return s_coEmptyValue;
}
