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

/******************************************************************************

Classes used for reading and working with cgi environment

******************************************************************************/

#ifndef _CGI_H
#define _CGI_H

#include "cgib.h"

// CGI class: read cgi environment and supplies environment variables (see also CGIBase)
class CGI: public CGIBase
{
public:
	CGI(const char* p_pchMethod = NULL, const char* p_pchQuery = NULL)
			: CGIBase(p_pchMethod, p_pchQuery)
	{}
	virtual ~CGI()
	{}

	const char* GetRemoteHost();
	const char* GetRemoteAddress();
	const char* GetPathInfo();
	const char* GetFirst(const char* p_pchName)
	{
		return HT.GetFirstValue(p_pchName);
	}
	const char* GetNext(const char* p_pchName)
	{
		return HT.GetNextValue(p_pchName);
	}
	bool GetNextParameter(const char** p_ppchName, const char** p_ppchValue);
	void ResetIterator();
	void ShowData();
};

#endif
