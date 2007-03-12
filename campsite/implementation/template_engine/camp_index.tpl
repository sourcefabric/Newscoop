<html>
<head>
  <title>Camp Smarty</title>
</head>
<body>
{{**** User ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">User</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#efefef" nowrap>Identifier:</td>
  <td bgcolor="#efefef">
    {{ $user.identifier }}
  </td>
  <td>{{ literal }}{{ $user.identifier }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#ffffff" nowrap>Name:</td>
  <td bgcolor="#ffffff">
    {{ $user.name }}
  </td>
  <td>{{ literal }}{{ $user.name }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#efefef" nowrap>UserName:</td>
  <td bgcolor="#efefef">
    {{ $user.uname }}
  </td>
  <td>{{ literal }}{{ $user.uname }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#ffffff" nowrap>E-mail:</td>
  <td bgcolor="#ffffff">
    {{ $user.email }}
  </td>
  <td>{{ literal }}{{ $user.email }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#efefef" nowrap>City:</td>
  <td bgcolor="#efefef">
    {{ $user.city }}
  </td>
  <td>{{ literal }}{{ $user.city }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#ffffff" nowrap>Street Address:</td>
  <td bgcolor="#ffffff">
    {{ $user.straddress }}
  </td>
  <td>{{ literal }}{{ $user.straddress }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#efefef" nowrap>State:</td>
  <td bgcolor="#efefef">
    {{ $user.state }}
  </td>
  <td>{{ literal }}{{ $user.state }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#ffffff" nowrap>Country:</td>
  <td bgcolor="#ffffff">
    {{ $user.country }}
  </td>
  <td>{{ literal }}{{ $user.country }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#efefef" nowrap>Phone:</td>
  <td bgcolor="#efefef">
    {{ $user.phone }}
  </td>
  <td>{{ literal }}{{ $user.phone }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#ffffff" nowrap>Employer:</td>
  <td bgcolor="#ffffff">
    {{ $user.employer }}
  </td>
  <td>{{ literal }}{{ $user.employer }}{{ /literal }}</td>
</tr>
</table>

{{**** Publication ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Publication</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#efefef" nowrap>Name:</td>
  <td bgcolor="#efefef">
    {{ $publication.name }}
  </td>
  <td>{{ literal }}{{ $publication.name }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#ffffff" nowrap>Identifier:</td>
  <td bgcolor="#ffffff">
    {{ $publication.identifier }}
  </td>
  <td>{{ literal }}{{ $publication.identifier }}{{ /literal }}</td>
</tr>
</table>

{{**** Issue ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Issue</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#efefef" nowrap>Name:</td>
  <td bgcolor="#efefef">
    {{ $issue.name }}
  </td>
  <td>{{ literal }}{{ $issue.name }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#ffffff" nowrap>Number:</td>
  <td bgcolor="#ffffff">
    {{ $issue.number }}
  </td>
  <td>{{ literal }}{{ $issue.number }}{{ /literal }}</td>
</tr>
</table>

{{**** Section ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Section</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#efefef" nowrap>Name:</td>
  <td bgcolor="#efefef">
    {{ $section.name }}
  </td>
  <td>{{ literal }}{{ $section.name }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#ffffff" nowrap>Number:</td>
  <td bgcolor="#ffffff">
    {{ $section.number }}
  </td>
  <td>{{ literal }}{{ $section.number }}{{ /literal }}</td>
</tr>
</table>

{{**** Article ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Article</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#efefef" nowrap>Title:</td>
  <td bgcolor="#efefef">
    {{ $article.name }}
  </td>
  <td nowrap>{{ literal }}{{ $article.title }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#ffffff" nowrap>Intro:</td>
  <td bgcolor="#ffffff">
    {{ $article.intro }}
  </td>
  <td nowrap>{{ literal }}{{ $article.intro }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#efefef" nowrap>Body:</td>
  <td bgcolor="#efefef">
    {{ $article.full_text }}
  </td>
  <td nowrap>{{ literal }}{{ $article.body }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#ffffff" nowrap>Link - URL:</td>
  <td bgcolor="#ffffff">
    {{ $article.link.url }}
  </td>
  <td nowrap>{{ literal }}{{ $article.link.url }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#efefef" nowrap>Service - Full Text:</td>
  <td bgcolor="#efefef">
    {{ $article.service.full_text }}
  </td>
  <td nowrap>{{ literal }}{{ $article.service.full_text }}{{ /literal }}</td>
</tr>
</table>

{{**** Attachment ****}}
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#6a6a6a"><font color="#ffffff">Article Attachment</font></td>
</tr>
</table>
<table cellspacing="1" cellpadding="4">
<tr>
  <td bgcolor="#efefef" nowrap>File Name:</td>
  <td bgcolor="#efefef">
    {{ $article.attachment.filename }}
  </td>
  <td nowrap>{{ literal }}{{ $article.attachment.filename }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#ffffff" nowrap>Mime Type:</td>
  <td bgcolor="#ffffff">
    {{ $article.attachment.mimetype }}
  </td>
  <td nowrap>{{ literal }}{{ $article.attachment.mimetype }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#efefef" nowrap>Extension:</td>
  <td bgcolor="#efefef">
    {{ $article.attachment.extension }}
  </td>
  <td nowrap>{{ literal }}{{ $article.attachment.extension }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#ffffff" nowrap>Size In Bytes:</td>
  <td bgcolor="#ffffff">
    {{ $article.attachment.sizeb }}
  </td>
  <td nowrap>{{ literal }}{{ $article.attachment.sizeb }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#ffffff" nowrap>Size In Kb:</td>
  <td bgcolor="#ffffff">
    {{ $article.attachment.sizekb }}
  </td>
  <td nowrap>{{ literal }}{{ $article.attachment.sizekb }}{{ /literal }}</td>
</tr>
<tr>
  <td bgcolor="#ffffff" nowrap>Size In Mb:</td>
  <td bgcolor="#ffffff">
    {{ $article.attachment.sizemb }}
  </td>
  <td nowrap>{{ literal }}{{ $article.attachment.sizemb }}{{ /literal }}</td>
</tr>
</table>


</body>
</html>
