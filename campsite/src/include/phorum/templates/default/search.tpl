<div class="PhorumNavBlock" style="text-align: left;">
  <span class="PhorumNavHeading PhorumHeadingLeft">{LANG->Goto}:</span>&nbsp;{IF URL->INDEX}<a class="PhorumNavLink" href="{URL->INDEX}">{LANG->ForumList}</a>&bull;{/IF}{IF URL->TOP}<a class="PhorumNavLink" href="{URL->TOP}">{LANG->MessageList}</a>&bull;{/IF}{IF URL->POST}<a class="PhorumNavLink" href="{URL->POST}">{LANG->NewTopic}</a>&bull;{/IF}{INCLUDE loginout_menu}
</div><br />
{IF SEARCH->noresults}
  <div align="center">
    <div class="PhorumStdBlockHeader PhorumNarrowBlock PhorumHeaderText" style="text-align: left;">
      {LANG->NoResults}
    </div>
    <div class="PhorumStdBlock PhorumNarrowBlock" style="text-align: left;">
      <div class="PhorumFloatingText">
        {LANG->NoResults Help}
      </div>
    </div>
  </div><br />
{/IF}
{IF SEARCH->showresults}
  {INCLUDE paging}
  <div class="PhorumStdBlockHeader" style="text-align: left;">
    <span class="PhorumHeadingLeft">{LANG->Results} {RANGE_START} - {RANGE_END} {LANG->of} {TOTAL}</span>
  </div>
  <div class="PhorumStdBlock">
    {LOOP MATCHES}
      <div class="PhorumRowBlock">
        <div class="PhorumColumnFloatLarge">{MATCHES->datestamp}</div>
        <div class="PhorumColumnFloatMedium">{MATCHES->author}</div>
        <div style="margin-right: 370px" class="PhorumLargeFont">{MATCHES->number}.&nbsp;<a href="{MATCHES->url}">{MATCHES->subject}</a></div>
        <div class="PhorumFloatingText">
          {MATCHES->short_body}<br />
          {IF MATCHES->forum_id}
            {LANG->Forum}: <a href="{MATCHES->forum_url}">{MATCHES->forum_name}</a>
          {ELSE}
            ({LANG->Announcement})
          {/IF}
        </div>
      </div>
    {/LOOP MATCHES}
  </div>
  {INCLUDE paging}
  <div class="PhorumNavBlock" style="text-align: left;">
    <span class="PhorumNavHeading PhorumHeadingLeft">{LANG->Goto}:</span>&nbsp;<a class="PhorumNavLink" href="{URL->INDEX}">{LANG->ForumList}</a>&bull;<a class="PhorumNavLink" href="{URL->TOP}">{LANG->MessageList}</a>&bull;<a class="PhorumNavLink" href="{URL->POST}">{LANG->NewTopic}</a>&bull;{IF LOGGEDIN true}<a class="PhorumNavLink" href="{URL->REGISTERPROFILE}">{LANG->MyProfile}</a>&bull;<a class="PhorumNavLink" href="{URL->LOGINOUT}">{LANG->LogOut}</a>{ELSE}<a class="PhorumNavLink" href="{URL->LOGINOUT}">{LANG->LogIn}</a>{/IF}
  </div><br />
{/IF}
<table width=100% border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="PhorumStdBlockHeader PhorumNarrowBlock" style="text-align: left;"><b>{LANG->SearchMessages}</b></td>
    <td style="width: 10px">&nbsp;</td>
    <td class="PhorumStdBlockHeader PhorumNarrowBlock" style="text-align: left;"><b>{LANG->SearchAuthors}</b></td>
  </tr>
  <tr>
    <td class="PhorumStdBlock PhorumNarrowBlock" style="padding: 10px;">
      <form action="{URL->ACTION}" method="get" style="display: inline;">
        <input type="hidden" name="forum_id" value="{SEARCH->forum_id}" />
        {POST_VARS}
        <input type="text" name="search" id="phorum_search_message" size="30" maxlength="" value="{SEARCH->safe_search}" />&nbsp;<input type="submit" value="{LANG->Search}" /><br />
        <div style="margin-top: 3px;">
          <select name="match_forum">
            <option value="ALL" {IF SEARCH->match_forum ALL}selected{/IF}>{LANG->MatchAllForums}</option>
            {IF SEARCH->allow_match_one_forum}
              <option value="THISONE" {IF SEARCH->match_forum THISONE}selected{/IF}>{LANG->MatchThisForum}</option>
            {/IF}
          </select>
        </div>
        <div style="margin-top: 3px;">
          <select name="match_dates">
            <option value="30" {IF SEARCH->match_dates 30}selected{/IF}>{LANG->Last30Days}</option>
            <option value="90" {IF SEARCH->match_dates 90}selected{/IF}>{LANG->Last90Days}</option>
            <option value="365" {IF SEARCH->match_dates 365}selected{/IF}>{LANG->Last365Days}</option>
            <option value="0" {IF SEARCH->match_dates 0}selected{/IF}>{LANG->AllDates}</option>
          </select>&nbsp;<select name="match_type">
            <option value="ALL" {IF SEARCH->match_type ALL}selected{/IF}>{LANG->MatchAll}</option>
            <option value="ANY" {IF SEARCH->match_type ANY}selected{/IF}>{LANG->MatchAny}</option>
            <option value="PHRASE" {IF SEARCH->match_type PHRASE}selected{/IF}>{LANG->MatchPhrase}</option>
          </select>
        </div>
      </form>
    </td>
    <td style="width: 10px">&nbsp;</td>
    <td class="PhorumStdBlock PhorumNarrowBlock" style="padding: 10px;">
      <form action="{URL->ACTION}" method="get" style="display: inline;">
        <input type="hidden" name="forum_id" value="{SEARCH->forum_id}" />
        <input type="hidden" name="match_type" value="AUTHOR" />
        {POST_VARS}
        <input type="text" id="phorum_search_author" name="search" size="30" maxlength="" value="{SEARCH->safe_search}" />&nbsp;<input type="submit" value="{LANG->Search}" /><br />
        <div style="margin-top: 3px;">
          <select name="match_forum">
            <option value="ALL" {IF SEARCH->match_forum ALL}selected{/IF}>{LANG->MatchAllForums}</option>
            {IF SEARCH->allow_match_one_forum}
              <option value="THISONE" {IF SEARCH->match_forum THISONE}selected{/IF}>{LANG->MatchThisForum}</option>
            {/IF}
          </select>
        </div>
        <div style="margin-top: 3px;">
          <select name="match_dates">
            <option value="30" {IF SEARCH->match_dates 30}selected{/IF}>{LANG->Last30Days}</option>
            <option value="90" {IF SEARCH->match_dates 90}selected{/IF}>{LANG->Last90Days}</option>
            <option value="365" {IF SEARCH->match_dates 365}selected{/IF}>{LANG->Last365Days}</option>
            <option value="0" {IF SEARCH->match_dates 0}selected{/IF}>{LANG->AllDates}</option>
          </select>
        </div>
      </form>
    </td>
  </tr>
</table>
