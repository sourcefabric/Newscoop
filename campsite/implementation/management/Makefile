include ../../make.env

ALL=$(shell if [ "$(PACKAGE_TYPE)" = "source" ]; then echo all; else echo dummy; fi)

PACKAGE_DIR=$(BIN_PACKAGE_DIR)/implementation

all: index.xql ad.xql login.xql do_login.xql logout.xql menu.xql home.xql
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

%.xql: %.m4 macros.m4
	m4 macros.m4 $< >$@

%.html: %.m4 macros.m4
	m4 macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(CGI_DIR)
	mkdir -p $(HTML_DIR)/look
	mkdir -p $(PRIV_DIR)/img
	mkdir -p $(PRIV_DIR)/img/buttons
	mkdir -p $(PRIV_DIR)/img/icons
	mkdir -p $(SCRIPT_DIR)
	cp -fr img/* $(PRIV_DIR)/img
	cp -f *.xql $(PRIV_DIR)/
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
	chown $(HTTP_USER).$(HTTP_GROUP) $(HTML_DIR)/*.xql
	chown $(HTTP_USER).$(HTTP_GROUP) $(HTML_DIR)/look -R
	chown $(HTTP_USER).$(HTTP_GROUP) $(PRIV_DIR) -R
	chown $(HTTP_USER).$(HTTP_GROUP) $(SCRIPT_DIR) -R
	chmod u+rw $(HTML_DIR)/*.xql
	chmod g+r $(HTML_DIR)/*.xql
	chmod o-rwx $(HTML_DIR)/*.xql
	chmod u+rw $(PRIV_DIR) -R
	chmod g+r $(PRIV_DIR) -R
	chmod o-rwx $(PRIV_DIR) -R
	chmod u+rw $(SCRIPT_DIR) -R
	chmod g+r $(SCRIPT_DIR) -R
	chmod o-rwx $(SCRIPT_DIR) -R

clean: dummy
	rm -f *~ macros.m4 .env_vars
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f *.xql; fi
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
	rm -f $(PRIV_DIR)/../*.xql
	rm -fr $(PRIV_DIR)
	rm -f $(CGI_DIR)/upload_i
	$(MAKE) -C src uninstall
	$(MAKE) -C templates uninstall

package: $(ALL)
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)/management
	cp -f Makefile $(PACKAGE_DIR)/management
	cp -f configure $(PACKAGE_DIR)/management
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
