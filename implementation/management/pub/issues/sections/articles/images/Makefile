export INSTALL_DIR6=$(INSTALL_DIR5)/images

all: view.xql del.xql do_del.xql add.xql do_add.xql edit.xql do_edit.xql upload_i.sh index.xql select.xql viewsel.xql do_sel.xql clean.sh

%.xql: %.m4 ../../../../../macros.m4
	m4 ../../../../../macros.m4 $< >$@

%.sh: %.m4 ../../../../../macros.m4
	m4 ../../../../../macros.m4 $< >$@

install: all
	mkdir -p $(INSTALL_DIR6)
	cp -f *.xql $(INSTALL_DIR6)/
	cp -f upload_i.sh $(CGI_BIN)/upload_i
	chmod 755 $(CGI_BIN)/upload_i

clean:
	rm -f *.xql *~ *.sh

package: all
	mkdir -p $(PACKAGE_DIR)/images
	cp -f Makefile $(PACKAGE_DIR)/images
	cp -f *.xql $(PACKAGE_DIR)/images
	cp -f *.sh $(PACKAGE_DIR)/images
