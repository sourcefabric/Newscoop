B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Menu})
E_HEAD

<!sql if $access>dnl
SET_ACCESS({mpa}, {ManagePub})
SET_ACCESS({muta}, {ManageUserTypes})
SET_ACCESS({mda}, {ManageDictionary})
SET_ACCESS({mca}, {ManageClasses})
SET_ACCESS({mcoa}, {ManageCountries})
SET_ACCESS({mata}, {ManageArticleTypes})
SET_ACCESS({mua}, {ManageUsers})
SET_ACCESS({mla}, {ManageLanguages})
SET_ACCESS({mta}, {ManageTempl})
SET_ACCESS({vla}, {ViewLogs})

B_STYLE
E_STYLE

B_MBODY

B_MENU
    X_MENU_ITEM({Home}, {home.xql})
    X_MENU_ITEM({Quick Menu}, {}, {window.open('X_ROOT/popup/', 'fpopup', 'menu=no,width=500,height=410'); return false;})
    X_MENU_BAR
    X_MENU_ITEM({Publications}, {pub/})
<!sql if $mta>dnl
    X_MENU_ITEM({Templates}, {/look/})
<!sql endif>dnl
<!sql if $mua>dnl
    X_MENU_ITEM({Users}, {users/})
<!sql endif>dnl
<!sql if $muta>dnl
    X_MENU_ITEM({User Types}, {u_types/})
<!sql endif>dnl
<!sql if $mata>dnl
    X_MENU_ITEM({Article Types}, {a_types/})
<!sql endif>dnl
<!sql if $mcoa>dnl
    X_MENU_ITEM({Countries}, {country/})
<!sql endif>dnl
<!sql if $mla>dnl
    X_MENU_ITEM({Languages}, {languages/})
<!sql endif>dnl
<!sql if $mda>dnl
    X_MENU_ITEM({Dictionary}, {dictionary/})
<!sql endif>dnl
<!sql if $mca>dnl
    X_MENU_ITEM({Classes}, {classes/})
<!sql endif>dnl
<!sql if $vla>dnl
    X_MENU_ITEM({Logs}, {logs/})
<!sql endif>dnl
    X_MENU_BAR
    X_MENU_ITEM({Logout}, {logout.xql})
E_MENU

E_MBODY
<!sql endif>dnl

E_DATABASE
E_HTML
