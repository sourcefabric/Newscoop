export PRIV_DIR2=$(PRIV_DIR)/country

subsystem: all

all: index.xql add.xql do_add.xql del.xql do_del.xql edit.xql do_edit.xql translate.xql do_translate.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.xql $(PRIV_DIR2)/

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f *.xql; fi

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/country
	cp -f Makefile $(PACKAGE_DIR)/country
	cp -f *.xql $(PACKAGE_DIR)/country
