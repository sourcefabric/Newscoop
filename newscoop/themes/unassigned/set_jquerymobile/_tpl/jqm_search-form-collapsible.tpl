    <div data-role="collapsible" data-collapsed="true" data-theme="c">
      <h3>Search</h3>
      <div class="ui-body ui-body-c">
      {{ search_form template="jqm_search.tpl" submit_button="Search" button_html_code="data-role=\"button\" data-icon=\"search\" data-iconpos=\"notext\"" }}
        {{ camp_edit object="search" attribute="keywords" html_code="value=\"Search\" id=\"s\"" }}
        <!--button class="replace" type="submit" name="submit"></button-->
      {{ /search_form }}
      </div>
    </div><!-- collapsible -->