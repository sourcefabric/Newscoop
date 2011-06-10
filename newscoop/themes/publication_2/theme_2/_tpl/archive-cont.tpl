<div id="accordion">
{{ list_issues constraints="number not 1" order="bypublishdate desc" }}
                    <h3 style="margin-bottom: 20px"><a href="#">{{ $gimme->issue->name }}</a></h3>
<div>
{{ list_sections }}
{{ list_articles }}
{{ if $gimme->current_articles_list->at_beginning }}

                    <h4><a href="{{ uri options="section" }}">{{ $gimme->section->name }}</a></h4>
                    <ul>

{{ /if }}                    

                        <li><a href="{{ uri options="article" }}">{{ $gimme->article->name }}</a> - {{ $gimme->article->publish_date|camp_date_format:"%e %M %Y" }} - {{ $gimme->article->comment_count }} comment(s)</li>

{{ if $gimme->current_articles_list->at_end }}

                    </ul>                           
{{ /if }} 
{{ /list_articles }}
{{ /list_sections }}                   

</div>
             {{ /list_issues }}                   
</div><!-- /#accordion -->                