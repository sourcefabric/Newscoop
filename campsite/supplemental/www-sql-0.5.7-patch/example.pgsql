<html>
<head><title>Telephone Numbers</title></head>
<body>
<H1>Telephone Numbers</H1>
<!-- Let user change query -->
<form action=example.sql>
<input name=sur> <input type=submit><br>
</form>
<! sql connect >
<! sql database telephone >
<! sql setdefault sur "-" >
<! sql setdefault ofs 0 >
<! sql query "begin" >
<! sql query "declare tmp cursor for select * from numbers where surname
 like '?sur' order by firstname" >
<! sql query "move $ofs in tmp" >
<! sql query "fetch 10 in tmp" q1 >
<! sql if $NUM_ROWS != 0 >
<!-- Put in table -->
<table>
<tr> <th>Surname</th> <th>First Name</th> <th>Number</th> </tr>
<! sql print_rows q1 "<tr> <td>@q1.0</td> <td>@q1.1</td>
 <td>@q1.2</td> </tr>\n" >
</table>
<!-- Put in navigation links -->
<center>
<! sql if 9 < $ofs >
<! sql print "<a href=\"example.sql\?sur=#sur&ofs=" ><! sql
 eval $ofs - 10 ><! sql print "\">">Prev</a>
<! sql else >
Prev
<! sql endif >
<! sql if $NUM_ROWS = 10 >
<! sql print "<a href=\"example.sql\?sur=#sur&ofs=" ><! sql
 eval $ofs + 10 ><! sql print "\">">Next</a>
<! sql else >
Next
<! sql endif >
</center>
<! sql endif >
<! sql free q1 >
<! sql query "end" >
<p>
<center><em>Page produced by WWW-SQL</em></center>
</body>
</html>
