<div class="phorum-menu" style="text-align: left; white-space: nowrap">
  {LANG->PrivateMessages}
  <ul>
    {LOOP PM_FOLDERS}
      {! Put a bit of space between incoming and the outgoing folder, in }
      {! case the user has own folders. }
      {IF PM_USERFOLDERS}{IF PM_FOLDERS->is_outgoing}{VAR SPACER 10px}{/IF}{/IF}
      <li {IF SPACER}style="margin-top: {SPACER}"{/IF}>
        <a {IF PM_FOLDERS->id FOLDER_ID}class="phorum-current-page" {/IF}href="{PM_FOLDERS->url}">{PM_FOLDERS->name}</a><small>{IF PM_FOLDERS->total}&nbsp;({PM_FOLDERS->total}){/IF}{IF PM_FOLDERS->new}&nbsp;(<span class="PhorumNewFlag">{PM_FOLDERS->new} {LANG->newflag}</span>){/IF}</small>
      </li>
    {/LOOP PM_FOLDERS}
  </ul>
  {LANG->Options}
  <ul>
    <li><a {IF PM_PAGE "folders"}class="phorum-current-page" {/IF}href="{URL->PM_FOLDERS}">{LANG->EditFolders}</a></li>
    <li><a {IF PM_PAGE "send"}class="phorum-current-page" {/IF}href="{URL->PM_SEND}">{LANG->SendPM}</a></li>
    <li><a {IF PM_PAGE "buddies"}class="phorum-current-page" {/IF} href="{URL->BUDDIES}">{LANG->Buddies}</a></li>
  </ul>
</div>
{INCLUDE pm_max_messagecount}
