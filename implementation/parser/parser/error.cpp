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

Implementation of CError class

******************************************************************************/

#include "error.h"

pthread_once_t CError::m_InitControl = PTHREAD_ONCE_INIT;
Int2String CError::messages;
Int2String CError::modes;
const string CError::req_word = "required";
const string CError::line_word = "Line";
const string CError::column_word = "Column";
const string CError::when_word = "When";
const string CError::error_word = "Error";

// Initialise messages and modes
void CError::init()
{
	// generic errors
	messages[ERR_NOTYPE] = "There is no such type of attribute.";
	messages[ERR_NOMEM] = "Error trying to alloc memory.";
	messages[ERR_SQLCONNECT] = "Error connecting to MYSQL server.";
	messages[ERR_QUERY] = "Internal error (query)";
	messages[ERR_NODATA] = "Required data doesn't exist in database";
	messages[ERR_NOHASH] = "";
	messages[ERR_NOHASHENT] = "";
	messages[ERR_NOOP] = "";
	messages[ERR_NOPARAM] = "";
	messages[ERR_NOLISTINDEX] = "";
	messages[ERR_NOACCESS] = "";
	messages[ERR_INVALID_FIELD] = "";
	messages[ERR_NOKEY] = "User key missing.";
	messages[ERR_LOCKHASH] = "Error locking/unlocking parser hash.";
	// parser errors
	messages[PERR_ATOM_MISSING] = "internal parser error: atom missing "
	                              "(report bug to http://bugs.campware.org)";
	messages[PERR_STATEMENT_MISSING] = "statement missing";
	messages[PERR_ATOM_NOT_STATEMENT] = "found atom, need statement";
	messages[PERR_IDENTIFIER_MISSING] = "missing identifier";
	messages[PERR_WRONG_STATEMENT] = "this statement is not allowed here";
	messages[PERR_NO_ATOM_IN_LEXEM] = "internal parser error: no atom in lexem "
	                                  "(report bug to http://bugs.campware.org)";
	messages[PERR_END_STATEMENT_MISSING] = "found identifier; need end statement";
	messages[PERR_NOT_VALUE] = "need value, not statement";
	messages[PERR_INVALID_ATTRIBUTE] = "not a valid attribute";
	messages[PERR_INVALID_STATEMENT] = "not a valid statement";
	messages[PERR_INVALID_OPERATOR] = "not a valid operator";
	messages[PERR_EOS_MISSING] = "end of statement mising";
	messages[PERR_DATA_TYPE] = "invalid value type";
	messages[PERR_ATTRIBUTE_REDEF] = "attribute was already defined";
	messages[PERR_INV_TYPE_VAL] = "invalid value of type attribute";
	messages[PERR_UNEXPECTED_EOF] = "Unexpected end of file."
	        " Check for missing EndIf or EndList";
	messages[PERR_INVALID_VALUE] = "invalid value";
	messages[PERR_INCLUDE_CICLE] = "include cicle: template "
	        "already included from this/other templates; ignoring";
	messages[PERR_INVALID_DATE_FORM] = "invalid date form; "
	        "valid combinations: %M (Month name), %W (Weekday name), %Y (Year, numeric, "
	        "4 digits), %y (Year, numeric, 2 digits), %m (Month, numeric: 01..12), %c (Month, "
	        "numeric: 1..12), %d (Day of the month, numeric: 00..31), %e (Day of the month, "
	        "numeric: 0..31), %j (Day of year: 001..366), %D (Day of the month with "
	        "english suffix: 1st, 2nd, 3rd, etc.), %w (Day of the week, numeric: 0-7),"
	        " %% (% character)";
	messages[PERR_INVALID_TEMPLATE] = "invalid template specified";
	messages[PERR_INTERNAL] = "parser internal error (report bug to http://bugs.campware.org)";
	// map errors
	messages[EMAP_STAT] = "Unable to stat template file";
	messages[EMAP_NOTREGFILE] = "Not a regular file";
	messages[EMAP_EOPENFILE] = "Unable to open template file";
	messages[EMAP_FAILED] = "Unable to map file into memory";
	// define modes
	modes[MODE_PARSE] = "parsing";
	modes[MODE_WRITE] = "writing output";
}

// Constructor
// Parameters:
//	int p_nCode - error code
//	int p_nMode - mode (parsing/writing output)
//	string p_coRequired - on parsing, required tokens
//	lint p_nRow - on parsing, the row where the error occured
//	lint p_nColumng - on parsing, the column where the error occured
CError::CError(int p_nCode, int p_nMode, string p_coRequired,
			   lint p_nRow, lint p_nColumn)
{
	code = p_nCode;
	mode = p_nMode;
	required = p_coRequired;
	row = p_nRow;
	column = p_nColumn;
	pthread_once(&m_InitControl, init);
}

// Print: print the error
// Parameters:
//	fstream& fs - the stream to print the error to
//	bool p_bPrintContext - if true print mode (parsing/writing output)
sockstream& CError::Print(sockstream& fs, bool p_bPrintContext)
{
	Int2String::iterator i2s_i1;
	Int2String::iterator i2s_i2;
	fs << "<font color=red>" << error_word << ' ' << code << ": ";
	i2s_i1 = messages.find(code);
	if (i2s_i1 != messages.end())
	{
		if (row > 0)
		{
			fs << line_word << " " << row;
			if (column > 0)
				fs << ',' << column_word << ' ' << column;
			fs << "; ";
		}
		if (p_bPrintContext)
		{
			i2s_i2 = modes.find(mode);
			if (i2s_i2 != modes.end())
				fs << when_word << ' ' << (*i2s_i2).second << ", ";
		}
		fs << (*i2s_i1).second << "; ";
		if (strlen(required.c_str()))
			if (mode == MODE_PARSE)
				fs << req_word << ": " << required << ';';
			else
				fs << "(" << required << ");";
	}
	fs << "</font>\\\n<br>";
	return fs;
}
