<div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left;">{LANG->PersProfile}</div>
<div class="PhorumStdBlock" style="text-align: left;">
  <table class="PhorumFormTable" cellspacing="0" border="0">
    <tr>
      <td>{LANG->Username}:</td>
      <td>{PROFILE->username}</td>
    </tr>
    <tr>
      <td>{LANG->RealName}:</td>
      <td>{PROFILE->real_name}</td>
    </tr>
    <tr>
      <td>{LANG->Email}:</td>
      <td>{PROFILE->email}</td>
    </tr>
    <tr>
      <td>{LANG->DateReg}:</td>
      <td>{PROFILE->date_added}</td>
    </tr>
    {IF PROFILE->date_last_active}
      <tr>
        <td>{LANG->DateActive}:</td>
       <td>{PROFILE->date_last_active}</td>
      </tr>
    {/IF}
    <tr>
      <td>{LANG->Posts}:</td>
      <td>{PROFILE->posts}</td>
    </tr>
    <tr>
      <td>{LANG->Signature}:</td>
      <td>{PROFILE->signature_formatted}</td>
    </tr>
  </table>
</div>
{IF PROFILE->admin}{ASSIGN SHOWPERMS 1}{/IF}
{IF UserPerms}{ASSIGN SHOWPERMS 1}{/IF}
{IF SHOWPERMS}
  <br />
  <div class="PhorumStdBlockHeader PhorumHeaderText" style="text-align: left;">{LANG->UserPermissions}</div>
  <div class="PhorumStdBlock" style="text-align: left;">
    <table class="PhorumFormTable" cellspacing="0" border="0">
      {IF PROFILE->admin}
        <tr>
          <td>{LANG->PermAdministrator}</td>
        </tr>
      {ELSEIF UserPerms}
        <tr>
          <th>{LANG->Forum}</th>
          <th>{LANG->Permission}</th>
        </tr>
        {LOOP UserPerms}
          <tr>
            <td>{UserPerms->forum}&nbsp;&nbsp;</td>
            <td>{UserPerms->perm}</td>
          </tr>
        {/LOOP UserPerms}
      {/IF}
    </table>
  </div>
{/IF}
