<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ECECEC">
        <tr>
          <td align="left" height="14" valign="middle">
	        <table cellpadding="0" cellspacing="0" border="0">
	          <tr>
		        <td><img src="/templates/img/strelica1.gif" height="14" width="14" border="0"></td>
     	{{ local }}
        {{ set_publication name="dynamic" }}
        {{ unset_issue }}
        {{ unset_section }}
        {{ unset_article }}
        {{ list_sections constraints="number greater 100" }}
        {{ if $campsite->section->number == 230 }}
        {{ else }}
		        <td><img src="/templates/img/transpa.gif"><img src="/templates/img/vert-siv-lin.gif" border="0"><span class="iznad-headera"><a class="crno-podvuceno" href="{{ uri options="reset_article_list" }}">{{ $campsite->section->name }}</a></span></td>
        {{ /if }}
		{{ /list_sections }}
		{{ /local }}
		      </tr>
	        </table>
	      </td>
	      <td align="right" height="14" valign="middle"><img src="/templates/img/vert-siv-lin.gif" border="0"><span class="iznad-headera" style="font-weight: bold; margin-left: 4px"><a class="crno-podvuceno" href="/">Home</a></span><img src="/templates/img/transpa.gif"></td>
        </tr>
      </table>
	  <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#CC0000">
	    <tr>
		  <td width="163" align="center"><a href="/"><img src="/templates/img/logo.gif" border="0"></a></td>
		  <td width="468" align="center"><img src="/templates/img/banner-header.gif" border="0"></td>
		  <td width="1"><img src="/templates/img/search-box-left.gif"></td>
		  <td valign="middle" style="padding-left: 10px">
		    <!-- search box  -->
<div id="search">

			{{ search_form template="search.tpl" submit_button="Search" }}
<span class="text">Search:</span> <span class="search">{{ camp_edit object="search" attribute="keywords" }}</span>
		{{ /search_form }}</div>

			<!-- end search box -->
		  </td>
		</tr>
	  </table>
	  <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#C2C2C2">
	    <tr>
		  <td width="118"><img src="/templates/img/rubrike.gif" border="0"></td>
		  <td>
		    <table cellpadding="0" cellspacing="0" border="0">
			  <tr>
        {{ local }}
        {{ set_publication name="dynamic" }}
        {{ set_issue current }}
        {{ unset_section }}
        {{ unset_article }}
        {{ list_sections constraints="number smaller 200" }}
			    <td><span class="hor-bar"><a class="hor-bar" href="{{ uri options="reset_article_list" }}">{{ $campsite->section->name }}</a></span></td>
				<td width="1"><img src="/templates/img/vert-siv-horbar.gif"></td>
		{{ /list_sections }}
		{{ /local }}
<td style="padding-left: 225px; margin: 5px 0 0 0;" valign="middle">
<a href="http://{{ $campsite->publication->site }}{{ uripath options="template rss.tpl" }}?{{ urlparameters options="template rss.tpl" }}"><img src="/templates/img/rss.gif" border="0"></a>
</td>

			  </tr>
			</table>
		  </td>
		</tr>
	  </table>
