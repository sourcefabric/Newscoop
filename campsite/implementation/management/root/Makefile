all: index.xql enter.xql password.xql sub_pub.xql dictionary.xql robots.xql jump.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

%.txt: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	cp -f *.xql $(HTML_DIR)/

clean:
	rm -f *~ db_connect.xql .env_vars
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f *.xql; fi

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/root
	cp -f Makefile $(PACKAGE_DIR)/root
	cp -f configure $(PACKAGE_DIR)/root
	cp -f *.xql $(PACKAGE_DIR)/root

dummy:
