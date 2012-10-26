<table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #003366; border-collapse: collapse">
                  <tr>
                    <td bgcolor="#003366"><p class="blok" align="center">Weekly special</p>
                  </tr>
                  <tr>
                    <td>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                          <td valign="top" style="padding-bottom:5px;">
  						{{ local }}
  						{{ set_current_issue }}
  						{{ unset_section }}
						{{ list_articles constraints="type is Special" }}
<p class="naslov" style="padding-left:5px;"><a class="naslov" href="{{ uri options="reset_subtitle_list" }}">{{ $campsite->article->name }}</a></p>
<p class="blok-podnaslov"><i>{{ $campsite->article->byline }}</i></p>
{{ if $campsite->article->has_image(1) }}
            <div align="center"><img src="/get_img.php?{{ urlparameters options="image 1" }}" align="center">
            <span class="caption">{{ $campsite->article->image1->description }}</span></div>
{{ /if }}							

							<p class="tekst" style="padding-left:5px;">{{ $campsite->article->intro }}</a></p>
<a href="{{ uri options="reset_subtitle_list" }}" class="dalje" style="padding-left:5px;">full story</a>
						{{ /list_articles }}
						{{ /local }}
						  </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
