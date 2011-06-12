    <div id="top-nav" class="wrap">
        <div class="fl">
            <ul id="nav">
                <li{{ if $gimme->template->name == "front.tpl" }} class="current_page_item"{{ /if }}><a href="http://{{ $gimme->publication->site }}">Home</a></li>
                    
{{ local }}
{{ set_current_issue }}
{{ list_sections }}                    
                    
                <li class="cat-item{{ if $gimme->section->number == $gimme->default_section->number }} current_page_item{{ /if }}"><a href="{{ uri options="section" }}" title="View all posts filed under {{ $gimme->section->name }}">{{ $gimme->section->name }}</a>

{{ /list_sections }}
{{ /local }}

            </ul>       
        </div>
{{ include file="_tpl/top-search-box.tpl" }}
    </div><!-- /#nav -->