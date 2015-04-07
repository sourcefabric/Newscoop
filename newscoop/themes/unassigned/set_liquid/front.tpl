{{ include file="_tpl/_html-head.tpl" }}


    {{ include file="_tpl/header.tpl" }}


<div id="page" class="container">

  <!-- Content -->
  <section id="content">

    <!-- Row (12) -->
    <div class="row">

      <!-- Span (8) -->
      <div class="span8">


        {{list_playlist_articles id="1" length="3"}}


        <!-- News item  -->
        {{if $gimme->current_list->index==1}}

        <article class="news_item">
            {{ image rendition="article" }}
            <a href="{{url options="article"}}" class="thumbnail">

              <img src="{{ $image->src }}"  alt="{{ $image->caption }} (photo: {{ $image->photographer }})" alt="" />
          </a>
          {{/image}}
                  <div class="content content_text">
                      <h6 class="info">{{list_article_authors}}
          {{if $gimme->current_list->index!=1}},&nbsp;{{/if}}

            {{ if $gimme->author->biography->first_name }}
            {{ $gimme->author->biography->first_name }} {{
            $gimme->author->biography->last_name }}
            {{ else }}
            {{ $gimme->author->name }}
            {{ /if }}
          {{if $gimme->current_list->at_end}}
          &nbsp;-&nbsp;{{/if}}{{/list_article_authors}}{{ $gimme->article->publish_date|camp_date_format:"%d.%m.%Y, %H:%i " }}</h6>
                      <h3 class="title"><a href="{{url options="article"}}">{{$gimme->article->title}}</a></h3>
                      {{ if !$gimme->article->content_accessible }}
                      <span class="premium_label">{{'premium'|translate}}</span>
                      {{/if}}
                      <p>{{$gimme->article->deck|strip_tags|truncate:250:"...":false}}</p>
                  </div>
      </article>
      {{/if}}
      <!-- End News item -->

      {{if $gimme->current_list->index==2}}
      <!-- Row (8) -->
      <div class="row">

          <!-- Span (3) -->
          <div class="span3">

            <!-- News item -->
            <article class="news_item">
             {{ image rendition="front_small" }}
             <a href="{{url options="article"}}" class="thumbnail">

               <img src="{{ $image->src }}"  alt="{{ $image->caption }} (photo: {{ $image->photographer }})" alt="" />
           </a>
           {{/image}}

                   <div class="content content_text">
                       <h6 class="info">{{list_article_authors}}
           {{if $gimme->current_list->index!=1}},&nbsp;{{/if}}

             {{ if $gimme->author->biography->first_name }}
             {{ $gimme->author->biography->first_name }} {{
             $gimme->author->biography->last_name }}
             {{ else }}
             {{ $gimme->author->name }}
             {{ /if }}
           {{if $gimme->current_list->at_end}}
           &nbsp;-&nbsp;{{/if}}{{/list_article_authors}}{{ $gimme->article->publish_date|camp_date_format:"%d.%m.%Y, %H:%i" }}</h6>
                       <h3 class="title"><a href="{{url options="article"}}">{{$gimme->article->title}}</a></h3>
                       {{ if !$gimme->article->content_accessible }}
                       <span class="premium_label">{{'premium'|translate}}</span>
                       {{/if}}
                       <p>{{$gimme->article->deck|strip_tags|truncate:200:"...":false}}</p>
                   </div>
       </article>
       <!-- End News item -->

   </div>
   <!--End Span (3) -->
   {{/if}}

   {{if $gimme->current_list->index==3}}
   <!-- Span (5) -->
   <div class="span5">

    <!-- News item -->
    <article class="news_item">
        {{ image rendition="front_medium" }}
        <a href="{{url options="article"}}" class="thumbnail">

           <img src="{{ $image->src }}"  alt="{{ $image->caption }} (photo: {{ $image->photographer }})" alt="" />
       </a>
       {{/image}}
               <div class="content content_text">
                   <h6 class="info">{{list_article_authors}}
       {{if $gimme->current_list->index!=1}},&nbsp;{{/if}}

         {{ if $gimme->author->biography->first_name }}
         {{ $gimme->author->biography->first_name }} {{
         $gimme->author->biography->last_name }}
         {{ else }}
         {{ $gimme->author->name }}
         {{ /if }}
       {{if $gimme->current_list->at_end}}
       &nbsp;-&nbsp;{{/if}}{{/list_article_authors}}{{ $gimme->article->publish_date|camp_date_format:"%d.%m.%Y, %H:%i" }}</h6>
                   <h3 class="title"><a href="{{url options="article"}}">{{$gimme->article->title}}</a></h3>
                   {{ if !$gimme->article->content_accessible }}
                   <span class="premium_label">{{'premium'|translate}}</span>
                   {{/if}}
                   <p>{{$gimme->article->deck|strip_tags|truncate:200:"...":false}}</p>
               </div>
   </article>
   <!-- End News item -->

</div>
<!-- End Span (5) -->

</div>
<!-- End Row (8) -->

</div>
<!-- End Span (4) -->
{{/if}}

{{/list_playlist_articles}}

<div class="span4 sidebar hidden-phone">

{{ render file="_tpl/community-feed.tpl"  issue=off section=off cache=400 }}


<a target="_blank" href="http://www.sourcefabric.org/" class="hidden-phone"><img src="{{ url static_file='_img/ads/300x250.png' }}" alt="" /></a>

{{render file="_tpl/front-poll.tpl"  issue=off section=off cache=4400 }}
</div>
</div>
<!-- End Row (12) -->




{{ render file="_tpl/front-columnists.tpl"  issue=off section=off cache=2800}}

<!-- Row (12) -->
<div class="row" id="masonry_container">


    {{list_playlist_articles id="1" }}
    {{if $gimme->current_list->index>3}}

  {{if $gimme->article->highlight}}
  <div class="span6">
      <article class="news_item">
          {{ image rendition="front_big" }}
          <a href="{{url options="article"}}" class="thumbnail">

             <img src="{{ $image->src }}"  alt="{{ $image->caption }} (photo: {{ $image->photographer }})" alt="" />
         </a>
         {{/image}}
         {{ include file="_tpl/front_article_cont.tpl" }}
     </article>
 </div>
 {{else}}
 <div class="span3">
    <article class="news_item">
     {{ image rendition="front_small" }}
     <a href="{{url options="article"}}" class="thumbnail">

        <img src="{{ $image->src }}"  alt="{{ $image->caption }} (photo: {{ $image->photographer }})" alt="" />
    </a>
    {{/image}}
    {{ include file="_tpl/front_article_cont.tpl" }}
</article>
</div>
{{/if}}
{{/if}}
{{/list_playlist_articles}}


</div>
<!-- End Row (12) -->


{{ render file="_tpl/box-most_tabs.tpl"  issue=off section=off cache=2400 }}


</section>
<!-- End Content -->













</div>

{{ include file="_tpl/footer.tpl" }}

{{ include file="_tpl/_html-foot.tpl" }}