<div class="PhorumNavBlock" style="text-align: left;">
  <span class="PhorumNavHeading PhorumHeadingLeft">{LANG->Goto}:</span>&nbsp;{IF URL->INDEX}<a class="PhorumNavLink" href="{URL->INDEX}">{LANG->ForumList}</a>&bull;{/IF}{IF URL->TOP}<a class="PhorumNavLink" href="{URL->TOP}">{LANG->MessageList}</a>&bull;{/IF}{IF URL->POST}<a class="PhorumNavLink" href="{URL->POST}">{LANG->NewTopic}</a>&bull;{/IF}<a class="PhorumNavLink" href="{URL->SEARCH}">{LANG->Search}</a>&bull;<a class="PhorumNavLink" href="{URL->REGISTERPROFILE}">{LANG->MyProfile}</a>&bull;<a class="PhorumNavLink" href="{URL->LOGINOUT}">{LANG->LogOut}</a>
</div>
<table id="phorum-menu-table" cellspacing="0" border="0">
  <tr>
    <td id="phorum-menu">
      {INCLUDE pm_menu}
    </td>
    <td id="phorum-content">
      {IF ERROR}
        <div class="PhorumUserError">{ERROR}</div>
      {/IF}
      {IF OKMSG}
        <div class="PhorumOkMsg">{OKMSG}</div>
      {/IF}
      <?php
      // don't touch this line
      include phorum_get_template($template);
      ?>
    </td>
  </tr>
</table>
