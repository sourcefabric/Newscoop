<div class="PhorumNavBlock" style="text-align: left;">
  <div style="float: right;">
    <span class="PhorumNavHeading">{LANG->GotoThread}:</span>&nbsp;<a class="PhorumNavLink" href="{URL->NEWERTHREAD}">{LANG->PrevPage}</a>&bull;<a class="PhorumNavLink" href="{URL->OLDERTHREAD}">{LANG->NextPage}</a>
  </div>
  <span class="PhorumNavHeading PhorumHeadingLeft">{LANG->Goto}:</span>&nbsp;{IF URL->INDEX}<a class="PhorumNavLink" href="{URL->INDEX}">{LANG->ForumList}</a>&bull;{/IF}<a class="PhorumNavLink" href="{URL->TOP}">{LANG->MessageList}</a>&bull;<a class="PhorumNavLink" href="{URL->POST}">{LANG->NewTopic}</a>&bull;<a class="PhorumNavLink" href="{URL->SEARCH}">{LANG->Search}</a>&bull;{IF LOGGEDIN true}<a class="PhorumNavLink" href="{URL->MARKTHREADREAD}">{LANG->MarkThreadRead}</a>&bull;<a class="PhorumNavLink" href="{URL->LOGINOUT}">{LANG->LogOut}</a>{ELSE}<a class="PhorumNavLink" href="{URL->LOGINOUT}">{LANG->LogIn}</a>{/IF}
</div>
{IF PAGES}
  {INCLUDE paging}
{/IF}
{LOOP MESSAGES}
  {IF NOT MESSAGES->parent_id 0}
    <a name="msg-{MESSAGES->message_id}"></a>
  {/IF}
  <div class="PhorumReadMessageBlock">
    {IF MESSAGES->is_unapproved}
      <div class="PhorumStdBlock">
        <div class="PhorumReadBodyHead"><strong>{LANG->UnapprovedMessage}</strong></div>
      </div>
    {/IF}
    <div class="PhorumStdBlock">
      {IF MESSAGES->parent_id 0}
        <div class="PhorumReadBodySubject">{MESSAGES->subject} <span class="PhorumNewFlag">{MESSAGES->new}</span></div>
      {ELSE}
        <div class="PhorumReadBodyHead"><strong>{MESSAGES->subject}</strong> <span class="PhorumNewFlag">{MESSAGES->new}</span></div>
      {/IF}
      <div class="PhorumReadBodyHead">{LANG->Postedby}: <strong>{MESSAGES->linked_author}</strong> ({MESSAGES->ip})</div>
      <div class="PhorumReadBodyHead">{LANG->Date}: {MESSAGES->datestamp}</div><br />
      <div class="PhorumReadBodyText">{MESSAGES->body}</div><br />
      {IF ATTACHMENTS}
        {IF MESSAGES->attachments}
          {LANG->Attachments}:
          {LOOP MESSAGES->attachments}
            <a href="{MESSAGES->attachments->url}">{MESSAGES->attachments->name} ({MESSAGES->attachments->size})</a>&nbsp;&nbsp;
          {/LOOP MESSAGES->attachments}
        {/IF}
      {/IF}
    </div>
    {IF MODERATOR true}
      <div class="PhorumReadNavBlock" style="text-align: left;">
        <span class="PhorumNavHeading PhorumHeadingLeft">{LANG->Moderate}:</span>&nbsp;{IF MESSAGES->threadstart true}<a class="PhorumNavLink" href="javascript:if(window.confirm('{LANG->ConfirmDeleteThread}')) window.location='{MESSAGES->delete_url2}';">{LANG->DeleteThread}</a>&bull;{IF MESSAGES->move_url}<a class="PhorumNavLink" href="{MESSAGES->move_url}">{LANG->MoveThread}</a>&bull;{/IF}<a class="PhorumNavLink" href="{MESSAGES->merge_url}">{LANG->MergeThread}</a>&bull;{IF MESSAGES->closed false}<a class="PhorumNavLink" href="{MESSAGES->close_url}">{LANG->CloseThread}</a>{ELSE}<a class="PhorumNavLink" href="{MESSAGES->reopen_url}">{LANG->ReopenThread}</a>{/IF}{ELSE}<a class="PhorumNavLink" href="javascript:if(window.confirm('{LANG->ConfirmDeleteMessage}')) window.location='{MESSAGES->delete_url1}';">{LANG->DeleteMessage}</a>&bull;<a class="PhorumNavLink" href="javascript:if(window.confirm('{LANG->ConfirmDeleteMessage}')) window.location='{MESSAGES->delete_url2}';">{LANG->DelMessReplies}</a>&bull;<a class="PhorumNavLink" href="{MESSAGES->split_url}">{LANG->SplitThread}</a>{/IF}{IF MESSAGES->is_unapproved}&bull;<a class="PhorumNavLink" href="{MESSAGES->approve_url}">{LANG->ApproveMessage}</a>{ELSE}&bull;<a class="PhorumNavLink" href="{MESSAGES->hide_url}">{LANG->HideMessage}</a>{/IF}&bull;<a class="PhorumNavLink" href="{MESSAGES->edit_url}">{LANG->EditPost}</a>
      </div>
    {/IF}
    <div class="PhorumReadNavBlock" style="text-align: left;">
      <span class="PhorumNavHeading PhorumHeadingLeft">{LANG->Options}:</span>&nbsp;<a class="PhorumNavLink" href="{MESSAGES->reply_url}">{LANG->Reply}</a>&bull;<a class="PhorumNavLink" href="{MESSAGES->quote_url}">{LANG->QuoteMessage}</a>{IF LOGGEDIN}{IF MESSAGES->private_reply_url}&bull;<a class="PhorumNavLink" href="{MESSAGES->private_reply_url}">{LANG->PrivateReply}</a>{/IF}&bull;<a class="PhorumNavLink" href="{MESSAGES->follow_url}">{LANG->FollowThread}</a>&bull;<a class="PhorumNavLink" href="{MESSAGES->report_url}">{LANG->Report}</a>{/IF}{IF MESSAGES->edit 1}&bull;<a class="PhorumNavLink" href="{MESSAGES->edituser_url}">{LANG->EditPost}</a>{/IF}
    </div>
  </div>
{/LOOP MESSAGES}
{IF PAGES}
  {INCLUDE paging}
{/IF}
<br /><br />
