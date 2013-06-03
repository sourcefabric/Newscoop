<div class="span8 section-articles">

{{ list_issues order="bynumber desc" constraints="number greater 1" }} 

    <article class="section-article issue-entry">
        <figure class="pull-left article-image">
            <img src="{{ url static_file="_img/archive-icon.png"}}" alt="{{ $gimme->issue->name}}">
        </figure>
        <header>
            <h2><a href="{{ uri options="template issue.tpl" }}">{{ $gimme->issue->name}}</a></h2>
            <span class="article-date">{{ #publishedOn# }} <time datetime="{{ $gimme->issue->publish_date|date_format:"%Y-%m-%dT%H:%MZ" }}">{{ $gimme->issue->publish_date|camp_date_format:"%d %M %Y" }}</time></span>
        </header>
        <div class="clearfix"></div>
    </article>
    <hr>

{{ /list_issues }}    

</div>
