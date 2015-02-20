


{{ include file="_tpl/_html-head.tpl" }}



    {{ include file="_tpl/header.tpl" }}


<div id="page" class="container">


    <!-- Content -->
    <section id="content">

      {{ list_search_results length="10"  ignore_issue="true" }}
        {{if $gimme->current_list->at_beginning}}
          <div class="bloger_news_items">
            <div class="space_left_content">
                  <ul>
        {{/if}}
                    <li class="news_item">
                        <div class="content content_text">

                          {{ image rendition="section" }}
                          <a href="{{url options="article"}}" class="thumbnail">
                             <img src="{{ $image->src }}"  alt="{{ $image->caption }} (photo: {{ $image->photographer }})"  class="thumbnail" />
                           </a>
                          {{/image}}



                    <li class="news_item">
                      {{ image rendition="section" }}
                        <a href="{{url options="article"}}" class="thumbnail">
                           <img src="{{ $image->src }}"  alt="{{ $image->caption }} (photo: {{ $image->photographer }})"  class="thumbnail" />
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
              &nbsp;-&nbsp;
              {{/if}}
            {{/list_article_authors}}{{ $gimme->article->publish_date|camp_date_format:"%d.%m.%Y " }}</h6>
                            <h3 class="title"><a href="{{url options="article"}}">{{$gimme->article->name}}</a></h3>

                            <p>{{$gimme->article->deck|strip_tags}}</p>
                        </div>
                    </li>

                    {{if $gimme->current_list->at_end}}



                </ul>
            </div>
        </div>

          {{ include file="_tpl/pagination-search.tpl" }}
        {{/if}}

        {{/list_search_results}}


        {{ if $gimme->prev_list_empty }}

          <p>{{'noSearchResults'|translate}}</p>
        {{ /if }}

    </section>
    <!-- End Content -->













</div>


{{ include file="_tpl/footer.tpl" }}
{{ include file="_tpl/_html-foot.tpl" }}
