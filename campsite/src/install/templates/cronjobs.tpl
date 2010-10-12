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
        <div class="title">Automated Tasks</div>
      </td>
      <td width="30%" nowrap>
        <div class="navigate"><input
        class="nav_button" type="button" value="&#139; Previous"
        onclick="submitForm( install_form, 'loaddemo' );" /> &nbsp;
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
          <th colspan="3"  align="left" class="innerHead">
            <div class="subtitle">What is this?</div>
          </th>
        </tr>
        <tr>
          <td width="35%" valign="top">
            <div class="help">
              <p><em>Campsite</em> needs some tasks to be run automatically
              in order to keep specific stuff up to date. At this step we
              will install them for you.</p>

              <p>This tasks will run as cron jobs on your system and so you
              will be able to edit and customize them depending on your
              own requirements.</p>

              <p>Individual cron job files will be saved to
              <em>install/cron_jobs/</em> directory. There will be an
              <em>all_at_once</em> file in the same directory which includes
              all the cron jobs.

              <p>Campsite has tools that perform the following automated tasks:</p>
            </div>
          </td>
          <td width="5%">&nbsp;</td>
          <td width="60%" valign="top">
            <div class="message">{{ $message }}</div>
            <div class="form_field">
            <p><strong>Autopublish:</strong> Modifies the status of issues and articles scheduled for certain actions.</p>

            <p><strong>Events Notifier:</strong> Sends emails to administrative users containing the latest events that took place in Campsite.</p>

            <p><strong>Indexer:</strong> Indexes the article content (update the search engine database).</p>

            <p><strong>Statistics:</strong> Updates Web site statistics.</p>

            <p><strong>Subscriptions Notifier:</strong> Sends emails to subscribers alerting them when their subscription ends.</p><br />

            <p>You can read more on this in the <a href="http://code.campware.org/manuals/campsite/3.0/index.php?id=198" target="_blank">Campsite manual</a>.</p>
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
          {{ if $s.order < 6 }}
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
