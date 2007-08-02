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

#ifndef AUDIOCLIP_H
#define AUDIOCLIP_H

#include "cms_types.h"
#include "globals.h"


class CNamespaces : public StringSet
{
	public:
		CNamespaces();
		bool validNamespace(string& p_rcoNamespace) const;
};


class CMetatagNames : public String2String
{
	public:
		CMetatagNames();
		bool validMetatagName(const string& p_rcoMetatagName) const;
};


class CAudioclip
{
public:
	CAudioclip(const string& p_rcoGunId = string(""))
	{
		fetch(p_rcoGunId);
	}

	bool fetch(const string& p_rcoGunId = string(""));

	const string& getGunId() const
	{
		return m_coGunId;
	}

	const string& getMetatagValue(const string& p_rcoTagName) const throw(InvalidValue);

	static bool IsValidNamespace(string& p_rcoNamespace)
	{
		return s_coValidNamespaces.validNamespace(p_rcoNamespace);
	}

	bool exists() const
	{
		return m_bExists;
	}

	bool operator == (const CAudioclip& p_rcoSource) const
	{
		return m_bExists == p_rcoSource.m_bExists
				&& m_coGunId == p_rcoSource.m_coGunId
				&& m_coMetadata == p_rcoSource.m_coMetadata;
	}

	bool operator != (const CAudioclip& p_rcoSource) const
	{
		return !(*this == p_rcoSource);
	}

	static const CNamespaces& GetNamespaces()
	{
		return s_coValidNamespaces;
	}

private:
	static string s_coEmptyValue;
	static CNamespaces s_coValidNamespaces;
	static CMetatagNames s_coMetadataTagNames;
	String2String m_coMetadata;
	string m_coGunId;
	bool m_bExists;
};

#endif
