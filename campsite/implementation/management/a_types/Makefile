export INSTALL_DIR2=$(INSTALL_DIR)/a_types

subsystem: all
	$(MAKE) -C fields

all: index.xql add.xql do_add.xql del.xql do_del.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: all
	mkdir -p $(INSTALL_DIR2)
	cp *.xql $(INSTALL_DIR2)/
	$(MAKE) -C fields install

clean:
	rm -f *.xql *~
	$(MAKE) -C fields clean

package: all
	mkdir -p $(PACKAGE_DIR)/a_types
	cp -f Makefile $(PACKAGE_DIR)/a_types
	cp -f *.xql $(PACKAGE_DIR)/a_types
	$(MAKE) -C fields package PACKAGE_DIR=$(PACKAGE_DIR)/a_types
