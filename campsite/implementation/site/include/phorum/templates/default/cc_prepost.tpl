<form action="{URL->ACTION}" method="POST">
  {POST_VARS}
  <div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left;">{LANG->UnapprovedMessages}</div>
  <div class="PhorumStdBlock PhorumFloatingText" style="text-align: left;">
    <input type="hidden" name="panel" value="{PROFILE->PANEL}" />
    <input type="hidden" name="forum_id" value="{PROFILE->forum_id}" />
    {LANG->ShowOnlyMessages}&nbsp;
    <select name="onlyunapproved">
      <option value="0"{IF SELECTED_2 0} selected="selected"{/IF}>{LANG->AllNotShown}</option>
      <option value="1"{IF SELECTED_2 1} selected="selected"{/IF}>{LANG->OnlyUnapproved}</option>
    </select>
    {LANG->DatePosted}&nbsp;
    <select name="moddays">
      <option value="1"{IF SELECTED 1} selected="selected"{/IF}>1 {LANG->Day}</option>
      <option value="2"{IF SELECTED 2} selected="selected"{/IF}>2 {LANG->Days}</option>
      <option value="7"{IF SELECTED 7} selected="selected"{/IF}>7 {LANG->Days}</option>
      <option value="30"{IF SELECTED 30} selected="selected"{/IF}>1 {LANG->Month}</option>
      <option value="180"{IF SELECTED 180} selected="selected"{/IF}>6 {LANG->Months}</option>
      <option value="365"{IF SELECTED 365} selected="selected"{/IF}>1 {LANG->Year}</option>
      <option value="0"{IF SELECTED 0} selected="selected"{/IF}>{LANG->AllDates}</option>
    </select>
    <input type="submit" class="PhorumSubmit" value="{LANG->Go}" />
  </div>
</form><br />
<table border="0" cellspacing="0" class="PhorumStdTable">
  {IF UNAPPROVEDMESSAGE}
    <tr>
      <td class="PhorumTableRow">
        {UNAPPROVEDMESSAGE}
      </td>
    </tr>
  {ELSE}
    {LOOP PREPOST}
      {IF PREPOST->checkvar 1}
        <tr>
          <th class="PhorumTableHeader" align="left" colspan="3">{PREPOST->forumname}</th>
        </tr>
        <tr>
          <th class="PhorumTableHeader" align="left">{LANG->Subject}</th>
          <th class="PhorumTableHeader" align="left" nowrap="nowrap" width="150">{LANG->Author}&nbsp;</th>
          <th class="PhorumTableHeader" align="left" nowrap="nowrap" width="150">{LANG->Date}&nbsp;</th>
        </tr>
      {/IF}
      <tr>
        <td class="PhorumTableRow">
          <?php echo $PHORUM['TMP']['marker'] ?><a href="{PREPOST->url}" target="_blank">{PREPOST->subject}</a><br />
          <span class="PhorumListModLink">&nbsp;&nbsp;&nbsp;&nbsp;<a class="PhorumListModLink" href="{PREPOST->delete_url}">{LANG->DeleteMessage}</a>&nbsp;&bull;&nbsp;<a class="PhorumListModLink" href="{PREPOST->approve_url}">{LANG->ApproveMessage Short}</a>&nbsp;&bull;&nbsp;<a class="PhorumListModLink" href="{PREPOST->approve_tree_url}">{LANG->ApproveMessageReplies}</a></span>
        </td>
        <td class="PhorumTableRow" nowrap="nowrap" width="150">{PREPOST->linked_author}&nbsp;</td>
        <td class="PhorumTableRow" nowrap="nowrap" width="150">{PREPOST->short_datestamp}&nbsp;</td>
      </tr>
    {/LOOP PREPOST}
  {/IF}
</table>
