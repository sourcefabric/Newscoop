export PRIV_DIR2=$(PRIV_DIR)/languages

all: index.xql add.xql do_add.xql del.xql do_del.xql modify.xql do_modify.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.xql $(PRIV_DIR2)/

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f *.xql; fi

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/languages
	cp -f Makefile $(PACKAGE_DIR)/languages
	cp -f *.xql $(PACKAGE_DIR)/languages
