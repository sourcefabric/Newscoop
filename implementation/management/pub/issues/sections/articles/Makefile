export PRIV_DIR5=$(PRIV_DIR4)/articles

MY_TARGETS=index.php add.php do_add.php del.php do_del.php edit.php edit_b.php edit_b_ns.php edit_b_apple.php edit_t.php preview.php status.php do_status.php upload.php translate.php do_translate.php empty.php list.php do_edit.php do_edit_t.php do_unlock.php

subsystem: all
	$(MAKE) -C images
	$(MAKE) -C topics

all: $(MY_TARGETS)

%.php: %.m4 ../../../../macros.m4
	m4 ../../../../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR5)
	cp -f *.php $(PRIV_DIR5)/
	$(MAKE) -C images install
	$(MAKE) -C topics install

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(MY_TARGETS); fi
	$(MAKE) -C images clean
	$(MAKE) -C topics clean

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/articles
	cp -f Makefile $(PACKAGE_DIR)/articles
	cp -f *.php $(PACKAGE_DIR)/articles
	$(MAKE) -C images package PACKAGE_DIR=$(PACKAGE_DIR)/articles
	$(MAKE) -C topics package PACKAGE_DIR=$(PACKAGE_DIR)/articles
