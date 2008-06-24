<table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td height="90" align="left"><!** include home-banner.tpl></td>
        </tr>
<!** list article type is Article topic is "Home rest:en" order bynumber asc>
        <tr>
          <td valign="top">
	        <!** if image 1>
             <div class="front-slika2"><img src="/cgi-bin/get_img?<!** urlparameters image 1>" width="72" height="50" border="0"><br><span class="caption"><!** print image description></span></div>
<p class="nadnaslov2"><!** print article deck></p>
            <p class="naslov2"><a class="naslov2" href="<!** uri>"><!** print article name></a></p>                 
            <p class="tekst"><!** print article intro><a href="<!** uri>" border="0"><img src="/look/img/dalje.gif" border="0"></a></p>
<!** if article teaser_a not "">                      
<p class="tizeri"><img src="/look/img/tizer.gif" width="11" height="11"> <!** print section name>  |   <span class="caption"><!** print article teaser_a></span></p>
<!** endif>
             <!** else>
			<span class="nadn"><!** print article deck></span>
            <span class="nasl"><a class="naslov2" href="<!** uri>"><!** print article name></a></span>                 
            <p class="tekst"><!** print article intro> <a href="<!** uri>" border="0"><img src="/look/img/dalje.gif" border="0"></a></p>
                 <!** endif>
		  </td>              
        </tr>
        <tr>
          <td height="1" background="/look/img/07linija.gif"></td>
		</tr>
		<tr>
		  <td height="2"></td>
		</tr>		
<!** endlist>
      </table>
