<table border="0" cellpadding="0" cellspacing="0">
<tr>
                <td class="menu"><p class="indeks" align="left"><a class="indeks" href="/">HOME</a></p></td>
              </tr>
        {{ local }}
        {{ set_publication identifier="5" }}
        {{ set_current_issue }}
        {{ unset_section }}
        {{ unset_article }}
        {{ list_sections constraints="number smaller 31" }}
              <tr>
                <td class="menu"><p class="indeks" align="left"><a class="indeks" href="{{ uri options="section reset_article_list" }}">{{ $campsite->section->name }}</a></p></td>
              </tr>
		{{ /list_sections }}
		{{ /local }}
              <tr>
                <td background="/templates/img/05bgmeni2b.gif">
                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                      <td><img border="0" src="/templates/img/05bgmeni2b.gif" width="1" height="17"></td>
                      <td width="126" style="border-right: 1px solid #003366;"><p class="index-naslovi">SECTIONS</p></td>
                    </tr>
                  </table>
                </td>
              </tr>
        {{ local }}
        {{ set_publication identifier="5" }}
        {{ set_current_issue }}
        {{ unset_section }}
        {{ unset_article }}
        {{ list_sections constraints="number greater 31" }}
        {{ if $campsite->section->number > 199 }}
        {{ else }}
              <tr>
			    <td class="menu"><p class="indeks" align="left"><a class="indeks" href="{{ uri options="section reset_article_list" }}">{{ $campsite->section->name }}</a></p></td>
              </tr>
        {{ /if }}
		{{ /list_sections }}
		{{ /local }}
              <tr>
                <td background="/templates/img/05bgmeni2b.gif">
				  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                      <td><img border="0" src="/templates/img/05bgmeni2b.gif" width="1" height="17"></td>
                      <td width="126" style="border-right: 1px solid #003366;"><p class="index-naslovi">ABOUT PT#1</p></td>
                    </tr>
                  </table>
                </td>
              </tr>
	{{ local }}
        {{ set_publication identifier="5" }}
        {{ unset_issue }}
        {{ unset_section }}
        {{ unset_article }}
        {{ list_sections constraints="number greater 199" }}
        {{ if $campsite->section->number == 230 }}
        {{ else }}
              <tr>
                <td class="menu"><p class="indeks" align="left"><a class="indeks" href="{{ uri options="section reset_article_list" }}">{{ $campsite->section->name }}</a></p></td>
              </tr>
        {{ /if }}
	{{ /list_sections }}
	{{ /local }}
              <tr>
                <td background="/templates/img/05bgmeni2b.gif">
                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                      <td><img border="0" src="/templates/img/05bgmeni2b.gif" width="1" height="17"></td>
                      <td width="126" style="border-right: 1px solid #003366;"><p class="index-naslovi">REGISTRATION</p></td>
                    </tr>
                  </table>
                </td>
              </tr>	
			  <tr>
			    <td bgcolor="#d3e5f1" align="center" style="padding: 3px 3px 3px 3px; border-right: 1px solid #003366;">
				  <img src="/templates/img/packaged.gif" width="125">
				  {{ include file="login-box.tpl" }}
				</td>
			  </tr>		  
              <tr>
                <td background="/templates/img/05bgmeni2b.gif">
                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                      <td><img border="0" src="/templates/img/05bgmeni2b.gif" width="1" height="17"></td>
                      <td width="126" style="border-right: 1px solid #003366;"><p class="index-naslovi">LINKS</p></td>
                    </tr>
                  </table>
                </td>
              </tr>
{{ local }}
{{ set_publication identifier="5" }}
{{ unset_issue }}
{{ set_section number="230" }}
{{ list_articles constraints="type is link" }}
              <tr>
                <td class="menu"><a class="indeks" href="http://{{ $campsite->article->url }}" target="_blank">{{ $campsite->article->name }}</a></td>
              </tr>
{{ /list_articles }}
{{ /local }}			  
              
			</table>