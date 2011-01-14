{IF ERROR}<div class="PhorumUserError">{ERROR}</div>{/IF}
{IF OKMSG}<div class="PhorumOkMsg">{OKMSG}</div>{/IF}
<form action="{URL->ACTION}" method="POST">
  <div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left;">{PROFILE->block_title}</div>
  {POST_VARS}
  <input type="hidden" name="panel" value="{PROFILE->PANEL}" />
  <input type="hidden" name="forum_id" value="{PROFILE->forum_id}" />
  <div class="PhorumStdBlock" style="text-align: left;">
    <table class="PhorumFormTable" cellspacing="0" border="0">
      {IF PROFILE->USERPROFILE}
        <tr>
          <td nowrap="nowrap">{LANG->RealName}:&nbsp;</td>
          <td><input type="text" name="real_name" size="30" value="{PROFILE->real_name}" /></td>
        </tr>
      {/IF}
      {IF PROFILE->SIGSETTINGS}
        <tr>
          <td valign="top" nowrap="nowrap">{LANG->Signature}:&nbsp;</td>
          <td width="100%"><textarea name="signature" rows="15" cols="50" style="width: 100%">{PROFILE->signature}</textarea></td>
        </tr>
      {/IF}
      {IF PROFILE->MAILSETTINGS}
        <tr>
          <td valign="top">
            {LANG->Email}*:&nbsp;
            {IF PROFILE->EMAIL_CONFIRM}<br />{LANG->EmailConfirmRequired}{/IF}
          </td>
          <td><input type="text" name="email" size="30" value="{PROFILE->email}" /></td>
        </tr>
        {IF PROFILE->email_temp_part}
          <tr>
            <td valign="top">{LANG->EmailVerify}:&nbsp;</td>
            <td>
              {LANG->EmailVerifyDesc} {PROFILE->email_temp_part}<br>
              {LANG->EmailVerifyEnterCode}: <input type="text" name="email_verify_code" value="" />
            </td>
          </tr>
        {/IF}
        <tr>
          <td colspan="2"><input type="checkbox" name="hide_email" value="1"{PROFILE->hide_email_checked} /> {LANG->AllowSeeEmail}</td>
        </tr>
        {IF PROFILE->show_moderate_options}
          <tr>
            <td colspan="2"><input type="checkbox" name="moderation_email" value="1"{PROFILE->moderation_email_checked} /> {LANG->ReceiveModerationMails}</td>
          </tr>
        {/IF}
      {/IF}
      {IF PROFILE->PRIVACYSETTINGS}
        <tr>
          <td colspan="2"><input type="checkbox" name="hide_email" value="1"{PROFILE->hide_email_checked} /> {LANG->AllowSeeEmail}</td>
        </tr>
        <tr>
          <td colspan="2"><input type="checkbox" name="hide_activity" value="1"{PROFILE->hide_activity_checked} /> {LANG->AllowSeeActivity}</td>
        </tr>
      {/IF}
      {IF PROFILE->BOARDSETTINGS}
        {IF PROFILE->TZSELECTION}
          <tr>
            <td nowrap="nowrap">{LANG->Timezone}:&nbsp;</td>
            <td>
              <select name="tz_offset">
                {LOOP TIMEZONE}
                  <option value="{TIMEZONE->tz}"{TIMEZONE->sel}>{TIMEZONE->str}</option>
                {/LOOP TIMEZONE}
              </select>
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap">{LANG->IsDST}:&nbsp;</td>
            <td><input type="checkbox" name="is_dst" value="1"{IF PROFILE->is_dst 1} checked="checked"{/IF}></td>
          </tr>
        {/IF}
        <tr>
          <td nowrap="nowrap">{LANG->Language}:&nbsp;</td>
          <td>
            <select name="user_language">
              {LOOP LANGUAGES}
                <option value="{LANGUAGES->file}"{LANGUAGES->sel}>{LANGUAGES->name}</option>
              {/LOOP LANGUAGES}
            </select>
          </td>
        </tr>
        {IF PROFILE->TMPLSELECTION}
          <tr>
            <td nowrap="nowrap">{LANG->Template}:&nbsp;</td>
            <td>
              <select name="user_template">
                {LOOP TEMPLATES}
                  <option value="{TEMPLATES->file}"{TEMPLATES->sel}>{TEMPLATES->name}</option>
                {/LOOP TEMPLATES}
              </select>
            </td>
          </tr>
        {/IF}
        <tr>
          <td nowrap="nowrap">{LANG->ThreadViewList}:&nbsp;</td>
          <td>
            <select name="threaded_list">
              <option value="0">{LANG->Default}</option>
              <option value="1" {IF PROFILE->threaded_list}selected{/IF}>{LANG->ViewThreaded}</option>
              <option value="2" {IF PROFILE->threaded_list 2}selected{/IF}>{LANG->ViewFlat}</option>
            </select>
          </td>
        </tr>
        <tr>
          <td nowrap="nowrap">{LANG->ThreadViewRead}:&nbsp;</td>
          <td>
            <select name="threaded_read">
              <option value="0">{LANG->Default}</option>
              <option value="1" {IF PROFILE->threaded_read}selected{/IF}>{LANG->ViewThreaded}</option>
              <option value="2" {IF PROFILE->threaded_read 2}selected{/IF}>{LANG->ViewFlat}</option>
            </select>
          </td>
        </tr>
        <tr>
          <td nowrap="nowrap">{LANG->EnableNotifyDefault}:&nbsp;</td>
          <td>
            <select name="email_notify">
              <option value="0"{IF PROFILE->email_notify 0} selected="selected" {/IF}>{LANG->No}</option>
              <option value="1"{IF PROFILE->email_notify 1} selected="selected" {/IF}>{LANG->Yes}</option>
            </select>
          </td>
        </tr>
        <tr>
          <td nowrap="nowrap">{LANG->AddSigDefault}:&nbsp;</td>
          <td>
            <select name="show_signature">
              <option value="0"{IF PROFILE->show_signature 0} selected="selected" {/IF}>{LANG->No}</option>
              <option value="1"{IF PROFILE->show_signature 1} selected="selected" {/IF}>{LANG->Yes}</option>
            </select>
          </td>
        </tr>
        <tr>
          <td nowrap="nowrap">{LANG->PMNotifyEnableSetting}:&nbsp;</td>
          <td>
            <select name="pm_email_notify">
              <option value="0"{IF PROFILE->pm_email_notify 0} selected="selected" {/IF}>{LANG->No}</option>
              <option value="1"{IF PROFILE->pm_email_notify 1} selected="selected" {/IF}>{LANG->Yes}</option>
            </select>
          </td>
        </tr>
      {/IF}
      {IF PROFILE->CHANGEPASSWORD}
        <tr>
          <td nowrap="nowrap">{LANG->Password}*:&nbsp;</td>
          <td><input type="password" name="password" size="30" value="" /></td>
        </tr>
        <tr>
          <td nowrap="nowrap">&nbsp;</td>
          <td><input type="password" name="password2" size="30" value="" /> ({LANG->again})</td>
        </tr>
      {/IF}
      {HOOK tpl_cc_usersettings PROFILE}
    </table>
    <div style="float: left; margin-top: 5px;">*{LANG->Required}</div>
    <div style="margin-top: 3px;" align="center"><input type="submit" class="PhorumSubmit" value=" {LANG->Submit} " /></div>
  </div>
</form>
