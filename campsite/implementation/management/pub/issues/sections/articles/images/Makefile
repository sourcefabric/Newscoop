export PRIV_DIR6=$(PRIV_DIR5)/images

MY_TARGETS=view.php del.php do_del.php add.php do_add.php edit.php do_edit.php upload_i.sh index.php select.php viewsel.php do_sel.php

all: $(MY_TARGETS)

%.php: %.m4 ../../../../../macros.m4
	m4 ../../../../../macros.m4 $< >$@

%.sh: %.m4 ../../../../../macros.m4
	m4 ../../../../../macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(PRIV_DIR6)
	cp -f *.php $(PRIV_DIR6)/
	cp -f upload_i.sh $(CGI_DIR)/upload_i
	chmod 755 $(CGI_DIR)/upload_i

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(MY_TARGETS); fi

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/images
	cp -f Makefile $(PACKAGE_DIR)/images
	cp -f *.php $(PACKAGE_DIR)/images
	cp -f *.sh $(PACKAGE_DIR)/images
