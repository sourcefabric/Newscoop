export PRIV_DIR3=$(PRIV_DIR2)/subscriptions

MY_TARGETS=index.php add.php do_add.php status.php do_status.php del.php do_del.php topay.php do_topay.php

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
	mkdir -p $(PACKAGE_DIR)/subscriptions
	cp -f Makefile $(PACKAGE_DIR)/subscriptions
	cp -f *.php $(PACKAGE_DIR)/subscriptions
	$(MAKE) -C sections package PACKAGE_DIR=$(PACKAGE_DIR)/subscriptions

dummy:
