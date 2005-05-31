include ../../../make.env

export PRIV_DIR2=$(PRIV_COMMON_DIR)/users

MY_TARGETS=add.php access.php do_access.php

subsystem: all
	$(MAKE) -C subscriptions

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	$(M4) ../macros.m4 $< >$@

install: all
	mkdir -p $(PRIV_DIR2)
	cp -f *.php $(PRIV_DIR2)/
	$(MAKE) -C subscriptions install

clean:
	rm -f *~ $(MY_TARGETS)
	$(MAKE) -C subscriptions clean

dummy:
