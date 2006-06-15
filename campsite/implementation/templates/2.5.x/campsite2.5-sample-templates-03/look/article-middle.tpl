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
			   
			</table>
