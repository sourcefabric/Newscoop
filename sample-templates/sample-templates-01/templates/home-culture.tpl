<table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #003366; border-collapse: collapse">
                  <tr>
                    <td bgcolor="#003366"><p class="blok" align="center">Culture and interview</p>
                  </tr>
                  <tr>
                    <td>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                          <td valign="top">
  						{{ local }}
						{{ set_section number="20" }}
						{{ list_articles length="3" constraints="type is Interview" order="bynumber desc" }}
							<p class="blok-naslov"><a class="blok-naslov" href="{{ uri options="reset_subtitle_list" }}">{{ $campsite->article->name }}</a></p>
							<p class="blok-podnaslov">{{ $campsite->article->intro }}</p>
						{{ /list_articles }}
						{{ /local }}
						  </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>