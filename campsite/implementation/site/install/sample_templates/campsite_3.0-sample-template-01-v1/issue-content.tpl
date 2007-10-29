<table class="issue" cellspacing="0" cellpadding="0">
<tr>
  <td>
    {{ list_articles length="1" order="bypublishdate desc" name="mainarticle" }}
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
    <table width="100%" cellspacing="0" cellpadding="4">
    <tr>
    {{ local }}
    {{ set_section number="60" }}
    {{ list_articles length="1" order="bynumber desc" name="articles" }}
      <td width="33%" valign="top" style="cursor: hand;cursor:pointer;" onclick="document.location.href='{{ uri options="article" }}'" onmouseover="this.style.backgroundColor='#efefef'" onmouseout="this.style.backgroundColor='#ffffff'">
        <p class="article_name_small">{{ $campsite->article->name }}</p>
        <p class="article_intro_small">{{ $campsite->article->intro|truncate:250 }}</p>
      </td>
    {{ /list_articles }}
    {{ /local }}

    {{ local }}
    {{ set_section number="30" }}
    {{ list_articles length="1" order="bynumber desc" name="articles" }}
      <td width="33%" valign="top" style="cursor: hand;cursor:pointer;" onclick="document.location.href='{{ uri options="article" }}'" onmouseover="this.style.backgroundColor='#efefef'" onmouseout="this.style.backgroundColor='#ffffff'">
        <p class="article_name_small">{{ $campsite->article->name }}</p>
        <p class="article_intro_small">{{ $campsite->article->intro|truncate:250 }}</p>
      </td>
    {{ /list_articles }}
    {{ /local }}

    {{ local }}
    {{ set_section number="10" }}
    {{ list_articles length="1" order="bynumber desc" name="articles" }}
      <td width="33%" valign="top" style="cursor: hand;cursor:pointer;" onclick="document.location.href='{{ uri options="article" }}'" onmouseover="this.style.backgroundColor='#efefef'" onmouseout="this.style.backgroundColor='#ffffff'">
        <p class="article_name_small">{{ $campsite->article->name }}</p>
        <p class="article_intro_small">{{ $campsite->article->intro|truncate:250 }}</p>
      </td>
    {{ /list_articles }}
    {{ /local }}
    </tr>
    </table>
    <div id="dotline"> </div>
    <table width="100%" cellspacing="0" cellpadding="4">
    <tr>
      <td>
        <p class="article_name_small">Quick Links</p>
        <ul id="article_list">
      {{ list_articles length="14" order="bypublishdate desc" name="articles_list" }}
        <li>[ {{ $campsite->article->publish_date|camp_date_format:"%Y-%m-%d" }} ] <a
        href="{{ uri options="article" }}">{{ $campsite->article->name }}</a></li>
      {{ /list_articles }}
        </ul>
      </td>
    </tr>
    </table>
  </td>
</tr>
</table>
