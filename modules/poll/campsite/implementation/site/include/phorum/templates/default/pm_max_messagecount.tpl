{IF MAX_PM_MESSAGECOUNT}
  <?php
    $avail = $PHORUM['DATA']['PM_SPACE_LEFT'];
    $used = $PHORUM['DATA']['PM_MESSAGECOUNT'];
    $total = $avail + $used;
    $size = 130;
    $usedsize = ceil($used/$total * $size);
    $usedperc = floor($used/$total * 100 + 0.5);
  ?>
  <div class="phorum-menu" style="margin-top: 6px">
    <div style="text-align: center; padding: 10px 0px 10px 0px">
      <div style="padding-bottom: 10px">
        {IF PM_SPACE_LEFT}
          {LANG->PMSpaceLeft}
        {ELSE}
          {LANG->PMSpaceFull}
        {/IF}
      </div>
      <table class="phorum-gaugetable" align="center">
        <tr>
          <td class="phorum-gaugeprefix"><?php echo "{$usedperc}%" ?></td>
          <td class="phorum-gauge" width="<?php echo $size?>"><img align="left" src="{gauge_image}" width="<?php echo $usedsize?>" height="16px" /></td>
        </tr>
      </table>
    </div>
  </div>
{/IF}
