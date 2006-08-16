<!-- ******* archive ******** -->	
<table border="0" cellpadding="5" cellspacing="0" width="400">
  <tr> 
    <td valign="top">
<!** section off>
<!** list length 8 issue order bydate desc>
  <!** if issue iscurrent>
  <!** else> 
  <tr>
    <td valign="top">
      <p class="blok-naslov"><b><a class="blok-naslov" href="<!** URIPath issue>"><!** print issue name>, dated <!** print issue date " %W, %M %Y"></b></a></p>
      <!** local>
      <!** publication name Dynamic>
      <!** section off>
      <!** list article>

        <span class="dalje"><a class="dalje" href="<!** uri reset_subtitle_list>"><!** print article name></a></span><br/>

      <!** endlist article>                          
      <!** endlocal>
    </td>
  </tr>
			  
  <!** if list end>

  <!-- forward / back -->

  <tr>
    <td align="center">
      <table border="0" cellpadding="0" cellspacing="0" width="200">
	<tr>
	  <td colspan="3"> </td>
	</tr>
	<tr>
	  <td align="center" width="99">

          <!** if previousitems>

            <a href="<!** uri template archive.tpl>"><img src="/look/img/nazad.gif" border="0"></a>

          <!** else>
          <!** endif>
          
        </td>
        <td width="2"> </td>
	<td align="center" width="99">
        
          <!** if nextitems>

          <a href="<!** uri template archive.tpl>"><img src="/look/img/napred.gif" border="0"></a>

          <!** else>                                     
          <!** endif>
        </td>
      </tr>
    </table>
  </td>
</tr>

<!** endif>

<!** endif>
<!** endlist issue>                
				  
<!-- ********* end arhiva *********** -->
    </td>
  </tr>
				  
</table>