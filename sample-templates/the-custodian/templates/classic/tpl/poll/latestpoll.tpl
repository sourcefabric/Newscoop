<div class="teaserframe teaserframebig teaserframe-poll teaserframebig-poll">
<div class="teaserframebiginner">
  <div class="teaserhead">
  <div class="teaserheadinner">
<a href="{{ uri options="template classic/tpl/poll/poll-list.tpl" }}">{{ if $gimme->language->name == "English" }}Latest Poll{{ else }}Última encuesta{{ /if }}</a>
</div><!-- .teaserheadinner -->
  </div><!-- .teaserhead -->

<div id="poll-box">

<script language="JavaScript" type="text/javascript">
<!--
function submitForm() {
window.open ("http://{{ $gimme->publication->site }}/?tpl=886", "NewWindow","menubar=0,resizable=1,width=350,height=350");
document.myform.submit();
}
//-->
</script>

{{ list_polls name="last" length="1" order="bynumber desc" }}
    <p class="question">{{ $gimme->poll->question }}</p> 

    <form id="poll-form" action="" name="myform" target="NewWindow">
    <input type="hidden" name="f_poll" value="1" />
    <input type="hidden" name="f_poll_nr" value="{{ $gimme->poll->number }}" />
    <input type="hidden" name="f_poll_language_id" value="{{ $gimme->language->number }}" />
    <input type="hidden" name="f_poll_mode" value="standard" />
    <input type="hidden" name="tpl" value="886" /><!-- template ID -->

{{ list_poll_answers }} 
<p class="answer">{{ pollanswer_edit }}{{ $gimme->pollanswer->answer }}</p>
{{ /list_poll_answers }}

<input class="button" type="submit" onClick="javascript:submitForm();return false;" value="Vote" />

<a href="{{ uri options="template classic/tpl/poll/poll-results.tpl" }}" onClick="javascript:submitForm();return false;">{{ if $gimme->language->name == "English" }}Results{{ else }}Resultados{{ /if }}</a>
        </form>
{{ /list_polls }}   

</div><!-- /#poll-box -->

</div><!-- .teaserframebiginner -->
</div><!-- .teaserframebig -->
