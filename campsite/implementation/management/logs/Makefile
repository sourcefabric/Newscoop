export PRIV_DIR2=$(PRIV_DIR)/logs

all: index.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.xql $(PRIV_DIR2)/

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f *.xql; fi

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/logs
	cp -f Makefile $(PACKAGE_DIR)/logs
	cp -f *.xql $(PACKAGE_DIR)/logs
