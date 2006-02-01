include ../../make.env

all:
	$(MAKE) -C parser

install: dummy
	$(MAKE) -C parser install

clean: dummy
	rm -f Doxyfile parser.kdevelop.pcs parser.kdevses
	$(MAKE) -C parser clean

uninstall: dummy
	$(MAKE) -C parser uninstall

dummy:
