<p class="footer" align="center">|
{{ local }}
{{ set_publication identifier="5" }}
{{ set_current_issue }}
{{ unset_section }}
{{ unset_article }} 
{{ list_sections constraints="number smaller 51" }}
<a class="footer" href="{{ uri options="section reset_article_list" }}">{{ $campsite->section->name }}</a> | 
{{ /list_sections }}
{{ /local }}
</p>