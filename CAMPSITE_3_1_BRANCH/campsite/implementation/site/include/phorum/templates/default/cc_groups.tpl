{IF Message}
  <div class="PhorumUserError">{Message}</div>
{/IF}
<div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left;">{LANG->JoinAGroup}</div>
<div class="PhorumStdBlock" style="text-align: left;">
  {LANG->JoinGroupDescription}
  <form method="POST" action="{GROUP->url}">
    <input type="hidden" name="forum_id" value="{PROFILE->forum_id}" />
    <select name="joingroup">
      <option value="0">&nbsp;</option>
      {LOOP JOINGROUP}
        <option value="{JOINGROUP->group_id}">{JOINGROUP->name}</option>
      {/LOOP JOINGROUP}
    </select>
    <input type="submit" value="{LANG->Join}" />
  </form>
</div><br />
<div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left;">{LANG->GroupMembership}</div>
<div class="PhorumStdBlock" style="text-align: left;">
  <table class="PhorumFormTable" cellspacing="0" border="0">
    <tr>
      <th>{LANG->Group}</th>
      <th>{LANG->Permission}</th>
    </tr>
    {LOOP Groups}
      <tr>
        <td>{Groups->groupname}&nbsp;&nbsp;</td>
        <td>{Groups->perm}</td>
      </tr>
    {/LOOP Groups}
  </table>
</div>
