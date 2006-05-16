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

#include "articlecomment.h"

CArticleComment::CArticleComment(id_type p_nMessageId)
	: m_bExists(false), m_nArticleNumber(-1), m_nLanguageId(-1), m_nMessageId(-1),
	m_nForumId(-1), m_nThreadId(-1), m_nParentId(-1), m_nStatus(-1), m_nModifyStamp(0),
	m_nUserId(-1), m_nThreadCount(0), m_nModeratorPost(-1), m_nSort(-1), m_nDateStamp(0),
	m_nViewcount(0), m_nClosed(-1), m_nLevel(-1)
{
}

CArticleComment::~CArticleComment()
{
}

bool CArticleComment::createComment(String2String& p_rcoValues, bool& p_rbRejected)
{
	p_rbRejected = false;
	return false;
}

bool CArticleComment::deleteComment()
{
	return false;
}

bool CArticleComment::setViewCount(ulint p_nViewCount)
{
	return true;
}

bool CArticleComment::setClosed(bool p_bClosed)
{
	return true;
}

bool CArticleComment::operator == (const CArticleComment& p_rcoSource) const
{
	return m_bExists == p_rcoSource.m_bExists
			&& m_nArticleNumber == p_rcoSource.m_nArticleNumber
			&& m_nLanguageId == p_rcoSource.m_nLanguageId
			&& m_nMessageId == p_rcoSource.m_nMessageId
			&& m_nForumId == p_rcoSource.m_nForumId
			&& m_nThreadId == p_rcoSource.m_nThreadId
			&& m_nParentId == p_rcoSource.m_nParentId
			&& m_coAuthor == p_rcoSource.m_coAuthor
			&& m_coSubject == p_rcoSource.m_coSubject
			&& m_coBody == p_rcoSource.m_coBody
			&& m_coEmail == p_rcoSource.m_coEmail
			&& m_coIP == p_rcoSource.m_coIP
			&& m_nStatus == p_rcoSource.m_nStatus
			&& m_coMsgId == p_rcoSource.m_coMsgId
			&& m_nModifyStamp == p_rcoSource.m_nModifyStamp
			&& m_nUserId == p_rcoSource.m_nUserId
			&& m_nThreadCount == p_rcoSource.m_nThreadCount
			&& m_nModeratorPost == p_rcoSource.m_nModeratorPost
			&& m_nSort == p_rcoSource.m_nSort
			&& m_nDateStamp == p_rcoSource.m_nDateStamp
			&& m_coMeta == p_rcoSource.m_coMeta
			&& m_nViewcount == p_rcoSource.m_nViewcount
			&& m_nClosed == p_rcoSource.m_nClosed
			&& m_nLevel == p_rcoSource.m_nLevel;
}

bool CArticleComment::IsUserBlocked(id_type p_nUserId)
{
	return false;
}

bool CArticleComment::Moderated(id_type p_nPublicationId)
{
	return false;
}

ulint CArticleComment::ArticleCommentCount(id_type p_nArticleNumber, id_type p_nLanguageId)
{
	return 0;
}

bool CArticleComment::ArticleCommentsEnabled(id_type p_nPublicationId, const string& p_rcoArticleType)
{
//	buf << "select comments_enabled from ArticleTypeMetadata where type_name = '";
	return true;
}

bool CArticleComment::CommentAccepted(String2String& p_rcoValues) const
{
	return true;
}
