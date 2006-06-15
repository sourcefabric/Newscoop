<table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td valign="top">
                  <!** list length 1 article type is Article onfrontpage is on order bynumber asc>
                  <!** if list start>
			        <!** if image 2>
            			<div class="tema-slika"><img src="/cgi-bin/get_img?<!** urlparameters image 2>" width="240" align="center">
<span class="caption"><!** print image 2 description></span>
</div>
			        <!** endif>
				    <p class="nadnaslov"><!** print article Deck></p>
                    <p class="main-naslov"><a class="main-naslov" href="<!** uri reset_subtitle_list>"><!** print article name></a></p>  
                    <p class="podnaslov"><!** print article Byline></p>
					<p class="tekst"><!** print article intro>    <span class="dalje"><a class="dalje" href="<!** uri reset_subtitle_list>">full story</a></span></p>
					<!** if article Teaser_a not "">
                    <p class="tizeri">><!** print article Teaser_a></p> 
					<!** else>
					<!** endif> 
					<!** if article Teaser_b not ""> 
                    <p class="tizeri">><!** print article Teaser_b></p> 
					<!** else>
					<!** endif>
		        <!** endif>
		  <!** endlist article>
				  </td> 
                </tr>
              </table>