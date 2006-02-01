#include "cgi_common.h"


string& trim(string& p_rcoStr)
{
	string::size_type nIndex;
	for (nIndex = 0; nIndex < p_rcoStr.size() && p_rcoStr[nIndex] == ' ';)
	{
		p_rcoStr.erase(nIndex, 1);
	}
	for (nIndex = p_rcoStr.size() - 1; nIndex >= 0 && p_rcoStr[nIndex] == ' ';)
	{
		p_rcoStr.erase(nIndex, 1);
		nIndex = p_rcoStr.size() - 1;
	}
	return p_rcoStr;
}


char* ReadPOSTQuery()
{
	int nQueryAlloc = 1000;
	char* pchQuery = (char*) malloc(nQueryAlloc);
	if (pchQuery == NULL)
		return NULL;
	int nIndex = 0;
	for (;;)
	{
		if (feof(stdin) || (nIndex > 0 && pchQuery[nIndex-1] == 0))
		{
			pchQuery[nIndex] = 0;
			break;
		}
		if (nIndex >= nQueryAlloc)
		{
			nQueryAlloc += nQueryAlloc;
			char* pchNewQuery = (char*) realloc(pchQuery, nQueryAlloc);
			pchQuery = pchNewQuery;
		}
		char chIn = fgetc(stdin);
		if (chIn > 0)
		{
			pchQuery[nIndex++] = chIn;
		}
		else
		{
			pchQuery[nIndex] = 0;
			break;
		}
	}
	return pchQuery;
}
