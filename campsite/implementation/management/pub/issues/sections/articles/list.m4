B_DATABASE{}dnl
<!sql setdefault IdLanguage 0>dnl
<!sql setdefault IdPublication 0>dnl
<!sql setdefault NrIssue 0>dnl
<!sql setdefault NrSection 0>dnl
<!sql if $IdLanguage>dnl
<!sql if $IdPublication>dnl
<!sql if $NrIssue>dnl
<!sql if $NrSection>dnl
<!sql query "SELECT Number, Name FROM Articles WHERE IdPublication=?IdPublication AND NrIssue=?NrIssue AND NrSection=?NrSection AND IdLanguage=?IdLanguage" q_art>dnl
<!sql print_rows q_art "~q_art.Number\n~q_art.Name\n">dnl
<!sql free q_sect>dnl
<!sql else>dnl
<!sql query "SELECT Number, Name FROM Sections WHERE IdPublication=?IdPublication AND NrIssue=?NrIssue AND IdLanguage=?IdLanguage" q_sect>dnl
<!sql print_rows q_sect "~q_sect.Number\n~q_sect.Name\n">dnl
<!sql free q_sect>dnl
<!sql endif>dnl
<!sql else>dnl
<!sql query "SELECT Number, Name FROM Issues WHERE IdPublication=?IdPublication AND IdLanguage=?IdLanguage ORDER BY Number DESC" q_iss>dnl
<!sql print_rows q_iss "~q_iss.Number\n~q_iss.Name\n">dnl
<!sql free q_iss>dnl
<!sql endif>dnl
<!sql else>dnl
<!sql query "SELECT Id, Name FROM Publications ORDER BY Id" q_pub>dnl
<!sql print_rows q_pub "~q_pub.Id\n~q_pub.Name\n">dnl
<!sql free q_pub>dnl
<!sql endif>dnl
<!sql else>dnl
<!sql query "SELECT Id, Name FROM Languages ORDER BY Id" q_lang>dnl
<!sql print_rows q_lang "~q_lang.Id\n~q_lang.Name\n">dnl
<!sql free q_lang>dnl
<!sql endif>dnl
E_DATABASE{}dnl