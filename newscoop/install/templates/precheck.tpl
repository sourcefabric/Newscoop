{{ include file="html_header.tpl" }}
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
          <th colspan="3" align="left" class="innerHead">
            <div class="subtitle">System Requirements:</div>
          </th>
        </tr>
        <tr>
          <td width="35%" valign="top">
            <div class="help">
              <p><em>Newscoop</em> needs the following requirements to be
              fulfilled in order to install and run on your system.</p>

              <p>If any of these requirements is not fulfilled (marked red),
              please correct them, otherwise you wont be able to continue
              with the installation.</p>

              <p>Exception are PHP CLI and APC.
              PHP CLI (Command Line Interface) enables running utility
              tools such as site backup and restore.
              We highly recommend to enable PHP APC
              caching system so that your site will perform much better.
              However, this is not mandatory and you still will be able
              to continue with the installation process.</p>
            </div>
          </td>
          <td width="5%">&nbsp;</td>
          <td width="60%" valign="top">
            <table class="view_list" cellspacing="0" cellpadding="0">
            {{ if $php_req_ok eq true }}
            <tr><td style="text-align:left;"><h2 style="color:lightgreen;">All system requirements are met.</h2></td></tr>
            {{ else }}
            <tr>
              <td class="first"><strong>Requirement</strong></td>
              <td>&nbsp;</td>
              <td><strong>Status</strong></td>
            </tr>
            {{ /if }}
            {{ foreach from=$php_functions item="phpfunc" }}
            <tr>
            {{ if $phpfunc.exists neq 'Yes' }}
              <td class="first">{{ $phpfunc.tag }}</td>
              <td>&nbsp;</td>
              <td align="center">
              {{ if $phpfunc.exists eq 'No' }}
                <span class="error">
              {{ else }}
                <span class="other">
              {{ /if }}
                {{ $phpfunc.exists }}</span>
              </td>
            {{ /if }}
            </tr>
            {{ /foreach }}

            {{ foreach from=$sys_requirements item="sysreq" }}
            <tr>
              {{ if $sysreq.exists eq 'No' }}
              <td class="first">{{ $sysreq.tag }}</td>
              <td>&nbsp;</td>
              <td align="center">
                <span class="error">
                    {{ $sysreq.exists }}
                </span>
                <small>
                  <br>
                  You will need to grant permissions to folder
                  <br>
                  <i>{{ $sysreq.path }}</i>
               </small>
              </td>
            {{ /if }}
            </tr>
            {{ /foreach }}
            
            {{ foreach from=$library_requirements item="libreq" }}
            <tr>
            {{ if $libreq.exists eq 'No' }}
              <td class="first">{{ $libreq.tag }}</td>
              <td>&nbsp;</td>
              <td align="center">
                <span class="error">No</span>
              </td>
            {{ /if }}
            </tr>
            {{ /foreach }}

            <tr>
              <td class="first"><strong>Recommended</strong></td>
              <td>&nbsp;</td>
              <td><strong>Status</strong></td>
            </tr>
            {{ foreach from=$php_recommended item="phpopt" }}
            <tr>
              <td class="first">{{ $phpopt.tag }}</td>
              <td>&nbsp;</td>
              <td align="center">
              {{ if $phpopt.exists eq 'Yes' }}
                <span class="success">
              {{ elseif $phpopt.exists eq 'No' }}
                <span class="warning">
              {{ else }}
                <span class="other">
              {{ /if }}
                {{ $phpopt.exists }}</span>
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
            <div class="subtitle"><h3>PHP Settings:</h3></div>
          </td>
        </tr>
        <tr>
          <td width="35%" valign="top">
            <div class="help">
              <p>These settings are recommended for PHP in order to ensure
              <em>Newscoop</em> will work well. <em>Newscoop</em> will still
              operate even though these settings are not set as suggested.</p>

              <p><span class="error">WARNING</span>: Always make sure that
              <span class="error"><em>register_globals</em> is OFF</span>,
              because it is a big security hole.</p>
            </div>
          </td>
          <td width="5%">&nbsp;</td>
          <td width="60%" valign="top">
            <table class="view_list" cellspacing="0" cellpadding="0">
            <tr>
              <td class="first"><strong>Option</strong></td>
              <td>&nbsp;</td>
              <td><strong>Recommended</strong></td>
              <td>&nbsp;</td>
              <td><strong>Current Set</strong></td>
            </tr>
            {{ foreach from=$php_settings item="phpset" }}
            <tr>
              <td class="first">{{ $phpset.tag }}</td>
              <td>&nbsp;</td>
              <td align="center">{{ $phpset.rec_state }}</td>
              <td>&nbsp;</td>
              <td align="center">
              {{ if $phpset.cur_state eq $phpset.rec_state }}
                <span class="success">
              {{ else }}
                <span class="warning">
              {{ /if }}
                {{ $phpset.cur_state }}</span>
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
