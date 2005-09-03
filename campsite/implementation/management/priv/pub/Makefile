export PRIV_DIR2=$(PRIV_COMMON_DIR)/pub

MY_TARGETS=index.php add.php do_add.php del.php do_del.php edit.php do_edit.php deftime.php countryadd.php do_countryadd.php deldeftime.php do_deldeftime.php editdeftime.php do_editdeftime.php add_article.php aliases.php add_alias.php do_add_alias.php edit_alias.php do_edit_alias.php del_alias.php do_del_alias.php

subsystem: all

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	$(M4) ../macros.m4 $< >$@

install: all
	mkdir -p $(PRIV_DIR2)
	cp -f *.php $(PRIV_DIR2)/

clean:
	rm -f *~ $(MY_TARGETS)
