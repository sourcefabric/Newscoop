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


#include "curl.h"


// readQueryString(): internal method; reads the parameters from the query string
void CURL::readQueryString(const string& p_rcoQueryString)
{
	if (p_rcoQueryString == "")
		return;

	string::size_type nStart = 0;
	while (true)
	{
		// read the parameter name
		string::size_type nIndex = p_rcoQueryString.find('=', nStart);
		if (nIndex == string::npos)
			break;
		string coParam = p_rcoQueryString.substr(nStart, nIndex - nStart);

		// read the parameter value
		nStart = nIndex + 1;
		nIndex = p_rcoQueryString.find("&", nStart);
		nIndex = nIndex == string::npos ? p_rcoQueryString.size() : nIndex;
		string coValue = p_rcoQueryString.substr(nStart, nIndex - nStart);

		// set the parameter value in the parameter map
		setValue(coParam, coValue);

		// prepare for the next iteration
		nStart = nIndex + 1;
		if (nStart >= p_rcoQueryString.size())
			break;
	}
}
