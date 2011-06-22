{{ if $gimme->article->comments_enabled && $gimme->article->content_accessible }}

<div id="comments_wrap">

{{ list_article_comments columns="2" order="bydate desc"}}

{{ if $gimme->current_list->at_beginning }}
  <h2>{{ $gimme->article->comment_count }} Response(s) to &#8220;{{ $gimme->article->name }}&#8221;</h2>
  <ol class="commentlist">
{{ /if }}

   <li class="comment {{ if $gimme->current_list->column == "1" }}odd{{ else }}even{{ /if }}">
      <div class="comment-head cl">
         <div class="user-meta">
             <strong class="name">{{ $gimme->comment->nickname }}</strong> {{ $gimme->comment->submit_date|camp_date_format:"%e.%m.%Y at %H:%i" }}
          </div>
      </div>
      <div class="comment-entry">
          <p>{{ $gimme->comment->content_real }}</p>
      </div>
   </li>

{{ if $gimme->current_list->at_end }}
  </ol>
{{ /if }}

{{ /list_article_comments }}

</div> <!-- end #comments_wrap -->


<div id="respond">

{{ if $gimme->submit_comment_action->defined && $gimme->submit_comment_action->rejected }}
    <div class="posterror">Your comment has not been accepted.</div>
{{ /if }}

{{ if $gimme->submit_comment_action->is_error }}
    <div class="posterror">{{ $gimme->submit_comment_action->error_message }}
        <span class="posterrorcode">{{ $gimme->submit_comment_action->error_code }}</span>
   </div>
{{ else }}
    {{ if $gimme->submit_comment_action->defined }}
        {{ if $gimme->publication->moderated_comments }}
            <div class="postinformation">Your comment has been sent for approval.</div>
        {{ /if }}
    {{ /if }}
{{ /if }}

{{* if $gimme->comment->defined }}
    <p><strong>{{ $gimme->comment->subject }}
        ({{ $gimme->comment->reader_email|obfuscate_email }}) -
        {{ $gimme->comment->level }}</strong></p>
    <p>{{ $gimme->comment->content }}</p>
{{ /if *}}

{{ if !$gimme->article->comments_locked }}
<h2>Leave a Reply</h2>

<div class="cancel-comment-reply">
  <small><a rel="nofollow" id="cancel-comment-reply-link" href="#respond" style="display:none;">Click here to cancel reply.</a></small>
</div>

{{ if $gimme->user->blocked_from_comments }}
    <div class="posterror">This user is banned from commenting!</div>
{{ else }}

{{ comment_form html_code="id=\"commentform\"" submit_button="SUBMIT" button_html_code="tabindex=\"6\"" }}

<div class="comment-fields">

<p>
<label for="author"><small>Name (required)</small></label>
{{ camp_edit object="comment" attribute="nickname" html_code="id=\"author\" size=\"22\" tabindex=\"1\"" }}
</p>

<p>
<label for="email"><small>Email (will not be published) (required)</small></label>
{{ camp_edit object="comment" attribute="reader_email" html_code="id=\"email\" size=\"22\" tabindex=\"2\"" }}
</p>

<!-- label for="CommentSubject">Comment subject:</label -->
<input type="hidden" name="f_comment_subject" value="Site comment" />
{{* camp_edit object="comment" attribute="subject" html_code="id=\"comment-subject\" tabindex=\"3\"" *}}

<p>
<label for="comment"><small>Comment</small></label>
{{ camp_edit object="comment" attribute="content" html_code="id=\"comment\" rows=\"5\" tabindex=\"4\"" }}
</p>

{{ if $gimme->publication->captcha_enabled }}
<p>
<img src="{{ captcha_image_link }}"><br />
<label for="f_captcha_code"><small>Enter the code:</small></label>{{ camp_edit object="captcha" attribute="code" html_code="id=\"comment-code\" tabindex=\"5\"" }}
</p>
{{ /if }}

<p>{{ /comment_form }}</p>

{{ /if }}

{{ unset_comment }}
{{ if $gimme->comment->defined }}
    <div class="posterror">Error: previous comment is still active</div>
{{ /if }}

<div style="clear: both"></div>
</div>

<div class="comment-info">
  <p>Please fill the required box or you can&rsquo;t comment at all. Please use kind words. Your e-mail address will not be published. </p>
  <p>You can use these HTML tags and attributes: &lt;a href="" title=""&gt; &lt;abbr title=""&gt; &lt;acronym title=""&gt; &lt;b&gt; &lt;blockquote cite=""&gt; &lt;cite&gt; &lt;code&gt; &lt;del datetime=""&gt; &lt;em&gt; &lt;i&gt; &lt;q cite=""&gt; &lt;strike&gt; &lt;strong&gt;</p>
</div>
{{ /if }}
<div class="fix"></div>

<div class="fix"></div>
</div> <!-- end #respond -->

{{ /if }}
