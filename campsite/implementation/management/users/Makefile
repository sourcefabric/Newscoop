export PRIV_DIR2=$(PRIV_DIR)/users

subsystem: all
	$(MAKE) -C subscriptions

all: index.xql add.xql do_add.xql del.xql do_del.xql chpwd.xql do_chpwd.xql access.xql do_access.xql info.xql do_info.xql passwd.xql do_passwd.xql ipaccesslist.xql ipadd.xql do_ipadd.xql ipdel.xql do_ipdel.xql

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.xql $(PRIV_DIR2)/
	$(MAKE) -C subscriptions install

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f *.xql; fi
	$(MAKE) -C subscriptions clean

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/users
	cp -f Makefile $(PACKAGE_DIR)/users
	cp -f *.xql $(PACKAGE_DIR)/users
	$(MAKE) -C subscriptions package PACKAGE_DIR=$(PACKAGE_DIR)/users

dummy:
