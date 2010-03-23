<div align="center">

<div class="PhorumNavBlock PhorumNarrowBlock" style="text-align: left;">
<span class="PhorumNavHeading PhorumHeadingLeft">{LANG->Goto}:</span>&nbsp;<a class="PhorumNavLink" href="{URL->INDEX}">{LANG->ForumList}</a>&bull;<a class="PhorumNavLink" href="{URL->TOP}">{LANG->MessageList}</a>&bull;<a class="PhorumNavLink" href="{URL->SEARCH}">{LANG->Search}</a>{if LOGGEDIN true}&bull;<a class="PhorumNavLink" href="{URL->REGISTERPROFILE}">{LANG->MyProfile}</a>{IF ENABLE_PM}&bull;<a class="PhorumNavLink" href="{URL->PM}">{LANG->PrivateMessages}</a>{/IF}&bull;<a class="PhorumNavLink" href="{URL->LOGINOUT}">{LANG->LogOut}</a>{/if}{if LOGGEDIN false}&bull;<a class="PhorumNavLink" href="{URL->LOGINOUT}">{LANG->LogIn}</a>{/if}</a>
</div>

<form method="POST" action="{URL->ACTION}">
{POST_VARS}
<input type="hidden" name="forum_id" value="{FORM->forum_id}" />
<input type="hidden" name="thread" value="{FORM->thread_id}" />
<input type="hidden" name="mod_step" value="{FORM->mod_step}" />

<div class="PhorumStdBlockHeader PhorumNarrowBlock" style="text-align: left;"><span class="PhorumHeadingLeft">{LANG->MoveThread}</span></div>
<div class="PhorumStdBlock PhorumNarrowBlock" style="text-align: left;">
<div class="PhorumFloatingText">
  {LANG->MoveThreadTo}:<br />
  <select name="moveto">
  <option value="0">{LANG->SelectForum}</option>
  {LOOP FORUMS}
  <option value="{FORUMS->forum_id}">{FORUMS->name}</option>
  {/LOOP FORUMS}
  </select>
  <input type="submit" class="PhorumSubmit" name="move" value="{LANG->MoveThread}" />
</div>
</div>

</form>

</div>
