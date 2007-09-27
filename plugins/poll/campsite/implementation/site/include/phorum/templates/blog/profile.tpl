<h3>{PROFILE->username}</h3>
<table cellspacing="0" border="0">
<tr>
    <td nowrap="nowrap">{LANG->Email}:&nbsp;</td>
    <td>{PROFILE->email}</td>
</tr>
{IF PROFILE->real_name}
<tr>
    <td nowrap="nowrap">{LANG->RealName}:&nbsp;</td>
    <td>{PROFILE->real_name}</td>
</tr>
{/IF}
{IF PROFILE->date_added}
<tr>
    <td nowrap="nowrap">{LANG->DateReg}:&nbsp;</td>
    <td>{PROFILE->date_added}</td>
</tr>
{/IF}
</table>
