export INSTALL_DIR5=$(INSTALL_DIR4)/articles

subsystem: all
	$(MAKE) -C images

all: index.xql add.xql do_add.xql del.xql do_del.xql edit.xql edit_b.xql edit_t.xql preview.xql status.xql do_status.xql upload.xql translate.xql do_translate.xql empty.xql list.xql do_edit.xql do_edit_t.xql do_unlock.xql

%.xql: %.m4 ../../../../macros.m4
	m4 ../../../../macros.m4 $< >$@

install: all
	mkdir -p $(INSTALL_DIR5)
	cp -f *.xql $(INSTALL_DIR5)/
	$(MAKE) -C images install

clean:
	rm -f *.xql *~
	$(MAKE) -C images clean

package: all
	mkdir -p $(PACKAGE_DIR)/articles
	cp -f Makefile $(PACKAGE_DIR)/articles
	cp -f *.xql $(PACKAGE_DIR)/articles
	$(MAKE) -C images package PACKAGE_DIR=$(PACKAGE_DIR)/articles
