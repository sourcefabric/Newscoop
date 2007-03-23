{IF CHARSET}
<?php
  header("Content-Type: text/html; charset=".htmlspecialchars($PHORUM['DATA']['CHARSET']))
?>
{/IF}
<?php echo '<?' ?>xml version="1.0" encoding="{CHARSET}"<?php echo '?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $PHORUM['locale']; ?>">
  <head>
    <style type="text/css">
      {INCLUDE css}
    </style>
    {IF URL->RSS}
      <link rel="alternate" type="application/rss+xml" title="RSS-Feed" href="{URL->RSS}" />
    {/IF}
    {IF URL->REDIRECT}
      <meta http-equiv="refresh" content="{IF REDIRECT_TIME}{REDIRECT_TIME}{ELSE}5{/IF}; url={URL->REDIRECT}" />
    {/IF}
    {LANG_META}
    <title>{HTML_TITLE}</title>
    {HEAD_TAGS}
  </head>
  <body onload="{IF FOCUS_TO_ID}var focuselt=document.getElementById('{FOCUS_TO_ID}'); if (focuselt) focuselt.focus();{/IF}">
    <div align="{forumalign}">
      <div class="PDDiv">
        {IF notice_all}
          <div class="PhorumNotificationArea PhorumNavBlock">
            {IF NEW_PRIVATE_MESSAGES}<a class="PhorumNavLink" href="{URL->PM}">{LANG->NewPrivateMessages}</a><br />{/IF}
            {IF notice_messages}<a class="PhorumNavLink" href="{notice_messages_url}">{LANG->UnapprovedMessagesLong}</a><br />{/IF}
            {IF notice_users}<a class="PhorumNavLink" href="{notice_users_url}">{LANG->UnapprovedUsersLong}</a><br />{/IF}
            {IF notice_groups}<a class="PhorumNavLink" href="{notice_groups_url}">{LANG->UnapprovedGroupMembers}</a><br />{/IF}
          </div>
        {/IF}
        <span class="PhorumTitleText PhorumLargeFont">
          {IF NAME}<a href="{URL->TOP}">{NAME}</a>&nbsp;:&nbsp;{/IF}
          {TITLE}
        </span>
        {IF URL->INDEX}<a href="{URL->INDEX}">{/IF}<img src="templates/default/images/logo.png" alt="The fastest message board... ever. " title="The fastest message board... ever. " width="170" height="42" border="0" />{IF URL->INDEX}</a>{/IF}
        <div class="PhorumFloatingText">{DESCRIPTION}&nbsp;</div>
