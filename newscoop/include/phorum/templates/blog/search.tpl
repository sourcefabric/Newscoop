{IF SEARCH->noresults}
<h3>{LANG->NoResults}</h3>
{/IF}

{IF SEARCH->showresults}


<h3>{LANG->Results} {RANGE_START} - {RANGE_END} {LANG->of} {TOTAL}</h3>
<hr />
{LOOP MATCHES}

<div class="match">
<h1>{MATCHES->number}.&nbsp;<a href="{MATCHES->url}">{MATCHES->subject}</a></h1>
<p>{MATCHES->short_body}</p>
<small>Post by {MATCHES->author} on {MATCHES->datestamp}&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{MATCHES->url}"><?php echo $PHORUM["TMP"]["MATCHES"]["thread_count"]-1; ?> comment(s)</a></small>
</div>
<hr />
{/LOOP MATCHES}

{IF PAGES}
<br /><br /><strong>{LANG->CurrentPage}:</strong>{CURRENTPAGE} {LANG->of} {TOTALPAGES}&nbsp;&nbsp;&nbsp;
<strong>{LANG->Pages}:</strong>&nbsp;
{IF URL->PREVPAGE}<a class="PhorumNavLink" href="{URL->PREVPAGE}">{LANG->PrevPage}</a>{/IF}
{IF URL->FIRSTPAGE}<a class="PhorumNavLink" href="{URL->FIRSTPAGE}">{LANG->FirstPage}...</a>{/IF}
{LOOP PAGES}<a class="PhorumNavLink" href="{PAGES->url}">{PAGES->pageno}</a>{/LOOP PAGES}
{IF URL->LASTPAGE}<a class="PhorumNavLink" href="{URL->LASTPAGE}">...{LANG->LastPage}</a>{/IF}
{IF URL->NEXTPAGE}<a class="PhorumNavLink" href="{URL->NEXTPAGE}">{LANG->NextPage}</a>{/IF}
<br />
{/IF}

{/IF}
