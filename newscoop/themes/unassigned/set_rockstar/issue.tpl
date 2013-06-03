{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content">

            <div class="title page-title">
            	<h2>{{ $gimme->issue->name }}</h2>
            </div>
            
            <section class="grid-6">
{{ list_sections }}  
{{ list_articles }}
{{ if $gimme->current_list->at_beginning }}          
            	<article>
                	<h3>{{ $gimme->section->name }}</h3>
{{ /if }} 
               	
                    <div class="article">
                		{{ if $gimme->current_list->at_beginning }}{{ include file="_tpl/img/img_square.tpl" }}{{ /if }}
                		<h4><a href="{{ url options="article" }}">{{ $gimme->article->name }}</a> <em>/ <a href="{{ url options="section" }}">{{ $gimme->section->name }}</a></em></h4>
                            <span class="time">{{ include file="_tpl/relative_date.tpl" date=$gimme->article->publish_date }}{{ if ! $gimme->article->content_accessible }} / <a href="{{ url options="article" }}">{{ #premium# }}</a>{{ /if }}</span>
                    </div>
                    
{{ if $gimme->current_list->at_end }}                    
                </article>
{{ /if }}                
{{ /list_articles }}                
{{ /list_sections }}                
            
            </section><!-- / 6 articles grid -->

        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>            