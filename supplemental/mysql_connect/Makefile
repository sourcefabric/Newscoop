#include ../../make.env

all:
	$(MAKE) -C mysql_connect

install:
	$(MAKE) -C mysql_connect install

clean:
	$(MAKE) -C mysql_connect clean

uninstall: dummy
	$(MAKE) -C mysql_connect uninstall

package: dummy
	mkdir -p $(PACKAGE_DIR)/mysql_connect
	cp -f configure $(PACKAGE_DIR)/mysql_connect
	cp -f Makefile $(PACKAGE_DIR)/mysql_connect
	$(MAKE) -C mysql_connect package PACKAGE_DIR=$(PACKAGE_DIR)/mysql_connect

dummy:
