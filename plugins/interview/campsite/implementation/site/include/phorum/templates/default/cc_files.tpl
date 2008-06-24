<form action="{URL->ACTION}" method="POST" enctype="multipart/form-data">
  <div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left;">{LANG->UploadFile}</div>
  <input type="hidden" name="forum_id" value="{PROFILE->forum_id}" />
  <input type="hidden" name="panel" value="{PROFILE->PANEL}" />
  {POST_VARS}
  <div class="PhorumStdBlock PhorumFloatingText" style="text-align: left;">
    {IF FILE_SIZE_LIMIT}{FILE_SIZE_LIMIT}<br />{/IF}
    {IF FILE_TYPE_LIMIT}{FILE_TYPE_LIMIT}<br />{/IF}
    {IF FILE_QUOTA_LIMIT}{FILE_QUOTA_LIMIT}<br />{/IF}
    <br />
    <input type="file" name="newfile" size="30" /><br /><br />
    <input class="PhorumSubmit" type="submit" value="{LANG->Submit}" />
  </div>
</form>
<form action="{URL->ACTION}" method="POST">
  {POST_VARS}
  <input type="hidden" name="forum_id" value="{PROFILE->forum_id}" />
  <input type="hidden" name="panel" value="{PROFILE->PANEL}" />
  <table border="0" cellspacing="0" class="PhorumStdTable">
    <tr>
      <th align="left" class="PhorumTableHeader">{LANG->Filename}</th>
      <th align="left" class="PhorumTableHeader">{LANG->Filesize}</th>
      <th align="left" class="PhorumTableHeader">{LANG->DateAdded}</th>
      <th align="center" class="PhorumTableHeader">{LANG->Delete}</th>
    </tr>
    {LOOP FILES}
      <tr>
        <td class="PhorumTableRow"><a href="{FILES->url}">{FILES->filename}</a></td>
        <td class="PhorumTableRow">{FILES->filesize}</td>
        <td class="PhorumTableRow">{FILES->dateadded}</td>
        <td class="PhorumTableRow" align="center"><input type="checkbox" name="delete[]" value="{FILES->file_id}" /></td>
      </tr>
    {/LOOP FILES}
    <tr>
      <th align="left" class="PhorumTableHeader">{LANG->TotalFiles}: {TOTAL_FILES}</th>
      <th align="left" class="PhorumTableHeader">{LANG->TotalFileSize}: {TOTAL_FILE_SIZE}</th>
      <th align="left" class="PhorumTableHeader">&nbsp;</th>
      <th align="center" class="PhorumTableHeader">
        <input type="submit" class="PhorumSubmit" value="{LANG->Delete}" />
      </th>
    </tr>
  </table>
</form>
