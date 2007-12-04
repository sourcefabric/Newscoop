<div class="PhorumNavBlock" style="text-align: left;">
  <span class="PhorumNavHeading PhorumHeadingLeft">{LANG->Goto}:</span>&nbsp;{IF URL->INDEX}<a class="PhorumNavLink" href="{URL->INDEX}">{LANG->ForumList}</a>&bull;{/IF}<a class="PhorumNavLink" href="{URL->POST}">{LANG->NewTopic}</a>&bull;<a class="PhorumNavLink" href="{URL->SEARCH}">{LANG->Search}</a>&bull;{INCLUDE loginout_menu}
</div>
{INCLUDE paging}
<table border="0" cellspacing="0" class="PhorumStdTable">
  <tr>
    <th class="PhorumTableHeader" align="left">{LANG->Subject}</th>
    {IF VIEWCOUNT_COLUMN}
      <th class="PhorumTableHeader" align="center" width="40">{LANG->Views}</th>
    {/IF}
    <th class="PhorumTableHeader" align="center" nowrap="nowrap" width="80">{LANG->Posts}&nbsp;</th>
    <th class="PhorumTableHeader" align="left" nowrap="nowrap" width="150">{LANG->StartedBy}&nbsp;</th>
    <th class="PhorumTableHeader" align="left" nowrap="nowrap" width="150">{LANG->LastPost}&nbsp;</th>
  </tr>
  <?php $rclass="Alt"; ?>
  {LOOP ROWS}
    <?php if($rclass=="Alt") $rclass=""; else $rclass="Alt"; ?>
    <tr>
      <td class="PhorumTableRow<?php echo $rclass;?>">
        <?php echo $PHORUM['TMP']['marker'] ?>
        {IF ROWS->sort PHORUM_SORT_STICKY}<span class="PhorumListSubjPrefix">{LANG->Sticky}: </span>{/IF}
        {IF ROWS->sort PHORUM_SORT_ANNOUNCEMENT}<span class="PhorumListSubjPrefix">{LANG->Announcement}: </span>{/IF}
        {IF ROWS->moved}<span class="PhorumListSubjPrefix">{LANG->MovedSubject}: </span>{/IF}
        <a href="{ROWS->url}">{ROWS->subject}</a>
        {IF ROWS->new}&nbsp;<span class="PhorumNewFlag">{ROWS->new}</span>{/IF}
        {IF ROWS->pages}<span class="PhorumListPageLink">&nbsp;&nbsp;&nbsp;{LANG->Pages}: {ROWS->pages}</span>{/IF}
        {IF MODERATOR true}<br /><span class="PhorumListModLink"><a href="javascript:if(window.confirm('{LANG->ConfirmDeleteThread}')) window.location='{ROWS->delete_url2}';">{LANG->DeleteThread}</a>{IF ROWS->move_url}&nbsp;&#8226;&nbsp;<a href="{ROWS->move_url}">{LANG->MoveThread}</a>{/IF}&nbsp;&#8226;&nbsp;<a href="{ROWS->merge_url}">{LANG->MergeThread}</a></span>{/IF}
      </td>
      {IF VIEWCOUNT_COLUMN}
        <td class="PhorumTableRow<?php echo $rclass;?>" align="center">{ROWS->viewcount}&nbsp;</td>
      {/IF}
      <td class="PhorumTableRow<?php echo $rclass;?>" align="center" nowrap="nowrap">{ROWS->thread_count}&nbsp;</td>
      <td class="PhorumTableRow<?php echo $rclass;?>" nowrap="nowrap">{ROWS->linked_author}&nbsp;</td>
      <td class="PhorumTableRow<?php echo $rclass;?> PhorumSmallFont" nowrap="nowrap">
        {ROWS->lastpost}&nbsp;<br />
        <span class="PhorumListSubText">
          <a href="{ROWS->last_post_url}">{LANG->LastPostLink}</a> {LANG->by} {ROWS->last_post_by}
        </span>
      </td>
    </tr>
  {/LOOP ROWS}
</table>
{INCLUDE paging}
<div class="PhorumNavBlock" style="text-align: left;">
  <span class="PhorumNavHeading PhorumHeadingLeft">{LANG->Options}:</span>
  {IF LOGGEDIN true}&nbsp;<a class="PhorumNavLink" href="{URL->MARKREAD}">{LANG->MarkRead}</a>{/IF}
</div>
