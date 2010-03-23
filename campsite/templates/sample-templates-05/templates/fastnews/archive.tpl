{{ include file="fastnews/htmlheader.tpl" }}

<!-- This is the ARCHIVE template -->
{{ include file="fastnews/utility-header.tpl" }}

<div class=rightfloat>
{{ include file="fastnews/userinfo.tpl" }}
</div>

<h1>Archive</h1>

<p>Access to some archived content might be restricted to subscribers.</p>

{{ list_issues order="bynumber desc" }}
<p><a href="{{ uri options="issue" }}">#{{ $campsite->issue->number }}&nbsp;&ndash; {{ $campsite->issue->name }}</a></p>
{{ /list_issues }}

</td>

{{ include file="fastnews/footer.tpl" }}
</body>
</html>
