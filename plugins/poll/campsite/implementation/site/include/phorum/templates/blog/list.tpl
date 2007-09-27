{LOOP ROWS}
  {IF NOT ROWS->sort 0} {! Skip announcements }
  <div class="entry">
    <h1>{ROWS->subject}</h1>
    <p>{ROWS->body}</p>
    <small>Post by {ROWS->linked_author} on {ROWS->datestamp}</small>
    <small>{IF MODERATOR true}<a href="<?php echo phorum_get_url(PHORUM_POSTING_URL, "moderation", $PHORUM["TMP"]["ROWS"]["message_id"]);?>">edit</a>&nbsp;|&nbsp;{/IF}<a href="{ROWS->url}"><?php echo $PHORUM["TMP"]["ROWS"]["thread_count"]-1; ?> comment(s)</a></small>
  </div>
  {/IF}
{/LOOP ROWS}
