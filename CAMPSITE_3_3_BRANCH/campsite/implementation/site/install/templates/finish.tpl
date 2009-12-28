{{ include file="html_header.tpl" }}

<form action="index.php" method="post" name="install_form">
<tr>
  <td valign="top">
    <table class="header" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <div class="title">You Are Done!</div>
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
            <div class="subtitle">Campsite Successfuly Installed</div>
          </td>
        </tr>
        <tr>
          <td width="40%" valign="top">
            <div class="help">
              Click the "Administrator" icon to start Campsite.
            </div>
          </td>
          <td width="60%" align="center">
            <div class="icon">
              <a href="/admin/index.php">
              <img src="img/admin.png" /><br />
              <span>Administrator</span>
              </a>
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
          {{ if $s.order < 7 }}
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
