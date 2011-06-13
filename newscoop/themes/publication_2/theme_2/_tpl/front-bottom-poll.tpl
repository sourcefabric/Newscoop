<div id="col_double">

{{ list_polls name="last" length="1" order="bynumber desc" }}

{{ if $gimme->poll_action->defined }}

  <p class="question">{{ $gimme->poll->question }}</p>
  {{ if $gimme->poll->user_vote_count >= $gimme->poll->votes_per_user || $gimme->poll_action->ok }}
    <p>Thank you for voting!</p>
  {{ elseif $gimme->poll_action->is_error }}
    {{ $gimme->poll_action->error_message }}
  {{/if}}
  <p>Total votes: {{ $gimme->poll->votes }}</p>

  {{ list_poll_answers order="byOrder asc" }}
  <div class="answer">{{ $gimme->pollanswer->percentage|string_format:"%d" }}%: {{ $gimme->pollanswer->answer }}
    <div style="width:{{ $gimme->pollanswer->percentage|string_format:"%d" }}%;background:#000;">&nbsp;</div>
  </div>
  {{ /list_poll_answers }}

{{else}}
  <h3>Poll</h3>
<div id="poll">
  <p class="question">{{ $gimme->poll->question }}</p>
  {{ if $gimme->poll->is_votable }}
    {{ poll_form template="_tpl/front-bottom-poll.tpl" submit_button=false }}
    {{ list_poll_answers }}
      <div class="poll-item">
        {{ pollanswer_edit }}{{ $gimme->pollanswer->answer }}<br />
      </div>
      {{ /list_poll_answers }}
    <div style="clear: both; margin-bottom: 10px"></div>
    <input type="submit" id="poll-button" class="button" value="Vote" />
    {{ /poll_form }}
  {{ else }}
    {{ if $gimme->poll->user_vote_count >= $gimme->poll->votes_per_user }}<p>Thank you for voting!</p>{{ /if }}
    <p>Total votes: {{ $gimme->poll->votes }}</p>
    {{ list_poll_answers }}
    <div class="answer">{{ $gimme->pollanswer->percentage|string_format:"%d" }}%: {{ $gimme->pollanswer->answer }}
      <div style="width:{{ $gimme->pollanswer->percentage|string_format:"%d" }}%;background:#5d4040;">&nbsp;</div>
    </div>
    {{ /list_poll_answers }}
  {{ /if }}
</div><!-- /#poll -->
{{ /if }}
{{ /list_polls }}                  
</div>