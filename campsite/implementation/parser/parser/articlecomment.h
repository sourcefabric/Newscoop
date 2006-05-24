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

#ifndef _CMS_ARTICLECOMMENT
#define _CMS_ARTICLECOMMENT

#include <mysql/mysql.h>
#include <string>
#include <set>
#include <typeinfo>

#include "globals.h"
#include "cms_types.h"
#include "error.h"
#include "cgiparams.h"

class CArticleComment
{
	public:
		// default constructor; if p_nMessageId > 0 reads the comment from the database
		CArticleComment(id_type p_nMessageId = 0);

		// copy-constructor
		CArticleComment(const CArticleComment& p_rcoSource)
		{ *this = p_rcoSource; }

		~CArticleComment();

		/**
		 * exists(): returns true if the article comment exists
		 * @return bool
		**/
		bool exists() const { return m_bExists; }

		/**
		 * createComment(): creates an article comment from the list of values
		 * received as parameter. Returns true if succesful. Sets p_rbRejected to
		 * true if the comment was rejected by filters.
		 * @param String2String& p_rcoValues
		 * @param bool& p_rbRejected
		 * @return bool
		**/
		bool createComment(String2String& p_rcoValues, bool& p_rbRejected);

		/**
		 * deleteComment(): delete the current article comment
		 * @return bool
		 **/
		bool deleteComment();

		id_type getArticleNumber() const { return m_nArticleNumber; }

		id_type getLanguageId() const { return m_nLanguageId; }

		id_type getMessageId() const { return m_nMessageId; }

		id_type getForumId() const { return m_nForumId; }

		id_type getThreadId() const { return m_nThreadId; }

		id_type getParentId() const { return m_nParentId; }

		const string& getAuthor() const { return m_coAuthor; }

		const string& getSubject() const { return m_coSubject; }

		const string& getBody() const { return m_coBody; }

		const string& getEmail() const { return m_coEmail; }

		const string& getIP() const { return m_coIP; }

		int getStatus() const { return m_nStatus; }

		ulint getModifyStamp() const { return m_nModifyStamp; }

		id_type getUserId() const { return m_nUserId; }

		ulint getThreadCount() const { return m_nThreadCount; }

		ulint getDateStamp() const { return m_nDateStamp; }

		ulint getViewCount() const { return m_nViewcount; }

		bool incrementViewCount();

		int getClosed() const { return m_nClosed; }

		bool setClosed(bool p_bClosed);

		int getLevel() const { return m_nLevel; }

		bool operator == (const CArticleComment& p_rcoSource) const;

		bool operator != (const CArticleComment& p_rcoSource) const
		{ return !(*this == p_rcoSource); }

		/**
		 * IsUserBlocked(): returns true if the user was blocked from posting comments
		 * @param p_nUserId
		 * @return bool
		 **/
		static bool IsUserBlocked(id_type p_nUserId);

		/**
		 * Moderated(): returns true if the article comments are moderated for the given
		 * publication
		 * @param p_nPublicationId
		 * @return bool
		 **/
		static bool Moderated(id_type p_nPublicationId);

		/**
		 * ArticleCommentCount(): returns the number of comments for the given article
		 * @param id_type p_nArticleNumber
		 * @param id_type p_nLanguageId
		 * @return ulint
		 **/
		static ulint ArticleCommentCount(id_type p_nArticleNumber, id_type p_nLanguageId);

		/**
		 * ArticleCommentsEnabled(): returns true if article comments were enabled for the
		 * given publication and article type
		 * @param id_type p_nPublicationId
		 * @param id_type p_nArticleNumber
		 * @param id_type p_nLanguageId
		 * @param string p_rcoArticleType
		 * @return bool
		 **/
		static bool ArticleCommentsEnabled(id_type p_nPublicationId, id_type p_nArticleNumber,
										   id_type p_nLanguageId);

	private:
		bool m_bExists;
		id_type m_nArticleNumber;
		id_type m_nLanguageId;
		id_type m_nMessageId;
		id_type m_nForumId;
		id_type m_nThreadId;
		id_type m_nParentId;
		string m_coAuthor;
		string m_coSubject;
		string m_coBody;
		string m_coEmail;
		string m_coIP;
		int m_nStatus;
		ulint m_nModifyStamp;
		id_type m_nUserId;
		ulint m_nThreadCount;
		ulint m_nDateStamp;
		ulint m_nViewcount;
		int m_nClosed;
		int m_nLevel;

	private:
		/**
		 * CommentAccepted(): returns true if the comment defined by the list of values
		 * was accepted by the filters
		 * @param String2String& p_rcoValues
		 * @return bool
		**/
		bool CommentAccepted(String2String& p_rcoValues) const;
};

#endif
