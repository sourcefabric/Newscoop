  <ul data-role="listview" data-inset="true">
    <li data-role="list-divider" data-role="collapsible">{{ $gimme->publication->name }}</li>
{{ local }}
{{ set_current_issue }}
{{ list_sections }}
    <li><a href="http://{{ $gimme->publication->site }}{{ uri options="section" }}">{{ $gimme->section->name }}</a></li>
{{ /list_sections }}
{{ /local }}
  </ul>