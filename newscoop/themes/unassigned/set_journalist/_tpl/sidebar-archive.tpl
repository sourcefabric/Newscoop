        <h3>{{ if $gimme->language->english_name == "English" }}Archive{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Archivo{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Archiwum{{ /if }}{{ if $gimme->language->english_name == "Russian" }}Архив{{ /if }}</h3>
        <select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;"> 
          <option selected="selected" value="">{{ if $gimme->language->english_name == "English" }}Choose month{{ /if }}{{ if $gimme->language->english_name == "Spanish" }}Elije un mes{{ /if }}{{ if $gimme->language->english_name == "Polish" }}Wybierz miesiąc{{ /if }}{{ if $gimme->language->english_name == "Russian" }}Выбрать месяц{{ /if }}</option>
{{ list_issues order="bynumber desc" }}
          <option value="{{ uri options="template archive-issues.tpl" }}">{{ $gimme->issue->name }} ({{ list_articles length="1" ignore_section="true" }}{{ $gimme->current_articles_list->count }}{{ /list_articles }})</option>
{{ /list_issues }}
        </select>
