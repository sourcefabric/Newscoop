<table class="issue" cellspacing="0" cellpadding="0">
<tr>
  <td>
    {{ list_search_results order="bypublishdate desc" }}
      {{ if $campsite->current_list->at_beginning }}
        <p>Found {{ $campsite->current_list->count }} articles matching the keyword(s)
        '{{ $campsite->search_articles_action->search_phrase }}'.</p>
      {{ /if }}
    <table width="100%" cellspacing="0" cellpadding="4">
        <tr>
          <td onmouseover="this.style.backgroundColor='#efefef'" style="cursor:pointer;cursor: hand;" onclick="document.location.href='{{ uri options="article" }}'" onmouseout="this.style.backgroundColor='#ffffff'" valign="top">
            <p class="article_name">{{ $campsite->article->name }}</p>
            <p class="article_fulltext">{{ $campsite->article->intro }}</p>
          </td>
        </tr>
    </table>
    {{ /list_search_results }}
  </td>
</tr>
</table>
