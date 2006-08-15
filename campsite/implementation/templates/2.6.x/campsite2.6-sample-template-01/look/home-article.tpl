<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
  <td valign="top">
    <!** list article type is Article onfrontpage is on order bynumber asc>
      <table>
      <tr>
         <td valign="top">
            <p class="main-naslov"><a class="main-naslov" href="<!** uri reset_subtitle_list>"><!** print article name></a></p>  
            <p class="tekst"><!** print article intro></p>
           <!** if article hasAttachments>
           <!** list articleAttachment>
           <a class="dalje" href="<!** URI ArticleAttachment>"><!** print articleAttachment Description></a> <span class="tekst">(<!** print ArticleAttachment MimeType>, <!** print ArticleAttachment sizeMB>MB)</span><br>
           <!** endlist>
           <!** endif>
           <span class="dalje"><a class="dalje" href="<!** uri reset_subtitle_list>">full story</a></span>
         </td>
         <td>
           <!** if image 2>
           <table><tr><td><img src="/cgi-bin/get_img?<!** urlparameters image 2>" align="center"></td></tr><tr><td align="center"><span class="caption"><!** print image 2 description></span></td></tr></table>
           <!** endif>
         </td>
      </tr>
      </table>

   <!** endlist article>
  </td> 
</tr>
</table>