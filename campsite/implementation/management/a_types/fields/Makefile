export PRIV_DIR3=$(PRIV_DIR2)/fields

all: index.xql add.xql do_add.xql del.xql do_del.xql

%.xql: %.m4 ../../macros.m4
	m4 ../../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR3)
	cp -f *.xql $(PRIV_DIR3)/

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f *.xql; fi

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/fields
	cp -f Makefile $(PACKAGE_DIR)/fields
	cp -f *.xql $(PACKAGE_DIR)/fields
