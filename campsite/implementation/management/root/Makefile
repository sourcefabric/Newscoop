MY_TARGETS=index.php enter.php password.php sub_pub.php dictionary.php robots.php jump.php

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

%.txt: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	cp -f *.php $(HTML_DIR)/
	cp -f db_connect.php $(HTML_DIR)/

clean:
	rm -f *~ db_connect.php .env_vars
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(MY_TARGETS); fi

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/root
	cp -f Makefile $(PACKAGE_DIR)/root
	cp -f configure $(PACKAGE_DIR)/root
	cp -f *.php $(PACKAGE_DIR)/root

dummy:
