<table width="100%" cellpadding="0" cellspacing="0" border="0">
{{ list_articles constraints="onfrontpage is on onsection is off" order="bynumber desc" }}
						  <tr>
						    <td height="8"></td>
						  </tr>
						  <tr>
						    <td>
							  <img style="float:left;margin-right:5px;" src="/get_img.php?{{ urlparameters options="image 1" }}"><p class="nadnaslov-front">{{ $campsite->article->deck }}</p>
							  <p class="big-naslov" style="font-size: 14px"><a class="naslov" href="{{ uri options="reset_subtitle_list" }}">{{ $campsite->article->name }}</a></p>
							  <p class="tekst-front">{{ $campsite->article->intro }}
							  <span class="dalje"><a class="dalje" href="{{ uri options="reset_subtitle_list" }}">full story<img src="/templates/img/dalje.gif" border="0"></a></span></p>
{{ /list_articles }}
							</td>
						  </tr>
						</table>
