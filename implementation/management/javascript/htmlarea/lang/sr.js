// I18N constants

// LANG: "sh", ENCODING: UTF-8 | ISO-8859-5
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
		bold:           "Масно",
		italic:         "Курзив",
		underline:      "Подвучено",
		strikethrough:  "Прецртано",
		subscript:      "Индекс-текст",
		superscript:    "Експонент-текст",
		justifyleft:    "Равнање улево",
		justifycenter:  "Равнање по симетрали",
		justifyright:   "Равнање удесно",
		justifyfull:    "Пуно равнање",
		orderedlist:    "Листа са редним бројевима",
		unorderedlist:  "Листа са симболима",
		outdent:        "Смањи увлачење",
		indent:         "Повећај увлачење",
		forecolor:      "Боја слова",
		hilitecolor:    "Боја позадине",
		horizontalrule: "Хоризонтална линија",
		createlink:     "додај веб линк",
		insertimage:    "додај/промени слику",
		inserttable:    "Убаци табелу",
		htmlmode:       "Пребаци на ХТМЛ код",
		popupeditor:    "Повећај едитор",
		about:          "О овом едитору",
		showhelp:       "Помоћ при коришћењу едитора",
		textindicator:  "Важећи стил",
		undo:           "Поништава последњу радњу",
		redo:           "Враћа последњу радњу",
		cut:            "Исеци изабрано",
		copy:           "Копирај изабрано",
		paste:          "Залепи из клипборда",
		lefttoright:    "Правац с десна на лево"
	},

	buttons: {
		"ok":           "OK",
		"cancel":       "Поништи"
	},

	msg: {
		"Path":         "Путања",
		"TEXT_MODE":    "Налазите се у ТЕКСТ режиму.  Користите [<>] дугме за повратак на ШВТИД (WYSIWYG).",

		"IE-sucks-full-screen" :
		// translate here
		"Рад у едитору пуне величине екрана прави проблеме у Интернет Експлореру, " +
		"а због грешке коју не можемо да заобиђемо.  Може доћи до погрешног приказа, " +
		"мањка функција едитора и/или неконтролисаног рушења едитора.  Ако је ваш систем Windows 9x " +
		"упозорили смо вас.  Притисните OK ако још увек желите да пробате рад у едитору пуне величине екрана.",

		"Moz-Clipboard" :
		"Скриптови без привилегије не могу програмски користити функције Исеци/Копирај/Залепи " +
		"из безбедносних разлога.  Кликните OK да видите техничко објашњење на mozilla.org " +
		"где можете сазнати како да ипак дозволите скрипту да приступи клипборду."
	},

	dialogs: {
		"Cancel"                                            : "Поништи",
		"Insert/Modify Link"                                : "додај/промени линк",
		"New window (_blank)"                               : "Новом прозору (_blank)",
		"None (use implicit)"                               : "користи подразумевано",
		"OK"                                                : "OK",
		"Other"                                             : "Друго",
		"Same frame (_self)"                                : "Исти фрејм (_self)",
		"Target:"                                           : "Отвори у:",
		"Title (tooltip):"                                  : "Назив (tooltip):",
		"Top frame (_top)"                                  : "Главни фрејм (_top)",
		"URL:"                                              : "УРЛ:",
		"You must enter the URL where this link points to"  : "Морате унети УРЛ на који води овај линк"
	}
};
