export INSTALL_DIR2=$(INSTALL_DIR)/classes

all: index.xql add.xql do_add.xql del.xql do_del.xql translate.xql do_translate.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: all
	mkdir -p $(INSTALL_DIR2)
	cp *.xql $(INSTALL_DIR2)/

clean:
	rm -f *.xql *~

package: all
	mkdir -p $(PACKAGE_DIR)/classes
	cp -f Makefile $(PACKAGE_DIR)/classes
	cp -f *.xql $(PACKAGE_DIR)/classes
