
all:
	$(MAKE) -C management
	$(MAKE) -C parser
	$(MAKE) -C mailnotify
	$(MAKE) -C java
	$(MAKE) -C search
	$(MAKE) -C tpl_cgi

install:
	$(MAKE) -C management install
	$(MAKE) -C parser install
	$(MAKE) -C mailnotify install
	$(MAKE) -C java install
	$(MAKE) -C search install
	$(MAKE) -C tpl_cgi install
	$(MAKE) -C database install
	chown $(HTTP_USER).$(HTTP_GROUP) $(CGI_BIN) -R
	chmod 750 $(CGI_BIN) -R

clean:
	$(MAKE) -C management clean
	$(MAKE) -C parser clean
	$(MAKE) -C mailnotify clean
	$(MAKE) -C java clean
	$(MAKE) -C search clean
	$(MAKE) -C tpl_cgi clean

uninstall: dummy
	$(MAKE) -C management uninstall
	$(MAKE) -C parser uninstall
	$(MAKE) -C mailnotify uninstall
	$(MAKE) -C java uninstall
	$(MAKE) -C search uninstall
	$(MAKE) -C tpl_cgi uninstall
	$(MAKE) -C database uninstall

package: dummy
	mkdir -p $(PACKAGE_DIR)/implementation
	cp -f Makefile $(PACKAGE_DIR)/implementation
	$(MAKE) -C database package PACKAGE_DIR=$(PACKAGE_DIR)/implementation
	$(MAKE) -C java package PACKAGE_DIR=$(PACKAGE_DIR)/implementation
	$(MAKE) -C mailnotify package PACKAGE_DIR=$(PACKAGE_DIR)/implementation
	$(MAKE) -C management package PACKAGE_DIR=$(PACKAGE_DIR)/implementation
	$(MAKE) -C parser package PACKAGE_DIR=$(PACKAGE_DIR)/implementation
	$(MAKE) -C search package PACKAGE_DIR=$(PACKAGE_DIR)/implementation
	$(MAKE) -C tpl_cgi package PACKAGE_DIR=$(PACKAGE_DIR)/implementation

dummy:
