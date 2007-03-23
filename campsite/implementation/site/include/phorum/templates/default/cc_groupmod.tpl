{IF Message}
  <div class="PhorumUserError">{Message}</div>
{/IF}
{IF GROUP->name}
  <div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left;">{LANG->AddToGroup} {GROUP->name}</div>
  <div class="PhorumStdBlock" style="text-align: left;">
    <form method="post" action="{GROUP->url}">
      <input type="hidden" name="forum_id" value="{PROFILE->forum_id}" />
      {IF NEWMEMBERS}
        <select name="adduser">
          <option value="0">&nbsp;</option>
          {LOOP NEWMEMBERS}
            <option value="{NEWMEMBERS->username}">{NEWMEMBERS->displayname}</option>
          {/LOOP NEWMEMBERS}
        </select>
      {ELSE}
        <input type="text" name="adduser" />
      {/IF}
      <input type="submit" value="{LANG->Add}" />
    </form>
  </div><br />
  <div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left;">{LANG->GroupMemberList} {GROUP->name}</div>
  <div class="PhorumStdBlock" style="text-align: left;">
    {LANG->Filter}:
    {LOOP FILTER}
      [{IF FILTER->enable}<a href="{FILTER->url}">{/IF}{FILTER->name}{IF FILTER->enable}</a>{/IF}]
    {/LOOP FILTER}
    <br /><br />
    <form method="post" action="{GROUP->url}">
      <table class="PhorumFormTable" cellspacing="0" border="0">
        <input type="hidden" name="forum_id" value="{PROFILE->forum_id}" />
        <tr>
          <th>{LANG->Username}</th>
          <th>{LANG->MembershipType}</th>
        </tr>
        {LOOP USERS}
          <tr>
            <td>
              {IF USERS->flag}<strong><em>{/IF}<a href="{USERS->profile}">{USERS->displayname}</a>{IF USERS->flag}</em></strong>{/IF}
            </td>
            <td>
              {IF USERS->disabled}
                {USERS->statustext}
              {ELSE}
                <select name="status[{USERS->userid}]">
                  {LOOP STATUS_OPTIONS}
                    <?php
                    // to get around a minor templating problem, we'll figure
                    // out if we have this line selected here
                    $PHORUM['TMP']['STATUS_OPTIONS']['selected'] = ($PHORUM['TMP']['STATUS_OPTIONS']['value'] == $PHORUM['TMP']['USERS']['status']);
                    ?>
                    <option value="{STATUS_OPTIONS->value}"{IF STATUS_OPTIONS->selected} selected="selected"{/IF}>{STATUS_OPTIONS->name}</option>
                  {/LOOP STATUS_OPTIONS}
                </select>
              {/IF}
            </td>
          </tr>
        {/LOOP USERS}
        <tr>
          <td colspan="2"><input type="submit" value="{LANG->SaveChanges}" /></td>
        </tr>
      </table>
    </form>
  </div>
{ELSE}
  <div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left;">{LANG->SelectGroupMod}</div>
  <div class="PhorumStdBlock" style="text-align: left;">
    <table class="PhorumFormTable" cellspacing="0" border="0">
      {LOOP GROUPS}
        <tr>
          <td><a href="{GROUPS->url}">{GROUPS->name}</a></td>
          <td><a href="{GROUPS->unapproved_url}">{GROUPS->unapproved} {LANG->Unapproved}</a></td>
        </tr>
      {/LOOP GROUPS}
    </table>
  </div>
{/IF}
