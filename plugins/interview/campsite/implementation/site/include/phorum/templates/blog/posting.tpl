<div id="phorum-post-form" align="center">

{IF ERROR}<div class="PhorumUserError">{ERROR}</div>{/IF}
{IF OKMSG}<div class="PhorumOkMsg">{OKMSG}</div>{/IF}

  {IF PREVIEW}
    {include posting_preview}
  {/IF}

  <form id="post_form" name="post" action="{URL->ACTION}" method="post"
   enctype="multipart/form-data">
  {POST_VARS}

  {include posting_messageform}

  {include posting_buttons}

  </form>

</div>
