export PRIV_DIR2=$(PRIV_DIR)/u_types

all: index.xql add.xql do_add.xql del.xql do_del.xql access.xql do_access.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.xql $(PRIV_DIR2)/

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f *.xql; fi

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/u_types
	cp -f Makefile $(PACKAGE_DIR)/u_types
	cp -f *.xql $(PACKAGE_DIR)/u_types

dummy:
