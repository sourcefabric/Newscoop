B_DATABASE
<!sql setdefault UserId 0><!sql if $UserId == ""><!sql set UserId 0><!sql endif>dnl
<!sql setdefault UserKey 0><!sql if $UserKey == ""><!sql set UserKey 0><!sql endif>dnl
<!sql setdefault IdPublication 0><!sql if $IdPublication == ""><!sql set IdPublication 0><!sql endif>dnl
<!sql setdefault NrIssue 0><!sql if $NrIssue == ""><!sql set NrIssue 0><!sql endif>dnl
<!sql setdefault NrSection 0><!sql if $NrSection == ""><!sql set NrSection 0><!sql endif>dnl
<!sql setdefault NrArticle 0><!sql if $NrArticle == ""><!sql set NrArticle 0><!sql endif>dnl
<!sql setdefault IdLanguage 0><!sql if $IdLanguage == ""><!sql set IdLanguage 0><!sql endif>dnl
<!sql setdefault Field "">dnl
<!sql setdefault Content "">dnl
<!sql query "SELECT COUNT(*) FROM Users WHERE Id=?UserId AND KeyId=?UserKey" Usr>dnl
<!sql if @Usr.0 != 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Type, LockUser FROM Articles WHERE IdPublication=?IdPublication AND NrIssue=?NrIssue AND NrSection=?NrSection AND Number=?NrArticle AND IdLanguage=?IdLanguage" q_art>dnl
<!sql if $NUM_ROWS>dnl
<!sql if @q_art.LockUser == $UserId>dnl
<!sql set AFFECTED_ROWS 0>dnl
<!sql query "UPDATE X?q_art.Type SET F?Field='?Content' WHERE NrArticle=?NrArticle AND IdLanguage=?IdLanguage">
<!sql if $AFFECTED_ROWS>dnl
TOLOK

<!sql else>dnl
TOLERR
<!sql endif>dnl
<!sql else>dnl
TOLLOCK

<!sql endif>dnl
<!sql else>dnl
TOLERR

<!sql endif>dnl
<!sql else>dnl
TOLERR

<!sql endif>dnl
E_DATABASE