export PRIV_DIR2=$(PRIV_DIR)/languages

MY_TARGETS=index.php add.php do_add.php del.php do_del.php modify.php do_modify.php

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
	mkdir -p $(PACKAGE_DIR)/languages
	cp -f Makefile $(PACKAGE_DIR)/languages
	cp -f *.php $(PACKAGE_DIR)/languages
