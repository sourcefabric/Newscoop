<div align="center">
  {INCLUDE posting_menu}
  {IF FORM->merge_none}
    <div class="PhorumStdBlockHeader PhorumNarrowBlock" style="text-align: left;"><span class="PhorumHeadingLeft">{LANG->MergeThread}</span></div>
    <div class="PhorumStdBlock PhorumNarrowBlock" style="text-align: left;">
      <div class="PhorumFloatingText">
        {LANG->MergeThreadWith}:<br /><br />
        {LANG->MergeThreadInfo}<br />
      </div>
    </div>
  {/IF}
  {IF FORM->merge_t1}
    <form method="POST" action="{URL->ACTION}">
      {POST_VARS}
      <input type="hidden" name="forum_id" value="{FORM->forum_id}" />
      <input type="hidden" name="thread" value="{FORM->thread_id}" />
      <input type="hidden" name="thread1" value="{FORM->merge_t1}" />
      <input type="hidden" name="mod_step" value="{FORM->mod_step}" />
      <div class="PhorumStdBlockHeader PhorumNarrowBlock" style="text-align: left;"><span class="PhorumHeadingLeft">{LANG->MergeThread}</span></div>
      <div class="PhorumStdBlock PhorumNarrowBlock" style="text-align: left;">
        <div class="PhorumFloatingText">
          {LANG->MergeThreadAction}:<br /><br />
          {LANG->Thread}: '{FORM->merge_subject1}'<br />
          {LANG->Thread}: '{FORM->thread_subject}'<br /><br />
          <input type="submit" class="PhorumSubmit" name="move" value="{LANG->MergeThread}" />
        </div>
      </div>
    </form>
  {/IF}
  {IF FORM->thread_id}
    <div class="PhorumFloatingText">
      <form method="POST" action="{URL->ACTION}">
        {POST_VARS}
        <input type="hidden" name="forum_id" value="{FORM->forum_id}" />
        <input type="hidden" name="thread" value="{FORM->thread_id}" />
        <input type="hidden" name="mod_step" value="{FORM->mod_step}" />
        <input type="submit" class="PhorumSubmit" name="move" value="{LANG->MergeThreadCancel}" />
      </form>
    </div>
  {/IF}
</div>
