export PRIV_DIR2=$(PRIV_COMMON_DIR)/templates

MY_TARGETS=new_dir.php upload_templ.php del.php do_upload_templ.php new_template.php dup.php edit_template.php refresh.php

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	$(M4) ../macros.m4 $< > $@

%.sh: %.m4 ../macros.m4
	$(M4) ../macros.m4 $< > $@

%.html: %.m4 ../macros.m4
	$(M4) ../macros.m4 $< > $@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.php $(PRIV_DIR2)/

clean:
	rm -f *~ $(MY_TARGETS)

uninstall: dummy

dummy:
