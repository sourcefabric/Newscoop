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

Implementation of the classes defined in readconf.h

******************************************************************************/

#include <fstream>

#include "readconf.h"

using std::fstream;
using std::ios;

#define READ_ATTR 0
#define READ_VAL 1

void ConfAttrValue::Open(string p_rcoConfFileName) throw (ConfException)
{
	fstream coConfFile(p_rcoConfFileName.c_str(), ios::in);
	if (!coConfFile.is_open())
	{
		static string coErr;
		coErr = string("Unable to open configuration file: ") + p_rcoConfFileName;
		throw ConfException(coErr.c_str());
	}
	while (!coConfFile.eof())
	{
		int nState = READ_ATTR;
		string coLastAttr;
		string coLine;
		string coWord;
		int nIndex = 0;
		getline(coConfFile, coLine);
		while ((coWord = ReadWord(coLine, nIndex)) != "")
		{
			if (nState == READ_ATTR)
			{
				coLastAttr = coWord;
				m_coAttrMap[coLastAttr] = "";
				nState = READ_VAL;
			}
			else
			{
				if (m_coAttrMap[coLastAttr] != "")
					m_coAttrMap[coLastAttr] += "";
				m_coAttrMap[coLastAttr] += coWord;
			}
		}
	}
}

const string& ConfAttrValue::ValueOf(string p_rcoAttribute) const throw (ConfException)
{
	map<string, string, str_case_less>::const_iterator coAttrIt = m_coAttrMap.find(p_rcoAttribute);
	if (coAttrIt == m_coAttrMap.end())
	{
		static string coErr;
		coErr = string("Invalid attribute name: ") + p_rcoAttribute;
		throw ConfException(coErr.c_str());
	}
	return (*coAttrIt).second;
}

string ConfAttrValue::ReadWord(string& p_rcoLine, int& p_rnIndex)
{
	const char* pchStr = p_rcoLine.c_str();
	int nStrLen = strlen(pchStr);
	if (nStrLen <= p_rnIndex)
		return "";
	int nStartIndex = p_rnIndex;
	while (nStartIndex <= nStrLen && isDel(pchStr[nStartIndex]))
		nStartIndex++;
	if (nStartIndex == nStrLen)
		return "";
	p_rnIndex = nStartIndex;
	while (p_rnIndex <= nStrLen && !isDel(pchStr[p_rnIndex]))
		p_rnIndex++;
	return p_rcoLine.substr(nStartIndex, p_rnIndex++ - nStartIndex);
}

bool ConfAttrValue::isDel(char p_chChar)
{
	if (p_chChar <= ' ' || p_chChar == ',')
		return true;
	return false;
}
