all: index.xql enter.xql password.xql sub_pub.xql dictionary.xql robots.xql jump.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

%.txt: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: all
	cp *.xql $(ROOT_DIR)/

clean:
	rm -f *.xql *~

package: all
	mkdir -p $(PACKAGE_DIR)/root
	cp -f Makefile $(PACKAGE_DIR)/root
	cp -f *.xql $(PACKAGE_DIR)/root
