<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td align="left">
<div class="tizeri" style="width:100px;float:right;"><ul class="tizeri"><li class="tizer"><a href="{{ uri options="template print.tpl" }}">Print article</a></li></ul></div><p class="datum-front">{{ $smarty.now|camp_date_format:"%W, %d. %M %Y." }}</p></td>
			  </tr>
			  <tr>
			    <td align="left" style="border-top: 1px solid #999999">
				     {{ if $campsite->article->has_image(2) }}
<div class="front-slika">
<img src="/get_img.php?{{ urlparameters options="image 2" }}" border="0"></div>
{{ /if }}

				  <p class="nadnaslov-front">{{ $campsite->article->deck }}</p>
				  <p class="big-naslov">{{ $campsite->article->name }}</p>
				  {{ if $campsite->article->byline != "" }}
				  <p class="nadnaslov-front">{{ $campsite->article->byline }}</p>
				  {{ /if }}
				  <p class="tekst-front">{{ $campsite->article->intro }}
				  <p class="tekst-front">{{ $campsite->article->full_text }}
				  </div>
				</td>
			  </tr>
			  
			  <!-- end tema dana -->
			  
			  <tr>
			    <td height="1" bgcolor="#999999"></td>
			  </tr>
			   
</tr>
{{ if $campsite->article->comments_enabled }}
	<tr>
		<td style="background-color: #F0F0F0">
			{{ if ! $campsite->submit_comment_action->is_error }}
				<a name="comments"></a>
			{{ /if }}
			{{ list_article_comments order="byDate asc" }}
				<table style="border-bottom: 1px solid black;" width="100%" cellspacing="0"
					cellpadding="0" id="comment_{{ $campsite->comment->identifier }}">
					<tr>
						<td valign="top" align="left" style="background-color: #D4D4D6; padding: 3px;">
							<p class="articleComment-text">{{ $campsite->comment->subject }}</p>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style=" font-size: 8pt; padding: 3px;">
							<p class="articleComment-text">Posted {{ $campsite->comment->submit_date }}
							by <b>{{ $campsite->comment->reader_email|obfuscate_email }}</b></p></td>
					</tr>
					<tr>
						<td valign="top" align="left" style="padding: 3px">
							<p class="articleComment-text">{{ $campsite->comment->content }}</p>
						</td>
					</tr>
				</table>
<script>
document.getElementById("comment_{{ $campsite->comment->identifier }}").style.padding-left=10*{{ $campsite->comment->level }}+"px";
</script>
				{{ /list_article_comments }}
{{ if $campsite->prev_list_empty }}
					<p class="articleComment-text">No comments have been posted.</p>
				{{ /if }}
		</td>
	</tr>

	<tr>
		<td style="padding-top: 15px;">
			{{ if $campsite->submit_comment_action->is_error }}
				<a name="comments"></a>
			{{ /if }}
			<div id="articleComment">
                        {{ comment_form submit_button="Submit comment" anchor="comments" }}
			<table cellpadding="3" style="border:1px solid black; background-color: #F0F0F0;">
			<tr>
				<td colspan="2">
					<p><b>Add a comment</b></p>
					<p><span style="color: red">{{ $campsite->submit_comment_action->error_message }}</span></p>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					<p>Your name/email:</p>
				</td>
				<td>
                                    {{ if $campsite->user->logged_in }}
                                        <p>{{ $campsite->user->email }}</p>
                                    {{ else }}
                                        {{ camp_edit object="comment" attribute="reader_email" size="40" }}
                                    {{ /if }}
				</td>
			</tr>
			<tr>
				<td>
					<p>Subject:</p>
				</td>
				<td>
                                        {{ camp_edit object="comment" attribute="subject" size="40" }}
				</td>
			</tr>
			<tr>
				<td valign="top">
					<p>Comment:</p>
				</td>
				<td>
                                        {{ camp_edit object="comment" attribute="content" }}
				</td>
			</tr>

	{{ if $campsite->publication->captcha_enabled }}
			<tr>
				<td colspan="2" align="center">
					<p>Type in this code (used to prevent spam):<br>
					<img src="{{ captcha_image_link }}"></p>
					{{ camp_edit object="captcha" attribute="code" }}
				</td>
			</tr>
	{{ /if }} <!-- end if articleComment CAPTCHAEnabled -->
			</table>
{{ /comment_form }}
</div>
		</td>
	</tr>
{{ /if }} <!-- end if articleComment enabled -->
</table>
