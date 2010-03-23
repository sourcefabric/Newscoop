<div id="search">
{{ search_form template="search.tpl" submit_button="Search" }}
		    <span class="search">{{ camp_edit object="search" attribute="keywords" }}</span> <br>{{ camp_select object="search" attribute="mode" }} match all keywords
		{{ /search_form }}
</div>
