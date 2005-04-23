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

Implementation of Item, SimpleList, HashTable, CGIBase and CGI classes

******************************************************************************/

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <iostream>

#include "cgi.h"

using std::cout;

#define LF 10
#define CR 13

void SimpleList::AddItem(const char* p_pchName, const char* p_pchVal)
{
	Item* pcoNode = new Item(p_pchName, p_pchVal);
	pcoNode->SetNext(0);
	if (!Hd)
	{
		Hd = Tl = Crt = pcoNode;
	}
	else
	{
		Tl->SetNext(pcoNode);
		Tl = pcoNode;
	}
}

const char* SimpleList::GetFirstValue(const char* p_pchName)
{
	Crt = Hd;
	while (Crt != NULL && strcmp(Crt->GetName(), p_pchName) != 0)
		Crt = Crt->GetNext();
	if (Crt)
		return Crt->GetValue();
	return NULL;
}

const char* SimpleList::GetNextValue(const char* p_pchName)
{
	if (Crt == NULL)
		return NULL;
	Crt = Crt->GetNext();
	while (Crt != NULL && strcmp(Crt->GetName(), p_pchName) != 0)
		Crt = Crt->GetNext();
	if (Crt)
		return Crt->GetValue();
	return NULL;
}

bool SimpleList::GetNextItem(const char** p_ppchName, const char** p_ppchValue)
{
	if (Crt == NULL)
		return false;
	*p_ppchName = Crt->GetName();
	*p_ppchValue = Crt->GetValue();
	Crt = Crt->GetNext();
	return true;
}

void SimpleList::ShowList()
{
	Item* pcoItem = Hd;
	cout << "<ul>";
	while (pcoItem)
	{
		cout << "<li>" << pcoItem->GetName() << " -> " << pcoItem->GetValue() << "  ";
		pcoItem = pcoItem->GetNext();
	}
	cout << "</ul>\n";
}

SimpleList::~SimpleList()
{
	Crt = Hd;
	while (Crt != NULL)
	{
		Hd = Crt->GetNext();
		delete Crt;
		Crt = Hd;
	}
	Hd = Tl = Crt = NULL;
}

int HashTable::hashValue(const char* key)
{
	unsigned long int hash;
	int len = strlen(key);
	for (hash = len; len--; )
		hash = ((hash << 5) ^ (hash >> 27)) ^ *key++;
	hash = hash % N;
	return hash;
}

void HashTable::AddValue(const char* p_pchName, const char* p_pchValue)
{
	if (p_pchName == NULL || p_pchValue == NULL)
		return ;
	int nHVal = hashValue(p_pchName);
	if (p_pchValue)
		L[nHVal].AddItem(p_pchName, p_pchValue);
}

bool HashTable::GetNextParameter(const char** p_ppchName, const char** p_ppchValue)
{
	if (m_nIterator >= N)
		return false;
	while (!L[m_nIterator].GetNextItem(p_ppchName, p_ppchValue) && m_nIterator < N)
		m_nIterator++;
	return m_nIterator < N;
}

void HashTable::ResetIterator()
{
	for (int i = 0; i <= m_nIterator; ++i)
		L[i].ResetIterator();
	m_nIterator = 0;
}

const char* CGI::GetPathInfo()
{
	char* pchEnvVal = getenv("PATH_INFO");
	if (pchEnvVal != NULL)
		return pchEnvVal;
	else
		return "";
}

const char* CGI::GetRemoteHost()
{
	char* pchEnvVal = getenv("REMOTE_HOST");
	if (pchEnvVal != NULL)
		return pchEnvVal;
	else
		return "";
}


const char* CGI::GetRemoteAddress()
{
	char* pchEnvVal = getenv("REMOTE_ADDR");
	if (pchEnvVal != NULL)
		return pchEnvVal;
	else
		return "";
}

const char* CGIBase::GetMethod()
{
	return m_pchMethod;
}

const char* CGIBase::GetQuery()
{
	const char* pchEnvVal = (m_pchMethod != NULL && m_pchQuery != NULL) ? m_pchQuery
	                        : getenv("QUERY_STRING");
	if (pchEnvVal != NULL)
		return pchEnvVal;
	else
		return "";
}

int CGIBase::GetLength()
{
	return (m_pchMethod != 0 && m_pchQuery != 0 ? strlen(m_pchQuery)
	        : atoi(getenv("CONTENT_LENGTH")));
}


void CGIBase::getword(char** word, const char** line, char stop)
{
	int x = 0;
	for (x = 0; *line != NULL && (*line)[x] != 0 && (*line)[x] != stop; x++);
	*word = (char*) malloc(x + 1);
	if (*line != NULL)
		strncpy(*word, *line, x);
	(*word)[x] = 0;
	if ((*line)[x] != 0)
		(*line) ++;
	*line += x;
}

char* CGIBase::makeword(char* line, char stop)
{
	if (line == NULL)
		return NULL;
	int x, y;
	char* word = (char *) malloc(sizeof(char) * (strlen(line) + 1));
	for (x = 0; line[x] != 0 && line[x] != stop; x++)
		word[x] = line[x];
	word[x] = '\0';
	if (line[x] != 0)
		x++;
	y = 0;
	while ((line[y++] = line[x++]) != 0);
	return word;
}

char* CGIBase::fmakeword(char stop, int* cl)
{
	int wsize;
	char* word;
	int ll;
	wsize = 1024;
	ll = 0;
	word = (char*) malloc(sizeof(char) * (wsize + 1));
	while (1)
	{
		word[ll] = getNextCh();
		if (ll == wsize)
		{
			word[ll + 1] = '\0';
			wsize += 1024;
			word = (char*)realloc(word, sizeof(char) * (wsize + 1));
		}
		--(*cl);
		if ((word[ll] == stop) || (isEOF()) || (!(*cl)))
		{
			if (word[ll] != stop) ll++;
			word[ll] = '\0';
			word = (char*) realloc(word, ll + 1);
			return word;
		}
		++ll;
	}
}

char CGIBase::x2c(char* what)
{
	register char digit;
	digit = (what[0] >= 'A' ? ((what[0] & 0xdf) - 'A') + 10 : (what[0] - '0'));
	digit *= 16;
	digit += (what[1] >= 'A' ? ((what[1] & 0xdf) - 'A') + 10 : (what[1] - '0'));
	return (digit);
}

void CGIBase::unescape_url(char* url)
{
	register int x, y;
	for (x = 0 , y = 0; url[y]; ++x, ++y)
	{
		if ((url[x] = url[y]) == '%')
		{
			url[x] = x2c(&url[y + 1]);
			y += 2;
		}
	}
	url[x] = '\0';
}

void CGIBase::plustospace(char* str)
{
	register int x;
	for (x = 0; str[x]; x++)
		if (str[x] == '+') str[x] = ' ';
}

int CGIBase::rind(char* s, char c)
{
	register int x;
	for (x = strlen(s) - 1; x != -1; x--)
		if (s[x] == c) return x;
	return -1;
}

int CGIBase::getline(char* s, int n, FILE* f)
{
	register int i = 0;
	while (1)
	{
		s[i] = (char)fgetc(f);
		if (s[i] == CR)
			s[i] = fgetc(f);
		if ((s[i] == 0x4) || (s[i] == LF) || (i == (n - 1)))
		{
			s[i] = '\0';
			return (feof(f) ? 1 : 0);
		}
		++i;
	}
}

void CGIBase::send_fd(FILE* f, FILE* fd)
{
	char c;
	while (1)
	{
		c = fgetc(f);
		if (feof(f))
			return ;
		fputc(c, fd);
	}
}

int CGIBase::ind(char* s, char c)
{
	register int x;
	for (x = 0; s[x]; x++)
		if (s[x] == c) return x;
	return -1;
}

void CGIBase::escape_shell_cmd(char* cmd)
{
	register int x, y, l;
	l = strlen(cmd);
	for (x = 0; cmd[x]; x++)
	{
		if (ind("&;`'\"|*?~<>^()[]{}$\\", cmd[x]) != -1)
		{
			for (y = l + 1; y > x; y--)
				cmd[y] = cmd[y - 1];
			l++;   /* length has been increased */
			cmd[x] = '\\';
			x++;   /* skip the character */
		}
	}
}

void CGI::ShowData()
{
	cout << "<ul>";
	for (int i = 0; i < N; i++)
	{
		cout << "<li>";
		getList(i).ShowList();
	}
	cout << "</ul>";
}

bool CGIBase::isEOF() const
{
	return m_pchQuery == 0 ? feof(m_pInFD) : m_pchQuery[nIndex] == 0;
}

char CGIBase::getNextCh()
{
	return m_pchQuery == 0 ? fgetc(m_pInFD) : m_pchQuery[nIndex++];
}

CGIBase::CGIBase(const char* p_pchMethod, const char* p_pchQuery)
{
	nIndex = 0;
	m_pInFD = stdin;
	m_pchMethod = p_pchMethod != 0 ? p_pchMethod : getenv("REQUEST_METHOD");
	if (!m_pchMethod)
		return ;
	m_pchQuery = p_pchQuery;
	register int x, m;
	char* pchVal;
	char* pchName;
	const char* pchQuery;
	const char* pchIndex;
	if (!strcmp(m_pchMethod, "GET"))
	{
		if ((pchIndex = pchQuery = GetQuery()) == 0)
			return ;
		else
			for (x = 0; *pchIndex != '\0'; x++)
			{
				m = x;
				getword(&pchName, &pchIndex, '=');
				getword(&pchVal, &pchIndex, '&');
				plustospace(pchVal);
				unescape_url(pchVal);
				if (*pchName)
					HT.AddValue(pchName, pchVal);
				free(pchName);
				free(pchVal);
			}
	}
	else if (!strcmp(m_pchMethod, "POST"))
	{
		int pchIndex = GetLength();
		for (x = 0; pchIndex && (!isEOF()); x++)
		{
			m = x;
			pchVal = fmakeword('&', &pchIndex);
			plustospace(pchVal);
			unescape_url(pchVal);
			pchName = makeword(pchVal, '=');
			HT.AddValue(pchName, pchVal);
		}
	}
}

bool CGI::GetNextParameter(const char** p_ppchName, const char** p_ppchValue)
{
	return HT.GetNextParameter(p_ppchName, p_ppchValue);
}

void CGI::ResetIterator()
{
	HT.ResetIterator();
}
