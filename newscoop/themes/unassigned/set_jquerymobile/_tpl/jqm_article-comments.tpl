{{ if $gimme->article->comments_enabled && $gimme->article->content_accessible }}

{{ list_article_comments columns="2" order="bydate desc"}}
  {{ if $gimme->current_list->at_beginning }}
    <h3>{{ $gimme->article->comment_count }} Response{{ if $gimme->article->comment_count > 1 }}s{{ /if }} to &#8220;{{ $gimme->article->name }}&#8221;</h3>
  {{ /if }}
  <div class="ui-body {{ if $gimme->current_list->column == "1" }}ui-body-e{{ else }}ui-body-d{{ /if }}">
  <p><strong class="name">{{ $gimme->comment->nickname }}</strong>
<br><small>{{ $gimme->comment->submit_date|camp_date_format:"%M %e, %Y at %H:%i" }}</small></p>
  <p>{{ $gimme->comment->content }}</p>
  </div>
  {{ if $gimme->current_list->at_end }}{{ /if }}
{{ /list_article_comments }}

{{ if $gimme->submit_comment_action->defined && $gimme->submit_comment_action->rejected }}
    <div class="ui-body ui-body-e"><h3>Your comment has not been accepted.</h3></div>
{{ /if }}

{{ if $gimme->submit_comment_action->is_error }}
    <div class="ui-body ui-body-e" style="border:1 px solid red;"><h3>{{ $gimme->submit_comment_action->error_message }}</h3>
        <span class="posterrorcode" style="display: none;">{{ $gimme->submit_comment_action->error_code }}</span>
   </div>
{{ else }}
    {{ if $gimme->submit_comment_action->defined }}
        {{ if $gimme->publication->moderated_comments }}
            <div class="ui-body ui-body-e"><h3>Your comment has been sent for approval.</h3></div>
        {{ /if }}
    {{ /if }}   
{{ /if }}

{{* if $gimme->comment->defined }}
    <p>{{ $gimme->comment->content }}</p>
    <p><strong>{{ $gimme->comment->subject }}
        ({{ $gimme->comment->reader_email|obfuscate_email }}) -
        {{ $gimme->comment->level }}</strong></p>
{{ /if *}}

<h3>Leave a Reply</h3>

{{ if $gimme->user->blocked_from_comments }}
    <div class="posterror">This user is banned from commenting!</div>
{{ else }}

{{ comment_form html_code="id=\"commentform\"" submit_button="SUBMIT" }}

<label for="author"><small>Name (required)</small></label>
{{ camp_edit object="comment" attribute="nickname" html_code="id=\"author\"" }}

<label for="email"><small>Email (will not be published) (required)</small></label>
{{ camp_edit object="comment" attribute="reader_email" html_code="id=\"email\"" }}

{{*
    <div data-role="fieldcontain">
<label for="CommentSubject">Comment subject:</label>
{{ camp_edit object="comment" attribute="subject" html_code="id=\"comment-subject\"" }}
    </div>
*}}
<input type="hidden" name="f_comment_subject" value="Site comment" />

<label for="comment"><small>Comment</small></label>
{{ camp_edit object="comment" attribute="content" html_code="id=\"comment\"" }}
<br clear="all" />
<img src="{{ captcha_image_link }}"><br clear="all" />
<label for="comment-code"><small>Enter the code:</small></label>{{ camp_edit object="captcha" attribute="code" html_code="id=\"comment-code\"" }}

{{ /comment_form }}
{{ /if }}

{{ unset_comment }}
{{ if $gimme->comment->defined }}
    <div class="posterror">Error: previous comment is still active</div>
{{ /if }}

  <p><small><em>Please fill the required box or you can&rsquo;t comment at all. Please use kind words. Your e-mail address will not be published.</em></small></p>
  <!--p>You can use these HTML tags and attributes: &lt;a href="" title=""&gt; &lt;abbr title=""&gt; &lt;acronym title=""&gt; &lt;b&gt; &lt;blockquote cite=""&gt; &lt;cite&gt; &lt;code&gt; &lt;del datetime=""&gt; &lt;em&gt; &lt;i&gt; &lt;q cite=""&gt; &lt;strike&gt; &lt;strong&gt;</p-->


{{ /if }}
