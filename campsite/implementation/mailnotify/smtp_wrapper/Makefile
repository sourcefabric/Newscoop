
all:
	$(MAKE) -C smtp_wrapper

install: dummy
	$(MAKE) -C smtp_wrapper install

clean: dummy
	$(MAKE) -C smtp_wrapper clean

uninstall: dummy
	$(MAKE) -C smtp_wrapper uninstall

package: dummy
	mkdir -p $(PACKAGE_DIR)/smtp_wrapper
	cp -f Makefile $(PACKAGE_DIR)/smtp_wrapper
	cp -f configure $(PACKAGE_DIR)/smtp_wrapper
	$(MAKE) -C smtp_wrapper package

dummy:
