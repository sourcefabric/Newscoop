

<!-- Footer -->
<footer id="footer">
  <div class="container">
      <ul id="footer_menu_first" class="menu hidden-phone">
        {{local}}
        {{ set_current_issue }}
            {{ list_sections }}
            <li{{ if ($gimme->section->number == $gimme->default_section->number) }} class="current"{{ /if }}><a href="{{ url options="section" }}" title="{{'viewAllPosts'|translate}} {{ $gimme->section->name }}">{{ $gimme->section->name }}</a></li>
            {{ /list_sections }}





        </ul>



        <ul id="footer_menu_second" class="menu">

                 {{ list_articles ignore_issue="true" ignore_section="true" order="bySectionOrder asc" constraints="issue is 1 section is 5 type is page"}}
            <li><a href="{{ uri options="article" }}" title="{{ $gimme->article->name }}">{{ $gimme->article->name }}</a></li>
            {{ /list_articles }}
            <li><a href="{{ uri options="template archive.tpl" }}">{{'archives'|translate}}</a></li>

            {{/local}}
        </ul>


    </div>
</footer>
<!-- End Footer -->