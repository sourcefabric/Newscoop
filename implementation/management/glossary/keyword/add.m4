INCLUDE_PHP_LIB(<*$ADMIN_DIR/glossary/keyword*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageDictionary*>)

B_HEAD
	X_TITLE(<*Add keyword infotype*>)
<?php  if ($access == 0) { ?>dnl
        X_AD(<*You do not have the right to add keyword infotypes.*>)
<?php  }
    query ("SELECT Id, Name FROM Classes WHERE 1=0", 'q_cls');
?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Keyword');
    todefnum('Language');
?>dnl
B_HEADER(<*Add keyword infotype*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Keyword infotype*>, <*glossary/keyword/?Keyword=<?php  print encURL($Keyword); ?>&Language=<?php  print encURL($Language); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Glossary*>, <*glossary/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Keyword FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language", 'q_dict');
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
    fetchRow($q_dict);
    fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Keyword*>, <*<?php  pgetHVar($q_dict,'Keyword'); ?>*>)
X_CURRENT(<*Language*>, <*<?php  pgetHVar($q_lang,'Name'); ?>*>)
E_CURRENT

<?php 
    query ("SELECT Id, Name FROM Classes WHERE IdLanguage=$Language", 'q_cls');
    if ($NUM_ROWS) { ?>dnl
<P>
B_DIALOG(<*Add keyword infotype*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Infotype*>)
	    <SELECT NAME="cClass" SIZE="5" class="input_select">
<?php 
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) { 
	fetchRow($q_cls);
	query ("SELECT COUNT(*) FROM KeywordClasses WHERE IdDictionary=$Keyword AND IdClasses=".getVar($q_cls,'Id')." AND IdLanguage=$Language", 'q_kwdcls');
	fetchRowNum($q_kwdcls);
	if (getNumVar($q_kwdcls,0) == 0)
		pcomboVar(getVar($q_cls,'Id'),'',getVar($q_cls,'Name'));
    }
?>dnl
	    </SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="Keyword" VALUE="<?php  pencHTML($Keyword); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  pencHTML($Language); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/glossary/keyword/?Keyword=<?php  pencURL($Keyword); ?>&Language=<?php  pencURL($Language); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No infotypes available.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

