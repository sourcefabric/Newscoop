<div id="genericform">
<table class="userform">
<tr>
  <td>
    {{ search_form template="search.tpl" submit_button="Search" }}
      <p>
        <label for="keywords">Keywords</label>:
        {{ camp_edit object="search" attribute="keywords" html_code="id=\"keywords\"" }}
        {{ camp_select object="search" attribute="mode" html_code="id=\"match_all\"" }}
        <label for="match_all">match all words</label>
      </p>
      <p>Search in the
        <input type="radio" name="f_search_scope" value="content" id="content" checked>
        <label for="content"><strong>whole content</strong></label>,
        <input type="radio" name="f_search_scope" value="title" id="title">
        <label for="title"><strong>title</strong></label> or
        <input type="radio" name="f_search_scope" value="author" id="author">
        <label for="author"><strong>author</strong></label>
      </p>
      <p>Issue: {{ camp_select object="search" attribute="issue" }}</p>
      <p>Section: {{ camp_select object="search" attribute="section" }}</p>
      <p>Start date: {{ camp_edit object="search" attribute="start_date" }}</p>
      <p>End date: {{ camp_edit object="search" attribute="end_date" }}</p>
      <p>Topic:
        <select name="f_search_topic">
          <option value="">&nbsp;</option>
          {{ unset_topic }}
          {{ list_subtopics }}
            <option value="{{ $campsite->topic->identifier }}>">
              {{ $campsite->topic->name }}
            </option>
          {{ /list_subtopics }}
        </select>
      </p>
    {{ /search_form }}
  </td>
</tr>
</table>
</div>
