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

Implementation of CContext methods

******************************************************************************/

#include <iostream>

#include "context.h"
#include "cpublication.h"
#include "util.h"

using std::cout;

const string CContext::emptystring = "";

// default constructor
CContext::CContext()
{
	ip = 0;
	user_id = -1;
	key = 0;
	is_reader = true;
	access_by_ip = false;
	access = A_PUBLISHED;
	level = CLV_ROOT;
	m_bEncodeHTML = true;
	language_id
			= publication_id
			= issue_nr
			= section_nr
			= article_nr
			= i_list_start
			= s_list_start
			= a_list_start
			= sr_list_start
			= list_index
			= list_row
			= list_column
			= list_length
			= i_prev_start
			= i_next_start
			= s_prev_start
			= s_next_start
			= a_prev_start
			= a_next_start
			= sr_prev_start
			= sr_next_start
			= def_language_id
			= def_publication_id
			= def_issue_nr
			= def_section_nr
			= def_article_nr
			= -1;
	m_nTopicId = m_nDefTopicId = m_nAttachment = -1;
	m_pcoArticleComment = NULL;
	lmode = LM_NORMAL;
	stmode = STM_NORMAL;
	do_subscribe = false;
	subs_type = ST_NONE;
	by_publication = false;
	subs_res = -1;
	nSubsTimeUnits = 0;
	m_bArticleCommentEnabled = false;
	m_bArticleCommentEnabledValid = false;
	adduser = modifyuser = login = search = search_and = false;
	adduser_res = modifyuser_res = login_res = search_res = m_nSubmitArticleCommentResult = -1;
	search_level = 0;
	m_pcoURL = NULL;
	m_pcoDefURL = NULL;
	ResetKwdIt();
}

// copy constructor
CContext::CContext(const CContext& c) : m_pcoArticleComment(NULL)
{
	*this = c;
}

// compare operator
int CContext::operator ==(const CContext& c) const
{
	return userinfo == c.userinfo
			&& ip == c.ip
			&& user_id == c.user_id
			&& key == c.key
			&& is_reader == c.is_reader
			&& access_by_ip == c.access_by_ip
			&& access == c.access
			&& level == c.level
			&& m_bEncodeHTML == c.m_bEncodeHTML
			&& language_id == c.language_id
			&& def_language_id == c.def_language_id
			&& publication_id == c.publication_id
			&& def_publication_id == c.def_publication_id
			&& issue_nr == c.issue_nr
			&& def_issue_nr == c.def_issue_nr
			&& section_nr == c.section_nr
			&& def_section_nr == c.def_section_nr
			&& article_nr == c.article_nr
			&& def_article_nr == c.def_article_nr
			&& m_nTopicId == c.m_nDefTopicId
			&& i_list_start == c.i_list_start
			&& s_list_start == c.s_list_start
			&& a_list_start == c.a_list_start
			&& sr_list_start == c.sr_list_start
			&& list_index == c.list_index
			&& list_row == c.list_row
			&& list_column == c.list_column
			&& list_length == c.list_length
			&& i_prev_start == c.i_prev_start
			&& i_next_start == c.i_next_start
			&& s_prev_start == c.s_prev_start
			&& s_next_start == c.s_next_start
			&& a_prev_start == c.a_prev_start
			&& a_next_start == c.a_next_start
			&& sr_prev_start == c.sr_prev_start
			&& sr_next_start == c.sr_next_start
			&& lmode == c.lmode
			&& stmode == c.stmode
			&& subs == c.subs
			&& keywords == c.keywords
			&& str_keywords == c.str_keywords
			&& do_subscribe == c.do_subscribe
			&& subs_type == c.subs_type
			&& by_publication == c.by_publication
			&& subs_res == c.subs_res
			&& nSubsTimeUnits == c.nSubsTimeUnits
			&& adduser == c.adduser
			&& modifyuser == c.modifyuser
			&& adduser_res == c.adduser_res
			&& modifyuser_res == c.modifyuser_res
			&& login == c.login
			&& login_res == c.login_res
			&& search == c.search
			&& search_res == c.search_res
			&& search_and == c.search_and
			&& search_level == c.search_level
			&& start_subtitle == c.start_subtitle
			&& default_start_subtitle == c.default_start_subtitle
			&& all_subtitles == c.all_subtitles
			&& st_list_start == c.st_list_start
			&& st_prev_start == c.st_prev_start
			&& st_next_start == c.st_next_start
			&& subtitles == c.subtitles
			&& fields == c.fields
			&& current_field == c.current_field
			&& current_art_type == c.current_art_type
			&& m_nAttachment == c.m_nAttachment
			&& m_coAttachmentExtension == c.m_coAttachmentExtension
			&& m_bArticleCommentEnabled == c.m_bArticleCommentEnabled
			&& m_bArticleCommentEnabledValid == c.m_bArticleCommentEnabledValid
			&& ((m_pcoArticleComment == NULL && c.m_pcoArticleComment == NULL)
				|| (m_pcoArticleComment != NULL && c.m_pcoArticleComment != NULL
				&& *m_pcoArticleComment == *c.m_pcoArticleComment))
			&& m_nSubmitArticleCommentResult == c.m_nSubmitArticleCommentResult
			&& m_pcoURL->equalTo(c.m_pcoURL)
			&& m_pcoDefURL->equalTo(c.m_pcoDefURL);
}

// assign operator
const CContext& CContext::operator =(const CContext& s)
{
	if (this == &s)
		return *this;
	userinfo = s.userinfo;
	ip = s.ip;
	user_id = s.user_id;
	key = s.key;
	is_reader = s.is_reader;
	access_by_ip = s.access_by_ip;
	access = s.access;
	level = s.level;
	m_bEncodeHTML = s.m_bEncodeHTML;
	language_id = s.language_id;
	def_language_id = s.def_language_id;
	publication_id = s.publication_id;
	def_publication_id = s.def_publication_id;
	issue_nr = s.issue_nr;
	def_issue_nr = s.def_issue_nr;
	section_nr = s.section_nr;
	def_section_nr = s.def_section_nr;
	article_nr = s.article_nr;
	def_article_nr = s.def_article_nr;
	m_nTopicId = s.m_nTopicId;
	m_nDefTopicId = s.m_nDefTopicId;
	i_list_start = s.i_list_start;
	s_list_start = s.s_list_start;
	a_list_start = s.a_list_start;
	sr_list_start = s.sr_list_start;
	list_index = s.list_index;
	list_row = s.list_row;
	list_column = s.list_column;
	list_length = s.list_length;
	i_prev_start = s.i_prev_start;
	i_next_start = s.i_next_start;
	s_prev_start = s.s_prev_start;
	s_next_start = s.s_next_start;
	a_prev_start = s.a_prev_start;
	a_next_start = s.a_next_start;
	sr_prev_start = s.sr_prev_start;
	sr_next_start = s.sr_next_start;
	st_list_start = s.st_list_start;
	st_prev_start = s.st_prev_start;
	st_next_start = s.st_next_start;
	lmode = s.lmode;
	stmode = s.stmode;
	subs = s.subs;
	keywords = s.keywords;
	kw_i = keywords.begin();
	str_keywords = s.str_keywords;
	do_subscribe = s.do_subscribe;
	subs_type = s.subs_type;
	by_publication = s.by_publication;
	subs_res = s.subs_res;
	nSubsTimeUnits = s.nSubsTimeUnits;
	adduser = s.adduser;
	adduser_res = s.adduser_res;
	modifyuser = s.modifyuser;
	modifyuser_res = s.modifyuser_res;
	login = s.login;
	login_res = s.login_res;
	search = s.search;
	search_res = s.search_res;
	search_and = s.search_and;
	search_level = s.search_level;
	start_subtitle = s.start_subtitle;
	default_start_subtitle = s.default_start_subtitle;
	all_subtitles = s.all_subtitles;
	subtitles = s.subtitles;
	fields = s.fields;
	current_field = s.current_field;
	current_art_type = s.current_art_type;
	m_nAttachment = s.m_nAttachment;
	m_coAttachmentExtension = s.m_coAttachmentExtension;
	m_bArticleCommentEnabled = s.m_bArticleCommentEnabled;
	m_bArticleCommentEnabledValid = s.m_bArticleCommentEnabledValid;
	if (m_pcoArticleComment != NULL)
	{
		delete m_pcoArticleComment;
		m_pcoArticleComment = NULL;
	}
	if (s.m_pcoArticleComment != NULL)
	{
		m_pcoArticleComment = new CArticleComment(*s.m_pcoArticleComment);
	}
	m_nSubmitArticleCommentResult = s.m_nSubmitArticleCommentResult;
	if (s.m_pcoURL != NULL)
		m_pcoURL = s.m_pcoURL->clone();
	else
		m_pcoURL = NULL;
	if (s.m_pcoDefURL != NULL)
		m_pcoDefURL = s.m_pcoDefURL->clone();
	else
		m_pcoDefURL = NULL;
	ResetKwdIt();
	return *this;
}

void CContext::SetArticleCommentId(id_type p_nArticleCommentId)
{
	if (m_pcoArticleComment != NULL)
	{
		delete m_pcoArticleComment;
		m_pcoArticleComment = NULL;
	}
	if (p_nArticleCommentId > 0)
	{
		m_pcoArticleComment = new CArticleComment(p_nArticleCommentId);
	}
}

id_type CContext::ArticleCommentId() const
{
	if (m_pcoArticleComment != NULL)
	{
		return m_pcoArticleComment->getMessageId();
	}
	return -1;
}

int CContext::ArticleCommentLevel() const
{
	if (m_pcoArticleComment != NULL)
	{
		return m_pcoArticleComment->getLevel();
	}
	return -1;
}

bool CContext::ArticleCommentEnabled() const
{
	if (!m_bArticleCommentEnabledValid)
	{
		string coArticleType;
		if (article_nr > 0)
		{
			stringstream buf;
			buf << "select Type from Articles where Number = '" << article_nr
					<< "' and IdLanguage = '" << language_id << "'";
			SQLQuery(MYSQLConnection(), buf.str().c_str());
			MYSQL_RES *res = mysql_store_result(MYSQLConnection());
			if (res == NULL)
			{
				return false;
			}
			MYSQL_ROW row = mysql_fetch_row(res);
			if (row == NULL)
			{
				return false;
			}
			coArticleType = row[0];
		}
		m_bArticleCommentEnabled = CArticleComment::ArticleCommentsEnabled(publication_id, coArticleType);
		m_bArticleCommentEnabledValid = true;
	}
	return m_bArticleCommentEnabled;
}

void CContext::SetLanguage(id_type l)
{
	if (language_id == l)
	{
		return;
	}
	SetURLValue(P_IDLANG, l);
	language_id = l;
	ResetPublicationParams(CLV_PUB_ISSUE);
}

void CContext::SetDefLanguage(id_type l)
{
	if (def_language_id == l)
	{
		return;
	}
	SetDefURLValue(P_IDLANG, l);
	def_language_id = l;
	ResetDefPublicationParams(CLV_PUB_ISSUE);
}

void CContext::SetPublication(id_type p)
{
	if (publication_id == p)
	{
		return;
	}
	SetURLValue(P_IDPUBL, p);
	publication_id = p;
	ResetPublicationParams(CLV_PUB_ISSUE);
}

void CContext::SetDefPublication(id_type p)
{
	if (def_publication_id == p)
	{
		return;
	}
	SetDefURLValue(P_IDPUBL, p);
	def_publication_id = p;
	ResetDefPublicationParams(CLV_PUB_ISSUE);
}

void CContext::SetIssue(id_type i)
{
	if (issue_nr == i)
	{
		return;
	}
	SetURLValue(P_NRISSUE, i);
	issue_nr = i;
	ResetPublicationParams(CLV_PUB_SECTION);
}

void CContext::SetDefIssue(id_type i)
{
	if (def_issue_nr == i)
	{
		return;
	}
	SetDefURLValue(P_NRISSUE, i);
	def_issue_nr = i;
	ResetDefPublicationParams(CLV_PUB_SECTION);
}

void CContext::SetSection(id_type s)
{
	if (section_nr == s)
	{
		return;
	}
	SetURLValue(P_NRSECTION, s);
	section_nr = s;
	ResetPublicationParams(CLV_PUB_ARTICLE);
}

void CContext::SetDefSection(id_type s)
{
	if (def_section_nr == s)
	{
		return;
	}
	SetDefURLValue(P_NRSECTION, s);
	def_section_nr = s;
	ResetDefPublicationParams(CLV_PUB_ARTICLE);
}

void CContext::SetArticle(id_type a)
{
	if (article_nr == a)
	{
		return;
	}
	SetURLValue(P_NRARTICLE, a);
	article_nr = a;
	m_bArticleCommentEnabledValid = false;
}

// SetStListStart: set the subtitles list start element to value for the given article
//		content(field) .If field is empty ("") set the subtitles start element to value for the
//		current article content
// Parameters:
//		lint value - start element from list
//		const string& field - field (from database) to set the start element for
void CContext::SetStListStart(lint value, const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return ;
	String2LInt::iterator it;
	it = st_list_start.find(actualField);
	if (it != st_list_start.end())
		st_list_start.erase(it);
	st_list_start[actualField] = value;
	String2StringList::iterator it2;
	it2 = subtitles.find(actualField);
	if (it2 == subtitles.end())
		return ;
	String2StringListIt::iterator it3;
	it3 = subtitles_it.find(actualField);
	if (it3 == subtitles_it.end())
		return ;
	(*it3).second = (*it2).second.begin();
	for (int i = 0; i < (*it).second && (*it3).second != (*it2).second.end();
	        i++, ++(*it3).second);
}

// SetStPrevStart: set the subtitles list start element to value for the given article
//		content(field) in "previous" context. If field is empty ("") set the subtitles start
//		element to value for the current article content
// Parameters:
//		lint value - start element from list
//		const string& field - field (from database) to set the start element for
void CContext::SetStPrevStart(lint value, const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return ;
	String2LInt::iterator it;
	it = st_prev_start.find(actualField);
	if (it != st_prev_start.end())
		st_prev_start.erase(it);
	st_prev_start[actualField] = value;
}

// SetStNextStart: set the subtitles list start element to value for the given article
//		content(field) in "next" context. If field is empty ("") set the subtitles start
//		element to value for the current article content
// Parameters:
//		lint value - start element from list
//		const string& field - field (from database) to set the start element for
void CContext::SetStNextStart(lint value, const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return ;
	String2LInt::iterator it;
	it = st_next_start.find(actualField);
	if (it != st_next_start.end())
		st_next_start.erase(it);
	st_next_start[actualField] = value;
}

// SetListStart: set the start element in a list to value; if the level (l) is CLV_SUBTITLE_LIST
//		set it for the given article content(field). If field is empty ("") set the subtitles
//		start element to value for the current article content
// Parameters:
//		lint value - start element from list
//		CListLevel l - list level
//		const string& field - field (from database) to set the start element for
void CContext::SetListStart(lint value, CListLevel l, const string& field)
{
	if (l == CLV_ROOT)
		return ;
	if (l == CLV_ISSUE_LIST)
		i_list_start = value;
	else if (l == CLV_SECTION_LIST)
		s_list_start = value;
	else if (l == CLV_ARTICLE_LIST)
		a_list_start = value;
	else if (l == CLV_SEARCHRESULT_LIST)
		sr_list_start = value;
	else
		SetStListStart(value, field);
}

// SetPrevStart: set the start element in a list to value in "previous" context; if the
//		level (l) is CLV_SUBTITLE_LIST set it for the given article content(field).
//		If field is empty ("") set the subtitles start element to value for the current
//		article content
// Parameters:
//		lint value - start element from list
//		CListLevel l - list level
//		const string& field - field (from database) to set the start element for
void CContext::SetPrevStart(lint val, CListLevel l, const string& field)
{
	if (l == CLV_ROOT)
		return ;
	if (l == CLV_ISSUE_LIST)
		i_prev_start = val;
	else if (l == CLV_SECTION_LIST)
		s_prev_start = val;
	else if (l == CLV_ARTICLE_LIST)
		a_prev_start = val;
	else if (l == CLV_SEARCHRESULT_LIST)
		sr_prev_start = val;
	else
		SetStPrevStart(val, field);
}

// SetNextStart: set the start element in a list to value in "next" context; if the
//		level (l) is CLV_SUBTITLE_LIST set it for the given article content(field).
//		If field is empty ("") set the subtitles start element to value for the current
//		article content
// Parameters:
//		lint value - start element from list
//		CListLevel l - list level
//		const string& field - field (from database) to set the start element for
void CContext::SetNextStart(lint val, CListLevel l, const string& field)
{
	if (l == CLV_ROOT)
		return ;
	if (l == CLV_ISSUE_LIST)
		i_next_start = val;
	else if (l == CLV_SECTION_LIST)
		s_next_start = val;
	else if (l == CLV_ARTICLE_LIST)
		a_next_start = val;
	else if (l == CLV_SEARCHRESULT_LIST)
		sr_next_start = val;
	else
		SetStNextStart(val, field);
}

// SetUserInfo: set user attribute/value
// Parameters:
//		const string& attr - attribute
//		const string& value - value
void CContext::SetUserInfo(const string& attr, const string& value)
{
	if (userinfo.find(attr) == userinfo.end())
		userinfo[attr] = value;
}

// SetSubs: set subscription for current user
//		id_type p_nPublicationId - publication identifier
//		id_type p_nSectionNumber - section identifier
//		id_type p_nLanguageId - language identifier
void CContext::SetSubs(id_type p_nPublicationId, id_type p_nSectionNumber, id_type p_nLanguageId)
{
	LInt2LIntMultiMap::iterator coPubIt = subs.find(p_nPublicationId);
	if (coPubIt == subs.end())
	{
		subs.insert(LInt2LIntMultiMap::value_type(p_nPublicationId, LIntMultiMap()));
		coPubIt = subs.find(p_nPublicationId);
	}
	if (coPubIt == subs.end())
	{
		return ;
	}
	pair<LIntMultiMap::iterator, LIntMultiMap::iterator> coSectionRange;
	coSectionRange = (*coPubIt).second.equal_range(p_nSectionNumber);
	LIntMultiMap::iterator coSectionIt = coSectionRange.first;
	for (; coSectionIt != coSectionRange.second; ++coSectionIt)
	{
		if ((*coSectionIt).second == p_nLanguageId)
		{
			return;
		}
	}
	(*coPubIt).second.insert(pair<lint, lint>(p_nSectionNumber, p_nLanguageId));
}

// AppendSubtitle: append subtitle to the article content (field) subtitle list; if field is
//		empty ("") perform the operation for the current field
// Parameters:
//		const string& subtitle - subtitle to append
//		const string& field - the field (article content) identifying the subtitles list to
//			which to append to
void CContext::AppendSubtitle(const string& subtitle, const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return ;
	String2StringList::iterator it;
	it = subtitles.find(actualField);
	if (it == subtitles.end())
	{
		subtitles[actualField] = StringList();
		it = subtitles.find(actualField);
	}
	if (it == subtitles.end())
		return ;
	(*it).second.insert((*it).second.end(), subtitle);
}

// ResetSubtitles: reset subtitles list for the given field (article content); if field is
//		empty ("") perform the operation for all fields
// Parameters:
//		const string& p_rcoField - the field (article content) identifying the subtitles list to
//			reset
void CContext::ResetSubtitles(const string& p_rcoField)
{
	if (p_rcoField == "")
	{
		subtitles.clear();
		return ;
	}
	String2StringList::iterator it;
	it = subtitles.find(p_rcoField);
	if (it != subtitles.end())
		(*it).second.clear();
}

// SetStartSubtitle: set the start subtitle of given article content (field) for printing
// Parameters:
//		int subtitle_nr - subtitle number
//		const string& field - field (article content) for which to set the start subtitle
void CContext::SetStartSubtitle(int subtitle_nr, const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return ;
	String2Int::iterator it;
	it = start_subtitle.find(actualField);
	if (it != start_subtitle.end())
		(*it).second = subtitle_nr;
	else
		start_subtitle[actualField] = subtitle_nr;
}

// SetDefaultStartSubtitle: set the default start subtitle of given article content (field)
// Parameters:
//		int subtitle_nr - subtitle number
//		const string& field - field (article content) for which to set the start subtitle
void CContext::SetDefaultStartSubtitle(int subtitle_nr, const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return ;
	String2Int::iterator it;
	it = default_start_subtitle.find(actualField);
	if (it != default_start_subtitle.end())
		(*it).second = subtitle_nr;
	else
		default_start_subtitle[actualField] = subtitle_nr;
}

// SetAllSubtitles: set all_subtitles value to true/false for the given field (article content)
//		If all_subtitles is true the whole article content is printed; else, only the start
//		subtitle of the article content is printed
// Parameters:
//		bool a - value to be set to all_subtitles
//		const string& field - field (article content) for which to set all_subtitles value
void CContext::SetAllSubtitles(bool a, const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return ;
	String2Bool::iterator it;
	it = all_subtitles.find(actualField);
	if (it != all_subtitles.end())
		(*it).second = a;
	else
		all_subtitles[actualField] = a;
}

// SetField: add field/article type pair to fields list
// Parameters:
//		const string& f - field
//		const string& at - article type
void CContext::SetField(const string& f, const string& at)
{
	if (f == "" || at == "")
		return ;
	fields[f] = at;
}

// StListStart: return subtitles list start for the given field (article content)
//		If the field is empty ("") perform the action for the current field
// Parameters:
//		const string& field - field (article content)
lint CContext::StListStart(const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return -1;
	String2LInt::iterator it;
	it = st_list_start.find(actualField);
	if (it != st_list_start.end())
		return (*it).second;
	return -1;
}

// StPrevStart: return subtitles list start in previous context for the given field
//		(article content). If the field is empty ("") perform the action for the current field
// Parameters:
//		const string& field - field (article content)
lint CContext::StPrevStart(const string &field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return -1;
	String2LInt::iterator it;
	it = st_prev_start.find(actualField);
	if (it != st_prev_start.end())
		return (*it).second;
	return -1;
}

// StNextStart: return subtitles list start in next context for the given field
//		(article content). If the field is empty ("") perform the action for the current field
// Parameters:
//		const string& field - field (article content)
lint CContext::StNextStart(const string &field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return -1;
	String2LInt::iterator it;
	it = st_next_start.find(actualField);
	if (it != st_next_start.end())
		return (*it).second;
	return -1;
}

// ListStart: return list start for the given level. If l is SLV_SUBTITLE_LIST return
//		subtitles list start for the given field.
// Parameters:
//		CListLevel l - list level
//		const string& field - field (article content)
lint CContext::ListStart(CListLevel l, const string& field)
{
	switch (l)
	{
	case CLV_ISSUE_LIST:
		return i_list_start;
	case CLV_SECTION_LIST:
		return s_list_start;
	case CLV_ARTICLE_LIST:
		return a_list_start;
	case CLV_SEARCHRESULT_LIST:
		return sr_list_start;
	case CLV_SUBTITLE_LIST:
		return StListStart(field);
	default:
		return -1;
	}
}

// PrevStart: return list start in previous context for the given level. If l is
//		SLV_SUBTITLE_LIST return subtitles list start for the given field.
// Parameters:
//		CListLevel l - list level
//		const string& field - field (article content)
lint CContext::PrevStart(CListLevel l, const string& field)
{
	switch (l)
	{
	case CLV_ISSUE_LIST:
		return i_prev_start;
	case CLV_SECTION_LIST:
		return s_prev_start;
	case CLV_ARTICLE_LIST:
		return a_prev_start;
	case CLV_SEARCHRESULT_LIST:
		return sr_prev_start;
	case CLV_SUBTITLE_LIST:
		return StPrevStart(field);
	default:
		return -1;
	}
}

// NextStart: return list start in next context for the given level. If l is
//		SLV_SUBTITLE_LIST return subtitles list start for the given field.
// Parameters:
//		CListLevel l - list level
//		const string& field - field (article content)
lint CContext::NextStart(CListLevel l, const string& field)
{
	switch (l)
	{
	case CLV_ISSUE_LIST:
		return i_next_start;
	case CLV_SECTION_LIST:
		return s_next_start;
	case CLV_ARTICLE_LIST:
		return a_next_start;
	case CLV_SEARCHRESULT_LIST:
		return sr_next_start;
	case CLV_SUBTITLE_LIST:
		return StNextStart(field);
	default:
		return -1;
	}
}

// UserInfo: return the value of an user attribute
// Parameters:
//		const string& attr - user attribute
const string& CContext::UserInfo(const string& attr)
{
	String2String::iterator p_i = userinfo.find(attr);
	if (p_i == userinfo.end())
		return emptystring;
	return (*p_i).second;
}

// IsUserInfo: return true if an user attribute is defined
// Parameters:
//		const string& attr - user attribute
bool CContext::IsUserInfo(const string& attr)
{
	return (userinfo.find(attr) != userinfo.end());
}

// IsSubs: return true if the subscription to given publication/section exists
// Parameters:
//		id_type p_nPublicationId - publication identifier
//		id_type p_nSectionId - section identifier
//		id_type p_nLanguageId - language identifier
bool CContext::IsSubs(id_type p_nPublicationId, id_type p_nSectionNumber, id_type p_nLanguageId) const
{
	LInt2LIntMultiMap::const_iterator coPubIt = subs.find(p_nPublicationId);
	if (coPubIt == subs.end())
	{
		return false;
	}
	pair<LIntMultiMap::const_iterator, LIntMultiMap::const_iterator> coSectionRange;
	coSectionRange = (*coPubIt).second.equal_range(p_nSectionNumber);
	LIntMultiMap::const_iterator coSectionIt = coSectionRange.first;
	for (; coSectionIt != coSectionRange.second; ++coSectionIt)
	{
		if ((*coSectionIt).second == p_nLanguageId || (*coSectionIt).second == 0)
		{
			return true;
		}
	}
	return false;
}

// NextKwd: returns the next keyword in the keywords list
const char* CContext::NextKwd()
{
	if (kw_i == keywords.end())
		return NULL;
	const char* k = (*kw_i).c_str();
	++kw_i;
	return k;
}

// SubtitlesNumber: returns the number of subtitles for the given field (article content)
//		If field is empty ("") perform the action for the current field
// Parameters:
//		const string& field - field (article content)
int CContext::SubtitlesNumber(const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return 0;
	if (subtitles.find(actualField) != subtitles.end())
		return (*subtitles.find(actualField)).second.size();
	return 0;
}

// FieldArtType: returns the article type corresponding to a given field (article content)
// Parameters:
//		const string& field - field (article content)
const string& CContext::FieldArtType(const string& field)
{
	if (field == "")
		return emptystring;
	if (fields.find(field) != fields.end())
		return (*fields.find(field)).second;
	return emptystring;
}

// NextSubtitle: returns the next subtitle in the list for the given field (article content)
//		If field is empty ("") perform the action for the current field
// Parameters:
//		const string& field - field (article content)
const string& CContext::NextSubtitle(const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return emptystring;
	String2StringList::iterator it = subtitles.find(actualField);
	if (it == subtitles.end())
		return emptystring;
	String2StringListIt::iterator it2 = subtitles_it.find(actualField);
	if (it2 == subtitles_it.end())
	{
		subtitles_it[actualField] = (*it).second.begin();
		it2 = subtitles_it.find(actualField);
	}
	if (it2 == subtitles_it.end())
		return emptystring;
	if ((*it2).second == (*it).second.end())
		return emptystring;
	TK_const_string result = &(*((*it2).second));
	++((*it2).second);
	return *(&result);
}

// CurrentSubtitle: returns the current subtitle in the list for the given field (article content)
//		If field is empty ("") perform the action for the current field
// Parameters:
//		const string& field - field (article content)
const string& CContext::CurrentSubtitle(const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return emptystring;
	String2StringList::iterator it;
	it = subtitles.find(actualField);
	if (it == subtitles.end())
		return emptystring;
	String2StringListIt::iterator it2;
	it2 = subtitles_it.find(actualField);
	if (it2 == subtitles_it.end())
	{
		subtitles_it[actualField] = (*it).second.begin();
		it2 = subtitles_it.find(actualField);
	}
	if (it2 == subtitles_it.end())
		return emptystring;
	if ((*it2).second == (*it).second.end())
		return emptystring;
	return (*((*it2).second));
}

// SelectSubtitle: returns the selected subtitle in the list for the given field (article content)
//		If field is empty ("") perform the action for the current field
// Parameters:
//		int index - selected subtitle number
//		const string& field - field (article content)
const string& CContext::SelectSubtitle(int index, const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return emptystring;
	String2StringList::iterator it;
	it = subtitles.find(actualField);
	if (it == subtitles.end())
		return emptystring;
	String2StringListIt::iterator it2;
	it2 = subtitles_it.find(actualField);
	if (it2 != subtitles_it.end())
		subtitles_it.erase(it2);
	subtitles_it[actualField] = (*it).second.begin();
	it2 = subtitles_it.find(actualField);
	if (it2 == subtitles_it.end())
		return emptystring;
	int i;
	for (i = 0; i < index; i++)
	{
		if ((*it2).second == (*it).second.end())
			return emptystring;
		++(*it2).second;
	}
	if (i == index && (*it2).second != (*it).second.end())
		return *((*it2).second);
	return emptystring;
}

// StartSubtitle: return the start subtitle (for printing purposes) for the given field (article
//		content). If field is empty ("") perform the action for the current field
// Parameters:
//		const string& field - field (article content)
int CContext::StartSubtitle(const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return 0;
	if (start_subtitle.find(actualField) == start_subtitle.end())
		return 0;
	return (*start_subtitle.find(actualField)).second;
}

// DefaultStartSubtitle: return the default start subtitle for the given field (article
//		content). If field is empty ("") perform the action for the current field
// Parameters:
//		const string& field - field (article content)
int CContext::DefaultStartSubtitle(const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return 0;
	if (default_start_subtitle.find(actualField) == default_start_subtitle.end())
		return 0;
	return (*default_start_subtitle.find(actualField)).second;
}

// AllSubtitles: return the all_subtitles value for the given field (article
//		content). If field is empty ("") perform the action for the current field
// Parameters:
//		const string& field - field (article content)
int CContext::AllSubtitles(const string& field)
{
	string actualField = (field == "" ? current_field : field);
	if (actualField == "")
		return 0;
	if (all_subtitles.find(actualField) == all_subtitles.end())
		return 0;
	return (*all_subtitles.find(actualField)).second;
}

// PrintSubs: print subscriptions (for test purposes)
void CContext::PrintSubs()
{
	for (LInt2LIntMultiMap::iterator p_i = subs.begin(); p_i != subs.end(); ++p_i)
	{
		cout << "publication: " << (*p_i).first << endl;
		LIntMultiMap::iterator s_i;
		for (s_i = (*p_i).second.begin(); s_i != (*p_i).second.end(); ++s_i)
			cout << "\tsection: " << (*s_i).first << " language: " << (*s_i).second << endl;
		cout << endl;
	}
}

void CContext::ResetPublicationParams(CPubLevel p_nLevel)
{
	MYSQL* pMySQL = MYSQLConnection();
	if (pMySQL == NULL)
		return;
	bool bResetSection = false;
	bool bResetArticle = false;
	if (p_nLevel == CLV_PUB_ISSUE
		   && !CPublication::isValidIssue(language_id, publication_id, issue_nr, pMySQL))
	{
		issue_nr = -1;
		SetURLValue(P_NRISSUE, issue_nr);
		bResetSection = true;
	}
	if (p_nLevel <= CLV_PUB_SECTION
		   && (bResetSection
		   || !CPublication::isValidSection(language_id, publication_id, issue_nr, section_nr,
											pMySQL)))
	{
		section_nr = -1;
		SetURLValue(P_NRSECTION, section_nr);
		bResetArticle = true;
	}
	if (p_nLevel <= CLV_PUB_ARTICLE
		   && (bResetSection || bResetArticle
		   || !CPublication::isValidArticle(language_id, publication_id, issue_nr,
											section_nr, article_nr, pMySQL)))
	{
		article_nr = -1;
		m_bArticleCommentEnabledValid = false;
		SetURLValue(P_NRARTICLE, article_nr);
	}
}

void CContext::ResetDefPublicationParams(CPubLevel p_nLevel)
{
	MYSQL* pMySQL = MYSQLConnection();
	if (pMySQL == NULL)
		return;
	bool bResetSection = false;
	bool bResetArticle = false;
	if (p_nLevel == CLV_PUB_ISSUE
		   && !CPublication::isValidIssue(def_language_id, def_publication_id, def_issue_nr,
										  pMySQL))
	{
		def_issue_nr = -1;
		SetDefURLValue(P_NRISSUE, def_issue_nr);
		bResetSection = true;
	}
	if (p_nLevel <= CLV_PUB_SECTION
		   && (bResetSection
		   || !CPublication::isValidSection(def_language_id, def_publication_id, def_issue_nr,
											def_section_nr, pMySQL)))
	{
		def_section_nr = -1;
		SetDefURLValue(P_NRSECTION, def_section_nr);
		bResetArticle = true;
	}
	if (p_nLevel <= CLV_PUB_ARTICLE
		   && (bResetSection || bResetArticle
		   || !CPublication::isValidArticle(def_language_id, def_publication_id, def_issue_nr,
											def_section_nr, def_article_nr, pMySQL)))
	{
		def_article_nr = -1;
		SetDefURLValue(P_NRARTICLE, def_article_nr);
		m_nAttachment = -1;
	}
}
