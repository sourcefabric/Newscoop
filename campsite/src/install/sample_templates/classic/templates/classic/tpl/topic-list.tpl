<!-- topic-list.tpl article topics start -->
{{ list_article_topics }}
{{ if $campsite->current_list->at_beginning }}
<div class="relatedtopics">
<div class="relatedtopicsinner">
Related topics 
{{ /if }}
: <a href="{{ uri options="template classic/topic.tpl" }}" class="topic">{{$campsite->topic->name }}</a>
{{ if $campsite->current_list->at_end }}
</div><!-- class="relatedtopicsinner"-->
</div><!-- class="relatedtopics" -->
{{ /if }}
{{ /list_article_topics }}
<!-- topic-list.tpl article topics end -->
