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
      <td width="70%">
        <div class="title">Sample Site</div>
      </td>
      <td width="30%" nowrap>
        <div class="navigate"><input
        class="nav_button" type="button" value="&#139; Previous"
        onclick="submitForm( install_form, 'mainconfig' );" /> &nbsp;
        <input
        class="nav_button" type="button" value="Next &#155;"
        onclick="if (validateForm(install_form, 0, 1, 0, 1, 8) == true) {
                 submitForm(install_form, 'finish'); }" />
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
            <div class="subtitle">Load Sample Data:</div>
          </td>
        </tr>
        <tr>
          <td width="35%" valign="top">
            <div class="help">
              <p>Is this the first time you install <em>Campsite</em>?
              are not you familiar with the template engine system?
              do you want to see <em>Campsite</em> in action before start
              writing template files for your own Web site? Then, choose
              "Yes" to install the sample site and you will can see a
              simple and functional Web site to get familiar with the
              system.</p>

              <p>Otherwise, simply check "No" and click on "Next" button to
              finish the installation.</p>
            </div>
          </td>
          <td width="5%">&nbsp;</td>
          <td width="60%" valign="top">
            <div class="message">{{ $message }}</div>
            <div class="form_field">
              <label for="db_hostname">Install Sample Site?</label> &nbsp;
              <input
                type="radio" id="install_demo" name="install_demo" value="1"
                {{ if $dm.loaddemo eq true }} checked {{ /if }} /> &nbsp; Yes &nbsp;
              <input
                type="radio" id="install_demo" name="install_demo" value="0"
                {{ if !$dm.loaddemo }} checked {{ /if }}/> &nbsp; No
            </div>
            <div class="demo_img">
              <img src="img/campsite_demo.png" />
            </div>
          </td>
        </tr>
        </table>
      </td>
    </tr>
    </table>
  </td>
  <td valign="top" nowrap>
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
          {{ if $s.order < 5 }}
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
<input type="hidden" name="this_step" value="loaddemo" />
<input type="hidden" name="step" value="" />
</form>
</table>

{{ include file="html_footer.tpl" }}
