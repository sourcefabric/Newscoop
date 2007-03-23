<div class="PhorumNavBlock">
  <span class="PhorumNavHeading">{LANG->Goto}:</span>&nbsp;{IF URL->INDEX}<a class="PhorumNavLink" href="{URL->INDEX}">{LANG->ForumList}</a>&bull;{/IF}<a class="PhorumNavLink" href="{URL->SEARCH}">{LANG->Search}</a>&bull;{INCLUDE loginout_menu}
</div>
<table id="phorum-index" cellspacing="0">
  {LOOP FORUMS}
    {IF FORUMS->folder_flag}
      <tr class="folder">
        {IF FORUMS->forum_id FORUMS->vroot}
          <th class="forum-name">{LANG->Forums}</th>
        {ELSE}
          <th class="forum-name"><a href="{FORUMS->url}">{FORUMS->name}</a></th>
        {/IF}
        <th class="forum-threads">{LANG->Threads}</th>
        <th class="forum-posts">{LANG->Posts}</th>
        <th class="forum-last-post">{LANG->LastPost}</th>
      </tr>
    {ELSE}
      <tr class="forum">
        <td class="forum-name">
          <a href="{FORUMS->url}">{FORUMS->name}</a><p>{FORUMS->description}</p><small>{LANG->Options}: {IF LOGGEDIN true}<a href="{FORUMS->url_markread}">{LANG->MarkForumRead}</a>{/IF}{IF FORUMS->url_rss}{IF LOGGEDIN true}&nbsp;&bull;&nbsp;{/IF}<a href="{FORUMS->url_rss}">{LANG->RSS}</a>{/IF}</small>
        </td>
        <td class="forum-threads" nowrap="nowrap">
          {FORUMS->thread_count}
          {IF FORUMS->new_threads}
            (<span class="PhorumNewFlag">{FORUMS->new_threads} {LANG->newflag}</span>)
          {/IF}
        </td>
        <td class="forum-posts" nowrap="nowrap">
          {FORUMS->message_count}
          {IF FORUMS->new_messages}
            (<span class="PhorumNewFlag">{FORUMS->new_messages} {LANG->newflag}</span>)
          {/IF}
        </td>
        <td class="forum-last-post" nowrap="nowrap">{FORUMS->last_post}</td>
      </tr>
    {/IF}
  {/LOOP FORUMS}
</table>
