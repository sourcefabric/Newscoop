all:
	$(MAKE) -C smtp_wrapper

install: dummy
	$(MAKE) -C smtp_wrapper install

clean: dummy
	rm -f Doxyfile smtp_wrapper.kdevelop.pcs smtp_wrapper.kdevses
	$(MAKE) -C smtp_wrapper clean

uninstall: dummy
	$(MAKE) -C smtp_wrapper uninstall

dummy:
