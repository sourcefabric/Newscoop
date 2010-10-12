{{ include file="html_header.tpl" }}
<script type="text/javascript" src="include/js/fValidate/fValidate.config.js"></script>
<script type="text/javascript" src="include/js/fValidate/fValidate.core.js"></script>
<script type="text/javascript" src="include/js/fValidate/fValidate.lang-enUS.js"></script>
<script type="text/javascript" src="include/js/fValidate/fValidate.validators.js"></script>
<form action="index.php" method="post" name="install_form" autocomplete="off">
<tr>
  <td valign="top">
    <table class="header" cellspacing="0" cellpadding="0">
    <tr>
      <td width="50%">
        <div class="title">Database Settings</div>
      </td>
      <td width="50%" nowrap>
        <div class="navigate"><input
        class="nav_button" type="button" value="&#139; Previous"
        onclick="submitForm( install_form, 'license' );" /> &nbsp;
        <input
        class="nav_button" type="button" value="Next &#155;"
        onclick="if (validateForm(install_form, 0, 1, 0, 1, 8) == true) {
                 submitForm(install_form, 'mainconfig'); }"/>
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
            <div class="subtitle">Connection Parameters:</div>
          </th>
        </tr>
        <tr>
          <td width="35%" valign="top">
            <div class="help">
              <p>To connect to your MySQL database you need to provide the
              hostname of the server, the MySQL user name to connect with
              and its corresponding password, and the <em>Campsite</em>
              database name.</p>

              <p><em>Server Port</em> is optional, if you leave it blank
              <em>Campsite</em> will assume the default MySQL port (3306)
              will be used.</p>

              <p>We STRONGLY recommend to create a dedicated MySQL username
              with password instead of use the default MySQL user (root).</p>
            </div>
          </td>
          <td width="5%">&nbsp;</td>
          <td width="60%" valign="top">
            <div class="message">{{ $message }}</div>
            <div class="form_field">
              <label for="db_hostname">Server Name/Address</label>:<br />
              <input class="inputbox" type="text" size="42" maxlength="40"
              id="db_hostname" name="db_hostname" value="{{ $db.hostname }}"
              alt="blank"
              emsg="You must complete the 'Server Name/Address' field" /><br />
            </div>
            <div class="form_field">
              <label for="db_hostport">Server Port</label>: (<em>Optional</em>)<br />
              <input class="inputbox" type="text" size="42" maxlength="40"
              id="db_hostport" name="db_hostport" value="{{ $db.hostport }}" /><br />
            </div>
            <div class="form_field">
              <label for="db_username">User Name</label>:<br />
              <input class="inputbox" type="text" size="42" maxlength="40"
              id="db_username" name="db_username" value="{{ $db.username }}"
              alt="blank"
              emsg="You must complete the 'Username' field" /><br />
            </div>
            <div class="form_field">
              <label for="db_userpass">User Password</label>:<br />
              <input class="inputbox" type="password" size="42" maxlength="40"
              id="db_userpass" name="db_userpass" value="{{ $db.userpass }}" /><br />
            </div>
            <div class="form_field">
              <label for="db_database">Database Name</label>:<br />
              <input class="inputbox" type="text" size="42" maxlength="40"
              id="db_database" name="db_database" value="{{ $db.database }}"
              alt="blank"
              emsg="You must complete the 'Database' field" /><br />
            </div>
	    {{ if $overwrite_db }}
	    <div class="form_field">
	      <label for="db_overwrite">Overwrite Database</label>: &nbsp;
	      Yes <input class="inputbox" type="radio"
	      id="db_overwrite" name="db_overwrite" value="1" /> &nbsp;
	      No <input class="inputbox" type="radio"
	      id="db_overwrite" name="db_overwrite" value="0" checked /><br />
	    </div>
	    {{ /if }}
          </td>
        </tr>
        </table>
      </td>
    </tr>
    </table>
  </td>
  <td width="200" valign="top" nowrap>
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
          {{ if $s.order < 3 }}
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
