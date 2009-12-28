<table id="post" cellspacing="0" border="0">

{! A submit button that will be used to catch users pressing enter }
<script type="text/javascript">
document.write('<input type="submit" name="ignore" style="display:none">');
</script>

{! Author =================================================================== }

<tr>
  <td style="white-space: nowrap">{LANG->YourName}:&nbsp;</td>
  <td width="100%">
  {! Editing a message }
  {IF POST->message_id}
    {IF POST->user_id}
      {POST->author}
    {ELSE}
      {IF MODERATOR}
        <input type="text" name="author" size="30" value="{POST->author}" />
      {ELSE}
        <?php print $PHORUM["user"]["username"] ?>
      {/IF}
    {/IF}
  {! Writing a new message }
  {ELSE}
    {IF LOGGEDIN}
      <?php print $PHORUM["user"]["username"] ?>
    {ELSE}
      <input type="text" name="author" size="30" value="{POST->author}" />
    {/IF}
  {/IF}
  </td>
</tr>

{! Subject ================================================================== }

<tr>
  <td style="white-space: nowrap">{LANG->Subject}:&nbsp;</td>
  <td>
    <input type="text" name="subject" id="phorum_subject" size="50" value="{POST->subject}" />
  </td>
</tr>

{HOOK tpl_editor_after_subject}

{IF POST->user_id}
<tr>
  <td></td>
    <td>
    {IF SHOW_THREADOPTIONS}{IF OPTION_ALLOWED->allow_reply}
    <input type="checkbox" name="allow_reply" value="1" {IF POST->allow_reply} checked="checked"{/IF}> {LANG->AllowReplies}<br/>
    {/IF}{/IF}
    <input type="checkbox" name="email_notify" value="1" {IF POST->email_notify} checked="checked"{/IF} /> {LANG->EmailReplies}<br/>
    <input type="checkbox" name="show_signature" value="1" {IF POST->show_signature} checked="checked"{/IF} /> {LANG->AddSig}
  </td>
</tr>
{/IF}

</table>

</div>

{! Attachments ============================================================== }

{IF ATTACHMENTS}
    {include posting_attachments}
{/IF}


{! Body ===================================================================== }


{HOOK tpl_editor_before_textarea}

<textarea name="body" id="phorum_textarea" rows="15" cols="50">{POST->body}</textarea>

