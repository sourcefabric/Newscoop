export PRIV_DIR4=$(PRIV_DIR3)/sections

MY_TARGETS=index.php add.php do_add.php del.php do_del.php add_article.php edit.php do_edit.php

subsystem: all
	$(MAKE) -C articles

all: $(MY_TARGETS)

%.php: %.m4 ../../../macros.m4
	m4 ../../../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR4)
	cp -f *.php $(PRIV_DIR4)/
	$(MAKE) -C articles install

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(MY_TARGETS); fi
	$(MAKE) -C articles clean

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/sections
	cp -f Makefile $(PACKAGE_DIR)/sections
	cp -f *.php $(PACKAGE_DIR)/sections
	$(MAKE) -C articles package PACKAGE_DIR=$(PACKAGE_DIR)/sections
