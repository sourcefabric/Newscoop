include ../../../make.env

export PRIV_DIR2=$(PRIV_COMMON_DIR)/users

MY_TARGETS=add.php do_add.php del.php do_del.php chpwd.php do_chpwd.php access.php do_access.php info.php do_info.php passwd.php do_passwd.php ipaccesslist.php ipadd.php do_ipadd.php ipdel.php do_ipdel.php

subsystem: all
	$(MAKE) -C subscriptions

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	$(M4) ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.php $(PRIV_DIR2)/
	$(MAKE) -C subscriptions install

clean:
	rm -f *~ $(MY_TARGETS)
	$(MAKE) -C subscriptions clean

dummy:
