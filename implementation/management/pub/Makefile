export PRIV_DIR2=$(PRIV_DIR)/pub

MY_TARGETS=index.php add.php do_add.php del.php do_del.php edit.php do_edit.php deftime.php countryadd.php do_countryadd.php deldeftime.php do_deldeftime.php editdeftime.php do_editdeftime.php add_article.php

subsystem: all
	$(MAKE) -C issues

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.php $(PRIV_DIR2)/
	$(MAKE) -C issues install

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(MY_TARGETS); fi
	$(MAKE) -C issues clean

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/pub
	cp -f Makefile $(PACKAGE_DIR)/pub
	cp -f *.php $(PACKAGE_DIR)/pub
	$(MAKE) -C issues package PACKAGE_DIR=$(PACKAGE_DIR)/pub
