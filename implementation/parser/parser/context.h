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

Define CContext class; used when scanning actions tree (see tol_parser.h,
tol_actions.h). The context contains all the cgi parameters and other context
information such as: user subscriptions, issue/section/article/subtitle list
index etc.

******************************************************************************/

#ifndef _CMS_CONTEXT
#define _CMS_CONTEXT

#include "globals.h"
#include "cms_types.h"

// TLMode: describe the list context mode
//	LM_PREV: inside an "if previousitems" statement
//	LM_NORMAL: normal list context
//	LM_NEXT: inside an "if nextitems" statement
typedef enum _TLMode {
    LM_PREV = -1,
    LM_NORMAL = 0,
    LM_NEXT = 1
} TLMode;

// TStMode: describe the subtitles list context mode
//	STM_PREV: inside an "if previousitems" statement
//	STM_NORMAL: normal list context
//	STM_NEXT: inside an "if nextitems" statement
typedef enum _TStMode {
    STM_PREV = -1,
    STM_NORMAL = 0,
    STM_NEXT = 1
} TStMode;

// CLevel: describe the list level
//	CLV_ROOT: not inside a list statement
//	CLV_ISSUE_LIST: inside a "list issue" statement
//	CLV_SECTION_LIST: inside a "list section" statement
//	CLV_ARTICLE_LIST: inside a "list article" statement
//	CLV_SEARCHRESULT_LIST: inside a "list SearchResult" statement
//	CLV_SUBTITLE_LIST: inside a "list subtitle" statement
typedef enum _CLevel {
    CLV_ROOT = 0,
    CLV_ISSUE_LIST = 1,
    CLV_SECTION_LIST = 2,
    CLV_ARTICLE_LIST = 3,
    CLV_SEARCHRESULT_LIST = 4,
    CLV_SUBTITLE_LIST = 5
} CLevel;

// TAccess: describes users type of access to articles and issues
//	A_PUBLISHED: user has access only to published articles
//	A_ALL: user has access to all articles and issues (including not published ones)
typedef enum _TAccess {
    A_PUBLISHED = 1,
    A_ALL = 2
} TAccess;

// TSubsType: describes user subscription type
//	ST_NONE: there was no subscription process
//	ST_TRIAL: trial subscription
//	ST_PAID: paid subscription
typedef enum _TSubsType {
    ST_NONE = 0,
    ST_TRIAL = 1,
    ST_PAID = 2
} TSubsType;


#include <map>
#include <set>
#include <list>

using std::set;
using std::map;
using std::list;
using std::string;
using std::less;

typedef set <long int> LIntSet;

typedef map <long int, LIntSet, less <long int> > LInt2LIntSet;

typedef map <string, long int, str_case_less> String2LInt;

typedef set <string, str_case_less> StringSet;

typedef list <string> StringList;

typedef map <string, StringList, str_case_less> String2StringList;

typedef map <string, StringList::iterator, str_case_less> String2StringListIt;

typedef map <string, string, str_case_less> String2String;

typedef map <string, int, str_case_less> String2Int;

typedef map <string, bool, str_case_less> String2Bool;

class CContext
{
private:
	String2String userinfo;		// informations about user (name, address, email etc.)
	unsigned long int ip;		// client IP
	long int user_id;			// user identifier
	unsigned long int key;		// user key (used for authentication purposes)
	bool is_reader;				// true if user is reader
	bool access_by_ip;			// true is access is by IP
	TAccess access;				// access type
	CLevel level;				// level (root, issue list etc.)
	long int language_id, def_language_id;		// current and default(from template start) language
	long int publication_id, def_publication_id;// current and default publication
	long int issue_nr, def_issue_nr;			// current and default issue
	long int section_nr, def_section_nr;		// current and default section
	long int article_nr, def_article_nr;		// current and default article
	long int i_list_start, s_list_start;		// list start index for issue, section, article
	long int a_list_start, sr_list_start;		// and search lists
	String2LInt st_list_start;					// list start index for subtitle list
	long int list_index;						// current list index
	long int list_row;							// current list row (table construction)
	long int list_column;						// current list column
	long int list_length;						// list length
	long int i_prev_start, i_next_start;		// list start index for issue, section, article,
	long int s_prev_start, s_next_start;		// and search lists in previous and next contexts
	long int a_prev_start, a_next_start;
	long int sr_prev_start, sr_next_start;
	String2LInt st_prev_start, st_next_start;	// list start index for subtitles list in previous
												// and next context
	TLMode lmode;								// list mode (PREV, NORMAL, NEXT)
	TStMode stmode;								// subtitles list mode
	LInt2LIntSet subs;							// user subscriptions
	StringSet keywords;							// keywords to search for
	string str_keywords;						// the string of keywords
	StringSet::iterator kw_i;					// keywords iterator; memorise the current element
	bool do_subscribe;							// true if subscribe process occured
	TSubsType subs_type;						// subscription type
	bool by_publication;						// subscription by: publication or sections
	long int subs_res;							// subscription result
	bool adduser;								// true if add user process occured
	bool modifyuser;							// true if modify user process occured
	long int adduser_res, modifyuser_res;		// add/modify user result
	bool login;									// true if login process occured
	long int login_res;							// login result
	bool search;								// true if search process occured
	long int search_res;						// search result
	bool search_and;							// true if search for all keywords
	int search_level;							// search level: 0 - all, 1 - issue, 2 - section
	String2StringList subtitles;				// current article body field subtitles/field
	String2StringListIt subtitles_it;			// subtitles iterator: current subtitle/field
	String2Int start_subtitle;					// start subtitle/field to print
	String2Int default_start_subtitle;			// start subtitle/field supplied as a parameter
	String2Bool all_subtitles;					// print all subtitles/field
	String2String fields;						// fields/article type to print
	string current_field;						// current printing field from article
	string current_art_type;					// current article type

	static const string emptystring;

public:
	// default constructor
	CContext();
	// copy constructor
	CContext(const CContext& c) { *this = c; }

	int operator ==(const CContext& c) const;

	const CContext& operator =(const CContext&);

	void SetUserInfo(const string&, const string&);

	void SetIP(unsigned long int i) { ip = i; }

	void SetUser(long int u) { user_id = u; }

	void SetKey(unsigned long int k) { key = k; }

	void SetReader(bool r)
	{
		is_reader = r;
	}
	void SetAccessByIP(bool a)
	{
		access_by_ip = a;
	}
	void SetAccess(TAccess a)
	{
		access = a;
	}
	void SetLevel(CLevel l)
	{
		level = l;
	}
	void SetLanguage(long int l)
	{
		language_id = l;
	}
	void SetDefLanguage(long int l)
	{
		def_language_id = l;
	}
	void SetPublication(long int p)
	{
		publication_id = p;
	}
	void SetDefPublication(long int p)
	{
		def_publication_id = p;
	}
	void SetIssue(long int i)
	{
		issue_nr = i;
	}
	void SetDefIssue(long int i)
	{
		def_issue_nr = i;
	}
	void SetSection(long int s)
	{
		section_nr = s;
	}
	void SetDefSection(long int s)
	{
		def_section_nr = s;
	}
	void SetArticle(long int a)
	{
		article_nr = a;
	}
	void SetDefArticle(long int a)
	{
		def_article_nr = a;
	}
	void SetIListStart(long int i)
	{
		i_list_start = i;
	}
	void SetSListStart(long int i)
	{
		s_list_start = i;
	}
	void SetAListStart(long int i)
	{
		a_list_start = i;
	}
	void SetSrListStart(long int i)
	{
		sr_list_start = i;
	}
	void SetStListStart(long int, const string& = "");
	void SetListStart(long int, CLevel, const string& = "");
	void SetListIndex(long int i)
	{
		list_index = i;
	}
	void SetListRow(long int i)
	{
		list_row = i;
	}
	void SetListColumn(long int i)
	{
		list_column = i;
	}
	void SetListLength(long int i)
	{
		list_length = i;
	}
	void SetIPrevStart(long int i)
	{
		i_prev_start = i;
	}
	void SetINextStart(long int i)
	{
		i_next_start = i;
	}
	void SetSPrevStart(long int i)
	{
		s_prev_start = i;
	}
	void SetSNextStart(long int i)
	{
		s_next_start = i;
	}
	void SetAPrevStart(long int i)
	{
		a_prev_start = i;
	}
	void SetANextStart(long int i)
	{
		a_next_start = i;
	}
	void SetSrPrevStart(long int i)
	{
		sr_prev_start = i;
	}
	void SetSrNextStart(long int i)
	{
		sr_next_start = i;
	}
	void SetStPrevStart(long int, const string& = "");
	void SetStNextStart(long int, const string& = "");
	void SetPrevStart(long int, CLevel, const string& = "");
	void SetNextStart(long int, CLevel, const string& = "");
	void SetLMode(TLMode lm)
	{
		lmode = lm;
	}
	void SetStMode(TStMode sm)
	{
		stmode = sm;
	}
	void SetSubs(long int, long int);
	void SetKeyword(const string& k)
	{
		keywords.insert(k);
	}
	void ResetKwdIt()
	{
		kw_i = keywords.begin();
	}
	void SetStrKeywords(const char* k)
	{
		str_keywords = k;
	}
	void SetSubscribe(bool s)
	{
		do_subscribe = s;
	}
	void SetSubsType(TSubsType st)
	{
		subs_type = st;
	}
	void SetByPublication(bool bp)
	{
		by_publication = bp;
	}
	void SetSubsRes(long int r)
	{
		subs_res = r;
	}
	void SetAddUser(bool au)
	{
		adduser = au;
	}
	void SetModifyUser(bool mu)
	{
		modifyuser = mu;
	}
	void SetAddUserRes(long int ur)
	{
		adduser_res = ur;
	}
	void SetModifyUserRes(long int ur)
	{
		modifyuser_res = ur;
	}
	void SetLogin(bool l)
	{
		login = l;
	}
	void SetLoginRes(long int lr)
	{
		login_res = lr;
	}
	void SetSearch(bool s)
	{
		search = s;
	}
	void SetSearchRes(long int sr)
	{
		search_res = sr;
	}
	void SetSearchAnd(bool a)
	{
		search_and = a;
	}
	void SetSearchLevel(int sl)
	{
		search_level = sl;
	}
	void AppendSubtitle(const string&, const string& = "");
	void ResetSubtitles(const string& = "");
	void SetStartSubtitle(int, const string& = "");
	void SetAllSubtitles(bool, const string& = "");
	void SetDefaultStartSubtitle(int, const string& = "");
	void SetField(const string&, const string&);
	void SetCurrentField(const string &f)
	{
		current_field = f;
	}
	void SetCurrentArtType(const string &f)
	{
		current_art_type = f;
	}

	const string& UserInfo(const string&);
	bool IsUserInfo(const string&);
	unsigned long int IP() const
	{
		return ip;
	}
	long int User() const
	{
		return user_id;
	}
	unsigned long int Key() const
	{
		return key;
	}
	bool IsReader() const
	{
		return is_reader;
	}
	bool AccessByIP() const
	{
		return access_by_ip;
	}
	TAccess Access()
	{
		return access;
	}
	CLevel Level() const
	{
		return level;
	}
	long int Language() const
	{
		return language_id;
	}
	long int DefLanguage() const
	{
		return def_language_id;
	}
	long int Publication() const
	{
		return publication_id;
	}
	long int DefPublication() const
	{
		return def_publication_id;
	}
	long int Issue() const
	{
		return issue_nr;
	}
	long int DefIssue() const
	{
		return def_issue_nr;
	}
	long int Section() const
	{
		return section_nr;
	}
	long int DefSection() const
	{
		return def_section_nr;
	}
	long int Article() const
	{
		return article_nr;
	}
	long int DefArticle() const
	{
		return def_article_nr;
	}
	long int IListStart() const
	{
		return i_list_start;
	}
	long int SListStart() const
	{
		return s_list_start;
	}
	long int SrListStart() const
	{
		return sr_list_start;
	}
	long int StListStart(const string& = "");
	long int AListStart() const
	{
		return a_list_start;
	}
	long int ListStart(CLevel, const string& = "");
	long int ListIndex() const
	{
		return list_index;
	}
	long int ListRow() const
	{
		return list_row;
	}
	long int ListColumn() const
	{
		return list_column;
	}
	long int ListLength() const
	{
		return list_length;
	}
	long int IPrevStart() const
	{
		return i_prev_start;
	}
	long int INextStart() const
	{
		return i_next_start;
	}
	long int SPrevStart() const
	{
		return s_prev_start;
	}
	long int SNextStart() const
	{
		return s_next_start;
	}
	long int APrevStart() const
	{
		return a_prev_start;
	}
	long int ANextStart() const
	{
		return a_next_start;
	}
	long int SrPrevStart() const
	{
		return sr_prev_start;
	}
	long int SrNextStart() const
	{
		return sr_next_start;
	}
	long int StPrevStart(const string& = "");
	long int StNextStart(const string& = "");
	long int PrevStart(CLevel, const string& = "");
	long int NextStart(CLevel, const string& = "");
	TLMode LMode() const
	{
		return lmode;
	}
	TStMode StMode() const
	{
		return stmode;
	}
	bool IsSubs(long int, long int);
	bool NoKeywords() const
	{
		return keywords.empty();
	}
	const char* NextKwd();
	size_t KeywordsNr() const
	{
		return keywords.size();
	}
	const char* StrKeywords() const
	{
		return str_keywords.c_str();
	}
	bool Subscribe() const
	{
		return do_subscribe;
	}
	TSubsType SubsType() const
	{
		return subs_type;
	}
	bool ByPublication() const
	{
		return by_publication;
	}
	long int SubsRes() const
	{
		return subs_res;
	}
	bool AddUser() const
	{
		return adduser;
	}
	bool ModifyUser() const
	{
		return modifyuser;
	}
	long int AddUserRes() const
	{
		return adduser_res;
	}
	long int ModifyUserRes() const
	{
		return modifyuser_res;
	}
	bool Login() const
	{
		return login;
	}
	long int LoginRes() const
	{
		return login_res;
	}
	bool Search() const
	{
		return search;
	}
	long int SearchRes() const
	{
		return search_res;
	}
	bool SearchAnd() const
	{
		return search_and;
	}
	long int SearchLevel()
	{
		return search_level;
	}
	int SubtitlesNumber(const string& = "");
	const string& NextSubtitle(const string& = "");
	const string& CurrentSubtitle(const string& = "");
	const string& SelectSubtitle(int, const string& = "");
	int StartSubtitle(const string& = "");
	int AllSubtitles(const string& = "");
	int DefaultStartSubtitle(const string& = "");
	const string& FieldArtType(const string&);
	String2String& Fields()
	{
		return fields;
	}
	const string& CurrentField()
	{
		return current_field;
	}
	const string& CurrentArtType()
	{
		return current_art_type;
	}
	String2LInt& StListStartMap()
	{
		return st_list_start;
	}
	String2LInt& StPrevStartMap()
	{
		return st_prev_start;
	}
	String2LInt& StNextStartMap()
	{
		return st_next_start;
	}
	String2StringList& SubtitlesMap()
	{
		return subtitles;
	}
	void PrintSubs();
};

#endif
