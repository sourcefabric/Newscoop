<table width="466"  border="0" cellspacing="5" cellpadding="0">
	<tr>
	<!** if article type Article>
		<!** include article-article.tpl>
	<!** endif>
	<!** if article type Special>
		<!** include article-special.tpl>
	<!** endif>
	<!** if article type Interview>
		<!** include article-interview.tpl>
	<!** endif>
	</tr>
<!** if articleComment enabled>
	<tr>
		<td style="background-color: #d3e5f1">
			<!** if not articleComment submitError>
				<a name="comments"></a>
			<!** endif>
			<!** list ArticleComment order byDate asc>
				<table style="border-bottom: 1px solid black;" width="100%" cellspacing="0"
					cellpadding="0" id="comment_<!** print ArticleComment identifier>">
					<tr>
						<td valign="top" align="left" style="background-color: #3878af; padding: 3px;">
							<!** print articlecomment subject>
						</td>
					</tr>
					<tr>
						<td align="left" valign="top" style=" font-size: 8pt; padding: 3px;">
							Posted <!** print articlecomment submitDate "%W, %M %e, %Y">
							by <b><!** print articlecomment readerEmailObfuscated></b></td>
					</tr>
					<tr>
						<td valign="top" align="left" style="padding: 3px">
							<!** print articlecomment content>
						</td>
					</tr>
				</table>
<script>
document.getElementById("comment_<!** print ArticleComment identifier>").style.padding-left=10*<!** print ArticleComment level>+"px";
</script>
				<!** ForEmptyList>
					No comments have been posted.
				<!** endlist>
		</td>
	</tr>

	<tr>
		<td style="padding-top: 15px;">
			<!** if articleComment submitError>
				<a name="comments"></a>
			<!** endif>
			<form name="articleComment" action="<!** URI>#comments" method="POST">
			<input type="hidden" name="IdLanguage" value="<!** print language number>">
			<input type="hidden" name="IdPublication" value="<!** print publication identifier>">
			<input type="hidden" name="NrIssue" value="<!** print issue number>">
			<input type="hidden" name="NrSection" value="<!** print section number>">
			<input type="hidden" name="NrArticle" value="<!** print article number>">
			<!** FormParameters articleComment>

			<table cellpadding="3" style="border:1px solid black; background-color: #d3e5f1;">
			<tr>
				<td colspan="2">
					<b>Add a comment</b><br>
					<span style="color: red"><!** print articlecomment submitError></span>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					Your name/email:
				</td>
				<td>
					<input type="text" name="CommentReaderEMail" maxlength="255" size="40"
						value="<!** print articleComment readerEmailPreview>" class="longfield">
				</td>
			</tr>
			<tr>
				<td>
					Subject:
				</td>
				<td>
					<input type="text" name="CommentSubject" maxlength="255" size="40"
						value="<!** print articleComment subjectPreview>" class="longfield">
				</td>
			</tr>
			<tr>
				<td valign="top">
					Comment:
				</td>
				<td>
					<textarea name="CommentContent" cols="40" rows="6"  class="textarea"><!** print articleComment contentPreview></textarea>
				</td>
			</tr>

	<!** if articleComment CAPTCHAEnabled>
			<tr>
				<td colspan="2" align="center">
					Type in this code (used to prevent spam):<br>
					<img src="<!** Print CAPTCHA ImageLink >"><br>
					<!** edit captcha code HTML class=\"field\">
				</td>
			</tr>
	<!** endif articleComment> <!-- end if articleComment CAPTCHAEnabled -->
			<tr>
				<td colspan="2" align="center">
					<input type="submit" name="submitComment" class="button"
						id="articleCommentSubmit" value="Submit comment">
				</td>
			</tr>
			</table>
</form>
		</td>
	</tr>
<!** endif articleComment> <!-- end if articleComment enabled -->
</table>
