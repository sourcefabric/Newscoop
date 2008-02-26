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

{{ if $campsite->article->comments_enabled }}
<tr>
  <td>
  <a name="comments"></a>
  <ul>
  {{ list_article_comments }}
    <li>{{ if $campsite->comment == $campsite->default_comment }}<b>{{ /if }}
    <a href="{{ uri }}#comments">
    Subject: {{ $campsite->comment->subject }}, Reader email: {{ $campsite->comment->reader_email }}
    </a>
    {{ if $campsite->comment == $campsite->default_comment }}</b>{{ /if }}<br/>
    Content: {{ $campsite->comment->content }}
    </li>
  {{ /list_article_comments }}
  </ul>
  </td>
</tr>
{{ if $campsite->submit_comment_action->is_error }}
<tr>
  <td>
  There was an error submitting the comment: {{ $campsite->submit_comment_action->error_message }}
  </td>
</tr>
{{ /if }}
{{ if $campsite->submit_comment_action->ok }}
<tr>
  {{ if $campsite->publication->moderated_comments }}
  <td>Your comment was submitted for approval.</td>
  {{ else }}
  <td>Your comment was approved.</td>
  {{ /if }}
</tr>
{{ /if }}
{{ if $campsite->preview_comment_action->is_error }}
<tr>
  <td>
  There was an error previewing the comment: {{ $campsite->preview_comment_action->error_message }}
  </td>
</tr>
{{ /if }}
{{ if $campsite->preview_comment_action->ok }}
<tr>
  <td>
    <b>Comment preview</b><br/>
    Subject: {{ $campsite->preview_comment_action->subject }}, Reader email: {{ $campsite->preview_comment_action->reader_email }}<br/>
    Content: {{ $campsite->preview_comment_action->content }}
  </td>
</tr>
{{ /if }}
{{ if $campsite->user->logged_in || $campsite->publication->public_comments }}
<tr>
  <td>
    {{ comment_form submit_button="Submit" preview_button="Preview" anchor="comments" }}
    <table class="commentform" cellspacing="0" cellpadding="0">
    <tr>
      <td>E-mail:</td>
      <td>
      {{ if $campsite->user->logged_in }}
        {{ $campsite->user->email }}
      {{ else }}
        {{ camp_edit object="comment" attribute="reader_email" size="40" }}
      {{ /if }}
      </td>
    </tr>
    <tr>
      <td>Subject:</td>
      <td>{{ camp_edit object="comment" attribute="subject" size="40" }}</td>
    </tr>
    <tr>
      <td valign="top">Comment:</td>
      <td>{{ camp_edit object="comment" attribute="content" }}</td>
    </tr>
    {{ if $campsite->publication->captcha_enabled }}
    <tr>
      <td colspan="2">
      Please fill in the code shown in the image below:
      {{ camp_edit object="captcha" attribute="code" }}
      </td>
    </tr>
    <tr>
      <td colspan="2" align="right">
      <img src="/include/captcha/image.php">
      </td>
    </tr>
    {{ /if }}
    </table>
    {{ /comment_form }}
  </td>
</tr>
{{ else }}
<tr>
  <td>You must be a registered reader in order to submit comments.</td>
</tr>
{{ /if }}
{{ /if }} {{* comments enabled *}}
</table>
