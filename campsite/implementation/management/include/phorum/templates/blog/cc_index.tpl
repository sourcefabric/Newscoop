{IF ERROR}
<h4>{ERROR}</h4>
{/IF}

<h2>{LANG->PersProfile}</h2>

<form id="profile-form" action="{URL->ACTION}" method="POST">
{POST_VARS}
<input type="hidden" name="panel" value="email" />
<input type="hidden" name="forum_id" value="{PROFILE->forum_id}" />
<table class="PhorumFormTable" cellspacing="0" border="0">
<tr>
    <td nowrap="nowrap">{LANG->RealName}:&nbsp;</td>
    <td><input type="text" name="real_name" size="30" value="{PROFILE->real_name}" /></td>
</tr>
<tr>
    <td valign="top">{LANG->Email}*:&nbsp;{if PROFILE->EMAIL_CONFIRM}<br />{LANG->EmailConfirmRequired}{/if}</td>
    <td><input type="text" name="email" size="30" value="{PROFILE->email}" /></td>
</tr>
{IF PROFILE->email_temp_part}
<tr>
    <td valign="top">{LANG->EmailVerify}:&nbsp;</td>
    <td>{LANG->EmailVerifyDesc} {PROFILE->email_temp_part}<br>
    {LANG->EmailVerifyEnterCode}: <input type="text" name="email_verify_code" value="" />
    </td>
</tr>
{/IF}
<tr>
    <td colspan="2"><input type="checkbox" name="hide_email" value="1"{IF PROFILE->hide_email 1}{ELSE} checked="checked"{/IF} /> {LANG->AllowSeeEmail}</td>
</tr>
<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td valign="top" nowrap="nowrap">{LANG->Signature}:&nbsp;</td>
    <td width="100%"><textarea name="signature" rows="15" cols="50">{PROFILE->signature}</textarea></td>
</tr>
</table>
<input type="submit" value="{LANG->Submit}" />
</form>
<br />
<br />
<br />
<h2>{LANG->ChangePassword}</h2>

<form id="profile-form" action="{URL->ACTION}" method="POST">
{POST_VARS}
<input type="hidden" name="panel" value="password" />
<input type="hidden" name="forum_id" value="{PROFILE->forum_id}" />
<table class="PhorumFormTable" cellspacing="0" border="0">
<tr>
    <td nowrap="nowrap">{LANG->Password}:&nbsp;</td>
    <td><input type="password" name="password" size="30" value="" /></td>
</tr>
<tr>
    <td nowrap="nowrap">&nbsp;</td>
    <td><input type="password" name="password2" size="30" value="" /> ({LANG->again})</td>
</tr>
</table>
*{LANG->Required}<br /><br />
<input type="submit" value="{LANG->Submit}" />
</form>
