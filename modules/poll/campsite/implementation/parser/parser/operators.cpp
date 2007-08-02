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

#include "operators.h"

string* CCompOperator::m_pcoEqual;
string* CCompOperator::m_pcoNotEqual;
string* CCompOperator::m_pcoGreater;
string* CCompOperator::m_pcoGreaterEqual;
string* CCompOperator::m_pcoLess;
string* CCompOperator::m_pcoLessEqual;

string* CCompOperator::m_pcoEqualSymbol;
string* CCompOperator::m_pcoNotEqualSymbol;
string* CCompOperator::m_pcoGreaterSymbol;
string* CCompOperator::m_pcoGreaterEqualSymbol;
string* CCompOperator::m_pcoLessSymbol;
string* CCompOperator::m_pcoLessEqualSymbol;
