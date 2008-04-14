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
<th>{{* tr *}}Login{{* tr *}}</th>
<tr>
  <td>
    <div id="loginform">
    {{ login_form submit_button="Login" html_code="class=\"loginbutton\"" }}
      <span class="formtext">User ID:</span><br />
      <div align="center">
      {{ camp_edit object="user" attribute="uname" }}
      </div>
      <span class="formtext">Password:</span><br />
      <div align="center">
      {{camp_edit object="user" attribute="password" }}
      </div>
    {{ /login_form }}
    </div>
  </td>
</tr>
<tr>
  <td><a href="{{ uri options="template subscription.tpl" }}">Subscribe</a></td>
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
