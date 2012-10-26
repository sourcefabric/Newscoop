<table width="100%" cellspacing="0" cellpadding="0" border="0"> 
              <tr> 
                <td colspan="5" class="ticker"> </td> 
              </tr> 
              <tr> 
                <td colspan="5"><img src="/templates/img/spacer.gif" width="1" height="2"></td> 
              </tr> 
              <tr> 
                <td width="11"></td> 
                <td width="1" background="/templates/img/bgrmiddle1.gif"></td> 
                <td width="424" valign="top"> <!-- main story --> 
                  <table width="100%" cellspacing="0" cellpadding="0" border="0"> 
                    <tr> 
                      <td height="1" background="/templates/img/bgrmiddle2.gif"></td> 
                    </tr> 
                    <tr> 
                      <td valign="top" class="tizeri"> 
{{ list_articles length="1" constraints="type is Service" order="bynumber desc" }}
<a class="navigation" href="{{ uri options="template print.tpl" }}"><img src="/templates/img/tizer.gif" width="8" height="5" border="0">Print version</a>
                        <table width="100%" cellpadding="0" cellspacing="0" border="0"> 
                          <tr> 
                            <td height="1" background="/templates/img/bgrmiddle2.gif"></td> 
                          </tr> 
                        </table></td> 
                    </tr> 
                  </table> 
                  <!-- titles --> 
                  <table width="100%"  border="0" cellspacing="0" cellpadding="0"> 
                    <tr> 
                      <td valign="top">
                        <p class="main-naslov">{{ $campsite->article->name }}</p> 
                        <p class="text">{{ $campsite->article->full_text }}</p> 
{{ /list_articles }}
			</td> 
                    </tr> 
                    <tr> 
                      <td> </td> 
                    </tr> 
                  </table></td> 
                <td width="1" background="/templates/img/bgrmiddle1.gif"></td> 
                <td width="11"></td> 
              </tr> 
            </table>