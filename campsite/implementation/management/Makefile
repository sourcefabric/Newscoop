
subsystem: all
	$(MAKE) -C root
	$(MAKE) -C src
	$(MAKE) -C pub
	$(MAKE) -C templates
	$(MAKE) -C users
	$(MAKE) -C languages
	$(MAKE) -C u_types
	$(MAKE) -C a_types
	$(MAKE) -C dictionary
	$(MAKE) -C classes
	$(MAKE) -C logs
	$(MAKE) -C country
	$(MAKE) -C popup

all: index.xql ad.xql login.xql do_login.xql logout.xql menu.xql home.xql

%.xql: %.m4 macros.m4
	m4 macros.m4 $< >$@

%.html: %.m4 macros.m4
	m4 macros.m4 $< >$@                      

macros.m4: macros.in
	echo "define(\`X_SCRIPT_BIN', \`$(SCRIPT_BIN)')dnl" > macros.m4
	echo "define(\`X_CGI_BIN', \`$(CGI_BIN)')dnl" >> macros.m4
	cat macros.in >>macros.m4

install: all
	mkdir -p $(CGI_BIN)
	mkdir -p $(ROOT_DIR)/look
	mkdir -p $(INSTALL_DIR)/img
	mkdir -p $(INSTALL_DIR)/img/buttons
	mkdir -p $(INSTALL_DIR)/img/icons
	mkdir -p $(SCRIPT_BIN)
	cp -r img/* $(INSTALL_DIR)/img
	cp *.xql $(INSTALL_DIR)/
	$(MAKE) -C root install
	$(MAKE) -C src install
	$(MAKE) -C pub install
	$(MAKE) -C templates install
	$(MAKE) -C users install
	$(MAKE) -C languages install
	$(MAKE) -C u_types install
	$(MAKE) -C a_types install
	$(MAKE) -C dictionary install
	$(MAKE) -C classes install
	$(MAKE) -C logs install
	$(MAKE) -C country install
	$(MAKE) -C popup install
	chown $(HTTP_USER).$(HTTP_GROUP) $(ROOT_DIR)/* -R
	chmod 755 $(ROOT_DIR)
	chmod 755 $(ROOT_DIR)/look
	chmod 755 $(INSTALL_DIR)
	chown $(HTTP_USER).$(HTTP_GROUP) $(SCRIPT_BIN) -R
	chmod 750 $(SCRIPT_BIN)

clean:
	rm -f *.xql *~ macros.m4
	$(MAKE) -C root clean
	$(MAKE) -C src clean
	$(MAKE) -C pub clean
	$(MAKE) -C templates clean
	$(MAKE) -C users clean
	$(MAKE) -C languages clean
	$(MAKE) -C u_types clean
	$(MAKE) -C a_types clean
	$(MAKE) -C dictionary clean
	$(MAKE) -C classes clean
	$(MAKE) -C logs clean
	$(MAKE) -C country clean
	$(MAKE) -C popup clean

uninstall: dummy
	rm -f $(INSTALL_DIR)/../*.xql
	rm -fr $(INSTALL_DIR)
	rm -f $(CGI_BIN)/upload_i
	$(MAKE) -C src uninstall
	$(MAKE) -C templates uninstall

package: all
	mkdir -p $(PACKAGE_DIR)/management
	cp -f Makefile $(PACKAGE_DIR)/management
	cp -f *.xql $(PACKAGE_DIR)/management
	cp -fr img $(PACKAGE_DIR)/management
	$(MAKE) -C a_types package PACKAGE_DIR=$(PACKAGE_DIR)/management
	$(MAKE) -C classes package PACKAGE_DIR=$(PACKAGE_DIR)/management
	$(MAKE) -C country package PACKAGE_DIR=$(PACKAGE_DIR)/management
	$(MAKE) -C dictionary package PACKAGE_DIR=$(PACKAGE_DIR)/management
	$(MAKE) -C languages package PACKAGE_DIR=$(PACKAGE_DIR)/management
	$(MAKE) -C logs package PACKAGE_DIR=$(PACKAGE_DIR)/management
	$(MAKE) -C popup package PACKAGE_DIR=$(PACKAGE_DIR)/management
	$(MAKE) -C pub package PACKAGE_DIR=$(PACKAGE_DIR)/management
	$(MAKE) -C root package PACKAGE_DIR=$(PACKAGE_DIR)/management
	$(MAKE) -C src package PACKAGE_DIR=$(PACKAGE_DIR)/management
	$(MAKE) -C templates package PACKAGE_DIR=$(PACKAGE_DIR)/management
	$(MAKE) -C u_types package PACKAGE_DIR=$(PACKAGE_DIR)/management
	$(MAKE) -C users package PACKAGE_DIR=$(PACKAGE_DIR)/management

dummy:
