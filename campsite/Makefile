include ./make.env

all:
	$(MAKE) -C implementation all
	$(MAKE) -C supplemental all

install: dummy
	mkdir -p $(CONF_DIR)
	chown $(ROOT_USER).$(HTTP_GROUP) $(CONF_DIR)
	chmod 750 $(CONF_DIR)
	echo "SERVER $(DATABASE_SERVER)" > $(CONF_DIR)/database.conf
	echo "PORT $(DATABASE_PORT)" >> $(CONF_DIR)/database.conf
	echo "USER $(DATABASE_USER)" >> $(CONF_DIR)/database.conf
	echo "PASSWORD $(DATABASE_PASSWORD)" >> $(CONF_DIR)/database.conf
	echo "NAME $(DATABASE_NAME)" >> $(CONF_DIR)/database.conf
	chown $(ROOT_USER).$(HTTP_GROUP) $(CONF_DIR)/database.conf
	chmod 640 $(CONF_DIR)/database.conf
	mkdir -p $(CONF_DIR)/install
	rm -f $(CONF_DIR)/install/.inst.modules
	cp $(INSTALL_CONF)/.inst.modules $(CONF_DIR)/install
	rm -f $(CONF_DIR)/install/.modules.conf
	cp $(INSTALL_CONF)/.modules.conf $(CONF_DIR)/install
	rm -f $(CONF_DIR)/install/.modules.desc
	cp $(INSTALL_CONF)/.modules.desc $(CONF_DIR)/install
	chown $(ROOT_USER).$(HTTP_GROUP) $(CONF_DIR)/install -R
	chmod 750 $(CONF_DIR)/install
	chmod 640 $(CONF_DIR)/install/.inst.modules
	chmod 640 $(CONF_DIR)/install/.modules.conf
	chmod 640 $(CONF_DIR)/install/.modules.desc
	$(MAKE) -C implementation install
	$(MAKE) -C supplemental install
	if [ -d $(BIN_DIR) ]; then \
	    chown $(ROOT_USER).$(HTTP_GROUP) $(BIN_DIR); \
	    chmod 750 $(BIN_DIR); \
	fi
	if [ -d $(PRIV_DIR) ]; then \
	    chown $(ROOT_USER).$(HTTP_GROUP) $(PRIV_DIR); \
	    chmod 750 $(PRIV_DIR); \
	fi
	if [ -d $(SCRIPT_DIR) ]; then \
	    chown $(ROOT_USER).$(HTTP_GROUP) $(SCRIPT_DIR); \
	    chmod 750 $(SCRIPT_DIR); \
	fi
	if ! grep $(APP_NAME) $(CAMPSITE_REGISTER) &> /dev/null; then echo $(APP_NAME) >> $(CAMPSITE_REGISTER); fi

clean:
	$(MAKE) -C implementation clean
	$(MAKE) -C supplemental clean
	rm -f install_log uninstall_log
	rm -fr .package

distclean: clean
	rm -f make.env

uninstall: dummy
	$(MAKE) -C implementation uninstall
	$(MAKE) -C supplemental uninstall
	rm -f $(CONF_DIR)/database.conf
	rm -f $(CONF_DIR)/install/.inst.modules
	rm -f $(CONF_DIR)/install/.modules.conf
	rm -f $(CONF_DIR)/install/.modules.desc
	if [ -d $(CONF_DIR)/install ]; then rmdir $(CONF_DIR)/install; fi
	if [ -d $(CONF_DIR) ]; then rmdir $(CONF_DIR); fi
	if [ -d $(BIN_DIR) ]; then rmdir $(BIN_DIR); fi
	if [ -d $(SCRIPT_DIR) ]; then rmdir $(SCRIPT_DIR); fi
	if grep $(APP_NAME) $(CAMPSITE_REGISTER); then \
	    grep -v $(APP_NAME) $(CAMPSITE_REGISTER) > .campsite.reg; \
	    mv -f .campsite.reg $(CAMPSITE_REGISTER); \
	fi

package: dummy
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	rm -fr $(BIN_PACKAGE_DIR)
	mkdir -p $(BIN_PACKAGE_DIR)
	echo "binary" > $(BIN_PACKAGE_DIR)/.package_type
	cp -f configure $(BIN_PACKAGE_DIR)
	cp -f Makefile $(BIN_PACKAGE_DIR)
	cp -f install $(BIN_PACKAGE_DIR)
	cp -f uninstall $(BIN_PACKAGE_DIR)
	cp -f AUTHORS $(BIN_PACKAGE_DIR)
	cp -f COPYING $(BIN_PACKAGE_DIR)
	cp -f INSTALL $(BIN_PACKAGE_DIR)
	cp -f README $(BIN_PACKAGE_DIR)
	cp -f ChangeLog $(BIN_PACKAGE_DIR)
	cp -f thisisacampsite.gif $(BIN_PACKAGE_DIR)
	cp -fr ./documentation $(BIN_PACKAGE_DIR)
	cp -fr ./.install_conf $(BIN_PACKAGE_DIR)
	$(MAKE) -C implementation package
	$(MAKE) -C supplemental package
	cd .package; tar czf campsite-$(CAMPSITE_VERSION).$(PROCESSOR_TYPE).tar.gz campsite; mv campsite-*.tar.gz ..; cd ..; rm -fr .package
	echo "Packge file built: campsite-$(CAMPSITE_VERSION).$(PROCESSOR_TYPE).tar.gz."

dummy:
