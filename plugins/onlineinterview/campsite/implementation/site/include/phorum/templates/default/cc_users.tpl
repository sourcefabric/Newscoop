<div class="PhorumLargeFont">{LANG->UnapprovedUsers}</div>
<form action="{ACTION}" method="post">
  {POST_VARS}
  <input type="hidden" name="panel" value="users" />
  <input type="hidden" name="forum_id" value="{FORUM_ID}" />
  <table border="0" cellspacing="0" class="PhorumStdTable">
    <tr>
      <th class="PhorumTableHeader" align="left">&nbsp;</th>
      <th class="PhorumTableHeader" align="left">{LANG->Username}</th>
      <th class="PhorumTableHeader" align="left" nowrap="nowrap" width="150">{LANG->Email}</th>
    </tr>
    {LOOP USERS}
      <tr>
        <td class="PhorumTableRow"><input type="checkbox" name="user_ids[]" value="{USERS->user_id}" /></td>
        <td class="PhorumTableRow" width="50%">{USERS->username}</td>
        <td class="PhorumTableRow" width="50%" nowrap="nowrap" width="150">{USERS->email}</td>
      </tr>
    {/LOOP USERS}
  </table>
  <div class="PhorumNavBlock" style="text-align: left;">
    <input type="submit" class="PhorumSubmit" name="approve" value="{LANG->ApproveUser}" />&nbsp;<input type="submit" class="PhorumSubmit" name="disapprove" value="{LANG->DenyUser}" />
  </div>
</form>
