export INSTALL_DIR3=$(INSTALL_DIR2)/issues

subsystem: all
	$(MAKE) -C sections

all: index.xql qadd.xql add_new.xql do_add_new.xql add_prev.xql do_add_prev.xql del.xql do_del.xql set.xql translate.xql do_translate.xql preview.xql status.xql do_status.xql empty.xql add_article.xql edit.xql do_edit.xql

%.xql: %.m4 ../../macros.m4
	m4 ../../macros.m4 $< >$@

install: all
	mkdir -p $(INSTALL_DIR3)
	cp *.xql $(INSTALL_DIR3)/
	$(MAKE) -C sections install

clean:
	rm -f *.xql *~
	$(MAKE) -C sections clean

package: all
	mkdir -p $(PACKAGE_DIR)/issues
	cp -f Makefile $(PACKAGE_DIR)/issues
	cp -f *.xql $(PACKAGE_DIR)/issues
	$(MAKE) -C sections package PACKAGE_DIR=$(PACKAGE_DIR)/issues
