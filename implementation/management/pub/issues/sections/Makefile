export INSTALL_DIR4=$(INSTALL_DIR3)/sections

subsystem: all
	$(MAKE) -C articles

all: index.xql add.xql do_add.xql del.xql do_del.xql add_article.xql edit.xql do_edit.xql

%.xql: %.m4 ../../../macros.m4
	m4 ../../../macros.m4 $< >$@

install: all
	mkdir -p $(INSTALL_DIR4)
	cp *.xql $(INSTALL_DIR4)/
	$(MAKE) -C articles install

clean:
	rm -f *.xql *~
	$(MAKE) -C articles clean

package: all
	mkdir -p $(PACKAGE_DIR)/sections
	cp -f Makefile $(PACKAGE_DIR)/sections
	cp -f *.xql $(PACKAGE_DIR)/sections
	$(MAKE) -C articles package PACKAGE_DIR=$(PACKAGE_DIR)/sections
