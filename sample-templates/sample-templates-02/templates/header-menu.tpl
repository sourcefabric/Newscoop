<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#D4D4D6" style="border: 1px solid #06264F; border-bottom: 2px solid #06264F">
			  <tr>
                <td>
                  <table cellpadding="0" cellspacing="0" border="0">
					<tr>
{{ set_current_issue }}
{{ list_sections constraints="number smaller 199" }}
					  <td><p class="main-index"><a class="main-index" href="{{ uri }}">{{ $campsite->section->name }}</a></p></td>
					  <td valign="middle"><img src="/templates/img/03.gif"></td>
{{ /list_sections }}
					  
					</tr>
	              </table>
	            </td>
                <td align="right"><div style="float:right;margin:2px 2px 2px 5px;"><a href="http://{{ $campsite->publication->site }}{{ uripath options="template rss.tpl" }}?{{ urlparameters options="template rss.tpl" }}"><img src="/templates/img/rss.gif" border="0"></a></div><div style="margin:3px 2px 2px 5px;"><p class="main-index">Belgrade, {{ $smarty.now|camp_date_format:"%d %M %Y" }}</p></div>
</td>
			  </tr>
            </table>