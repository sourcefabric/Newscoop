{{ strip }}
<table class="article" cellspacing="0" cellpadding="0">
<tr>
  <td>
    <p>{{ $campsite->article->publish_date }}</p>
    <p>{{ $campsite->article->name }}</p>
    <p>{{ $campsite->article->intro }}</p>
    <p>{{ $campsite->article->full_text }}</p>
  </td>
</tr>
{{ if $article->comments_enabled }}
<tr>
  <td>
    {{** list_articlecomments **}}

    {{** /list_articlecomments **}}
  </td>
</tr>
<tr>
  <td>
    {{ comment_form submit_name="send" }}
    <table class="commentform" cellspacing="0" cellpadding="0">
    <tr>
      <td>{{ tr }}User Name{{ /tr }}:</td>
      <td>{{ camp_edit object="user" attribute="uname" }}</td>
    </tr>
    <tr>
      <td>{{ tr }}E-mail{{ /tr }}:</td>
      <td>{{ camp_edit object="user" attribute="email" }}</td>
    </tr>
    <tr>
      <td>{{ tr }}Subject{{ /tr }}:</td>
      <td>{{ camp_edit object="comment" attribute="subject" }}</td>
    </tr>
    <tr>
      <td>{{ tr }}Comment{{ /tr }}:</td>
      <td>{{ camp_edit object="comment" attribute="comment" }}</td>
    </tr>
    </table>
  {{ /comment_form }}
  </td>
</tr>
{{ /if }}
</table>
{{ /strip }}