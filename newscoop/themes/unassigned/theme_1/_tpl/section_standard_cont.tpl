

    <div class="row" id="masonry_container">


       {{ list_articles ignore_issue="true" ignore_section="false" order="byPublishDate desc" length="10" constraints="type not poll" }}

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


      {{if $gimme->current_list->at_end}}



    </div>


{{ include file="_tpl/pagination.tpl" }}
{{/if}}

{{/list_articles}}