<h2 style="margin: 0 0 16px 16px">[{{ $gimme->section->name }}]</h2>

{{ list_articles ignore_issue="true" ignore_section="true" order="bynumber desc" constraints="section is 30" }}
    <a title="{{ $gimme->article->name }} ({{ $gimme->article->publish_date|camp_date_format:"%e %M %Y" }})" href="{{ uri options="article" }}">
    {{ list_article_images length="1" }}
    <div class="photoblog-item" style="background: transparent url({{uri options="image"}}&ImageRatio=30) no-repeat center center">
    </div>                            
    {{ /list_article_images }}
    </a>
{{ /list_articles }}