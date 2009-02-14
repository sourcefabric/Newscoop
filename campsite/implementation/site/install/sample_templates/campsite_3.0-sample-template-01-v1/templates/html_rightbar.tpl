<table class="rightbox" cellspacing="0" cellpadding="0">
<tr>
  <td>
    <a href=""><img src="/{{ $siteinfo.templates_path }}/img/motto-razor.jpg" border="0" /></a>
  </td>
</tr>
</table>

<table class="rightbar" cellspacing="0" cellpadding="0">
<th>{{* tr *}}All The Issues{{* /tr *}}</th>
<tr>
  <td>
    <ul id="lists">
    {{ list_issues name="issues" }}
      <li><a
        href="{{ uri options="issue" }}">{{ $campsite->issue->name }}</a></li>
    {{ /list_issues }}
    </ul>
  </td>
</tr>
</table>

<table class="rightbar" cellspacing="0" cellpadding="0">
{{ if ! $campsite->user->logged_in }}
<tr>
<th>{{* tr *}}Login{{* tr *}}</th>
  <td>
    <div id="genericform">
    <a href="{{ uri options="template subscription.tpl" }}">
      <span class="formtext">Subscribe</span>
    </a>
    </div>
  </td>
</tr>
<tr>
  <td colspan="2">
    {{ if $campsite->login_action->is_error }}
        <p>There was an error logging in: {{ $campsite->login_action->error_message }}</p>
    {{ /if }}
    <div id="genericform">
    {{ login_form submit_button="Login" button_html_code="class=\"submitbutton\"" }}
      <div align="left">
        <span class="formtext">User ID:</span>
        {{ camp_edit object="user" attribute="uname" }}
        <span class="formtext">Password:</span>
        {{camp_edit object="user" attribute="password" }}
      </div>
    <div align="center">{{ /login_form }}</div>
    </div>
  </td>
</tr>
{{ else }}
<th>Welcome {{ $campsite->user->name }}</th>
<td><a href="?logout=true">Logout</a></td>
{{ /if }}
</table>

<table class="rightbar" cellspacing="0" cellpadding="0">
<tr>
  <td>
    <div align="center">
    <a href=""><img src="/{{ $siteinfo.templates_path }}/img/campsite_logo.png" border="0" /></a>
    <br /><br />
    <a href=""><img src="/{{ $siteinfo.templates_path }}/img/campcaster_logo.png" border="0" /></a>
    <br /><br />
    <a href=""><img src="/{{ $siteinfo.templates_path }}/img/mdlf_logo.png" border="0" /></a>
    </div>
  </td>
</tr>
</table>
