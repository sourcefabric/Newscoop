export PRIV_DIR2=$(PRIV_DIR)/a_types

MY_TARGETS=index.php add.php do_add.php del.php do_del.php

subsystem: all
	$(MAKE) -C fields

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.php $(PRIV_DIR2)/
	$(MAKE) -C fields install

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(MY_TARGETS); fi
	$(MAKE) -C fields clean

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/a_types
	cp -f Makefile $(PACKAGE_DIR)/a_types
	cp -f *.php $(PACKAGE_DIR)/a_types
	$(MAKE) -C fields package PACKAGE_DIR=$(PACKAGE_DIR)/a_types
