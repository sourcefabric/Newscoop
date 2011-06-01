{{ include file="html_header.tpl" }}

<form action="index.php" method="post" name="install_form">
<tr>
  <td valign="top">
    <table class="header" cellspacing="0" cellpadding="0">
    <tr>
      <td width="50%">
        <div class="title">License Agreement</div>        
      </td>
      <td width="50%">
        <div class="navigate"><input
        class="nav_button" type="button" value="&#139; Previous"
        onclick="submitForm( install_form, 'precheck' );" /> &nbsp;
        <input
        class="nav_button" type="button" value="Next &#155;"
        onclick="submitForm( install_form, 'database' );" id="nav_button_next"/></div>
      </td>
    </tr>
    </table>
    <div class="table_spacer"> </div>
    <table class="inside" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="2">
            <div class="subtitle">
            	<h2>GNU/GPL Free Software License:</h2>
            	<div class="message" id="license_error_message"><p>{{ $message }}</p></div>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="help">
              <iframe src="include/gpl.txt" class="license" frameborder="0" marginwidth="25px" scrolling="auto"></iframe>
            </div>
          </td>
        </tr>
        <tr>
        	<td>
        		<div class="subtitle">
        			<div class="form_field">
	        			<input class="inputbox" type="checkbox" name="license_agreement" value="1" id="license_agreement"/>
	        			<label for="license_agreement">I accept the terms of the License Agreement</label>
	        		</div>
        		</div>
        	</td>
        </tr>
        </table>
      </td>
    </tr>
    </table>
  </td>
  <td width="200" valign="top">
    <table class="right_header" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <div class="title">Progress...</div>
      </td>
    </tr>
    </table>
    <div class="table_spacer"> </div>
    <table class="right" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <ul id="steps_list">
        {{ foreach from=$step_titles key="step" item="s" }}
          {{ if $s.order < 2 }}
            <li class="stepdone">{{ $s.title }}</span>
          {{ else }}
            <li>{{ $s.title }}
          {{ /if }}
          {{ if $s.title eq $current_step_title }}
            &nbsp; <img src="img/checked.png" />
          {{ /if }}
          </li>
        {{ /foreach }}
        </ul>
      </td>
    </tr>
    </table>
    <div class="table_spacer"> </div>
    <div align="center">
      <img src="img/installation-progress.png" />
    </div>
  </td>
</tr>
<input type="hidden" name="step" value="" />
</form>
</table>

{{ include file="html_footer.tpl" }}
