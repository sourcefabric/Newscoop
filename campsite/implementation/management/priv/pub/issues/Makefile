export PRIV_DIR3=$(PRIV_DIR2)/issues

MY_TARGETS=qadd.php add_new.php do_add_new.php add_prev.php do_add_prev.php do_del.php do_translate.php add_article.php 

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
