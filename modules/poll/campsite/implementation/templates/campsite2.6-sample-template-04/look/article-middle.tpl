<table width="100%" cellspacing="0" cellpadding="0" border="0"> 
              <tr> 
                <td colspan="5" class="ticker"> </td> 
              </tr> 
              <tr> 
                <td colspan="5"><img src="/look/img/spacer.gif" width="1" height="2"></td> 
              </tr> 
              <tr> 
                <td width="11"></td> 
                <td width="1" background="/look/img/bgrmiddle1.gif"></td> 
                <td width="424" valign="top"> <!-- main story --> 
                  <table width="100%" cellspacing="0" cellpadding="0" border="0"> 
                    <tr> 
                      <td height="1" background="/look/img/bgrmiddle2.gif"></td> 
                    </tr> 
                    <tr> 
                      <td valign="top" class="tizeri"> <a class="navigation" href="<!** uri template print.tpl>"><img src="/look/img/tizer.gif" width="8" height="5" border="0">Print version</a>
                        <table width="100%" cellpadding="0" cellspacing="0" border="0"> 
                          <tr> 
                            <td height="1" background="/look/img/bgrmiddle2.gif"></td> 
                          </tr> 
                        </table></td> 
                    </tr> 
                  </table> 
                  <!-- titles --> 
                  <table width="100%"  border="0" cellspacing="0" cellpadding="0"> 
                    <tr> 
                      <td valign="top">
<!** if image 2>
<div class="front-slika1"><img src="/cgi-bin/get_img?<!** urlparameters image 2>" border="0"> 
                          <p class="text"><!** print image 2 description></p> 
                        </div> 
<!** endif>
                        <p class="nadnaslov"><!** print article Deck></p> 
                        <p class="main-naslov"><!** print article name></p> 
                        <p class="text"><!** print article byline></p> 
                        <p class="text"><!** print article intro></p>
                        <p class="text"><!** print article Full_text></p> 
			</td> 
                    </tr> 
                    <tr> 
                      <td> </td> 
                    </tr> 
<!** if articleComment enabled>
	<tr>
		<td style="background-color: #9FD98B">
			<!** if not articleComment submitError>
				<a name="comments"></a>
			<!** endif>
			<!** list ArticleComment order byDate asc>
				<table style="border-bottom: 1px solid black;" width="100%" cellspacing="0"
					cellpadding="0" id="comment_<!** print ArticleComment identifier>">
					<tr>
						<td valign="top" align="left" style="background-color: #006B24; padding: 3px;">
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

			<table cellpadding="3" style="border:1px solid black; background-color: #9FD98B;">
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
                  </table></td> 
                <td width="1" background="/look/img/bgrmiddle1.gif"></td> 
                <td width="11"></td> 
              </tr> 
            </table>