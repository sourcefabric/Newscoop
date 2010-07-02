{{ if $campsite->article->comments_enabled }}
<div class="comments comments-{{ $campsite->section->number }}">
<div class="commentsinner">
  <h3><a name="comments">Comments</a></h3>
  {{ list_article_comments order="bydate desc" }}
  <div class="comment">
  <div class="commentinner">
	<div class ="field-label" id="subject-label">Subject:</div>
	<div class ="field-value" id="subject-value">
{{ if $campsite->comment == $campsite->default_comment }}<b>{{ /if }}
    <!--a href="{{ uri }}#comments"-->{{ $campsite->comment->subject }}<!--/a-->
{{ if $campsite->comment == $campsite->default_comment }}</b>{{ /if }}</div>
	<div class ="field-label" id="sender-label">From:</div>
	<div class ="field-value" id="sender-value">{{ $campsite->comment->reader_email }}</div>
	<div class ="field-label" id="content-label">Content:</div>
	<div class ="field-value" id="content-value">{{ $campsite->comment->content }}</div>
    </div><!-- .commentinner -->
    </div><!-- .comment -->
  {{ /list_article_comments }}

{{ if $campsite->submit_comment_action->is_error }}
<div class="error"><div class="errorinner">
  There was an error submitting the comment: {{ $campsite->submit_comment_action->error_message }}
</div></div>
{{ /if }}
{{ if $campsite->submit_comment_action->ok }}
<div class="notice"><div class="noticeinner">
  {{ if $campsite->publication->moderated_comments }}
  Your comment was submitted for approval.
  {{ else }}
  Your comment was approved.
  {{ /if }}
</div></div>
{{ /if }}
{{ if $campsite->preview_comment_action->is_error }}
<div class="error"><div class="errorinner">
  There was an error previewing the comment: {{ $campsite->preview_comment_action->error_message }}
</div></div>
{{ /if }}
{{ if $campsite->preview_comment_action->ok }}
<div class="preview"><div class="previewinner">
    <h3>Comment preview</h3>
    Subject: {{ $campsite->preview_comment_action->subject }}, Reader email: {{ $campsite->preview_comment_action->reader_email }}<br/>
    Content: {{ $campsite->preview_comment_action->content }}
</div></div>
{{ /if }}
{{ if !$campsite->article->comments_locked
      && ($campsite->user->logged_in || $campsite->publication->public_comments) }}
  <div id="genericform">
    {{ comment_form submit_button="Submit" preview_button="Preview" anchor="comments" button_html_code="class=\"submitbutton\"" }}

      <div class="field-label">E-mail:</div>
      <div class="field-value">
      {{ if $campsite->user->logged_in }}
        {{ $campsite->user->email }}
      {{ else }}
        {{ camp_edit object="comment" attribute="reader_email" html_code="class=\"input_long\"" }}
      {{ /if }}
      </div>
      <div class="field-label">Subject:</div>
      <div class="field-value">{{ camp_edit object="comment" attribute="subject" html_code="class=\"input_long\"" }}</div>
      <div class="field-label">Comment:</div>
      <div class="field-value">{{ camp_edit object="comment" attribute="content" }}</div>
    {{ if $campsite->publication->captcha_enabled }}

      <div class="field-label" id="captcha-info">Please fill in the code shown in the image below:</div>
      <div class="field-value" id="captcha-field">{{ camp_edit object="captcha" attribute="code" }}
      <div id="captcha-image"><img src="/include/captcha/image.php"></div>
	</div>
    {{ /if }}

    <div align="center">{{ /comment_form }}</div>
    </div>

{{ elseif !$campsite->article->comments_locked }}
<div class="notice"><div class="noticeinner">
You must be a registered reader in order to submit comments.
</div></div>
{{ else }}
<!--div class"notice">Comment posting is not allowed.</div -->

{{ /if }}

</div><!-- .commentsinner -->
</div><!-- .comments comments- -->
{{ /if }}