export PRIV_DIR6=$(PRIV_DIR5)/topics

MY_TARGETS=del.php do_del.php do_add.php index.php

all: $(MY_TARGETS)

%.php: %.m4 ../../../../../macros.m4
	$(M4) ../../../../../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR6)
	cp -f *.php $(PRIV_DIR6)/

clean:
	rm -f *~ $(MY_TARGETS)
