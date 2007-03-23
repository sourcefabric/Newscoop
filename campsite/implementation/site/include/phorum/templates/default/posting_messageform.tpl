<div class="PhorumStdBlockHeader PhorumNarrowBlock">
  <table class="PhorumFormTable" cellspacing="0" border="0">
    {! A submit button that will be used to catch users pressing enter }
    <script type="text/javascript">
      document.write('<input type="submit" name="ignore" style="display:none">');
    </script>
    {! Author =================================================================== }
    <tr>
      <td style="white-space: nowrap">{LANG->YourName}:&nbsp;</td>
      <td width="100%">
        {IF POST->message_id}
          {! Editing a message }
          {IF POST->user_id}
            {POST->author}
          {ELSE}
            {IF MODERATOR}
              <input type="text" name="author" size="30" value="{POST->author}" />
            {ELSE}
              <?php echo $PHORUM['user']['username'] ?>
            {/IF}
          {/IF}
        {ELSE}
          {! Writing a new message }
          {IF LOGGEDIN}
            <?php echo $PHORUM['user']['username'] ?>
          {ELSE}
            <input type="text" name="author" size="30" value="{POST->author}" />
          {/IF}
        {/IF}
      </td>
    </tr>
    {! Email ==================================================================== }
    <tr>
      <td style="white-space: nowrap">{LANG->YourEmail}:&nbsp;</td>
      <td width="100%">
        {IF POST->message_id}
          {! Editing a message }
          {IF POST->user_id}
            {POST->email}
          {ELSE}
            {IF MODERATOR}
              <input type="text" name="email" size="30" value="{POST->email}" />
            {ELSE}
              <?php echo $PHORUM['user']['email'] ?>
            {/IF}
          {/IF}
        {ELSE}
          {! Writing a new message }
          {IF LOGGEDIN true}
            <?php echo $PHORUM['user']['email'] ?>
          {ELSE}
            <input type="text" name="email" size="30" value="{POST->email}" />
          {/IF}
        {/IF}
      </td>
    </tr>
    {! Subject ================================================================== }
    <tr>
      <td style="white-space: nowrap">{LANG->Subject}:&nbsp;</td>
      <td><input type="text" name="subject" id="phorum_subject" size="50" value="{POST->subject}" /></td>
    </tr>
    {HOOK tpl_editor_after_subject}
    {! Moderator only fields ==================================================== }
    {IF SHOW_THREADOPTIONS}
      <tr>
        <td>{LANG->Special}:&nbsp;</td>
        <td>
          {IF SHOW_SPECIALOPTIONS}
            <select name="special">
              <option value=""></option>
              {IF OPTION_ALLOWED->sticky}
                <option value="sticky"{IF POST->special "sticky"} selected{/IF}>{LANG->MakeSticky}</option>
              {/IF}
              {IF OPTION_ALLOWED->announcement}
                <option value="announcement" {IF POST->special "announcement"} selected{/IF}>{LANG->MakeAnnouncement}</option>
              {/IF}
            </select>
          {/IF}
          {IF OPTION_ALLOWED->allow_reply}
            <input type="checkbox" name="allow_reply" value="1" {IF POST->allow_reply} checked="checked"{/IF}> {LANG->AllowReplies}
          {/IF}
        </td>
      </tr>
    {/IF}
    {! Email notify ============================================================= }
    {IF POST->user_id}
      {IF EMAILNOTIFY}
        <tr>
          <td colspan="2">
            <input type="checkbox" name="email_notify" value="1" {IF POST->email_notify} checked="checked"{/IF} /> {LANG->EmailReplies}
          </td>
        </tr>
      {/IF}
    {/IF}
    {! Show signature =========================================================== }
    {IF POST->user_id}
      <tr>
        <td colspan="2">
          <input type="checkbox" name="show_signature" value="1" {IF POST->show_signature} checked="checked"{/IF} /> {LANG->AddSig}
        </td>
      </tr>
    {/IF}
  </table>
</div>
{! Attachments ============================================================== }
{IF ATTACHMENTS}
  {INCLUDE posting_attachments}
{/IF}
{! Body ===================================================================== }
{HOOK tpl_editor_before_textarea}
<div class="PhorumStdBlock PhorumNarrowBlock">
  <textarea name="body" id="phorum_textarea" rows="15" cols="50" style="width: 99%">{POST->body}</textarea>
  {IF MODERATED}
    {LANG->ModeratedForum}<br />
  {/IF}
</div>
