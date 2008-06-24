<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td align="left">
<div class="tizeri" style="width:100px;float:right;"><ul class="tizeri"><li class="tizer"><a href="<!** uri template print.tpl>">Print article</a></li></ul></div><p class="datum-front"><!** date " %W, %d. %M %Y."></p></td>
			  </tr>
			  <tr>
			    <td align="left" style="border-top: 1px solid #999999">
				     <!** if image 2>
<div class="front-slika">
<img src="/cgi-bin/get_img?<!** urlparameters image 2>" border="0"></div>
<!** endif>

				  <p class="nadnaslov-front"><!** print article deck></p>
				  <p class="big-naslov"><!** print article name></p>
				  <!** if article byline not "">
				  <p class="nadnaslov-front"><!** print article byline></p>
				  <!** endif>
				  <p class="tekst-front"><!** print article intro>
				  <p class="tekst-front"><!** print article Full_text>
				  </div>
				</td>
			  </tr>
			  
			  <!-- end tema dana -->
			  
			  <tr>
			    <td height="1" bgcolor="#999999"></td>
			  </tr>
			   
</tr>
<!** if articleComment enabled>
	<tr>
		<td style="background-color: #F0F0F0">
			<!** if not articleComment submitError>
				<a name="comments"></a>
			<!** endif>
			<!** list ArticleComment order byDate asc>
				<table style="border-bottom: 1px solid black;" width="100%" cellspacing="0"
					cellpadding="0" id="comment_<!** print ArticleComment identifier>">
					<tr>
						<td valign="top" align="left" style="background-color: #D4D4D6; padding: 3px;">
							<p class="articleComment-text"><!** print articlecomment subject></p>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style=" font-size: 8pt; padding: 3px;">
							<p class="articleComment-text">Posted <!** print articlecomment submitDate "%W, %M %e, %Y">
							by <b><!** print articlecomment readerEmailObfuscated></b></p></td>
					</tr>
					<tr>
						<td valign="top" align="left" style="padding: 3px">
							<p class="articleComment-text"><!** print articlecomment content></p>
						</td>
					</tr>
				</table>
<script>
document.getElementById("comment_<!** print ArticleComment identifier>").style.padding-left=10*<!** print ArticleComment level>+"px";
</script>
				<!** ForEmptyList>
					<p class="articleComment-text">No comments have been posted.</p>
				<!** endlist>
		</td>
	</tr>

	<tr>
		<td style="padding-top: 15px;">
			<!** if articleComment submitError>
				<a name="comments"></a>
			<!** endif>
			<div id="articleComment">
			<form name="articleComment" action="<!** URI>#comments" method="POST">
			<input type="hidden" name="IdLanguage" value="<!** print language number>">
			<input type="hidden" name="IdPublication" value="<!** print publication identifier>">
			<input type="hidden" name="NrIssue" value="<!** print issue number>">
			<input type="hidden" name="NrSection" value="<!** print section number>">
			<input type="hidden" name="NrArticle" value="<!** print article number>">
			<!** FormParameters articleComment>

			<table cellpadding="3" style="border:1px solid black; background-color: #F0F0F0;">
			<tr>
				<td colspan="2">
					<p><b>Add a comment</b></p>
					<p><span style="color: red"><!** print articlecomment submitError></span></p>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					<p>Your name/email:</p>
				</td>
				<td>
					<input type="text" name="CommentReaderEMail" maxlength="255" size="40"
						value="<!** print articleComment readerEmailPreview>">
				</td>
			</tr>
			<tr>
				<td>
					<p>Subject:</p>
				</td>
				<td>
					<input type="text" name="CommentSubject" maxlength="255" size="40"
						value="<!** print articleComment subjectPreview>">
				</td>
			</tr>
			<tr>
				<td valign="top">
					<p>Comment:</p>
				</td>
				<td>
					<textarea name="CommentContent" cols="35" rows="6"><!** print articleComment contentPreview></textarea>
				</td>
			</tr>

	<!** if articleComment CAPTCHAEnabled>
			<tr>
				<td colspan="2" align="center">
					<p>Type in this code (used to prevent spam):<br>
					<img src="<!** Print CAPTCHA ImageLink >"></p>
					<!** edit captcha code>
				</td>
			</tr>
	<!** endif articleComment> <!-- end if articleComment CAPTCHAEnabled -->
			<tr>
				<td colspan="2" align="center">
					<input type="submit" name="submitComment" id="articleCommentSubmit" value="Submit comment">
				</td>
			</tr>
			</table>
</form>
</div>
		</td>
	</tr>
<!** endif articleComment> <!-- end if articleComment enabled -->
</table>
