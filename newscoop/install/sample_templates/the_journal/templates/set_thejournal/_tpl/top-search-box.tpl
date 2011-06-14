<div class="search">            
{{ search_form template="search.tpl" submit_button="&nbsp;" html_code="id=\"topSearch\"" button_html_code="class=\"replace\"" }} 
           <p class="fields">
               {{ camp_edit object="search" attribute="keywords" html_code="id=\"s\" onfocus=\"if (this.value == 'Search') {this.value = '';}\" onblur=\"if (this.value == '') {this.value = 'Search';}\"" }}
                <!--button class="replace" type="submit" name="submit"></button-->
           </p>
{{ /search_form }}
</div><!-- /.search -->