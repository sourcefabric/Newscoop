export INSTALL_DIR3=$(INSTALL_DIR2)/subscriptions

subsystem: all
	$(MAKE) -C sections

all: index.xql add.xql do_add.xql status.xql do_status.xql del.xql do_del.xql topay.xql do_topay.xql

%.xql: %.m4 ../../macros.m4
	m4 ../../macros.m4 $< >$@

install: all
	mkdir -p $(INSTALL_DIR3)
	cp *.xql $(INSTALL_DIR3)/
	$(MAKE) -C sections install

clean:
	rm -f *.xql *~
	$(MAKE) -C sections clean

package: all
	mkdir -p $(PACKAGE_DIR)/subscriptions
	cp -f Makefile $(PACKAGE_DIR)/subscriptions
	cp -f *.xql $(PACKAGE_DIR)/subscriptions
	$(MAKE) -C sections package PACKAGE_DIR=$(PACKAGE_DIR)/subscriptions
