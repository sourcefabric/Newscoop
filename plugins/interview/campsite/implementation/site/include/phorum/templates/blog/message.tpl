{IF ERROR}
<h4>{ERROR}</h4>
{/IF}

{IF MESSAGE}
<p>{MESSAGE}</p>
{/IF}

{IF URL->REDIRECT}
<a href="{URL->REDIRECT}">{BACKMSG}</a>
{/IF}
