            <div class="paging-holder">
               {{if isset($paginator->previous)}}
            	<a href="{{ $view->url(['page' => $paginator->previous]) }}" class="prev"><span>+ {{ #previous# }}</span> {{ #page# }}</span></a>
            	{{/if}}
            	
            	<span class="paging">
            	{{foreach $paginator->pagesInRange as $page}}
            	<a href="{{ $view->url(['page' => $page]) }}"{{if $paginator->current eq $page}} class="active"{{/if}}>{{ $page }}</a>
            	{{/foreach}}
            	</span>
            	
            	{{if isset($paginator->next)}}
            	<a href="#" class="next"><span>{{ #next# }}</span> {{ #page# }} <span>+</span></a>
            	{{/if}}
            </div><!-- / Pagination -->
