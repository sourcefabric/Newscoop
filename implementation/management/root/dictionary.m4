<HTML>
B_DATABASE

<!sql setdefault class 0>dnl
<!sql setdefault keyword 0>dnl
<!sql setdefault IdLanguage 0>dnl

<HEAD>
    <META HTTP-EQUIV="Expires" CONTENT="now">
    <TITLE>Dictionary</TITLE>
</HEAD>

<BODY BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">

<!sql query "SELECT * FROM KeywordClasses WHERE IdDictionary=?keyword AND IdClasses=?class AND IdLanguage=?IdLanguage" kc>dnl
<!sql print_loop kc>dnl

<!sql query "SELECT * FROM Dictionary WHERE Id=?keyword AND IdLanguage=?IdLanguage" kwd>dnl
<!sql query "SELECT * FROM Classes WHERE Id=?class AND IdLanguage=?IdLanguage" cls>dnl


<H1><!sql print ~kwd.Keyword></H1>
<HR NOSHADE SIZE="1" COLOR="BLACK">
<H2><!sql print ~cls.Name></H1>

<BLOCKQUOTE>
<!sql print ~kc.Definition>
</BLOCKQUOTE>

<!sql query "SELECT * FROM KeywordClasses WHERE IdDictionary=?keyword AND IdClasses != ?class AND IdLanguage=?IdLanguage" kc>dnl
<!sql set heading 1>dnl
<!sql print_loop kc>dnl
<!sql if $heading>dnl
<H2>See also</H2>
<UL>
<!sql set heading 0>dnl
<!sql endif>dnl
<!sql query "SELECT * FROM Classes WHERE Id=?kc.IdClasses AND IdLanguage=?IdLanguage" cls>dnl
<!sql print_rows cls "<LI><A HREF=\"$PATH_INFO\?class=#cls.Id&keyword=#keyword&IdLanguage=#IdLanguage\">~cls.Name</A></LI>">
<!sql done>dnl
</UL>

<!sql done>dnl

</BODY>

E_DATABASE
</HTML>
