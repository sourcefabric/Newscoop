#!/usr/bin/env python

IN_FILE = 'country_codes.txt'
OUT_FILE_PHP = 'country_codes.php'
OUT_FILE_JS = 'country_codes.js'

def arrayize_cc_list(in_name, out_name_php, out_name_js):
    in_fh = open(in_name)
    out_fh_php = open(out_name_php, 'w')
    out_fh_php.write("<?php\n$country_codes_alpha_2 = array(\n")

    js_names = []
    js_values = []

    for line in in_fh.readlines():
        line = line.strip()
        if 0 == len(line):
            continue
        if "#" == line[0]:
            continue
        line_list = line.split("\t")
        cc_name = line_list[0].replace('!', '/').replace("'", "\\'").strip()
        cc_code = line_list[1].replace("'", "\\'").strip()
        out_fh_php.write("'" + cc_name + "' => '" + cc_code + "',\n")
        js_names.append(cc_code)
        js_values.append(cc_name)

    out_fh_php.write(");\n?>\n")
    out_fh_php.close()
    in_fh.close()

    out_fh_js = open(out_name_js, 'w')

    out_fh_js.write("var country_codes_alpha_2 = [];\n")
    for ind in range(len(js_names)):
        out_fh_js.write("country_codes_alpha_2.push('" + js_names[ind] + "');\n")

    out_fh_js.write("var country_codes_alpha_2_countries = {};\n")
    for ind in range(len(js_names)):
        out_fh_js.write("country_codes_alpha_2_countries['" + js_names[ind] + "'] = '" + js_values[ind] + "';\n")

    out_fh_js.close()

if ('__main__' == __name__):
    arrayize_cc_list(IN_FILE, OUT_FILE_PHP, OUT_FILE_JS)

