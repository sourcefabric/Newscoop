{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">
            
            <div class="title page-title">
            	<h2>{{ #issuesArchive# }}</h2>
            </div>

{{ list_issues order="bynumber desc" constraints="number not 1" }}           
{{ if $gimme->current_list->at_beginning }}    
            <section class="grid-6 extended">
{{ /if }}               
                
                <article>
                	<h3><a href="{{ url options="template issue.tpl" }}">{{ $gimme->issue->name }}</a></h3>
                    <ul class="slider single-slider jcarousel-skin-single">
                    {{ list_sections }}
                    {{ list_articles length="1" }}
                        <li class="article">
                            {{ include file="_tpl/img/img_square.tpl" }}
                            <h4><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a> <em>/ <a href="{{ url options="section" }}">{{ $gimme->section->name }}</a></em></h4>
                            <span class="time">{{ include file="_tpl/relative_date.tpl" date=$gimme->article->publish_date }}{{ if ! $gimme->article->content_accessible }} / <a href="{{ url options="article" }}">premium*</a>{{ /if }}</span>
                        </li>
                    {{ /list_articles }}
                    {{ /list_sections }}                    
                    </ul>
                </article>

{{ if $gimme->current_list->at_end }}            
            </section><!-- / 6 articles grid -->
{{ /if }}        
{{ /list_issues }}    
        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>