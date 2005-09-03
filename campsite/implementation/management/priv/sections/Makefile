export PRIV_DIR4=$(PRIV_COMMON_DIR)/sections

MY_TARGETS=do_add.php do_edit.php

subsystem: all

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	$(M4) ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR4)
	cp -f *.php $(PRIV_DIR4)/

clean:
	rm -f *~ $(MY_TARGETS)
