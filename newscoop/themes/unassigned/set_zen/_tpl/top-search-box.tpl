{{ search_form template="search.tpl" submit_button="Search" html_code="id=\"topSearch\"" button_html_code="class=\"searchbutton replace\"" }} 
  <p class="fields">
    {{ camp_edit object="search" attribute="keywords" html_code="id=\"searchtext\" placeholder=\"search\"" }}
  </p>
{{ /search_form }}