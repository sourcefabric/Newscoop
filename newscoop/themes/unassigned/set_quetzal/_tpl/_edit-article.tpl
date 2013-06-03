{{ if $gimme->user->is_admin }}
<a style="text-decoration: none; border: 1px solid #ff7e00; position:absolute; color: #000; padding: 0px 2px 3px 2px; background:#ff7e00; font-family:sans; font-size:9px;"
href="http://{{ $gimme->publication->site }}/admin/articles/edit.php?f_publication_id={{ $gimme->publication->identifier }}&f_issue_number={{ $gimme->issue->number }}&f_section_number={{ $gimme->section->number }}&f_article_number={{ $gimme->article->number }}&f_language_id={{ $gimme->language->number }}&f_language_selected={{ $gimme->language->number }}" target="_blank" 
style="" title="Edit article">
<i class="icon-pencil"></i>
</a>
{{ /if }}