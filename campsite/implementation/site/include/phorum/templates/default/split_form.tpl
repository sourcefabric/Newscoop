<div align="center">
  {INCLUDE posting_menu}
  <form method="POST" action="{URL->ACTION}">
    {POST_VARS}
    <input type="hidden" name="forum_id" value="{FORM->forum_id}" />
    <input type="hidden" name="thread" value="{FORM->thread_id}" />
    <input type="hidden" name="message" value="{FORM->message_id}" />
    <input type="hidden" name="mod_step" value="{FORM->mod_step}" />
    <div class="PhorumStdBlockHeader PhorumNarrowBlock" style="text-align: left;"><span class="PhorumHeadingLeft">{LANG->SplitThread}</span></div>
    <div class="PhorumStdBlock PhorumNarrowBlock" style="text-align: left;">
      <div class="PhorumFloatingText">
        {LANG->SplitThreadInfo}<br /><br />
        {LANG->Message}: '{FORM->message_subject}'<br /><br />
        <input type="submit" class="PhorumSubmit" name="move" value="{LANG->SplitThread}" />
      </div>
    </div>
  </form>
</div>
