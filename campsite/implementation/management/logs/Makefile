export PRIV_DIR2=$(PRIV_DIR)/logs

MY_TARGETS=index.php

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
	mkdir -p $(PACKAGE_DIR)/logs
	cp -f Makefile $(PACKAGE_DIR)/logs
	cp -f *.php $(PACKAGE_DIR)/logs
