{{ include file="html_header.tpl" }}

<table class="main" cellspacing="0" cellpadding="0">
<tr>
  <td valign="top">
    <div id="breadcrubm">
    {{ breadcrumb }}
    </div>
    {{** main content area **}}
    <table class="content" cellspacing="0" cellpadding="0">
    
    <tr><td colspan="3">{{ include file='blog/blog-form.tpl' }}</td></tr>
    
    <tr><td>&nbsp</td></tr>
     
    {{ if !$gimme->blog->identifier && !$gimme->blogentry->identifier && !$gimme->blogcomment->identifier }}  

        <tr>
            <th align="left">id</th>
            <th align="left">title</th>
            <th align="left">user id</th>
            <th align="left">info</th>
        </tr>  
        
        <tr><td colspan="6"><hr></td></tr>
               
        {{ list_blogs name="blogs_list" length="20" order="byidentifier desc"}}
           <tr>
            <td>
                {{ $gimme->blog->identifier }}
                {{ include file="blog/blog-actions.tpl" }}
            </td>
            <td><a href="{{ url }}">{{ $gimme->blog->title|truncate:20 }}</a></td>
            <td>{{ $gimme->blog->user_id }}</td>
            <td>{{ $gimme->blog->info|truncate:30 }}</td>
          </tr>
 
        {{ /list_blogs }}

    
    {{ elseif !$gimme->blogentry->identifier && !$gimme->blogcomment->identifier }}  
        <p>
        
        <tr>
            <th align="left">entry id</th>
            <th align="left">title</th>
            <th align="left">user id</th>
            <th align="left">content</th>
            <th align="left">mood</th>
        </tr>  
        
        <tr><td colspan="6"><hr></td></tr>
        
        {{ list_blogentries name="blogentries_list" length="20" order="byidentifier desc" order="byidentifier desc"}}
           <tr>
            <td>
                {{ $gimme->blogentry->identifier }}
                {{ include file="blog/blogentry-actions.tpl" }}
            </td>
            <td><a href="{{ url }}">{{ $gimme->blogentry->title|truncate:20 }}</a></td>
            <td>{{ $gimme->blogentry->user_id }}</td>
            <td>{{ $gimme->blogentry->content|truncate:30 }}</td>
            <td>{{ $gimme->blogentry->mood }}</td>
          </tr>
          
        {{ /list_blogentries }}
    
    {{ elseif !$gimme->blogcomment->identifier }}  
        <p>
        
        <tr>
            <th align="left">comment id</th>
            <th align="left">name</th>
            <th align="left">user id</th>
            <th align="left">content</th>
            <th align="left">mood</th>
        </tr>  
        
        <tr><td colspan="6"><hr></td></tr>
        
        {{ list_blogcomments name="blogcomments_list" length="100" }}
           <tr>
            <td>
                {{ $gimme->blogcomment->identifier }}
                {{ include file="blog/blogcomment-actions.tpl" }}
            </td>
            <td><a href="{{ url }}">{{ $gimme->blogcomment->title|truncate:20 }}</a></td>
            <td>{{ $gimme->blogcomment->user_id }}</td>
            <td>{{ $gimme->blogcomment->content|truncate:30 }}</td>
            <td>{{ $gimme->blogcomment->mood }}</td>
          </tr>
            
        {{ /list_blogcomments }}
    
    {{ /if }}
    
    
      </td>
    </tr>
    </table>
    {{** end main content area **}}
  </td>
  <td valign="top">
    {{ include file="html_rightbar.tpl" }}
  </td>
</tr>
</table>
{{ include file="html_footer.tpl" }}