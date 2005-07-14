export PRIV_DIR3=$(PRIV_COMMON_DIR)/users/subscriptions

MY_TARGETS=index.php add.php do_add.php status.php do_status.php del.php do_del.php topay.php do_topay.php

subsystem: all
	$(MAKE) -C sections

all: $(MY_TARGETS)

%.php: %.m4 ../../macros.m4
	$(M4) ../../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR3)
	cp -f *.php $(PRIV_DIR3)/
	$(MAKE) -C sections install

clean:
	rm -f *~ $(MY_TARGETS)
	$(MAKE) -C sections clean

dummy:
