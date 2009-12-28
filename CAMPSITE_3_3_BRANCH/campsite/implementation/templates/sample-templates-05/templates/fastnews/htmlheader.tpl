<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
{{ if $campsite->url->get_parameter('logout') == 'true' }}
<META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserId=; path=/">
<META HTTP-EQUIV="Set-Cookie" CONTENT="LoginUserKey=; path=/">
{{ $campsite->url->reset_parameter('logout') }}
<META HTTP-EQUIV="Refresh" content="0;url={{ uri }}">
{{ /if }}
