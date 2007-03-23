{IF ReportPostMessage}
  <div class="PhorumUserError">{ReportPostMessage}</div>
{/IF}
<div class="PhorumNavBlock" style="text-align: left;">
  <span class="PhorumNavHeading PhorumHeadingLeft">{LANG->Goto}:</span>&nbsp;{IF URL->INDEX}<a class="PhorumNavLink" href="{URL->INDEX}">{LANG->ForumList}</a>&bull;{/IF}<a class="PhorumNavLink" href="{URL->TOP}">{LANG->MessageList}</a>&bull;<a class="PhorumNavLink" href="{URL->POST}">{LANG->NewTopic}</a>&bull;<a class="PhorumNavLink" href="{URL->SEARCH}">{LANG->Search}</a>&bull;{IF LOGGEDIN true}<a class="PhorumNavLink" href="{URL->LOGINOUT}">{LANG->LogOut}</a>{ELSE}<a class="PhorumNavLink" href="{URL->LOGINOUT}">{LANG->LogIn}</a>{/IF}
</div>
<div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left;">{LANG->Report}</div>
<div class="PhorumStdBlock" style="text-align: left;">
  <strong>{LANG->ConfirmReportMessage}</strong><br /><br />
  <div class="PhorumReadBodySubject">{PostSubject}</div>
  <div class="PhorumReadBodyHead">{LANG->Postedby}: {PostAuthor}</div>
  <div class="PhorumReadBodyHead">{LANG->Date}: {PostDate}</div>
  <div class="PhorumReadBodyText">{PostBody}</div><br />
  {LANG->ReportPostExplanation}<br />
  <form method="post" action="{ReportURL}">
    <textarea name="explanation" rows="5" cols="60" wrap="virtual">{explanation}</textarea><br />
    <input type="submit" name="report" value="{LANG->Report}" />
  </form>
</div>
