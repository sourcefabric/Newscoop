export INSTALL_DIR2=$(INSTALL_DIR)/logs

all: index.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: all
	mkdir -p $(INSTALL_DIR2)
	cp *.xql $(INSTALL_DIR2)/

clean:
	rm -f *.xql *~

package: all
	mkdir -p $(PACKAGE_DIR)/logs
	cp -f Makefile $(PACKAGE_DIR)/logs
	cp -f *.xql $(PACKAGE_DIR)/logs
