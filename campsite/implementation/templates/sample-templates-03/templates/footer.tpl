<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	    <tr>
		  <td width="300" height="35" align="right" bgcolor="#C2C2C2">
              {{ local }}
                          {{ set_publication name="dynamic" }}
                          {{ set_issue current }}
                          {{ unset_section }}
                          {{ unset_article }}

            <form class="form" name="sadrzaj" style="margin:0;padding:0;">
              <select style="margin: 0px 0px 0px 0px" name="sadrzaj" onChange="MM_jumpMenu('parent',this,1)">
                <option selected>Quick menu</option>
{{ list_sections constraints="number smaller 99" }}
                <option value="{{ uri options="reset_article_list" }}">{{ $campsite->section->name }}</option>
{{ /list_sections }}
                <option value="{{ uri options="issue" }}">Home</option>
              </select>
            </form>

                           {{ /local }}
          </td>
		  <td width="25" bgcolor="#C2C2C2"></td>
		  <td height="35" align="left" valign="middle" bgcolor="#C2C2C2"><p class="footer">Copyright © 2004 CAMPSITE 
{{ local }}
{{ unset_issue }}
{{ list_sections constraints="number is 210" }}
<a class="crno-podvuceno" href="{{ uripath }}">{{ $campsite->section->name }}</a>
{{ /list_sections }}
{{ /local }}
</p></td>
		</tr>
		<tr>
		  <td colspan="3" height="5" bgcolor="#CC0000"></td>
		</tr>
	  </table>