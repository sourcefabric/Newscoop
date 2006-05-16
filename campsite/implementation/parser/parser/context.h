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
#include "curl.h"
#include "articlecomment.h"

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

// CListLevel: describes the list level
//	CLV_ROOT: not inside a list statement
//	CLV_ISSUE_LIST: inside a "list issue" statement
//	CLV_SECTION_LIST: inside a "list section" statement
//	CLV_ARTICLE_LIST: inside a "list article" statement
//	CLV_SEARCHRESULT_LIST: inside a "list SearchResult" statement
//	CLV_SUBTITLE_LIST: inside a "list subtitle" statement
typedef enum _CListLevel {
    CLV_ROOT = 0,
    CLV_ISSUE_LIST = 1,
    CLV_SECTION_LIST = 2,
    CLV_ARTICLE_LIST = 3,
    CLV_SEARCHRESULT_LIST = 4,
    CLV_SUBTITLE_LIST = 5
} CListLevel;

// CPubLevel: describes the publication level
//	CLV_PUB_ISSUE: inside a "list issue" statement
//	CLV_PUB_SECTION: inside a "list section" statement
//	CLV_PUB_ARTICLE: inside a "list article" statement
typedef enum _CPubLevel {
	CLV_PUB_ISSUE = 1,
	CLV_PUB_SECTION = 2,
	CLV_PUB_ARTICLE = 3,
} CPubLevel;

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

typedef multimap <lint, lint, less <lint> > LIntMultiMap;

typedef map <lint, LIntMultiMap, less <lint> > LInt2LIntMultiMap;

typedef map <string, lint, str_case_less> String2LInt;

typedef set <string, str_case_less> StringSet;

typedef list <string> StringList;

typedef map <string, StringList, str_case_less> String2StringList;

typedef map <string, StringList::iterator, str_case_less> String2StringListIt;

typedef map <string, int, str_case_less> String2Int;

typedef map <string, bool, str_case_less> String2Bool;

class CContext
{
private:
	String2String userinfo;					// informations about user (name, address, email etc.)
	ulint ip;								// client IP
	id_type user_id;						// user identifier
	ulint key;								// user key (used for authentication purposes)
	bool is_reader;							// true if user is reader
	bool access_by_ip;						// true is access is by IP
	TAccess access;							// access type
	CListLevel level;						// level (root, issue list etc.)
	bool m_bEncodeHTML;						// if true will encode the output to HTML format
	id_type language_id, def_language_id;	// current and default(from template start) language
	id_type publication_id, def_publication_id;// current and default publication
	id_type issue_nr, def_issue_nr;			// current and default issue
	id_type section_nr, def_section_nr;		// current and default section
	id_type article_nr, def_article_nr;		// current and default article
	id_type i_list_start, s_list_start;		// list start index for issue, section, article
	id_type a_list_start, sr_list_start;	// and search lists
	String2LInt st_list_start;				// list start index for subtitle list
	lint list_index;						// current list index
	lint list_row;							// current list row (table construction)
	lint list_column;						// current list column
	lint list_length;						// list length
	lint i_prev_start, i_next_start;		// list start index for issue, section, article,
	lint s_prev_start, s_next_start;		// and search lists in previous and next contexts
	lint a_prev_start, a_next_start;
	lint sr_prev_start, sr_next_start;
	String2LInt st_prev_start;				// subtitles list start index in the previous context
	String2LInt st_next_start;				// subtitles list start index in the next context
	TLMode lmode;							// list mode (PREV, NORMAL, NEXT)
	TStMode stmode;							// subtitles list mode
	LInt2LIntMultiMap subs;					// user subscriptions
	StringSet keywords;						// keywords to search for
	string str_keywords;					// the string of keywords
	StringSet::iterator kw_i;				// keywords iterator; memorise the current element
	bool do_subscribe;						// true if subscribe process occured
	TSubsType subs_type;					// subscription type
	bool by_publication;					// subscription by: publication or sections
	lint subs_res;							// subscription result
	lint nSubsTimeUnits;					// default subscription time in the time units
											// defined by the publication: days, months etc.
	bool adduser;							// true if add user process occured
	bool modifyuser;						// true if modify user process occured
	lint adduser_res, modifyuser_res;		// add/modify user result
	bool login;								// true if login process occured
	lint login_res;							// login result
	bool search;							// true if search process occured
	lint search_res;						// search result
	bool search_and;						// true if search for all keywords
	int search_level;						// search level: 0 - all, 1 - issue, 2 - section
	String2StringList subtitles;			// current article body field subtitles/field
	String2StringListIt subtitles_it;		// subtitles iterator: current subtitle/field
	String2Int start_subtitle;				// start subtitle/field to print
	String2Int default_start_subtitle;		// start subtitle/field supplied as a parameter
	String2Bool all_subtitles;				// print all subtitles/field
	String2String fields;					// fields/article type to print
	string current_field;					// current printing field from article
	string current_art_type;				// current article type
	id_type m_nTopicId;						// topic numeric identifier
	id_type m_nDefTopicId;					// topic numeric identifier
	id_type m_nAttachment;					// article attachment identifier
	string m_coAttachmentExtension;			// article attachment extension
	mutable bool m_bArticleCommentEnabled;	// true if add comment event occurred
	mutable bool m_bArticleCommentEnabledValid; // true if the m_bArticleCommentEnabled member was computed
											// for the current context
	CArticleComment* m_pcoArticleComment;	// article comment identifier
	id_type m_nSubmitArticleCommentResult;	// result of the add comment event
	CURL* m_pcoURL;
	CURL* m_pcoDefURL;

	static const string emptystring;

	void SetURLValue(const string& p_coParam, id_type p_nValue);
	void SetDefURLValue(const string& p_coParam, id_type p_nValue);
	void EraseURLParam(const string& p_coParam);
	void EraseDefURLParam(const string& p_coParam);
	void ResetPublicationParams(CPubLevel p_nLevel);
	void ResetDefPublicationParams(CPubLevel p_nLevel);

public:
	// default constructor
	CContext();

	// copy constructor
	CContext(const CContext& c);

	~CContext();

	int operator ==(const CContext& c) const;

	const CContext& operator =(const CContext&);

	void SetUserInfo(const string&, const string&);

	void SetIP(ulint i) { ip = i; }

	void SetUser(id_type u) { user_id = u; }

	void SetKey(ulint k) { key = k; }

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
	void SetLevel(CListLevel l)
	{
		level = l;
	}
	void SetEncodeHTML(bool p_bEncodeHTML)
	{
		m_bEncodeHTML = p_bEncodeHTML;
	}
	void SetLanguage(id_type l);
	void SetDefLanguage(id_type l);
	void SetPublication(id_type p);
	void SetDefPublication(id_type p);
	void SetIssue(id_type i);
	void SetDefIssue(id_type i);
	void SetSection(id_type s);
	void SetDefSection(id_type s);
	void SetArticle(id_type a);
	void SetDefArticle(id_type a)
	{
		SetDefURLValue(P_NRARTICLE, a);
		def_article_nr = a;
	}
	void SetIListStart(lint i)
	{
		i_list_start = i;
	}
	void SetSListStart(lint i)
	{
		s_list_start = i;
	}
	void SetAListStart(lint i)
	{
		a_list_start = i;
	}
	void SetSrListStart(lint i)
	{
		sr_list_start = i;
	}
	void SetStListStart(lint, const string& = "");
	void SetListStart(lint, CListLevel, const string& = "");
	void SetListIndex(lint i)
	{
		list_index = i;
	}
	void SetListRow(lint i)
	{
		list_row = i;
	}
	void SetListColumn(lint i)
	{
		list_column = i;
	}
	void SetListLength(lint i)
	{
		list_length = i;
	}
	void SetIPrevStart(lint i)
	{
		i_prev_start = i;
	}
	void SetINextStart(lint i)
	{
		i_next_start = i;
	}
	void SetSPrevStart(lint i)
	{
		s_prev_start = i;
	}
	void SetSNextStart(lint i)
	{
		s_next_start = i;
	}
	void SetAPrevStart(lint i)
	{
		a_prev_start = i;
	}
	void SetANextStart(lint i)
	{
		a_next_start = i;
	}
	void SetSrPrevStart(lint i)
	{
		sr_prev_start = i;
	}
	void SetSrNextStart(lint i)
	{
		sr_next_start = i;
	}
	void SetStPrevStart(lint, const string& = "");
	void SetStNextStart(lint, const string& = "");
	void SetPrevStart(lint, CListLevel, const string& = "");
	void SetNextStart(lint, CListLevel, const string& = "");
	void SetLMode(TLMode lm)
	{
		lmode = lm;
	}
	void SetStMode(TStMode sm)
	{
		stmode = sm;
	}
	void SetSubs(id_type p_nPublicationId, id_type p_nSectionNumber, id_type p_nLanguageId);
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
	void SetSubsRes(lint r)
	{
		subs_res = r;
	}
	void SetSubsTimeUnits(lint t)
	{
		nSubsTimeUnits = t;
	}
	void SetAddUser(bool au)
	{
		adduser = au;
	}
	void SetModifyUser(bool mu)
	{
		modifyuser = mu;
	}
	void SetAddUserRes(lint ur)
	{
		adduser_res = ur;
	}
	void SetModifyUserRes(lint ur)
	{
		modifyuser_res = ur;
	}
	void SetLogin(bool l)
	{
		login = l;
	}
	void SetLoginRes(lint lr)
	{
		login_res = lr;
	}
	void SetSearch(bool s)
	{
		search = s;
	}
	void SetSearchRes(lint sr)
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
	void SetTopic(id_type t) throw(InvalidValue)
	{
		if (t != -1 && !Topic::isValid(t))
			throw InvalidValue("topic identifier", (string)Integer(t));
		m_nTopicId = t;
	}
	void SetDefTopic(id_type t) throw(InvalidValue)
	{
		if (t != -1 && !Topic::isValid(t))
			throw InvalidValue("topic identifier", (string)Integer(t));
		m_nDefTopicId = t;
	}
	void SetAttachment(id_type p_nAttachment)
	{
		m_nAttachment = p_nAttachment;
	}
	void SetAttachmentExtension(const string& p_rcoAttachmentExtension)
	{
		m_coAttachmentExtension = p_rcoAttachmentExtension;
	}
	void SetArticleCommentId(id_type p_nArticleCommentId);
	void SetArticleCommentResult(id_type p_nSubmitArticleCommentResult)
	{
		m_nSubmitArticleCommentResult = p_nSubmitArticleCommentResult;
	}
	void SetURL(CURL* p_pcoURL)
	{
		m_pcoURL = p_pcoURL;
	}
	void SetDefURL(CURL* p_pcoURL)
	{
		m_pcoDefURL = p_pcoURL;
	}

	const string& UserInfo(const string&);
	bool IsUserInfo(const string&);
	ulint IP() const
	{
		return ip;
	}
	id_type User() const
	{
		return user_id;
	}
	ulint Key() const
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
	CListLevel Level() const
	{
		return level;
	}
	bool EncodeHTML() const
	{
		return m_bEncodeHTML;
	}
	id_type Language() const
	{
		return language_id;
	}
	id_type DefLanguage() const
	{
		return def_language_id;
	}
	id_type Publication() const
	{
		return publication_id;
	}
	id_type DefPublication() const
	{
		return def_publication_id;
	}
	id_type Issue() const
	{
		return issue_nr;
	}
	id_type DefIssue() const
	{
		return def_issue_nr;
	}
	id_type Section() const
	{
		return section_nr;
	}
	id_type DefSection() const
	{
		return def_section_nr;
	}
	id_type Article() const
	{
		return article_nr;
	}
	id_type DefArticle() const
	{
		return def_article_nr;
	}
	lint IListStart() const
	{
		return i_list_start;
	}
	lint SListStart() const
	{
		return s_list_start;
	}
	lint SrListStart() const
	{
		return sr_list_start;
	}
	lint StListStart(const string& = "");
	lint AListStart() const
	{
		return a_list_start;
	}
	lint ListStart(CListLevel, const string& = "");
	lint ListIndex() const
	{
		return list_index;
	}
	lint ListRow() const
	{
		return list_row;
	}
	lint ListColumn() const
	{
		return list_column;
	}
	lint ListLength() const
	{
		return list_length;
	}
	lint IPrevStart() const
	{
		return i_prev_start;
	}
	lint INextStart() const
	{
		return i_next_start;
	}
	lint SPrevStart() const
	{
		return s_prev_start;
	}
	lint SNextStart() const
	{
		return s_next_start;
	}
	lint APrevStart() const
	{
		return a_prev_start;
	}
	lint ANextStart() const
	{
		return a_next_start;
	}
	lint SrPrevStart() const
	{
		return sr_prev_start;
	}
	lint SrNextStart() const
	{
		return sr_next_start;
	}
	lint StPrevStart(const string& = "");
	lint StNextStart(const string& = "");
	lint PrevStart(CListLevel, const string& = "");
	lint NextStart(CListLevel, const string& = "");
	TLMode LMode() const
	{
		return lmode;
	}
	TStMode StMode() const
	{
		return stmode;
	}
	bool IsSubs(id_type p_nPublicationId, id_type p_nSectionNumber, id_type p_nLanguageId) const;
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
	lint SubsRes() const
	{
		return subs_res;
	}
	lint SubsTimeUnits() const
	{
		return nSubsTimeUnits;
	}
	bool AddUser() const
	{
		return adduser;
	}
	bool ModifyUser() const
	{
		return modifyuser;
	}
	lint AddUserRes() const
	{
		return adduser_res;
	}
	lint ModifyUserRes() const
	{
		return modifyuser_res;
	}
	bool Login() const
	{
		return login;
	}
	lint LoginRes() const
	{
		return login_res;
	}
	bool Search() const
	{
		return search;
	}
	lint SearchRes() const
	{
		return search_res;
	}
	bool SearchAnd() const
	{
		return search_and;
	}
	lint SearchLevel()
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
	id_type Topic() const
	{
		return m_nTopicId;
	}
	id_type DefTopic() const
	{
		return m_nDefTopicId;
	}
	id_type Attachment() const
	{
		return m_nAttachment;
	}
	const string& AttachmentExtension() const
	{
		return m_coAttachmentExtension;
	}
	bool ArticleCommentEnabled() const;
	CArticleComment* const ArticleComment() const { return m_pcoArticleComment; }
	id_type ArticleCommentId() const;
	int ArticleCommentLevel() const;
	bool SubmitArticleCommentEvent() const
	{
		return m_nSubmitArticleCommentResult >= 0;
	}
	id_type SubmitArticleCommentResult() const
	{
		return m_nSubmitArticleCommentResult;
	}
	CURL* URL() const
	{
		return m_pcoURL;
	}
	CURL* DefURL() const
	{
		return m_pcoDefURL;
	}

	void PrintSubs();
};

inline void CContext::SetURLValue(const string& p_coParam, id_type p_nValue)
{
	if (m_pcoURL != NULL) {
		if (p_nValue == -1)
			m_pcoURL->deleteParameter(p_coParam);
		else
			m_pcoURL->replaceValue(p_coParam, p_nValue);
	}
}

inline void CContext::SetDefURLValue(const string& p_coParam, id_type p_nValue)
{
	if (m_pcoDefURL != NULL) {
		if (p_nValue == -1)
			m_pcoDefURL->deleteParameter(p_coParam);
		else
			m_pcoDefURL->replaceValue(p_coParam, p_nValue);
	}
}

inline void CContext::EraseURLParam(const string& p_coParam)
{
	if (m_pcoURL != NULL)
		m_pcoURL->deleteParameter(p_coParam);
}

inline void CContext::EraseDefURLParam(const string& p_coParam)
{
	if (m_pcoDefURL != NULL)
		m_pcoDefURL->deleteParameter(p_coParam);
}

inline CContext::~CContext()
{
	if (m_pcoURL != NULL)
		delete m_pcoURL;
	if (m_pcoDefURL != NULL)
		delete m_pcoDefURL;
}

#endif
