include ../../make.env

PACKAGE_DIR=$(BIN_PACKAGE_DIR)/implementation

all:
	$(MAKE) -C tpl_cgi

install: dummy
	$(MAKE) -C tpl_cgi install

clean: dummy
	$(MAKE) -C tpl_cgi clean

uninstall: dummy
	$(MAKE) -C tpl_cgi uninstall

package: dummy
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)/tpl_cgi
	cp -f Makefile $(PACKAGE_DIR)/tpl_cgi
	cp -f configure $(PACKAGE_DIR)/tpl_cgi
	$(MAKE) -C tpl_cgi package PACKAGE_DIR=$(PACKAGE_DIR)/tpl_cgi

dummy:
