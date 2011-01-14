<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#DDDDDD">
						  <tr>
						    <td height="8" bgcolor="#FFFFFF"></td>
						  </tr>
						  <tr>
						    <td bgcolor="#7F7F7F"><p class="navig-belo">CAMPSITE NEWS</p></td>
						  </tr>
						  <tr>
						    <td height="8"></td>
						  </tr>
						  <tr>
						    <td>
							  <table width="100%" cellpadding="0" cellspacing="0" border="0">
{{ list_articles constraints="type is Article onfrontpage is off onsection is on" }}
							    <tr>
								  <td width="20" valign="top"><img src="/templates/img/strelica-prog-sh.gif"></td>
								  <td><p class="prog-shema">{{ $campsite->article->byline }}<br><b><a class="naslov" href="{{ uri }}">{{ $campsite->article->name }}</a></b><br></td>
								</tr>
								<tr>
								  <td colspan="2" height="10"></td>
								</tr>
{{ /list_articles }}
																																		
							  </table>
							</td>
						  </tr>						  					  						  						  						  						  						  
						</table>
