export PRIV_DIR2=$(PRIV_DIR)/pub

subsystem: all
	$(MAKE) -C issues

all: index.xql add.xql do_add.xql del.xql do_del.xql edit.xql do_edit.xql deftime.xql countryadd.xql do_countryadd.xql deldeftime.xql do_deldeftime.xql editdeftime.xql do_editdeftime.xql add_article.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.xql $(PRIV_DIR2)/
	$(MAKE) -C issues install

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f *.xql; fi
	$(MAKE) -C issues clean

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/pub
	cp -f Makefile $(PACKAGE_DIR)/pub
	cp -f *.xql $(PACKAGE_DIR)/pub
	$(MAKE) -C issues package PACKAGE_DIR=$(PACKAGE_DIR)/pub
