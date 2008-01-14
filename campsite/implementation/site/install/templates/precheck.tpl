{{ include file="html_header.tpl" }}
<SCRIPT type="text/javascript" src="/javascript/domTT/domLib.js"></SCRIPT>
<SCRIPT type="text/javascript" src="/javascript/domTT/domTT.js"></SCRIPT>
<SCRIPT type="text/javascript">
<!--
var domTT_styleClass = 'domTTOverlib';
//-->
</SCRIPT>
<form action="index.php" method="post" name="install_form">
<tr>
  <td valign="top">
    <table class="header" cellspacing="0" cellpadding="0">
    <tr>
      <td width="50%">
        <div class="title">Pre-installation Check</div>
      </td>
      <td width="50%">
        <div class="navigate"><input
        class="nav_button" type="button" value="Re-check"
	onclick="submitForm( install_form, 'precheck' );" /> &nbsp;
      {{ if $php_req_ok eq true }}
        <input
        class="nav_button" type="button" value="Next &#155;"
        onclick="submitForm( install_form, 'license' );" />
      {{ else }}
        <input
        class="nav_button_disabled" type="button" value="Next &#155;" />
      {{ /if }}
        </div>
      </td>
    </tr>
    </table>
    <div class="table_spacer"> </div>
    <table class="inside" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="3">
            <div class="subtitle">System Requirements:</div>
          </td>
        </tr>
        <tr>
          <td width="35%" valign="top">
            <div class="help">
              <p><em>Campsite</em> demands some requirements in order to
              be installed and run on top of your system.</p>

              <p>If any of these requirements is not fulfilled (marked red),
              please correct them, otherwise you wont be able to continue
              with the installation.</p>
            </div>
          </td>
          <td width="5%">&nbsp;</td>
          <td width="60%" valign="top">
            <table class="view_list" cellspacing="0" cellpadding="0">
            <tr>
              <td><strong>Requirement</strong></td>
              <td>&nbsp;</td>
              <td><strong>Status</strong></td>
            </tr>
            {{ foreach from=$php_functions item="phpfunc" }}
            <tr>
              <td>{{ $phpfunc.tag }}</td>
              <td>&nbsp;</td>
              <td align="center">
              {{ if $phpfunc.exists eq 'Yes' }}
                <span class="success">
              {{ elseif $phpfunc.exists eq 'No' }}
                <span class="error">
              {{ /if }}
                {{ $phpfunc.exists }}</span>
              </td>
            </tr>
            {{ /foreach }}

            {{ foreach from=$sys_requirements item="sysreq" }}
            <tr>
              <td>{{ $sysreq.tag }}</td>
              <td>&nbsp;</td>
              <td align="center">
              {{ if $sysreq.exists eq 'Yes' }}
                <span class="success">
              {{ elseif $sysreq.exists eq 'No' }}
                <span class="error">
              {{ /if }}
                {{ $sysreq.exists }}</span>
              </td>
            </tr>
            {{ /foreach }}
            </table>
          </td>
        </tr>
        </table>
        <div class="table_spacer"> </div>
        <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="3">
            <div class="subtitle">Recommended PHP Settings:</div>
          </td>
        </tr>
        <tr>
          <td width="35%" valign="top">
            <div class="help">
              These settings are recommended for PHP in order to ensure
              <em>Campsite</em> will work quite well. However,
              <em>Campsite</em> will still operate if your settings do not
              quite match the recommended.
            </div>
          </td>
          <td width="5%">&nbsp;</td>
          <td width="60%" valign="top">
            <table class="view_list" cellspacing="0" cellpadding="0">
            <tr>
              <td><strong>Option</strong></td>
              <td>&nbsp;</td>
              <td><strong>Recommended</strong></td>
              <td>&nbsp;</td>
              <td><strong>Current Set</strong></td>
            </tr>
            {{ foreach from=$php_options item="phpopt" }}
            <tr>
              <td>{{ $phpopt.tag }}</td>
              <td>&nbsp;</td>
              <td align="center">{{ $phpopt.rec_state }}</td>
              <td>&nbsp;</td>
              <td align="center">
              {{ if $phpopt.cur_state eq $phpopt.rec_state }}
                <span class="success">
              {{ else }}
                <span class="error">
              {{ /if }}
                {{ $phpopt.cur_state }}</span>
              </td>
            </tr>
            {{ /foreach }}
            </table>
          </td>
        </tr>
        </table>
      </td>
    </tr>
    </table>
  </td>
  <td valign="top">
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
          <li>{{ $s.title }}
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
