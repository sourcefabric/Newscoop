include ../../make.env

ALL=$(shell if [ "$(PACKAGE_TYPE)" = "source" ]; then echo all; fi)

PACKAGE_DIR=$(BIN_PACKAGE_DIR)/implementation

all:
	$(MAKE) -C parser

install: dummy
	$(MAKE) -C parser install

clean: dummy
	$(MAKE) -C parser clean

uninstall: dummy
	$(MAKE) -C parser uninstall

package: dummy
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir $(PACKAGE_DIR)/parser
	cp -f configure $(PACKAGE_DIR)/parser
	cp -f Makefile $(PACKAGE_DIR)/parser
	$(MAKE) -C parser package PACKAGE_DIR=$(PACKAGE_DIR)/parser

dummy:
