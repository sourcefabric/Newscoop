export PRIV_DIR4=$(PRIV_DIR3)/sections

subsystem: all
	$(MAKE) -C articles

all: index.xql add.xql do_add.xql del.xql do_del.xql add_article.xql edit.xql do_edit.xql

%.xql: %.m4 ../../../macros.m4
	m4 ../../../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR4)
	cp -f *.xql $(PRIV_DIR4)/
	$(MAKE) -C articles install

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f *.xql; fi
	$(MAKE) -C articles clean

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/sections
	cp -f Makefile $(PACKAGE_DIR)/sections
	cp -f *.xql $(PACKAGE_DIR)/sections
	$(MAKE) -C articles package PACKAGE_DIR=$(PACKAGE_DIR)/sections
