export INSTALL_DIR3=$(INSTALL_DIR2)/keyword

all: index.xql add.xql do_add.xql del.xql do_del.xql edit.xql do_edit.xql

%.xql: %.m4 ../../macros.m4
	m4 ../../macros.m4 $< >$@

install: all
	mkdir -p $(INSTALL_DIR3)
	cp *.xql $(INSTALL_DIR3)/

clean:
	rm -f *.xql *~

package: all
	mkdir -p $(PACKAGE_DIR)/keyword
	cp -f Makefile $(PACKAGE_DIR)/keyword
	cp -f *.xql $(PACKAGE_DIR)/keyword
