{{ include file="html_header.tpl" }}

<table class="main" cellspacing="0" cellpadding="0">
<tr>
  <td valign="top">
    <div id="breadcrubm">
    {{ breadcrumb }}
    </div>
    {{** main content area **}}
    <table class="content" cellspacing="0" cellpadding="0">
    
    <tr><td colspan="3">{{ include file='blog-form.tpl' }}</td></tr>
    
    <tr><td>&nbsp</td></tr>
     
    {{ if !$campsite->blog->identifier && !$campsite->blogentry->identifier && !$campsite->blogcomment->identifier }}  

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
                <a href="{{ url }}">
                    {{ $campsite->blog->identifier }}
                </a>
                &nbsp;
                {{ if $campsite->blog->user_id == $campsite->user->identifier }}
                    <a href="{{ url }}&amp;f_blog_action=edit">
                        edit
                    </a>
                {{ /if }}
            </td>
            <td>{{ $campsite->blog->title|truncate:20 }}</td>
            <td>{{ $campsite->blog->user_id }}</td>
            <td>{{ $campsite->blog->info|truncate:30 }}</td>
          </tr>
 
        {{ /list_blogs }}

    
    {{ elseif !$campsite->blogentry->identifier && !$campsite->blogcomment->identifier }}  
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
                <a href="{{ url }}">
                    {{ $campsite->blogentry->identifier }}
                </a>
            </td>
            <td>{{ $campsite->blogentry->title|truncate:20 }}</td>
            <td>{{ $campsite->blogentry->user_id }}</td>
            <td>{{ $campsite->blogentry->content|truncate:30 }}</td>
            <td>{{ $campsite->blogentry->mood }}</td>
          </tr>
          
        {{ /list_blogentries }}
    
    {{ elseif !$campsite->blogcomment->identifier }}  
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
                <a href="{{ url }}">
                    {{ $campsite->blogcomment->identifier }}
                </a>
            </td>
            <td>{{ $campsite->blogcomment->title|truncate:20 }}</td>
            <td>{{ $campsite->blogcomment->user_id }}</td>
            <td>{{ $campsite->blogcomment->content|truncate:30 }}</td>
            <td>{{ $campsite->blogcomment->mood }}</td>
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