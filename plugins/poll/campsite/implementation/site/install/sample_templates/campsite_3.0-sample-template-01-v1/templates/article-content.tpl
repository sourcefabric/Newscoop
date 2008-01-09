<table class="article" cellspacing="0" cellpadding="0">
<tr>
  <td>
    <p class="article_date">{{ $campsite->article->publish_date }}</p>
    <p class="article_name">{{ $campsite->article->name }}</p>
    <p class="article_byline">Written by {{ $campsite->article->byline }}</p>
    <p class="article_intro">{{ $campsite->article->intro }}</p>
    <p class="article_fulltext">{{ $campsite->article->full_text }}</p>

    {{ list_article_attachments name="article_attachments" }}
      {{ if $campsite->current_list->count }}
        {{ if $campsite->current_list->at_beginning }}
          <div id="attachments">
          <p class="article_subtitle">Related files</p>
          <ul id="attachments_list">
        {{ /if }}
          <li><a href="{{ uri options="articleattachment" }}">{{ $campsite->attachment->file_name }}</a>&nbsp;
          ({{ $campsite->attachment->size_b|camp_filesize_format:"KB" }})</li>
        {{ if $campsite->current_list->at_end }}
          </ul>
          </div>
        {{ /if }}
      {{ /if }}
    {{ /list_article_attachments }}
  </td>
</tr>

{{ if $article->comments_enabled }}
<tr>
  <td>
    {{** list_articlecomments **}}

    {{** /list_articlecomments **}}
  </td>
</tr>
<tr>
  <td>
    {{* comment_form submit_name="send" *}}
    <table class="commentform" cellspacing="0" cellpadding="0">
    <tr>
      <td>{{* tr *}}User Name{{* /tr *}}:</td>
      <td>{{ camp_edit object="user" attribute="uname" }}</td>
    </tr>
    <tr>
      <td>{{* tr *}}E-mail{{* /tr *}}:</td>
      <td>{{ camp_edit object="user" attribute="email" }}</td>
    </tr>
    <tr>
      <td>{{* tr *}}Subject{{* /tr *}}:</td>
      <td>{{ camp_edit object="comment" attribute="subject" }}</td>
    </tr>
    <tr>
      <td>{{* tr *}}Comment{{* /tr *}}:</td>
      <td>{{ camp_edit object="comment" attribute="comment" }}</td>
    </tr>
    </table>
  {{* /comment_form *}}
  </td>
</tr>
{{ /if }}
</table>
