{IF PREVIEW}
  <div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left;">{LANG->Preview}</div>
  <div class="PhorumStdBlock" style="text-align: left;">
    <div class="PhorumReadBodySubject">{PREVIEW->subject}</div>
    <div class="PhorumReadBodyHead">{LANG->From}: <strong><a href="#">{PREVIEW->from_username}</a></strong></div>
    <div class="PhorumReadBodyHead">
      {LANG->To}:
      {ASSIGN ISFIRST true}
      {LOOP PREVIEW->recipients}
        <div style="display:inline; white-space: nowrap">
          {IF NOT ISFIRST} / {/IF}
          <strong><a href="#">{PREVIEW->recipients->username}</a></strong>
          {ASSIGN ISFIRST false}
        </div>
      {/LOOP PREVIEW->recipients}
    </div><br />
    <div class="PhorumReadBodyText">{PREVIEW->message}</div><br />
  </div><br />
{/IF}
<form action="{ACTION}" method="post">
  {POST_VARS}
  <input type="hidden" name="action" value="post" />
  <input type="hidden" name="forum_id" value="{FORUM_ID}" />
  <input type="hidden" name="hide_userselect" value="{HIDE_USERSELECT}" />
  <div class="PhorumStdBlockHeader" style="text-align: left; width:99%">
    <table class="PhorumFormTable" cellspacing="0" border="0" style="width:99%">
      <tr>
        <td>{LANG->From}:&nbsp;</td>
        <td>{MESSAGE->from_username}</td>
      </tr>
      <tr>
        <td valign="top">{LANG->To}:&nbsp;</td>
        <td valign="top" width="100%">
          {! Show user selection}
          {IF SHOW_USERSELECTION}
            <div class="phorum-pmuserselection">
              {IF USERS}
                <select id="userselection" name="to_id" size="1" align="middle">
                  <option value=""> {LANG->PMSelectARecipient}</option>
                  {LOOP USERS}
                    <option value="{USERS->user_id}" <?php if (isset($_POST['to_id']) && $_POST['to_id'] == $PHORUM['TMP']['USERS']['user_id']) echo 'selected="selected"'?>>{USERS->displayname}</option>
                  {/LOOP USERS}
                </select>
              {ELSE}
                <input type="text" id="userselection" name="to_name" value="<?php if (isset($_POST['to_name'])) echo htmlspecialchars($_POST['to_name'])?>" />
              {/IF}
              <input type="submit" class="PhorumSubmit" style="font-size: {smallfontsize}" name="rcpt_add" value="{LANG->PMAddRecipient}" />
              {! Always show recipient list on a separate line}
              {IF RECIPIENT_COUNT}<br style="clear:both" />{/IF}
            </div>
          {/IF}
          {! Display the current list of recipients}
          {LOOP MESSAGE->recipients}
            <div class="phorum-recipientblock">
              {MESSAGE->recipients->username}
              <input type="hidden" name="recipients[{MESSAGE->recipients->user_id}]" value="{MESSAGE->recipients->username}" />
              <input type="image" src="{delete_image}" name="del_rcpt::{MESSAGE->recipients->user_id}" style="margin-left: 3px;vertical-align:top">
            </div>
          {/LOOP MESSAGE->recipients}
        </td>
      </tr>
      <tr>
        <td>{LANG->Subject}:&nbsp;</td>
        <td><input type="text" id="subject" name="subject" size="50" value="{MESSAGE->subject}" /></td>
      </tr>
      <tr>
        <td colspan="2"><input type="checkbox" name="keep" value="1"{IF MESSAGE->keep} checked="checked" {/IF} /> {LANG->KeepCopy}</td>
      </tr>
    </table>
  </div>
  <div class="PhorumStdBlock" style="width:99%; text-align: center">
    <textarea id="message" name="message" rows="20" cols="50" style="width: 98%">{MESSAGE->message}</textarea>
    <div style="margin-top: 3px; width:99%" align="right">
      <input name="preview" type="submit" class="PhorumSubmit" value=" {LANG->Preview} " />
      <input type="submit" class="PhorumSubmit" value=" {LANG->Post} " />
    </div>
  </div>
</form>
