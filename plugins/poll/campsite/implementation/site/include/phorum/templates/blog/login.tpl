{IF ERROR}
<h4>{ERROR}</h4>
{/IF}

<form id="login-form" action="{URL->ACTION}" method="post">
{POST_VARS}
<input type="hidden" name="forum_id" value="{LOGIN->forum_id}" />
<input type="hidden" name="redir" value="{LOGIN->redir}" />
<table cellspacing="0">
<tr>
    <td>{LANG->Username}:&nbsp;</td>
    <td><input type="text" name="username" size="30" value="{LOGIN->username}" /></td>
</tr>
<tr>
    <td>{LANG->Password}:&nbsp;</td>
    <td><input type="password" name="password" size="30" value="" /></td>
</tr>
<tr>
    <td colspan="2" align="right"><input type="submit" class="PhorumSubmit" value="{LANG->Submit}" /></td>
</tr>
</table>
<br /><a href="{URL->REGISTER}">{LANG->NotRegistered}</a>
</form>
<br /><br />
<form id="lostpass-form" action="{URL->ACTION}" method="post">
{POST_VARS}
<input type="hidden" name="lostpass" value="1" />
<input type="hidden" name="forum_id" value="{LOGIN->forum_id}" />
<input type="hidden" name="redir" value="{LOGIN->redir}" />
<h2>{LANG->LostPassword}</h2>
{LANG->LostPassInfo}<br />
<input type="text" name="lostpass" size="30" value="" /> <input type="submit" class="PhorumSubmit" value="{LANG->Submit}" />
</form>
