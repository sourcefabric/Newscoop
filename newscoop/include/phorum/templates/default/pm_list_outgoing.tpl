<table border="0" cellspacing="0" class="PhorumStdTable">
  <tr>
    <th class="PhorumTableHeader" align="left" width="20">
      {ASSIGN ITEMCOUNT MESSAGECOUNT}
      {INCLUDE pm_list_selectall}
    </th>
    <th class="PhorumTableHeader" align="left">{LANG->Subject}</th>
    <th class="PhorumTableHeader" align="left" nowrap="nowrap">{LANG->To}&nbsp;</th>
    <th class="PhorumTableHeader" align="left" nowrap="nowrap">{LANG->PMRead}&nbsp;</th>
    <th class="PhorumTableHeader" align="left" nowrap="nowrap">{LANG->Date}&nbsp;</th>
  </tr>
  {IF MESSAGECOUNT}
    {LOOP MESSAGES}
      <tr>
        <td class="PhorumTableRow">
          <input type="checkbox" name="checked[]" value="{MESSAGES->pm_message_id}" />
        </td>
        <td class="PhorumTableRow"><a href="{MESSAGES->read_url}">{MESSAGES->subject}</a></td>
        <td class="PhorumTableRow" nowrap="nowrap">
          {IF MESSAGES->recipient_count 1}
            {LOOP MESSAGES->recipients}
              <a href="{MESSAGES->recipients->to_profile_url}">{MESSAGES->recipients->username}</a>&nbsp;
            {/LOOP MESSAGES->recipients}
          {ELSE}
            {MESSAGES->recipient_count}&nbsp;{LANG->Recipients}&nbsp;
          {/IF}
        </td>
        <td class="PhorumTableRow" nowrap="nowrap" align="left">
          {IF MESSAGES->recipient_count 1}
            {LOOP MESSAGES->recipients}
              {IF MESSAGES->recipients->read_flag}{LANG->Yes}{ELSE}{LANG->No}{/IF}
            {/LOOP MESSAGES->recipients}
          {ELSE}
            {IF MESSAGES->receive_count MESSAGES->recipient_count}
              {LANG->Yes}
            {ELSE}
              {MESSAGES->receive_count}&nbsp;{LANG->of}&nbsp;{MESSAGES->recipient_count}
            {/IF}
          {/IF}
        </td>
        <td class="PhorumTableRow" nowrap="nowrap" style="white-space:nowrap" width="1">
          <div style="white-space:nowrap">{MESSAGES->date}&nbsp;</div>
        </td>
      </tr>
    {/LOOP MESSAGES}
  {ELSE}
    <tr>
      <td colspan="5" style="text-align: center" class="PhorumTableRow">
        <br />
        <i>{LANG->PMFolderIsEmpty}</i><br />
        <br />
      </td>
    </tr>
  {/IF}
</table>
<div class="PhorumStdBlock" style="border-top:none">
  <input type="submit" name="delete" class="PhorumSubmit" value="{LANG->Delete}" onclick="return confirm('<?php echo addslashes($PHORUM['DATA']['LANG']['AreYouSure'])?>')" />
</div>
