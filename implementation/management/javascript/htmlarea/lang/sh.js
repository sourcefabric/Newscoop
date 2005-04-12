// I18N constants

// LANG: "sh", ENCODING: UTF-8 | ISO-8859-2
// Author: Ljuba Ranković, http://www.rankovic.net/ljubar

// FOR TRANSLATORS:
//
//   1. PLEASE PUT YOUR CONTACT INFO IN THE ABOVE LINE
//      (at least a valid email address)
//
//   2. PLEASE TRY TO USE UTF-8 FOR ENCODING;
//      (if this is not possible, please include a comment
//       that states what encoding is necessary.)

HTMLArea.I18N = {

	// the following should be the filename without .js extension
	// it will be used for automatically load plugin language.
	lang: "sh",

	tooltips: {
		bold:           "Masno",
		italic:         "Kurziv",
		underline:      "Podvučeno",
		strikethrough:  "Precrtano",
		subscript:      "Indeks-tekst",
		superscript:    "Eksponent-tekst",
		justifyleft:    "Ravnanje ulevo",
		justifycenter:  "Ravnanje po simetrali",
		justifyright:   "Ravnanje udesno",
		justifyfull:    "Puno ravnanje",
		orderedlist:    "Lista sa rednim brojevima",
		unorderedlist:  "Lista sa simbolima",
		outdent:        "smanji uvlačenje",
		indent:         "Povećaj uvlačenje",
		forecolor:      "Boja slova",
		hilitecolor:    "Boja pozadine",
		horizontalrule: "Horizontalna linija",
		createlink:     "Dodaj web link",
		insertimage:    "Dodaj/promeni sliku",
		inserttable:    "Ubaci tabelu",
		htmlmode:       "Prebaci na HTML kod",
		popupeditor:    "Povećaj editor",
		about:          "O ovom editoru",
		showhelp:       "Pomoć pri korišćenju editora",
		textindicator:  "Važeći stil",
		undo:           "Poništava poslednju radnju",
		redo:           "Vraća poslednju radnju",
		cut:            "Iseci izabrano",
		copy:           "Kopiraj izabrano",
		paste:          "Zalepi iz klipborda",
		lefttoright:    "Pravac s desna nalevo"
	},

	buttons: {
		"ok":           "OK",
		"cancel":       "Poništi"
	},

	msg: {
		"Path":         "Putanja",
		"TEXT_MODE":    "Nalazite se u TEXT režimu.  Koristite [<>] dugme za povratak na WYSIWYG.",

		"IE-sucks-full-screen" :
		// translate here
		"Rad u editoru pune veličine ekrana pravi probleme u Internet Exploreru, " +
		"a zbog greške koju ne možemo da zaobiđemo.  Može doći do pogrešnog prikaza, " +
		"manjka funkcija editora i/ili nekontrolisanog rušenja editora.  ako je vaš sistem Windows 9x " +
		"upozorili smo vas.  Pritisnite OK ako još uvek želite da probate rad u editoru pune veličine ekrana.",

		"Moz-Clipboard" :
		"Skriptovi bez privilegije ne mogu programski koristiti funkcije Iseci/Kopiraj/Zalepi " +
		"iz bezbednosnih razloga.  Kliknite OK da vidite tehničko objašnjenje na mozilla.org " +
		"gde možete saznati kako da ipak dozvolite skriptu da pristupi klipbordu."
	},

	dialogs: {
		"Cancel"                                            : "Poništi",
		"Insert/Modify Link"                                : "Dodaj/promeni Link",
		"New window (_blank)"                               : "Novom prozoru (_blank)",
		"None (use implicit)"                               : "koristi podrazumevano",
		"OK"                                                : "OK",
		"Other"                                             : "Drugo",
		"Same frame (_self)"                                : "Isti frejm (_self)",
		"Target:"                                           : "Otvori u:",
		"Title (tooltip):"                                  : "Naziv (tooltip):",
		"Top frame (_top)"                                  : "Glavni frejm (_top)",
		"URL:"                                              : "URL:",
		"You must enter the URL where this link points to"  : "Morate uneti URL na koji vodi ovaj link"
	}
};
