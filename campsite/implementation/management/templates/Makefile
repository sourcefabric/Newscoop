export PRIV_DIR2=$(PRIV_DIR)/templates

MY_TARGETS=index.php idx.php new_dir.php do_new_dir.php upload_templ.php del.php do_del.php do_upload_templ.php new_template.php dup.php do_new_templ.php edit_template.php do_edit.php do_dup.php

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	m4 ../macros.m4 $< > $@

%.sh: %.m4 ../macros.m4
	m4 ../macros.m4 $< > $@

%.html: %.m4 ../macros.m4
	m4 ../macros.m4 $< > $@

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.php $(PRIV_DIR2)/

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(MY_TARGETS); fi

uninstall: dummy

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/templates
	cp -f Makefile $(PACKAGE_DIR)/templates
	cp -f *.php $(PACKAGE_DIR)/templates
	cp -f *.sh $(PACKAGE_DIR)/templates

dummy:
