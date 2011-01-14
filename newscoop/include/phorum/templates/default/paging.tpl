<div class="PhorumNavBlock" style="text-align: left;">
  {IF NOT TOTALPAGES 1}
    <div style="float: right;">
      <span class="PhorumNavHeading">{LANG->Pages}:</span>&nbsp;{IF URL->PREVPAGE}<a class="PhorumNavLink" href="{URL->PREVPAGE}">{LANG->PrevPage}</a>{/IF}{IF URL->FIRSTPAGE}<a class="PhorumNavLink" href="{URL->FIRSTPAGE}">{LANG->FirstPage}...</a>{/IF}{LOOP PAGES}{IF PAGES->pageno CURRENTPAGE}<span class="PhorumNavLink"><strong>{PAGES->pageno}</strong></span>{ELSE}<a class="PhorumNavLink" href="{PAGES->url}">{PAGES->pageno}</a>{/IF}{/LOOP PAGES}{IF URL->LASTPAGE}<a class="PhorumNavLink" href="{URL->LASTPAGE}">...{LANG->LastPage}</a>{/IF}{IF URL->NEXTPAGE}<a class="PhorumNavLink" href="{URL->NEXTPAGE}">{LANG->NextPage}</a>{/IF}
    </div>
  {/IF}
  <span class="PhorumNavHeading PhorumHeadingLeft">{LANG->CurrentPage}: </span>{CURRENTPAGE} {LANG->of} {TOTALPAGES}
</div>
