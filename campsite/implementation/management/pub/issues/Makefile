export PRIV_DIR3=$(PRIV_DIR2)/issues

MY_TARGETS=index.php qadd.php add_new.php do_add_new.php add_prev.php do_add_prev.php del.php do_del.php set.php translate.php do_translate.php preview.php status.php do_status.php empty.php add_article.php edit.php do_edit.php

subsystem: all
	$(MAKE) -C sections

all: $(MY_TARGETS)

%.php: %.m4 ../../macros.m4
	m4 ../../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR3)
	cp -f *.php $(PRIV_DIR3)/
	$(MAKE) -C sections install

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(MY_TARGETS); fi
	$(MAKE) -C sections clean

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/issues
	cp -f Makefile $(PACKAGE_DIR)/issues
	cp -f *.php $(PACKAGE_DIR)/issues
	$(MAKE) -C sections package PACKAGE_DIR=$(PACKAGE_DIR)/issues
