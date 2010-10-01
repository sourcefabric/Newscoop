{{ if $campsite->article->comments_enabled }}

<div id="comments">
<a name="comments">
  <h3>{{ if $campsite->language->name == "English" }}Comments{{ else }}Comentarios{{ /if }}</h3>
</a>
{{ list_article_comments }}
{{ if $campsite->current_list->at_beginning }}
<a name="commentlist">
  <h4>{{ if $campsite->language->name == "English" }}Previous comments{{ else }}Los comentarios anteriores{{ /if }}</h4>
</a>
{{ /if }}
  <div class="comment" {{ if $campsite->current_list->at_end }}id="everlast"{{ /if }}>
    <p><strong>{{ $campsite->comment->nickname }}</strong><br>
    {{ $campsite->comment->content }}</p>
    <p><em>{{ $campsite->comment->subject }} | <span>{{ $campsite->comment->submit_date|camp_date_format:"%M %e, %Y" }}</span></em></p>
  </div><!-- /.comment -->
{{ /list_article_comments }}

<a name="commentform">
    <h4>{{ if $campsite->language->name == "English" }}Your comment{{ else }}Su comentario{{ /if }}</h4>
</a>
{{ if $campsite->submit_comment_action->rejected }}
    <div class="posterror">{{ if $campsite->language->name == "English" }}Your comment has not been accepted.{{ else }}Su comentario no ha sido aceptada.{{ /if }}</div>
{{ /if }}

{{ if $campsite->submit_comment_action->is_error }}
    <div class="posterror">{{ $campsite->submit_comment_action->error_message }}
        <span class="posterrorcode">{{ $campsite->submit_comment_action->error_code }}</span>
   </div>
{{ else }}
    {{ if $campsite->submit_comment_action->defined }}
        {{ if $campsite->publication->moderated_comments }}
            <div class="postinformation">{{ if $campsite->language->name == "English" }}Your comment has been sent for approval.{{ else }}Tu comentario ha sido enviado para su aprobación.{{ /if }}</div>
        {{ /if }}
    {{ /if }}   
{{ /if }}

{{* if $campsite->comment->defined }}
    <p><strong>{{ $campsite->comment->subject }}
        ({{ $campsite->comment->reader_email|obfuscate_email }}) -
        {{ $campsite->comment->level }}</strong></p>
    <p>{{ $campsite->comment->content }}</p>
{{ /if *}}

{{ if $campsite->user->blocked_from_comments }}
    <div class="posterror">{{ if $campsite->language->name == "English" }}This user is banned from commenting!{{ else }}Este usuario está prohibido de comentar!{{ /if }}</div>
{{ else }}

{{ comment_form submit_button="Send comment" }}
    <div class="form-element">
      <label for="CommentSubject">{{ if $campsite->language->name == "English" }}Your name (mandatory):{{ else }}Tu nombre (obligatorio):{{ /if }}</label>{{ camp_edit object="comment" attribute="subject" }}
    </div>
   
    <div class="form-element">
      <label for="CommentEmail">{{ if $campsite->language->name == "English" }}Your e-mail (mandatory):{{ else }}Tu e-mail (obligatorio):{{ /if }}</label>{{ camp_edit object="comment" attribute="reader_email" }}
    </div>

    <div class="form-element">
      <label for="CommentNickname">{{ if $campsite->language->name == "English" }}Comment subject:{{ else }}Comentar tema{{ /if }}</label>{{ camp_edit object="comment" attribute="nickname" }}
    </div>
    
    <div class="form-element">
      <label for="CommentContent">{{ if $campsite->language->name == "English" }}Comment text{{ else }}Texto del comentario{{ /if }}</label>{{ camp_edit object="comment" attribute="content" }}
    </div>
    {{ if $campsite->publication->captcha_enabled }}
    <div class="form-element clearfix">
      <img src="{{ captcha_image_link }}"><br />
      <label for="f_captcha_code">{{ if $campsite->language->name == "English" }}Enter the code:{{ else }}Introduce el código:{{ /if }} </label>{{ camp_edit object="captcha" attribute="code" }}
    </div>
    {{ /if }}
    <div class="form-element">
      <label for="submitComment"></label>
    </div>
{{ /comment_form }}
{{ /if }}

{{ unset_comment }}
{{ if $campsite->comment->defined }}
    <div class="posterror">{{ if $campsite->language->name == "English" }}Error: previous comment is still active{{ else }}Error: El comentario anterior es aún activo{{ /if }}</div>
{{ /if }}

</div><!-- /#comments -->

{{ /if }}