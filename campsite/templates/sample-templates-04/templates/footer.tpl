<tr>
          <td align="left" style="padding-left: 10px"><p class="footer"><strong>©</strong> 2004 CAMPSITE</p></td>
          <td align="right" style="padding-right: 10px">
		    <p class="footer">
			  {{ list_sections length="3" constraints="number greater 199" }}
			  <a class="footer" href="{{ uri options="reset_article_list" }}"> {{ $campsite->section->name }}</a> {{ if $campsite->current_list->index == 3 }}{{ else }}-{{ /if }}
			  {{ /list_sections }}</p>  


		  </td>
        </tr>