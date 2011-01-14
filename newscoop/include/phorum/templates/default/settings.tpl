{! --- defines are used by the engine and vars are used by the template --- }

{! --- This is used to indent messages in threaded view --- }
{define indentstring &nbsp;&nbsp;&nbsp;}
{! --- How many px to indent for each level --- }
{define indentmultiplier 15}

{! --- This is used to load the message-bodies in the message-list for that template if set to 1 --- }
{define bodies_in_list 0}

{! --- This is the marker for messages in threaded view --- }

{define marker <img src="templates/default/images/carat.gif" border="0" width="8" height="8" alt="" style="vertical-align: middle;" />&nbsp;}

{! -- This is the image to use as a delete button for recipients in PM --- }

{var delete_image templates/default/images/delete.gif}

{! -- This is the image for the gauge bar to show how full the PM box is -- }

{var gauge_image templates/default/images/gauge.gif}

{! --- these are the colors used in the style sheet --- }

{! --- you can use them or replace them in the style sheet --- }



{! --- common body-colors --- }
{var bodybackground White}
{var defaulttextcolor Black}
{var backcolor White}
{var forumwidth 100%}
{var forumalign center}
{var newflagcolor #CC0000}
{var errorfontcolor Red}
{var okmsgfontcolor DarkGreen}

{! --- for the forum-list ... alternating colors --- }
{var altbackcolor #EEEEEE}
{var altlisttextcolor #000000}

{! --- common link-settings --- }
{var linkcolor #000099}
{var activelinkcolor #FF6600}
{var visitedlinkcolor #000099}
{var hoverlinkcolor #FF6600}

{! --- for the Navigation --- }
{var navbackcolor #EEEEEE}
{var navtextcolor #000000}
{var navhoverbackcolor #FFFFFF}
{var navhoverlinkcolor #FF6600}
{var navtextweight normal}
{var navfont Lucida Sans Unicode, Lucida Grande, Arial}
{var navfontsize 12px}

{! --- for the PhorumHead ... the list-header --- }
{var headerbackcolor #EEEEEE}
{var headertextcolor #000000}
{var headertextweight bold}
{var headerfont Lucida Sans Unicode, Lucida Grande, Arial}
{var headerfontsize 12px}



{var tablebordercolor #808080}

{var listlinecolor #F2F2F2}

{var listpagelinkcolor #707070}
{var listmodlinkcolor #707070}





{! --- You can set the table width globaly here ... ONLY tables, no divs are changed--- }
{var tablewidth 100%}
{var narrowtablewidth 600px}



{! --- Some font stuff --- }
{var defaultfont "Bitstream Vera Sans", "Lucida Sans Unicode", "Lucida Grande", Arial}
{var largefont "Bitstream Vera Sans", "Trebuchet MS", Verdana, Arial, sans-serif}
{var tinyfont "Bitstream Vera Sans", Arial, sans-serif}
{var fixedfont Lucida Console, Andale Mono, Courier New, Courier}
{var defaultfontsize 12px}
{var defaultboldfontsize 13px}
{var largefontsize 16px}
{var smallfontsize 11px}
{var tinyfontsize 10px}
