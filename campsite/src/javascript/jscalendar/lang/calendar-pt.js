// ** I18N

// Calendar pt language
// Author: Hugo Chasqueira, <hchasqueira@netcabo.pt>
// Encoding: UTF-8
// Distributed under the same terms as the calendar itself.

// For translators: please use UTF-8 if possible.  We strongly believe that
// Unicode is the answer to a real internationalized world.  Also please
// include your contact information in the header, as can be seen above.

// full day names
Calendar._DN = new Array
("Domingo",
 "Segunda",
 "Terça",
 "Quarta",
 "Quinta",
 "Sexta",
 "Sábado",
 "Domingo");

// Please note that the following array of short day names (and the same goes
// for short month names, _SMN) isn't absolutely necessary.  We give it here
// for exemplification on how one can customize the short day names, but if
// they are simply the first N letters of the full name you can simply say:
//
//   Calendar._SDN_len = N; // short day name length
//   Calendar._SMN_len = N; // short month name length
//
// If N = 3 then this is not needed either since we assume a value of 3 if not
// present, to be compatible with translation files that were written before
// this feature.

// short day names
Calendar._SDN = new Array
("Dom",
 "Seg",
 "Ter",
 "Qua",
 "Qui",
 "Sex",
 "Sáb",
 "Dom");

Calendar._FD = 1;

// full month names
Calendar._MN = new Array
("Janeiro",
 "Fevereiro",
 "Março",
 "Abril",
 "Maio",
 "Junho",
 "Julho",
 "Agosto",
 "Setembro",
 "Outubro",
 "Novembro",
 "Dezembro");

// short month names
Calendar._SMN = new Array
("Jan",
 "Fev",
 "Mar",
 "Abr",
 "Mai",
 "Jun",
 "Jul",
 "Ago",
 "Set",
 "Out",
 "Nov",
 "Dez");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "Sobre o calendário";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"Para obter a última versao, visite: http://www.dynarch.com/projects/calendar/\n" +
"Distribuído sobre a licença GNU LGPL.  Visite a página http://gnu.org/licenses/lgpl.html para detalhes." +
"\n\n--------------------------\n\n" +
"Para seleccionar uma data:\n" +
"- Use os botões « e » para seleccionar o ano;\n" +
"- Use os botões < e > para seleccionar o mês;\n" +
"- Mantenha o botão do rato premido, sobre qualquer um destes botões, para uma selecção rápida.";
Calendar._TT["ABOUT_TIME"] = "\n\n--------------------------\n\n" +
"Para seleccionar uma hora:\n" +
"- Clique sobre qualquer uma das partes da hora para a incrementar;\n" +
"- ou faça shift-clique para a decrementar.\n" +
"- Clique e arraste o rato horizontalmente para uma variação rápida.";

Calendar._TT["PREV_YEAR"] = "Ano ant. (manter para menu)";
Calendar._TT["PREV_MONTH"] = "Mês ant. (manter para menu)";
Calendar._TT["GO_TODAY"] = "Hoje";
Calendar._TT["NEXT_MONTH"] = "Mês seg. (manter para menu)";
Calendar._TT["NEXT_YEAR"] = "Ano seg. (manter para menu)";
Calendar._TT["SEL_DATE"] = "Seleccione uma data";
Calendar._TT["DRAG_TO_MOVE"] = "Arraste para mover";
Calendar._TT["PART_TODAY"] = " (hoje)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "Mostrar este dia primeiro";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "Fechar";
Calendar._TT["TODAY"] = "Hoje";
Calendar._TT["TIME_PART"] = "(shift-)Clique para mudar o valor";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%A, %e %B";

Calendar._TT["WK"] = "sm";
Calendar._TT["TIME"] = "Hora:";
