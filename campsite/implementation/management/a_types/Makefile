export PRIV_DIR2=$(PRIV_DIR)/a_types

subsystem: all
	$(MAKE) -C fields

all: index.xql add.xql do_add.xql del.xql do_del.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.xql $(PRIV_DIR2)/
	$(MAKE) -C fields install

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f *.xql; fi
	$(MAKE) -C fields clean

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/a_types
	cp -f Makefile $(PACKAGE_DIR)/a_types
	cp -f *.xql $(PACKAGE_DIR)/a_types
	$(MAKE) -C fields package PACKAGE_DIR=$(PACKAGE_DIR)/a_types
