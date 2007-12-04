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

Classes used for reading and working with cgi parameters

******************************************************************************/

#ifndef _CGIB_H
#define _CGIB_H

#include <stdio.h>
#define N 13

class Item
{
private:
	char* m_pchName;
	char* m_pchVal;
	Item* m_pcoNext;

public:
	Item(const char* p_pchName, const char* p_pchVal)
	{
		m_pchName = new char[strlen(p_pchName) + 1];
		strcpy(m_pchName, p_pchName);
		if (p_pchVal)
		{
			m_pchVal = new char[strlen(p_pchVal) + 1];
			strcpy(m_pchVal, p_pchVal);
		}
		else
			m_pchVal = 0;
		m_pcoNext = 0;
	}
	~Item()
	{
		delete []m_pchName;
		delete []m_pchVal;
	}

	const char* GetName()
	{
		return m_pchName;
	}
	const char* GetValue()
	{
		return m_pchVal;
	}
	void SetNext(Item* p_pcoThisOne)
	{
		m_pcoNext = p_pcoThisOne;
	}
	Item* GetNext()
	{
		return m_pcoNext;
	}
};

class SimpleList
{
private:
	Item* Hd;
	Item* Tl;
	Item* Crt;

public:
	SimpleList()
	{
		Hd = Tl = Crt = 0;
	}
	~SimpleList();

	const char* GetFirstValue(const char*);
	const char* GetNextValue(const char*);
	bool GetNextItem(const char** p_ppchName, const char** p_ppchValue);
	int HasMoreItems()
	{
		return !Crt == 0;
	}
	void AddItem(const char *, const char *);
	void ShowList();
	void ResetIterator()
	{
		Crt = Hd;
	}
};

class HashTable
{
private:
	SimpleList L[N];
	int hashValue(const char *);
	int m_nIterator;

public:
	HashTable() : m_nIterator(0)
	{}
	~HashTable()
	{}

	const char* GetFirstValue(const char *name)
	{
		return L[hashValue(name)].GetFirstValue(name);
	}
	const char* GetNextValue(const char *name)
	{
		return L[hashValue(name)].GetNextValue(name);
	}
	void AddValue(const char*, const char*);
	SimpleList& operator[](int i)
	{
		return L[i];
	}
	bool GetNextParameter(const char** p_ppchName, const char** p_pchValue);
	void ResetIterator();
};

class CGIBase
{
protected:
	HashTable HT;

	SimpleList& getList(int hashval)
	{
		return HT[hashval];
	}

private:
	const char* m_pchMethod;
	const char* m_pchQuery;
	int nIndex;
	FILE* m_pInFD;

	void getword(char**, const char**, char);
	char* makeword(char*, char);
	char* fmakeword(char, int*);
	char x2c(char*);
	void unescape_url(char*);
	void plustospace(char*);
	int rind(char*, char);
	int getline(char*, int, FILE*);
	void send_fd(FILE*, FILE*);
	int ind(char*, char);
	void escape_shell_cmd(char*);
	bool isEOF() const;
	char getNextCh();

public:
	CGIBase(const char* p_pchMethod = 0, const char* p_pchQuery = 0);
	virtual ~CGIBase()
	{}

	const char* GetQuery();
	const char* GetMethod();
	int GetLength();
};

#endif
