


{{extends file="layout.tpl"}}

{{block content}}




                       <div class="bloger_news_items">
                         <div>
                           <ul>
                            {{ list_issues order="bynumber desc" constraints="number greater 1" }}


                             <li class="news_item">
                               <div class="content content_text">





                                <h6 class="info">{{ $gimme->issue->publish_date|camp_date_format:"%d %M %Y" }}</h6>
                                 <h3 class="title"><a href="{{ url options="template issue.tpl" }}">{{ $gimme->issue->name }}</a></h3>


                               </div>
                             </li>






                    {{ /list_issues }}


                        </ul>
                      </div>
                    </div>









{{/block}}

