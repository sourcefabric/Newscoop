<?php $count = 0; ?>
{LOOP MESSAGES}
{IF MESSAGES->parent_id 0}
<div class="entry">
<h1>{MESSAGES->subject}</h1>
<p>{MESSAGES->body}</p>
<small>Post by {MESSAGES->linked_author} on {MESSAGES->datestamp}</small>
<small>{IF MODERATOR true}<a href="{MESSAGES->edit_url}">edit</a>&nbsp;|&nbsp;<a href="{MESSAGES->delete_url2}">delete</a>{/IF}</small>
</div>
<div id="comments">
<h2>Comments</h2>
{ELSE}
<a name="msg-{MESSAGES->message_id}"></a>
<?php
    $class = ($count%2==0) ? "comment-alt" : "";
    $count++;
?>
<div class="comment <?php echo $class;?>">
<h1>{MESSAGES->subject}</h1>
<p>{MESSAGES->body}</p>
<small>Post by {MESSAGES->linked_author} on {MESSAGES->datestamp}</small>
{IF MODERATOR true}<small><a href="{MESSAGES->edit_url}">edit</a>&nbsp;|&nbsp;<a href="{MESSAGES->delete_url1}">delete</a></small>{/IF}
</div>

{/IF}

{/LOOP MESSAGES}
</div>

<h3>Post A Comment</h3>
