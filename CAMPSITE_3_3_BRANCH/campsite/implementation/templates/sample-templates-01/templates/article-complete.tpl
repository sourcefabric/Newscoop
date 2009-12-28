<table width="466"  border="0" cellspacing="5" cellpadding="0">
	<tr>
	{{ if $campsite->article->type_name == "Article" }}
		{{ include file="article-article.tpl" }}
	{{ /if }}
	{{ if $campsite->article->type_name == "Special" }}
		{{ include file="article-special.tpl" }}
	{{ /if }}
	{{ if $campsite->article->type_name == "Interview" }}
		{{ include file="article-interview.tpl" }}
	{{ /if }}
	</tr>
{{ if $campsite->article->comments_enabled }}
	<tr>
		<td style="background-color: #d3e5f1">
			{{ if ! $campsite->submit_comment_action->is_error }}
				<a name="comments"></a>
			{{ /if }}
			{{ list_article_comments order="byDate asc" }}
				<table style="border-bottom: 1px solid black;" width="100%" cellspacing="0"
					cellpadding="0" id="comment_{{ $campsite->comment->identifier }}">
					<tr>
						<td valign="top" align="left" style="background-color: #3878af; padding: 3px;">
							<p class="tekst">{{ $campsite->comment->subject }}</p>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style=" font-size: 8pt; padding: 3px;">
							<p class="tekst">Posted {{ $campsite->comment->submit_date }}
							by <b>{{ $campsite->comment->reader_email|obfuscate_email }}</b></p>
                        </td>
					</tr>
					<tr>
						<td valign="top" align="left" style="padding: 3px">
							<p class="tekst">{{ $campsite->comment->content }}</p>
						</td>
					</tr>
				</table>
<script>
document.getElementById("comment_{{ $campsite->comment->identifier }}").style.padding-left=10*{{ $campsite->comment->level }}+"px";
</script>
				{{ /list_article_comments }}
{{ if $campsite->prev_list_empty }}
					<p class="tekst">No comments have been posted.</p>
				{{ /if }}
		</td>
	</tr>

	<tr>
		<td style="padding-top: 15px;">
			{{ if $campsite->submit_comment_action->is_error }}
				<a name="comments"></a>
			{{ /if }}
                        {{ comment_form submit_button="Submit comment" anchor="comments" button_html_code="class=\"button\"" }}
			{{ formparameters options="articlecomment" }}

			<table cellpadding="3" style="border:1px solid black; background-color: #d3e5f1;">
			<tr>
				<td colspan="2">
					<p class="tekst"><b>Add a comment</b></p>
					<span style="color: red">{{ $campsite->submit_comment_action->error_message }}</span>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					<p class="tekst">Your name/email:</p>
				</td>
				<td>
                    {{ if $campsite->user->logged_in }}
                        <p class="tekst">{{ $campsite->user->email }}</p>
                    {{ else }}
                        {{ camp_edit object="comment" attribute="reader_email" html_code="class=\"longfield\"" }}
                    {{ /if }}
				</td>
			</tr>
			<tr>
				<td>
					<p class="tekst">Subject:</p>
				</td>
				<td>
                    {{ camp_edit object="comment" attribute="subject" html_code="class=\"longfield\"" }}
				</td>
			</tr>
			<tr>
				<td valign="top">
					<p class="tekst">Comment:</p>
				</td>
				<td>
                    {{ camp_edit object="comment" attribute="content" columns="35" rows="5" html_code="class=\"textarea\"" }}
				</td>
			</tr>

	{{ if $campsite->publication->captcha_enabled }}
			<tr>
				<td colspan="2" align="center">
					<p class="tekst">Type in this code (used to prevent spam):</p>
					<img src="{{ captcha_image_link }}"><br>
					{{ camp_edit object="captcha" attribute="code" html_code="class=\"field\"" }}
				</td>
			</tr>
	{{ /if }} <!-- end if articleComment CAPTCHAEnabled -->
			</table>
{{ /comment_form }}
		</td>
	</tr>
{{ /if }} <!-- end if articleComment enabled -->
</table>
