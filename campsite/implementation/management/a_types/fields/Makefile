export INSTALL_DIR3=$(INSTALL_DIR2)/fields

all: index.xql add.xql do_add.xql del.xql do_del.xql

%.xql: %.m4 ../../macros.m4
	m4 ../../macros.m4 $< >$@

install: all
	mkdir -p $(INSTALL_DIR3)
	cp *.xql $(INSTALL_DIR3)/

clean:
	rm -f *.xql *~

package: all
	mkdir -p $(PACKAGE_DIR)/fields
	cp -f Makefile $(PACKAGE_DIR)/fields
	cp -f *.xql $(PACKAGE_DIR)/fields
