export INSTALL_DIR2=$(INSTALL_DIR)/dictionary

subsystem: all
	$(MAKE) -C keyword

all: index.xql add.xql do_add.xql translate.xql do_translate.xql del.xql do_del.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: all
	mkdir -p $(INSTALL_DIR2)
	cp *.xql $(INSTALL_DIR2)/
	$(MAKE) -C keyword install

clean:
	rm -f *.xql *~
	$(MAKE) -C keyword clean

package: all
	mkdir -p $(PACKAGE_DIR)/dictionary
	cp -f Makefile $(PACKAGE_DIR)/dictionary
	cp -f *.xql $(PACKAGE_DIR)/dictionary
	$(MAKE) -C keyword package PACKAGE_DIR=$(PACKAGE_DIR)/dictionary
