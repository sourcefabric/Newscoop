export PRIV_DIR3=$(PRIV_DIR2)/subscriptions

subsystem: all
	$(MAKE) -C sections

all: index.xql add.xql do_add.xql status.xql do_status.xql del.xql do_del.xql topay.xql do_topay.xql

%.xql: %.m4 ../../macros.m4
	m4 ../../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR3)
	cp -f *.xql $(PRIV_DIR3)/
	$(MAKE) -C sections install

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f *.xql; fi
	$(MAKE) -C sections clean

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/subscriptions
	cp -f Makefile $(PACKAGE_DIR)/subscriptions
	cp -f *.xql $(PACKAGE_DIR)/subscriptions
	$(MAKE) -C sections package PACKAGE_DIR=$(PACKAGE_DIR)/subscriptions

dummy:
