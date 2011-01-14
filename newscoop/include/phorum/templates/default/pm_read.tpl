<form action="{ACTION}" method="post">
  {POST_VARS}
  <input type="hidden" name="action" value="list" />
  <input type="hidden" name="folder_id" value="{FOLDER_ID}" />
  <input type="hidden" name="forum_id" value="{FORUM_ID}" />
  <input type="hidden" name="pm_id" value="{MESSAGE->pm_message_id}" />
  <div class="PhorumStdBlock">
    <div class="PhorumReadBodySubject">{MESSAGE->subject}</div>
    <div class="PhorumReadBodyHead">{LANG->From}: <strong><a href="{MESSAGE->from_profile_url}">{MESSAGE->from_username}</a></strong></div>
    <div class="PhorumReadBodyHead">
      {LANG->To}:
      {ASSIGN ISFIRST true}
      {LOOP MESSAGE->recipients}
        <div style="display:inline; white-space: nowrap">
          {IF NOT ISFIRST} / {/IF}
          <strong><a href="{MESSAGE->recipients->to_profile_url}">{MESSAGE->recipients->username}</a></strong>
          {IF USERINFO->user_id MESSAGE->from_user_id}
            {IF NOT MESSAGE->recipients->read_flag}({LANG->PMUnread}){/IF}
          {/IF}
          {ASSIGN ISFIRST false}
        </div>
      {/LOOP MESSAGE->recipients}
    </div>
    <div class="PhorumReadBodyHead">{LANG->Date}: {MESSAGE->date}</div><br />
    <div class="PhorumReadBodyText">{MESSAGE->message}</div><br />
  </div>
  <div class="PhorumStdBlock" style="border-top:none">
    {IF FOLDER_IS_INCOMING}
      {VAR MOVE_SUBMIT_NAME move_message}
      {INCLUDE pm_moveselect}
    {/IF}
    <input type="submit" name="close_message" class="PhorumSubmit" value="{LANG->PMCloseMessage}" />
    {IF NOT MESSAGE->from_user_id USERINFO->user_id}
      <input type="submit" name="reply" class="PhorumSubmit" value="{LANG->PMReply}" />
      {IF NOT MESSAGE->recipient_count 1}
        <input type="submit" name="reply_to_all" class="PhorumSubmit" value="{LANG->PMReplyToAll}" />
      {/IF}
    {/IF}
    <input type="submit" name="delete_message" class="PhorumSubmit" value="{LANG->Delete}" onclick="return confirm('<?php echo addslashes($PHORUM['DATA']['LANG']['AreYouSure'])?>')" />
  </div>
</form>
