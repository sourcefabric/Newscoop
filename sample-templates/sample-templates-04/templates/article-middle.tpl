<table width="100%" cellspacing="0" cellpadding="0" border="0"> 
              <tr> 
                <td colspan="5" class="ticker"> </td> 
              </tr> 
              <tr> 
                <td colspan="5"><img src="/templates/img/spacer.gif" width="1" height="2"></td> 
              </tr> 
              <tr> 
                <td width="11"></td> 
                <td width="1" background="/templates/img/bgrmiddle1.gif"></td> 
                <td width="424" valign="top"> <!-- main story --> 
                  <table width="100%" cellspacing="0" cellpadding="0" border="0"> 
                    <tr> 
                      <td height="1" background="/templates/img/bgrmiddle2.gif"></td> 
                    </tr> 
                    <tr> 
                      <td valign="top" class="tizeri"> <a class="navigation" href="{{ uri options="template print.tpl" }}"><img src="/templates/img/tizer.gif" width="8" height="5" border="0">Print version</a>
                        <table width="100%" cellpadding="0" cellspacing="0" border="0"> 
                          <tr> 
                            <td height="1" background="/templates/img/bgrmiddle2.gif"></td> 
                          </tr> 
                        </table></td> 
                    </tr> 
                  </table> 
                  <!-- titles --> 
                  <table width="100%"  border="0" cellspacing="0" cellpadding="0"> 
                    <tr> 
                      <td valign="top">
{{ if $campsite->article->has_image(2) }}
<div class="front-slika1"><img src="/get_img.php?{{ urlparameters options="image 2" }}" border="0"> 
                          <p class="text">{{ $campsite->article->image2->description }}</p> 
                        </div> 
{{ /if }}
                        <p class="nadnaslov">{{ $campsite->article->deck }}</p> 
                        <p class="main-naslov">{{ $campsite->article->name }}</p> 
                        <p class="text">{{ $campsite->article->byline }}</p> 
                        <p class="text">{{ $campsite->article->intro }}</p>
                        <p class="text">{{ $campsite->article->full_text }}</p> 
			</td> 
                    </tr> 
                    <tr> 
                      <td> </td> 
                    </tr> 
{{ if $campsite->article->comments_enabled }}
	<tr>
		<td style="background-color: #9FD98B">
			{{ if ! $campsite->submit_comment_action->is_error }}
				<a name="comments"></a>
			{{ /if }}
			{{ list_article_comments order="byDate asc" }}
				<table style="border-bottom: 1px solid black;" width="100%" cellspacing="0"
					cellpadding="0" id="comment_{{ $campsite->comment->identifier }}">
					<tr>
						<td valign="top" align="left" style="background-color: #006B24; padding: 3px;">
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
			{{ formparameters options="articlecomment" }}

			<table cellpadding="3" style="border:1px solid black; background-color: #9FD98B;">
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
                                        {{ camp_edit object="comment" attribute="reader_email" }}
                                    {{ /if }}
				</td>
			</tr>
			<tr>
				<td>
					<p>Subject:</p>
				</td>
				<td>
                                        {{ camp_edit object="comment" attribute="subject" }}
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
                  </table></td> 
                <td width="1" background="/templates/img/bgrmiddle1.gif"></td> 
                <td width="11"></td> 
              </tr> 
            </table>
