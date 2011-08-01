{{ if $gimme->article->comments_enabled && $gimme->article->content_accessible }}
{{ list_article_comments columns="2" order="bydate desc"}}

<div class="block" id="comments_wrap">
{{ if $gimme->current_list->at_beginning }}
  <a name="comments">  </a>
  <h3>{{ $gimme->article->comment_count }} Response(s) to &#8220;{{ $gimme->article->name }}&#8221;</h3>

  <ol class="commentlist">
{{ /if }}
  
   <li class="comment {{ if $gimme->current_list->column == "1" }}odd{{ else }}even{{ /if }}">
      <div class="comment-head">
         <div class="user-meta">
{{* get gravatar image *}}
{{ assign var="profile_email" value=`$gimme->comment->reader_email` }}
{{ php }}
$profile_email = $this->get_template_vars('profile_email');
print "<img src=\"http://www.gravatar.com/avatar/".md5( strtolower( trim( $profile_email ) ) )."?s=60\" / class=\"commentimage\" width=60 height=60 />";
{{ /php }}
             <span class="name">{{ $gimme->comment->nickname }}</span> {{ $gimme->comment->submit_date|camp_date_format:"%e.%m.%Y at %H:%i" }}
          </div>
      </div>
      <div class="comment-entry">{{ $gimme->comment->content }}</div>
   </li>
 
{{ if $gimme->current_list->at_end }}                 
  </ol>    

{{ /if }}
</div> <!-- comments_wrap -->

{{ /list_article_comments }}

<div class="block" id="respond">
<a name="commentform">  </a>
<h3>Leave a Reply</h3>
{{ include file="_tpl/article-comments-errorcheck.tpl" }}
{{* if $gimme->comment->defined }}
    <p><strong>{{ $gimme->comment->subject }}
        ({{ $gimme->comment->reader_email|obfuscate_email }}) -
        {{ $gimme->comment->level }}</strong></p>
    <p>{{ $gimme->comment->content }}</p>
{{ /if *}}

{{ if $gimme->user->blocked_from_comments }}
    <div class="message messagecomment messageerror">This user has been banned from writing comments.</div>
{{ else }}

{{ comment_form html_code="id=\"commentform\"" submit_button="SUBMIT" button_html_code="tabindex=\"6\"" }}

<div class="comment-form">
<script type="text/javascript">
function switchName() {
	var anonymous = document.getElementById('is_anonymous').checked;
	if (anonymous) {
		document.getElementById('author').disabled = true;
	}
	else {
		document.getElementById('author').disabled = false;
	}
}
</script>
<p>
<label for="author">Name {{ if ! $gimme->user->logged_in }}(required){{ /if }}</label>
{{ camp_edit object="comment" attribute="nickname" html_code="class=\"textfield\" id=\"author\" size=\"22\" tabindex=\"1\"" }}
</p>
<p>
{{ if $gimme->user->logged_in }}
	{{ camp_edit object="comment" attribute="is_anonymous" html_code="id=\"is_anonymous\" onClick=\"switchName();\""}} <label for="is_anonymous">Post anonymous</label>
{{ /if }}
</p>

<p>
<label for="email">Email (hidden)</label>
{{ camp_edit object="comment" attribute="reader_email" html_code="class=\"textfield\" id=\"email\" size=\"22\" tabindex=\"2\"" }}
</p>

<!-- label for="CommentSubject">Comment subject:</label -->
<input type="hidden" name="f_comment_subject" value="Site comment" />
{{* camp_edit object="comment" attribute="subject" html_code="class=\"textfield\" id=\"comment-subject\" tabindex=\"3\"" *}}

<p>
<label for="comment">Comment</label>
{{ camp_edit object="comment" attribute="content" html_code="id=\"comment\" rows=\"5\" tabindex=\"4\"" }}
</p>

<p>
<label for="f_captcha_image">&nbsp;</label>
<img src="{{ captcha_image_link }}">
</p>

<p>
<label for="f_captcha_code">Enter the code:</label>
{{ camp_edit object="captcha" attribute="code" html_code="class=\"textfield\" id=\"comment-code\" tabindex=\"5\"" }}
</p>

<p>{{ /comment_form }}</p>
{{ /if }}

{{ unset_comment }}
{{ if $gimme->comment->defined }}
    <div class="message messagecomment messageerror">Error: previous comment is still active</div>
{{ /if }}

<div style="clear: both"></div>
</div>

<div class="comment-info">
  <p>Please fill the required box or you can&rsquo;t comment at all. Please use kind words. Your e-mail address will not be published. </p>
  <p>You can use these HTML tags and attributes: &lt;a href="" title=""&gt; &lt;abbr title=""&gt; &lt;acronym title=""&gt; &lt;b&gt; &lt;blockquote cite=""&gt; &lt;cite&gt; &lt;code&gt; &lt;del datetime=""&gt; &lt;em&gt; &lt;i&gt; &lt;q cite=""&gt; &lt;strike&gt; &lt;strong&gt;</p>
</div>

<div class="fix"></div>

<div class="fix"></div>
</div> <!-- end #respond -->

{{ /if }}
