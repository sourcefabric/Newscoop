<table class="section" cellspacing="0" cellpadding="0">
<tr>
  <td>
    <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        {{ list_articles length="1" order="bynumber desc" name="mainarticle" }}
        <table width="100%" cellspacing="0" cellpadding="4">
          {{ if $campsite->current_list->at_beginning }}
          <tr>
            <td onmouseover="this.style.backgroundColor='#efefef'" style="cursor:pointer;cursor: hand;" onclick="document.location.href='{{ uri options="article" }}'" onmouseout="this.style.backgroundColor='#ffffff'" valign="top">
              <p class="article_name">{{ $campsite->article->name }}</p>
              <p class="article_fulltext">{{ $campsite->article->intro }}</p>
            </td>
          </tr>
          {{ /if }}
        </table>
        {{ /list_articles }}
        <div id="dotline"> </div>
        {{ list_articles length="3" order="bynumber desc" name="articles" }}
        <table width="100%" cellspacing="0" cellpadding="4">
          <tr>
            <td valign="top" style="cursor: hand;cursor:pointer;" onclick="document.location.href='{{ uri options="article" }}'" onmouseover="this.style.backgroundColor='#efefef'" onmouseout="this.style.backgroundColor='#ffffff'">
              <p class="article_name_small">{{ $campsite->article->name }}</p>
              <p class="article_intro_small">{{ $campsite->article->intro }}</p>
            </td>
          </tr>
        </table>
        <div id="dotline"> </div>
        {{ /list_articles }}
      </td>
    </tr>
    </table>
  </td>
</tr>
</table>