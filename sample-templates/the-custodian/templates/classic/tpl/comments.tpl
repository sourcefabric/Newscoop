{{ if $gimme->article->comments_enabled }}

<div id="comments">
<a name="comments">
  <h3>{{ if $gimme->language->name == "English" }}Comments{{ else }}Comentarios{{ /if }}</h3>
</a>
{{ list_article_comments }}
{{ if $gimme->current_list->at_beginning }}
<a name="commentlist">
  <h4>{{ if $gimme->language->name == "English" }}Previous comments{{ else }}Los comentarios anteriores{{ /if }}</h4>
</a>
{{ /if }}
  <div class="comment" {{ if $gimme->current_list->at_end }}id="everlast"{{ /if }}>
    <p><strong>{{ $gimme->comment->nickname }}</strong><br>
    {{ $gimme->comment->content }}</p>
    <p><em>{{ $gimme->comment->subject }} | <span>{{ $gimme->comment->submit_date|camp_date_format:"%M %e, %Y" }}</span></em></p>
  </div><!-- /.comment -->
{{ /list_article_comments }}

<a name="commentform">
    <h4>{{ if $gimme->language->name == "English" }}Your comment{{ else }}Su comentario{{ /if }}</h4>
</a>
{{ if $gimme->submit_comment_action->rejected }}
    <div class="posterror">{{ if $gimme->language->name == "English" }}Your comment has not been accepted.{{ else }}Su comentario no ha sido aceptada.{{ /if }}</div>
{{ /if }}

{{ if $gimme->submit_comment_action->is_error }}
    <div class="posterror">{{ $gimme->submit_comment_action->error_message }}
        <span class="posterrorcode">{{ $gimme->submit_comment_action->error_code }}</span>
   </div>
{{ else }}
    {{ if $gimme->submit_comment_action->defined }}
        {{ if $gimme->publication->moderated_comments }}
            <div class="postinformation">{{ if $gimme->language->name == "English" }}Your comment has been sent for approval.{{ else }}Tu comentario ha sido enviado para su aprobación.{{ /if }}</div>
        {{ /if }}
    {{ /if }}   
{{ /if }}

{{* if $gimme->comment->defined }}
    <p><strong>{{ $gimme->comment->subject }}
        ({{ $gimme->comment->reader_email|obfuscate_email }}) -
        {{ $gimme->comment->level }}</strong></p>
    <p>{{ $gimme->comment->content }}</p>
{{ /if *}}

{{ if $gimme->user->blocked_from_comments }}
    <div class="posterror">{{ if $gimme->language->name == "English" }}This user is banned from commenting!{{ else }}Este usuario está prohibido de comentar!{{ /if }}</div>
{{ else }}

{{ comment_form submit_button="Send comment" }}
    <div class="form-element">
      <label for="CommentNickname">{{ if $gimme->language->name == "English" }}Your name (mandatory):{{ else }}Tu nombre (obligatorio):{{ /if }}</label>{{ camp_edit object="comment" attribute="nickname" }}
    </div>
   
    <div class="form-element">
      <label for="CommentEmail">{{ if $gimme->language->name == "English" }}Your e-mail (mandatory):{{ else }}Tu e-mail (obligatorio):{{ /if }}</label>{{ camp_edit object="comment" attribute="reader_email" }}
    </div>

    <div class="form-element">
      <label for="CommentSubject">{{ if $gimme->language->name == "English" }}Comment subject:{{ else }}Comentar tema{{ /if }}</label>{{ camp_edit object="comment" attribute="subject" }}
    </div>
    
    <div class="form-element">
      <label for="CommentContent">{{ if $gimme->language->name == "English" }}Comment text{{ else }}Texto del comentario{{ /if }}</label>{{ camp_edit object="comment" attribute="content" }}
    </div>
    {{ if $gimme->publication->captcha_enabled }}
    <div class="form-element clearfix">
      <label>&nbsp;</label><img src="{{ captcha_image_link }}"><br />
    </div>
    <div class="form-element clearfix">
      <label for="f_captcha_code">{{ if $gimme->language->name == "English" }}Enter the code:{{ else }}Introduce el código:{{ /if }} </label>{{ camp_edit object="captcha" attribute="code" }}
    </div>
    {{ /if }}
    <div class="form-element">
      <label for="submitComment"></label>
    </div>
{{ /comment_form }}
{{ /if }}

{{ unset_comment }}
{{ if $gimme->comment->defined }}
    <div class="posterror">{{ if $gimme->language->name == "English" }}Error: previous comment is still active{{ else }}Error: El comentario anterior es aún activo{{ /if }}</div>
{{ /if }}

</div><!-- /#comments -->

{{ /if }}
