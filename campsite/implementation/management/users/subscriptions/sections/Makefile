export PRIV_DIR4=$(PRIV_DIR3)/sections

MY_TARGETS=index.php add.php do_add.php do_del.php change.php do_change.php

all: $(MY_TARGETS)

%.php: %.m4 ../../../macros.m4
	$(M4) ../../../macros.m4 $< >$@

install: all
	mkdir -p $(PRIV_DIR4)
	cp -f *.php $(PRIV_DIR4)/

clean:
	rm -f *~ $(MY_TARGETS)
