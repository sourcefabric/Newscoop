export PRIV_DIR2=$(PRIV_DIR)/popup

all: index.xql empty.xql lang.xql i1.xql pub.xql i2.xql iss.xql i3.xql sect.xql i4.xql art.xql img.xql

popup.m4: popup.in
	cat popup.in >popup.m4

%.xql: %.m4 ../macros.m4 popup.m4
	m4 ../macros.m4 popup.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.xql $(PRIV_DIR2)/

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f *.xql; fi

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/popup
	cp -f Makefile $(PACKAGE_DIR)/popup
	cp -f *.xql $(PACKAGE_DIR)/popup
