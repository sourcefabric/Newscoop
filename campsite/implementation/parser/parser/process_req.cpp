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

Implementation of functions for client request processing

******************************************************************************/

#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <sstream>

#include "process_req.h"
#include "parser.h"
#include "util.h"
#include "auto_ptr.h"
#include "curl.h"
#include "cpublication.h"

using std::stringstream;
using std::cout;
using std::endl;


// RunParser:
//   - prepare the context: read cgi environment into context, read user subscriptions
//     into context
//   - perform requested actions: log user in, add user to database, add subscriptions,
//     modify user informations, search keywords against database
//   - launch parser with current context: search for a parser instance of the requested
//     template; if not found, create parser instance for requested template and add it
//     to parsers hash; call Parse and WriteOutput methods of parser instance; eventually
//     call PrintParseErrors and PrintWriteErrors for admin users
// Return RES_OK if no errors occured, error code otherwise
// Parameters:
//		MYSQL* p_pSql - pointer to MySQL connection
//		CURL* p_pURL - pointer to the URL object
//		const char* p_pchRemoteIP - pointer to string containing the client IP address
//		sockstream& p_rOs - output stream
int RunParser(MYSQL* p_pSQL, CURL* p_pcoURL, const char* p_pchRemoteIP, sockstream& p_rOs)
	throw(RunException, bad_alloc)
{
	if (p_pcoURL == NULL)
		throw RunException("Invalid params");
	if (p_pSQL == NULL)
		throw RunException("MYSQL connection not initialised");
	static const char* ppchParams[PARAM_NR] =
	{
		"Name", "EMail", "CountryCode", "UName", "Password", "PasswordAgain",
		"City", "StrAddress", "State", "Phone", "Fax", "Contact", "Phone2",
		"Title", "Gender", "Age", "PostalCode", "Employer", "EmployerType",
		"Position", "Interests", "How", "Languages", "Improvements", "Pref1",
		"Pref2", "Pref3", "Pref4", "Field1", "Field2", "Field3", "Field4",
		"Field5", "Text1", "Text2", "Text3"
	};
	static const int errs[ERR_NR] =
	{
		UERR_NO_NAME, UERR_NO_EMAIL, UERR_NO_COUNTRY, UERR_NO_UNAME,
		UERR_NO_PASSWORD, UERR_NO_PASSWORD_AGAIN
	};
	p_pcoURL->lockTemplate();
	SafeAutoPtr<CContext> pcoCtx(new CContext);
	string coStr;
	bool bDebug = false, bPreview = false, bTechDebug = false;
	char pchBuf[300];
	const CPublication* pcoPub = NULL;
	id_type nIssue = -1;
	pcoCtx->SetIP(htonl(inet_addr(p_pchRemoteIP)));
	if ((coStr = p_pcoURL->getValue(P_IDLANG)) != "")
	{
		pcoCtx->SetLanguage(atol(coStr.c_str()));
		pcoCtx->SetDefLanguage(atol(coStr.c_str()));
	}
	if ((coStr = p_pcoURL->getValue(P_IDPUBL)) != "")
	{
		pcoCtx->SetPublication(atol(coStr.c_str()));
		pcoCtx->SetDefPublication(atol(coStr.c_str()));
		pcoPub = CPublicationsRegister::getInstance().getPublication(pcoCtx->Publication());
	}
	if ((coStr = p_pcoURL->getValue(P_NRISSUE)) != "")
	{
		pcoCtx->SetIssue(atol(coStr.c_str()));
		pcoCtx->SetDefIssue(atol(coStr.c_str()));
		nIssue = pcoCtx->Issue();
	}
	if ((coStr = p_pcoURL->getValue(P_NRSECTION)) != "")
	{
		pcoCtx->SetSection(atol(coStr.c_str()));
		pcoCtx->SetDefSection(atol(coStr.c_str()));
	}
	if ((coStr = p_pcoURL->getValue(P_NRARTICLE)) != "")
	{
		pcoCtx->SetArticle(atol(coStr.c_str()));
		pcoCtx->SetDefArticle(atol(coStr.c_str()));
	}
	try {
		if ((coStr = p_pcoURL->getValue(P_TOPIC_ID)) != "")
		{
			pcoCtx->SetTopic(Integer(coStr));
			pcoCtx->SetDefTopic(Integer(coStr));
		}
	}
	catch (InvalidValue& rcoEx)
	{
		// do nothing
	}

	// Set the current and initial (default) URL
	pcoCtx->SetURL(p_pcoURL);
	pcoCtx->SetDefURL(p_pcoURL->clone());

	// delete topic parameter from URL
	pcoCtx->URL()->deleteParameter(P_TOPIC_ID);
	pcoCtx->DefURL()->deleteParameter(P_TOPIC_ID);

	// read issue, section, article, search and subtitle lists start positions
	if ((coStr = p_pcoURL->getValue(P_ILSTART)) != "")
		pcoCtx->SetIListStart(atol(coStr.c_str()));
	if ((coStr = p_pcoURL->getValue(P_SLSTART)) != "")
		pcoCtx->SetSListStart(atol(coStr.c_str()));
	if ((coStr = p_pcoURL->getValue(P_ALSTART)) != "")
		pcoCtx->SetAListStart(atol(coStr.c_str()));
	if ((coStr = p_pcoURL->getValue(P_SRLSTART)) != "")
		pcoCtx->SetSrListStart(atol(coStr.c_str()));

	// delete lists start positions
	pcoCtx->URL()->deleteParameter(P_ILSTART);
	pcoCtx->URL()->deleteParameter(P_SLSTART);
	pcoCtx->URL()->deleteParameter(P_ALSTART);
	pcoCtx->URL()->deleteParameter(P_SRLSTART);
	pcoCtx->DefURL()->deleteParameter(P_ILSTART);
	pcoCtx->DefURL()->deleteParameter(P_SLSTART);
	pcoCtx->DefURL()->deleteParameter(P_ALSTART);
	pcoCtx->DefURL()->deleteParameter(P_SRLSTART);

	// delete CAPTCHA code from URL (it is processed in index.php)
	pcoCtx->URL()->deleteParameter("f_captcha_code");
	pcoCtx->DefURL()->deleteParameter("f_captcha_code");

	// read the article comment id and delete it from the URL
	if ((coStr = p_pcoURL->getValue("acid")) != "")
	{
		try {
			pcoCtx->SetArticleCommentId(Integer(coStr));
		}
		catch (const InvalidValue& rcoEx) {
			// do nothing
		}
		pcoCtx->URL()->deleteParameter("acid");
		pcoCtx->DefURL()->deleteParameter("acid");
	}
	if ((coStr = p_pcoURL->getValue("ST_max")) != "")
	{
		int st_max = atol(coStr.c_str());
		for (int i = 1; i <= st_max; i++)
		{
			string coField, coArtType;
			sprintf(pchBuf, "ST%d", i);
			if ((coStr = p_pcoURL->getValue(pchBuf)) != "")
				coField = coStr;
			pcoCtx->URL()->deleteParameter(pchBuf);
			pcoCtx->DefURL()->deleteParameter(pchBuf);
			sprintf(pchBuf, "ST_T%d", i);
			if ((coStr = p_pcoURL->getValue(pchBuf)) != "")
				coArtType = coStr;
			pcoCtx->URL()->deleteParameter(pchBuf);
			pcoCtx->DefURL()->deleteParameter(pchBuf);
			if (coField == "" || coArtType == "")
				continue;
			pcoCtx->SetField(coField, coArtType);
			sprintf(pchBuf, "ST_LS%d", i);
			if ((coStr = p_pcoURL->getValue(pchBuf)) != "")
				pcoCtx->SetStListStart(atol(coStr.c_str()), coField);
			pcoCtx->URL()->deleteParameter(pchBuf);
			pcoCtx->DefURL()->deleteParameter(pchBuf);
			sprintf(pchBuf, "ST_PS%d", i);
			if ((coStr = p_pcoURL->getValue(pchBuf)) != "")
			{
				pcoCtx->SetStartSubtitle(atol(coStr.c_str()), coField);
				pcoCtx->SetDefaultStartSubtitle(atol(coStr.c_str()), coField);
			}
			pcoCtx->URL()->deleteParameter(pchBuf);
			pcoCtx->DefURL()->deleteParameter(pchBuf);
			sprintf(pchBuf, "ST_AS%d", i);
			if ((coStr = p_pcoURL->getValue(pchBuf)) != "")
				pcoCtx->SetAllSubtitles(atol(coStr.c_str()), coField);
			pcoCtx->URL()->deleteParameter(pchBuf);
			pcoCtx->DefURL()->deleteParameter(pchBuf);
		}
		pcoCtx->URL()->deleteParameter("ST_max");
		pcoCtx->DefURL()->deleteParameter("ST_max");
	}
	CheckUserInfo(*pcoCtx, ppchParams, PARAM_NR);
	bool bAccessAll = false;
	pcoCtx->SetReader(true);
	if ((coStr = p_pcoURL->getValue(P_SEARCH)) != "")
	{
		int nRes = Search(*pcoCtx, p_pSQL);
		if (nRes < SRERR_INTERNAL && nRes != 0)
			nRes = SRERR_INTERNAL;
		pcoCtx->SetSearchRes(nRes);
		pcoCtx->URL()->deleteParameter(P_SEARCH);
		pcoCtx->DefURL()->deleteParameter(P_SEARCH);
	}
	if ((coStr = p_pcoURL->getValue(P_USERADD)) != "")
	{
		int nRes = AddUser((*pcoCtx), p_pSQL, ppchParams, PARAM_NR, errs, ERR_NR);
		if (nRes < UERR_INTERNAL && nRes != 0)
			nRes = UERR_INTERNAL;
		pcoCtx->SetAddUserRes(nRes);
		if (nRes == 0)
			p_rOs << "<META HTTP-EQUIV=\"Set-Cookie\" CONTENT=\"LoginUserId="
			<< pcoCtx->User() << "; path=/\">\n"
			<< "<META HTTP-EQUIV=\"Set-Cookie\" CONTENT=\"LoginUserKey="
			<< pcoCtx->Key() << "; path=/\">\n";
		pcoCtx->URL()->deleteParameter(P_USERADD);
		pcoCtx->DefURL()->deleteParameter(P_USERADD);
	}
	else if ((coStr = p_pcoURL->getValue(P_LOGIN)) != "")
	{
		int nRes = Login(*pcoCtx, p_pSQL);
		if (nRes < LERR_INTERNAL && nRes != 0)
			nRes = LERR_INTERNAL;
		pcoCtx->SetLoginRes(nRes);
		if (nRes == 0)
		{
			string coExpires = "";
			if ((coStr = p_pcoURL->getValue(P_REMEMBER_USER)) != "")
			{
				coExpires = "; expires=Tuesday, 31-Dec-2069 00:00:00 GMT";
				pcoCtx->URL()->deleteParameter(P_REMEMBER_USER);
				pcoCtx->DefURL()->deleteParameter(P_REMEMBER_USER);
			}
			p_rOs << "<META HTTP-EQUIV=\"Set-Cookie\" CONTENT=\"LoginUserId="
			<< pcoCtx->User() << "; path=/" << coExpires << "\">\n"
			<< "<META HTTP-EQUIV=\"Set-Cookie\" CONTENT=\"LoginUserKey="
			<< pcoCtx->Key() << "; path=/" << coExpires << "\">\n";
		}
		pcoCtx->URL()->deleteParameter(P_LOGIN);
		pcoCtx->DefURL()->deleteParameter(P_LOGIN);
	}
	else
	{
		if ((coStr = p_pcoURL->getValue("LoginUserId")) != "")
			pcoCtx->SetUser(atol(coStr.c_str()));
		else if ((coStr = p_pcoURL->getCookie("LoginUserId")) != "")
			pcoCtx->SetUser(atol(coStr.c_str()));
		if ((coStr = p_pcoURL->getValue("LoginUserKey")) != "")
			pcoCtx->SetKey(strtoul(coStr.c_str(), 0, 10));
		else if ((coStr = p_pcoURL->getCookie("LoginUserKey")) != "")
			pcoCtx->SetKey(strtoul(coStr.c_str(), 0, 10));
		if ((coStr = p_pcoURL->getValue("AdminAccess")) != "")
			bAccessAll = coStr == "all";
		else if ((coStr = p_pcoURL->getCookie("AdminAccess")) != "")
			bAccessAll = coStr == "all";
		bPreview = p_pcoURL->getValue(P_PREVIEW_MODE) == "on";
		bTechDebug = p_pcoURL->getValue(P_DEBUG_MODE) == "on";
	}
	if ((coStr = p_pcoURL->getValue(P_USERMODIFY)) != "")
	{
		int nRes = ModifyUser((*pcoCtx), p_pSQL, ppchParams, PARAM_NR, errs, ERR_NR);
		if (nRes < UERR_INTERNAL && nRes != 0)
			nRes = UERR_INTERNAL;
		pcoCtx->SetModifyUserRes(nRes);
		pcoCtx->URL()->deleteParameter(P_USERMODIFY);
		pcoCtx->DefURL()->deleteParameter(P_USERMODIFY);
	}
	if ((coStr = p_pcoURL->getValue(P_SUBSTYPE)) != "")
	{
		if (strcasecmp(coStr.c_str(), "paid") == 0)
			pcoCtx->SetSubsType(ST_PAID);
		if (strcasecmp(coStr.c_str(), "trial") == 0)
			pcoCtx->SetSubsType(ST_TRIAL);
		pcoCtx->URL()->deleteParameter(P_SUBSTYPE);
		pcoCtx->DefURL()->deleteParameter(P_SUBSTYPE);
	}
	if ((coStr = p_pcoURL->getValue(P_SUBSCRIBE)) != "")
	{
		int nRes = DoSubscribe((*pcoCtx), p_pSQL);
		if (nRes < SERR_INTERNAL && nRes != 0)
			nRes = SERR_INTERNAL;
		pcoCtx->SetSubsRes(nRes);
		pcoCtx->URL()->deleteParameter(P_SUBSCRIBE);
		pcoCtx->DefURL()->deleteParameter(P_SUBSCRIBE);
	}
	lint nIdUserIP = -1;
	sprintf(pchBuf, "select SIP.IdUser from SubsByIP as SIP left join Subscriptions S "
			"on SIP.IdUser = S.IdUser where SIP.StartIP <= %lu and "
			"%lu <= (SIP.StartIP + SIP.Addresses - 1) and S.Active = 'Y'",
			pcoCtx->IP(), pcoCtx->IP());
	SQLQuery(p_pSQL, pchBuf);
	StoreResult(p_pSQL, coSqlRes);
	if (mysql_num_rows(*coSqlRes) > 0)
	{
		MYSQL_ROW row = mysql_fetch_row(*coSqlRes);
		if (pcoCtx->User() < 0)
			pcoCtx->SetUser(atol(row[0]));
		else
			nIdUserIP = atol(row[0]);
		pcoCtx->SetAccessByIP(true);
	}
	if (pcoCtx->User() >= 0)
	{
		sprintf(pchBuf, "select Reader, KeyId from Users where Id = %ld", pcoCtx->User());
		SQLQuery(p_pSQL, pchBuf);
		StoreResult(p_pSQL, coSqlRes);
		MYSQL_ROW row;
		bool bHasAccess = false;
		if (mysql_num_rows(*coSqlRes) > 0)
		{
			row = mysql_fetch_row(*coSqlRes);
			pcoCtx->SetReader(row[0][0] == 'Y');
			if (row[1] != 0 && pcoCtx->Key() == strtoul(row[1], 0, 10))
			{
				bHasAccess = true;
			}
			else
			{
				pcoCtx->SetKey(0);
			}
		}
		else
		{
			pcoCtx->SetUser(-1);
		}
		if (!bHasAccess && nIdUserIP >= 0)
		{
			sprintf(pchBuf, "select Reader from Users where Id = %ld", nIdUserIP);
			SQLQuery(p_pSQL, pchBuf);
			coSqlRes = mysql_store_result(p_pSQL);
			if (mysql_num_rows(*coSqlRes) > 0)
			{
				row = mysql_fetch_row(*coSqlRes);
				pcoCtx->SetUser(nIdUserIP);
				pcoCtx->SetReader(row[0][0] == 'Y');
				pcoCtx->SetKey(0);
			}
		}
		if (bHasAccess || pcoCtx->AccessByIP())
		{
			if (bAccessAll && !pcoCtx->IsReader())
				pcoCtx->SetAccess(A_ALL);
			if (pcoCtx->IsReader())
			{
				bTechDebug = bDebug = bPreview = false;
				SetReaderAccess((*pcoCtx), p_pSQL);
			}
		}
		else
		{
			bTechDebug = bDebug = bPreview = false;
		}
	}
	try {
		int nResult = Integer(pcoCtx->URL()->getValue("ArticleCommentSubmitResult"));
		pcoCtx->SetArticleCommentResult(nResult);
	}
	catch (const InvalidValue& rcoEx) {
		// do nothing
	}
	pcoCtx->URL()->deleteParameter("ArticleCommentSubmitResult");
	pcoCtx->DefURL()->deleteParameter("ArticleCommentSubmitResult");
	try
	{
		string coDocumentRoot = p_pcoURL->getDocumentRoot();
		string coTemplate;
		string coTplId = p_pcoURL->getValue(P_TEMPLATE_ID);
		if (coTplId != "")
		{
			string coQuery = string("select Name from Templates where Id = ") + coTplId;
			CMYSQL_RES coRes;
			MYSQL_ROW qRow = QueryFetchRow(p_pSQL, coQuery.c_str(), coRes);
			if (qRow == NULL)
				throw InvalidValue("template identifier", coTplId.c_str());
			coTemplate = p_pcoURL->getDocumentRoot() + "/look/" + qRow[0];
		}
		else if (p_pcoURL->getTemplate() != "")
		{
			coTemplate = p_pcoURL->getDocumentRoot() + "/look/" + p_pcoURL->getTemplate();
		}
		else
		{
			id_type nLanguage = p_pcoURL->getLanguage();
			id_type nPublication = p_pcoURL->getPublication();
			id_type nSection = p_pcoURL->getSection();
			id_type nArticle = p_pcoURL->getArticle();
			coTemplate = p_pcoURL->getDocumentRoot() + "/look/"
		               + CPublication::getTemplate(nLanguage, nPublication, nIssue,
		                                           nSection, nArticle, p_pSQL, !bTechDebug);
		}
		pcoCtx->URL()->deleteParameter(P_TEMPLATE_ID);
		pcoCtx->URL()->unlockTemplate();
		pcoCtx->DefURL()->deleteParameter(P_TEMPLATE_ID);
		CParser::setMYSQL(p_pSQL);
#ifdef _DEBUG
		cout << "running parser for " << coTemplate << ": " << coDocumentRoot << endl;
#endif
		CParser* p = CParser::parserOf(coTemplate.c_str(), coDocumentRoot.c_str());
		p->setDebug(bTechDebug);
#ifdef _DEBUG
		cout << "writing output for " << coTemplate << ": " << coDocumentRoot << endl;
#endif
		p->writeOutput(*pcoCtx, p_rOs);
		if (bPreview == true)
		{
#ifdef _DEBUG
			cout << "writing errors for " << coTemplate << endl;
#endif
			p_rOs << "<script LANGUAGE=\"JavaScript\">parent.e.document.open();\n"
			"parent.e.document.write(\"<html><head><title>Errors</title>"
			"</head><body bgcolor=white text=black>\\\n<pre>\\\n";
			p_rOs << "\\\n<b>Parse errors:</b>\\\n";
			p->printParseErrors(p_rOs, true);
			p_rOs << "</pre></body></html>\\\n\");\nparent.e.document.close();\n</script>\n";
		}
#ifdef _DEBUG
			cout << "done answering request for " << coTemplate << endl;
#endif
		CParser::setMYSQL(NULL);
	}
	catch (InvalidValue& rcoEx)
	{
		stringstream coStr;
		if (pcoPub != NULL && nIssue > 0)
		{
			coStr << "Please verify if the issue, section and article templates were set for "
					<< "publication " << *(pcoPub->getAliases().begin())
					<< ", issue number " << nIssue << ".";
		}
		else
		{
			coStr << "Internal error of invalid value: " << rcoEx.what();
		}
		throw RunException(coStr.str());
	}
	catch (ExStat& rcoEx)
	{
		throw RunException("Error loading template file");
	}
	catch (RunException& rcoEx)
	{
		throw rcoEx;
	}
	catch (ExMutex& rcoEx)
	{
		throw RunException(rcoEx.Message());
	}
	catch (bad_alloc& rcoEx)
	{
		throw RunException("unable to allocate memory");
	}
	return 0;
}

// Login: perform login action: log user in
// Parameters:
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
int Login(CContext& c, MYSQL* pSql)
{
	c.SetLogin(true);
	string s;
	string uname, password, q;
	if ((s = c.URL()->getValue("LoginUName")) == "")
		return LERR_NO_UNAME;
	c.URL()->deleteParameter("LoginUName");
	c.DefURL()->deleteParameter("LoginUName");
	uname = s;
	if ((s = c.URL()->getValue("LoginPassword")) == "")
		return LERR_NO_PASSWORD;
	c.URL()->deleteParameter("LoginPassword");
	c.DefURL()->deleteParameter("LoginPassword");
	password = s;
	char pchUserNameEscaped[141], pchPasswordEscaped[201];
	ulint nUserNameSize = uname.size() >= 70 ? 70 : uname.size();
	mysql_real_escape_string(pSql, pchUserNameEscaped, uname.c_str(), nUserNameSize);
	ulint nPasswordSize = password.size() >= 100 ? 100 : password.size();
	mysql_real_escape_string(pSql, pchPasswordEscaped, password.c_str(), nPasswordSize);
	q = "select Password, SHA1(\'" + string(pchPasswordEscaped) + "\'), PASSWORD(\'"
			+ pchPasswordEscaped + "\') from Users where UName = \'" + pchUserNameEscaped + "\'";
	SQLQuery(pSql, q.c_str());
	StoreResult(pSql, coSqlRes);
	if (mysql_num_rows(*coSqlRes) < 1)
	{
		return LERR_INVALID_UNAME;
	}
	FetchRow(*coSqlRes, row);
	if (strcmp(row[0], row[1]) != 0 && strcmp(row[0], row[2]) != 0)
	{
		q = "select Password, OLD_PASSWORD(\'" + string(pchPasswordEscaped) + "\') from Users "
				" where UName = \'" + pchUserNameEscaped + "\'";
		SQLQuery(pSql, q.c_str());
		StoreResult(pSql, coSqlRes);
		if (mysql_num_rows(*coSqlRes) < 1)
		{
			return LERR_INVALID_PASSWORD;
		}
		FetchRow(*coSqlRes, row);
		if (strcmp(row[0], row[1]) != 0)
		{
			return LERR_INVALID_PASSWORD;
		}
	}
	q = "update Users set KeyId = RAND()*1000000000+RAND()*1000000+RAND()*1000 "
	    "where UName = \"" + uname + "\"";
	SQLQuery(pSql, q.c_str());
	q = "select Id, KeyId from Users where UName = \"" + uname + "\"";
	SQLQuery(pSql, q.c_str());
	coSqlRes = mysql_store_result(pSql);
	CheckForRows(*coSqlRes, 1);
	row = mysql_fetch_row(*coSqlRes);
	c.SetUser(atol(row[0]));
	c.SetKey(atol(row[1]));
	return 0;
}

// CheckUserInfo: read user informations from CGI parameters
// Parameters:
//		CContext& c - current context
//		const char* ppchParams[] - parameters to read
//		int param_nr - parameters number
int CheckUserInfo(CContext& c, const char* ppchParams[], int param_nr)
{
	if (ppchParams == NULL || param_nr <= 0)
		return 0;
	string field_pref = "User";
	int found = 0;
	set <string, less<string> > coPrefs;
	for (int k = 1; k <= 4; k++)
	{
		stringstream coPref;
		coPref << "HasPref" << k;
		if (!c.URL()->isSet(coPref.str()))
			continue;
		string s = c.URL()->getValue(coPref.str());
		c.URL()->deleteParameter(coPref.str());
		c.DefURL()->deleteParameter(coPref.str());
		coPrefs.insert(coPref.str().substr(3));
	}
	for (int i = 0; i < param_nr; i++)
	{
		if (ppchParams[i] == NULL)
			continue;
		string fld = field_pref + ppchParams[i];
		if (!c.URL()->isSet(fld))
			continue;
		string s = c.URL()->getValue(fld);
		c.URL()->deleteParameter(fld);
		c.DefURL()->deleteParameter(fld);
		c.SetUserInfo(string(ppchParams[i]), s);
		if (strncasecmp(ppchParams[i], "Pref", 4) == 0)
			coPrefs.erase(ppchParams[i]);
		found ++;
	}
	set <string, less<string> >::const_iterator coIt = coPrefs.begin();
	for (; coIt != coPrefs.end(); ++coIt)
		c.SetUserInfo(*coIt, "off");
	return found;
}

// AddUser: perform add user action (add user to database); return error code
// Parameters:
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
//		const char* ppchParams[] - parameters to read from context (user information)
//		int param_nr - parameters number
//		const int errs[] - error codes
//		int err_nr - errors number
int AddUser(CContext& c, MYSQL* pSql, const char* ppchParams[], int param_nr,
            const int errs[], int err_nr)
{
	if (ppchParams == NULL || param_nr <= 0)
		return -1;
	c.SetAddUser(true);
	string field_pref = "User", q, fn, fv, uname, email, password;
	fn = "KeyId";
	fv = "RAND()*1000000000+RAND()*1000000+RAND()*1000";
	for (int i = 0; i < param_nr; i++)
	{
		if (ppchParams[i] == NULL)
			continue;
		string s = c.UserInfo(string(ppchParams[i]));
		char pchBuf[10001];
		ulint nParamSize = s.size() >= 5000 ? 5000 : s.size();
		mysql_escape_string(pchBuf, s.c_str(), nParamSize);
		if (i < err_nr && s == "") return errs[i];
		if (s == "") continue;
		if (i == 4)
		{
			password = pchBuf;
			continue;
		}
		if (i == 1) email = s;
		if (i == 3) uname = s;
		const char* pchPrevParam = i > 0 && ppchParams[i - 1] ? ppchParams[i - 1] : "";
		fn += string(", ") + (i == 5 ? pchPrevParam : ppchParams[i]);
		if (i == 5)
		{
			if (password != pchBuf)
				return UERR_PASSWORDS_DONT_MATCH;
			if (strlen(s.c_str()) < 6)
				return UERR_PASSWORD_TOO_SIMPLE;
			fv += string(", SHA1(\'") + pchBuf + "\')";
		}
		else if (strncasecmp(ppchParams[i], "Pref", 4))
			fv += string(", \'") + pchBuf + "\'";
		else
		{
			if (s == "on")
				fv += ", \'Y\'";
			else
				fv += ", \'N\'";
		}
	}
	q = "select * from Users where UName = \"" + uname + "\"";
	SQLQuery(pSql, q.c_str());
	StoreResult(pSql, coSqlRes);
	if (mysql_num_rows(*coSqlRes) > 0)
		return UERR_USER_EXISTS;
	q = "select * from Users where EMail = \"" + email + "\"";
	SQLQuery(pSql, q.c_str());
	coSqlRes = mysql_store_result(pSql);
	if (mysql_num_rows(*coSqlRes) > 0)
		return UERR_DUPLICATE_EMAIL;
	q = "insert into Users (" + fn + ") values(" + fv + ")";
	SQLQuery(pSql, q.c_str());
	q = "select Id, KeyId from Users where UName = \"" + uname + "\"";
	SQLQuery(pSql, q.c_str());
	coSqlRes = mysql_store_result(pSql);
	CheckForRows(*coSqlRes, 1);
	FetchRow(*coSqlRes, row);
	c.SetUser(atol(row[0]));
	c.SetKey(atol(row[1]));
	return 0;
}


// ModifyUser: perform modify user action (modify user information in the database)
// Return error code.
// Parameters:
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
//		const char* ppchParams[] - parameters to read from context (user information)
//		int param_nr - parameters number
//		const int errs[] - error list (errors codes)
//		int err_nr - errors number
int ModifyUser(CContext& c, MYSQL* pSql, const char* ppchParams[], int param_nr,
               const int errs[], int err_nr)
{
	if (ppchParams == NULL || param_nr <= 0)
		return -1;
	char pchBuf[10000];
	c.SetModifyUser(true);
	string field_pref = "User", q, f, password;
	bool first = true;
	for (int i = 0; i < param_nr; i++)
	{
		if (ppchParams[i] == NULL)
			continue;
		const char* pchPrevParam = i > 0 && ppchParams[i - 1] ? ppchParams[i - 1] : "";
		if (!c.IsUserInfo(string(ppchParams[i])) || i == 3)
			continue;
		string s = c.UserInfo(string(ppchParams[i]));
		int slen = strlen(s.c_str()) > 5000 ? 5000 : strlen(s.c_str());
		mysql_escape_string(pchBuf, s.c_str(), slen);
		if (i < err_nr && s == "") return errs[i];
		if (i == 4)
		{
			password = pchBuf;
			continue;
		}
		if (!first) f += ", ";
		f += string((i == 5 ? pchPrevParam : ppchParams[i])) + " = ";
		if (i == 5)
		{
			if (password != pchBuf)
				return UERR_PASSWORDS_DONT_MATCH;
			if (strlen(s.c_str()) < 6)
				return UERR_PASSWORD_TOO_SIMPLE;
			f += string("SHA1(\'") + pchBuf + "\')";
		}
		else if (strncasecmp(ppchParams[i], "Pref", 4))
			f += string("\'") + pchBuf + "\'";
		else
			f += string("\'") + (s == "on" ? "Y" : "N") + "\'";
		if (first) first = false;
	}
	sprintf(pchBuf, "%ld", c.User());
	q = string("select * from Users where Id = \"") + pchBuf + "\"";
	SQLQuery(pSql, q.c_str());
	StoreResult(pSql, coSqlRes);
	if (mysql_num_rows(*coSqlRes) <= 0)
		return UERR_INVALID_USER;
	q = string("update Users set ") + f + " where Id = \"" + pchBuf + "\"";
	SQLQuery(pSql, q.c_str());
	return 0;
}


typedef multimap<id_type, lint, less<id_type> > SubscriptionSections;

int CreateSubscriptionSections(CContext& c, MYSQL* pSql, id_type p_nSubscriptionId,
							   lint p_nLanguageId, bool p_bByPublication, double p_nUnitCost,
							   int p_nSubscriptionTime, const string& p_rcoTimeUnit,
							   const string& p_rcoCurrency,
							   const SubscriptionSections& p_rcoSections)
{
	char pchBuf[1000];
	double nToPay = 0;
	// create subscription sections
	for (SubscriptionSections::const_iterator coIt = p_rcoSections.begin();
			coIt != p_rcoSections.end(); ++coIt)
	{
		id_type nSection = (*coIt).first;
		lint nTimeUnits = (*coIt).second;
		
		// compute the subscription length in days
		sprintf(pchBuf, "select TO_DAYS(ADDDATE(now(), INTERVAL %ld %s)) - TO_DAYS(now())",
				nTimeUnits, p_rcoTimeUnit.c_str());
		SQLQuery(pSql, pchBuf);
		StoreResult(pSql, coSqlRes);
		CheckForRows(*coSqlRes, 1);
		FetchRow(*coSqlRes, row);
		lint nSubsSectionDays = atol(row[0]);
		lint nPaidDays = c.SubsType() == ST_TRIAL ? nSubsSectionDays : 0;
		
		// compute the subscription payment
		if (c.SubsType() == ST_PAID)
		{
			nToPay += (double)nTimeUnits * p_nUnitCost;
		}

		// check if the subscription section already existed
		sprintf(pchBuf, "select TO_DAYS(ADDDATE(StartDate, INTERVAL Days DAY)) - TO_DAYS(now()) "
				"from SubsSections where IdSubscription = %ld and SectionNumber = %ld "
				"and IdLanguage = %ld", p_nSubscriptionId, nSection, p_nLanguageId);
		SQLQuery(pSql, pchBuf);
		coSqlRes = mysql_store_result(pSql);
		if ((row = mysql_fetch_row(*coSqlRes)) != NULL) // yes, update subscription
		{
			if (atol(row[0]) > 0)
				sprintf(pchBuf, "update SubsSections set Days = Days + %ld, PaidDays = "
						"PaidDays + %ld where IdSubscription = %ld and SectionNumber = %ld "
								"and IdLanguage = %ld",
						nSubsSectionDays, nPaidDays, p_nSubscriptionId, nSection, p_nLanguageId);
			else
				sprintf(pchBuf, "update SubsSections set StartDate = now(), Days = %ld, "
						"PaidDays = %ld where IdSubscription = %ld and SectionNumber = %ld "
								"and IdLanguage = %ld",
						nSubsSectionDays, nPaidDays, p_nSubscriptionId, nSection, p_nLanguageId);
		}
		else // no, create subscription to section
		{
			sprintf(pchBuf, "insert into SubsSections (IdSubscription, SectionNumber, IdLanguage,"
					" StartDate, Days, PaidDays) values(%ld, %ld, %ld, now(), %ld, %ld)",
					p_nSubscriptionId, nSection, p_nLanguageId, nSubsSectionDays, nPaidDays);
		}
		SQLQuery(pSql, pchBuf);
	}
	
	// set the subscription currency and payment amount
	sprintf(pchBuf, "update Subscriptions set ToPay = ToPay + %f, Currency = '%s' "
	        "where Id = %ld", nToPay, p_rcoCurrency.c_str(), p_nSubscriptionId);
	SQLQuery(pSql, pchBuf);
	
	return 0;
}


// DoSubscribe: perform subscribe action (subscribe user to a certain publication)
// Parameters:
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
int DoSubscribe(CContext& c, MYSQL* pSql)
{
	if (c.SubsType() == ST_NONE)
		return SERR_TYPE_NOT_SPECIFIED;
	c.SetSubscribe(true);
	char pchBuf[2000];
	if (c.User() < 0)
		return SERR_USER_NOT_SPECIFIED;
	if (!c.IsReader())
		return SERR_USER_NOT_READER;
	if (c.Publication() < 0)
		return SERR_PUBL_NOT_SPECIFIED;
	
	// see if subscription is to all languages or only to selected ones
	bool bAllLanguages = case_comp(c.URL()->getValue("subs_all_languages"), "on") == 0;
	
	// read time unit, cost, currency
	sprintf(pchBuf, "select TimeUnit, UnitCost, UnitCostAllLang, Currency from Publications "
			"where Id = %ld", c.Publication());
	SQLQuery(pSql, pchBuf);
	StoreResult(pSql, coSqlRes);
	CheckForRows(*coSqlRes, 1);
	FetchRow(*coSqlRes, row);
	string coTimeUnit = "";
	switch (row[0][0]) {
		case 'D':
			coTimeUnit = "DAY"; break;
		case 'W':
			coTimeUnit = "WEEK"; break;
		case 'M':
			coTimeUnit = "MONTH"; break;
		case 'Y':
			coTimeUnit = "YEAR"; break;
		default:
			return SERR_UNIT_NOT_SPECIFIED;
	}
	double nUnitCost = bAllLanguages ? atof(row[2]) : atof(row[1]);
	string coCurrency = row[3];
	
	// create the subscription; check if the subscription already existed
	id_type nSubscriptionId;
	sprintf(pchBuf, "select Id, Active, ToPay from Subscriptions where IdUser = %ld and "
			"IdPublication = %ld", c.User(), c.Publication());
	SQLQuery(pSql, pchBuf);
	coSqlRes = mysql_store_result(pSql);
	char chSubsType = c.SubsType() == ST_TRIAL ? 'T' : 'P';
	if ((row = mysql_fetch_row(*coSqlRes)) != NULL)	// subscription already existed; update it
	{
		if (atof(row[2]) > 0)
		{
			return SERR_SUBS_NOT_PAID;
		}
		nSubscriptionId = atol(row[0]);
		if (row[1][0] != 'Y')
		{
			sprintf(pchBuf, "update Subscriptions set Active = 'Y', Type = '%c' where "
					"IdUser = %ld and IdPublication = %ld", chSubsType, c.User(), c.Publication());
			SQLQuery(pSql, pchBuf);
		}
	}
	else	// create the subscription
	{
		sprintf(pchBuf, "insert into Subscriptions (IdUser, IdPublication, Active, Type) "
				"values(%ld, %ld, 'Y', '%c')", c.User(), c.Publication(), chSubsType);
		SQLQuery(pSql, pchBuf);
		sprintf(pchBuf, "select Id from Subscriptions where IdUser = %ld and "
				"IdPublication = %ld", c.User(), c.Publication());
		SQLQuery(pSql, pchBuf);
		coSqlRes = mysql_store_result(pSql);
		CheckForRows(*coSqlRes, 1);
		row = mysql_fetch_row(*coSqlRes);
		nSubscriptionId = atol(row[0]);
	}
	
	// Compute the subscription length; search for the default subscription intervals in
	// the countries subscriptions intervals table first
	const char* pchSubsTypeTime = c.SubsType() == ST_TRIAL ? "TrialTime" : "PaidTime";
	sprintf(pchBuf, "select %s from SubsDefTime, Users where IdPublication = %ld and "
			"SubsDefTime.CountryCode = Users.CountryCode and Users.Id = %ld",
			pchSubsTypeTime, c.Publication(), c.User());
	SQLQuery(pSql, pchBuf);
	coSqlRes = mysql_store_result(pSql);
	
	// If the default subscription interval was not found in the countries subscriptions
	// intervals table read it from the publications table
	if (mysql_num_rows(*coSqlRes) < 1)
	{
		sprintf(pchBuf, "select %s from Publications where Id = %ld", pchSubsTypeTime,
				c.Publication());
		SQLQuery(pSql, pchBuf);
		coSqlRes = mysql_store_result(pSql);
		CheckForRows(*coSqlRes, 1);
	}
	row = mysql_fetch_row(*coSqlRes);
	lint nSubscriptionTime = atol(row[0]);
	c.SetSubsTimeUnits(nSubscriptionTime);
	
	// read the subscription mode: by publication or by section
	string coValue;
	bool bByPublication = (coValue = c.URL()->getValue("by")) != ""
			&& strcasecmp(coValue.c_str(), "publication") == 0;
	c.URL()->deleteParameter("by");
	c.DefURL()->deleteParameter("by");
	
	// initialise the list of subscription sections
	SubscriptionSections coSubscriptionSections;
	
	// subscribe mode: by section
	// read the list of sections from the parameters list
	coValue = c.URL()->getValue(P_CB_SUBS);
	while (!bByPublication && coValue != "")
	{
		id_type nSection = atol(coValue.c_str());
		sprintf(pchBuf, "%s%ld", P_TX_SUBS, nSection);
		lint nTimeUnits = nSubscriptionTime;
		if ((coValue = c.URL()->getValue(pchBuf)) != "")
		{
			nTimeUnits = atol(coValue.c_str());
			
			// delete parameters we don't need anymore
			c.URL()->deleteParameter(pchBuf);
			c.DefURL()->deleteParameter(pchBuf);
		}
		coSubscriptionSections.insert(pair<id_type, lint>(nSection, nTimeUnits));
		coValue = c.URL()->getNextValue(P_CB_SUBS);
	}
	// delete parameters we don't need anymore
	c.URL()->deleteParameter(P_CB_SUBS);
	c.DefURL()->deleteParameter(P_CB_SUBS);
	
	// initialize the language list
	list<id_type> coLanguages;
	coValue = c.URL()->getValue("subscription_language");
	while (!bAllLanguages && coValue != "")
	{
		coLanguages.push_back(atol(coValue.c_str()));
		coValue = c.URL()->getNextValue("subscription_language");
	}
	c.URL()->deleteParameter("subscription_language");
	c.DefURL()->deleteParameter("subscription_language");
	if (coLanguages.empty())
	{
		coLanguages.push_back(bAllLanguages ? 0 : c.Language());
	}
	
	// run the CreateSubscriptionSections function for each language
	for (list<id_type>::iterator coIt = coLanguages.begin(); coIt != coLanguages.end(); ++coIt)
	{
		// subscribe mode: by publication
		// create the list of subscription sections for the current language
		if (bByPublication)
		{
			if (*coIt == 0)
			{
				sprintf(pchBuf, "select Number from Sections where IdPublication = %ld "
						"group by Number", c.Publication());
			}
			else
			{
				sprintf(pchBuf, "select Number from Sections where IdPublication = %ld "
						"and IdLanguage = %ld group by Number", c.Publication(), *coIt);
			}
			SQLQuery(pSql, pchBuf);
			StoreResult(pSql, coSqlRes);
			MYSQL_ROW row;
			coSubscriptionSections.clear();
			while ((row = mysql_fetch_row(*coSqlRes)) != NULL)
			{
				coSubscriptionSections.insert(pair<id_type, lint>(atol(row[0]),
											  nSubscriptionTime));
			}
		}
	
		int res = CreateSubscriptionSections(c, pSql, nSubscriptionId, *coIt, bByPublication,
											 nUnitCost, nSubscriptionTime, coTimeUnit,
											 coCurrency, coSubscriptionSections);
		if (res != 0)
		{
			return res;
		}
	}
	return 0;
}


// SetReaderAccess: update current context: set reader access to publication sections
// according to user subscriptions.
// Parameters:
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
void SetReaderAccess(CContext& c, MYSQL* pSql)
{
	if (pSql == 0)
		return ;
	char pchBuf[300];
	sprintf(pchBuf, "select IdPublication, Id from Subscriptions where IdUser = %ld"
	        " and Active = \"Y\"", c.User());
	if (mysql_query(pSql, pchBuf))
		return ;
	MYSQL_RES* pSqlRes = mysql_store_result(pSql);
	if (pSqlRes == 0)
		return ;
	MYSQL_ROW row;
	while ((row = mysql_fetch_row(pSqlRes)))
	{
		id_type id_publ = atol(row[0]), id_subs = atol(row[1]);
		sprintf(pchBuf, "select SectionNumber, (TO_DAYS(now())-TO_DAYS(StartDate)), "
		        "PaidDays, IdLanguage from SubsSections where IdSubscription = %ld", id_subs);
		if (mysql_query(pSql, pchBuf))
			continue;
		MYSQL_RES *res2 = mysql_store_result(pSql);
		if (res2 == 0)
			continue;
		MYSQL_ROW row2;
		while ((row2 = mysql_fetch_row(res2)))
		{
			id_type nr_section = atol(row2[0]);
			lint passed_days = atol(row2[1]);
			lint days = atol(row2[2]);
			lint languageId = atol(row2[3]);
			if (passed_days <= days)
				c.SetSubs(id_publ, nr_section, languageId);
		}
		mysql_free_result(res2);
	}
	mysql_free_result(pSqlRes);
}

// Search: perform search action; search against the database for keywords retrieved from
// cgi environment
// Parameters:
//		CContext& c - current context
//		MYSQL* pSql - pointer to MySQL connection
int Search(CContext& c, MYSQL* pSql)
{
	c.SetSearch(true);

	string coKeywords = c.URL()->getValue("SearchKeywords");
	c.URL()->deleteParameter("SearchKeywords");
	c.DefURL()->deleteParameter("SearchKeywords");

	string coSearchMode = c.URL()->getValue("SearchMode");
	c.URL()->deleteParameter("SearchMode");
	c.DefURL()->deleteParameter("SearchMode");

	string coLevel = c.URL()->getValue("SearchLevel");
	c.URL()->deleteParameter("SearchLevel");
	c.DefURL()->deleteParameter("SearchLevel");

	if (coKeywords == "")
		return SRERR_NO_KEYWORDS;

	ParseKeywords(coKeywords.c_str(), c);
	c.SetStrKeywords(coKeywords.c_str());

	if (coSearchMode != "" && strcasecmp(coSearchMode.c_str(), "on") == 0)
		c.SetSearchAnd(true);

	int level = coLevel != "" ? atol(coLevel.c_str()) : 0;
	level = level < 0 ? 0 : level;
	level = level > 2 ? 2 : level;
	c.SetSearchLevel(level);

	c.SetMultiplePublicationSearch(c.URL()->getValue("MultiplePublicationSearch") == "true");
	c.URL()->deleteParameter("MultiplePublicationSearch");
	c.DefURL()->deleteParameter("MultiplePublicationSearch");

	return 0;
}

// ParseKeywords: read keywords from a string of keywords and add them to current context
// Parameters:
//		const char* s - string of keywords
//		CContext& c - current context
void ParseKeywords(const char* s, CContext& c)
{
	// " \t\n\r,./\\<>?:;\"'{}[]~`!%^&*()+=\\|"
	const char* p;
	const char* q;
	char tmp[256];
	int l;
	if (s)
	{
		for (q = s; *q; )
		{
			p = q;
			while (*q && !IsSeparator(*q))
				q++;
			l = q - p;
			if (l > 1)
			{
				strncpy(tmp, p, (l > 255 ? 255 : l));
				tmp[(l > 255 ? 255 : l)] = 0;
				c.SetKeyword(tmp);
			}
			else
			{
				while (*q && IsSeparator(*q))
					q++;
			}
		}
	}
}

// IsSeparator: return true if c character is separator
// Parameters:
//		char c - character to test
bool IsSeparator(char c)
{
	static const char separators[] = " \t\n\r,./\\<>?:;\"'{}[]~`!%^&*()+=\\|";
	for (int i = 0; separators[i]; i++)
		if (c == separators[i])
			return true;
	return false;
}
