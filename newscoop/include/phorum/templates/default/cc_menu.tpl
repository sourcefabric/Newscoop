<div class="phorum-menu" style="text-align: left;">
  {LANG->PersProfile}
  <ul>
    <li><a {IF PROFILE->PANEL "summary"}class="phorum-current-page" {/IF}href="{URL->CC0}">{LANG->ViewProfile}</a></li>
    <li><a {IF PROFILE->PANEL "user"}class="phorum-current-page" {/IF}href="{URL->CC3}">{LANG->EditUserinfo}</a></li>
    <li><a {IF PROFILE->PANEL "sig"}class="phorum-current-page" {/IF}href="{URL->CC4}">{LANG->EditSignature}</a></li>
    <li><a {IF PROFILE->PANEL "email"}class="phorum-current-page" {/IF}href="{URL->CC5}">{LANG->EditMailsettings}</a></li>
    <li><a {IF PROFILE->PANEL "privacy"}class="phorum-current-page" {/IF}href="{URL->CC14}">{LANG->EditPrivacy}</a></li>
    <li><a {IF PROFILE->PANEL "groups"}class="phorum-current-page" {/IF}href="{URL->CC16}">{LANG->ViewJoinGroups}</a></li>
  </ul>
  {LANG->Subscriptions}
  <ul>
    <li><a {IF PROFILE->PANEL "subthreads"}class="phorum-current-page" {/IF}href="{URL->CC1}">{LANG->ListThreads}</a></li>
  </ul>
  {LANG->Options}
  <ul>
    <li><a {IF PROFILE->PANEL "forum"}class="phorum-current-page" {/IF}href="{URL->CC6}">{LANG->EditBoardsettings}</a></li>
    <li><a {IF PROFILE->PANEL "password"}class="phorum-current-page" {/IF}href="{URL->CC7}">{LANG->ChangePassword}</a></li>
  </ul>
  {IF MYFILES}
    {LANG->Files}
    <ul>
      <li><a {IF PROFILE->PANEL "files"}class="phorum-current-page" {/IF}href="{URL->CC9}">{LANG->EditMyFiles}</a></li>
    </ul>
  {/IF}
  {IF MODERATOR}
    {LANG->Moderate}
    <ul>
      {IF MESSAGE_MODERATOR}
        <li><a {IF PROFILE->PANEL "messages"}class="phorum-current-page" {/IF}href="{URL->CC8}">{LANG->UnapprovedMessages}</a></li>
      {/IF}
      {IF USER_MODERATOR}
        <li><a {IF PROFILE->PANEL "users"}class="phorum-current-page" {/IF}href="{URL->CC10}">{LANG->UnapprovedUsers}</a></li>
      {/IF}
      {IF GROUP_MODERATOR}
        <li><a {IF PROFILE->PANEL "membership"}class="phorum-current-page" {/IF}href="{URL->CC15}">{LANG->GroupMembership}</a></li>
      {/IF}
    </ul>
  {/IF}
</div>
