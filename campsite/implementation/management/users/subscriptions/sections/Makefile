export PRIV_DIR4=$(PRIV_DIR3)/sections

all: index.xql add.xql do_add.xql del.xql do_del.xql change.xql do_change.xql pay.xql do_pay.xql

%.xql: %.m4 ../../../macros.m4
	m4 ../../../macros.m4 $< >$@

install: all
	mkdir -p $(PRIV_DIR4)
	cp -f *.xql $(PRIV_DIR4)/

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f *.xql; fi

package: all
	mkdir -p $(PACKAGE_DIR)/sections
	cp -f Makefile $(PACKAGE_DIR)/sections
	cp -f *.xql $(PACKAGE_DIR)/sections
