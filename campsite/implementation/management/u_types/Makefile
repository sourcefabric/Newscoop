export PRIV_DIR2=$(PRIV_DIR)/u_types

MY_TARGETS=index.php add.php do_add.php del.php do_del.php access.php do_access.php

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.php $(PRIV_DIR2)/

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(MY_TARGETS); fi

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/u_types
	cp -f Makefile $(PACKAGE_DIR)/u_types
	cp -f *.php $(PACKAGE_DIR)/u_types

dummy:
