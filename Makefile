include ./make.env

all:
	$(MAKE) -C implementation all
	$(MAKE) -C supplemental all

install: dummy
	mkdir -p $(CONF_DIR)
	chown $(ROOT_USER):$(HTTP_GROUP) $(CONF_DIR)
	chmod 750 $(CONF_DIR)
	echo "SERVER $(DATABASE_SERVER)" > $(CONF_DIR)/database.conf
	echo "PORT $(DATABASE_PORT)" >> $(CONF_DIR)/database.conf
	echo "USER $(DATABASE_USER)" >> $(CONF_DIR)/database.conf
	echo "PASSWORD $(DATABASE_PASSWORD)" >> $(CONF_DIR)/database.conf
	echo "NAME $(DATABASE_NAME)" >> $(CONF_DIR)/database.conf
	chown $(ROOT_USER):$(HTTP_GROUP) $(CONF_DIR)/database.conf
	chmod 640 $(CONF_DIR)/database.conf
	mkdir -p $(CONF_DIR)/install
	rm -f $(CONF_DIR)/install/.inst.modules
	cp $(INSTALL_CONF)/.inst.modules $(CONF_DIR)/install
	rm -f $(CONF_DIR)/install/.modules.conf
	cp $(INSTALL_CONF)/.modules.conf $(CONF_DIR)/install
	rm -f $(CONF_DIR)/install/.modules.desc
	cp $(INSTALL_CONF)/.modules.desc $(CONF_DIR)/install
	chown -R $(ROOT_USER):$(HTTP_GROUP) $(CONF_DIR)/install
	chmod 750 $(CONF_DIR)/install
	chmod 640 $(CONF_DIR)/install/.inst.modules
	chmod 640 $(CONF_DIR)/install/.modules.conf
	chmod 640 $(CONF_DIR)/install/.modules.desc
	$(MAKE) -C implementation install
	$(MAKE) -C supplemental install
	if [ -d $(BIN_DIR) ]; then \
	    chown $(ROOT_USER):$(HTTP_GROUP) $(BIN_DIR); \
	    chmod 755 $(BIN_DIR); \
	fi
	if [ -d $(CGI_DIR) ]; then \
	    chown $(HTTP_USER):$(HTTP_GROUP) $(CGI_DIR); \
	    chmod 755 $(CGI_DIR); \
	fi
	if [ -d $(PRIV_DIR) ]; then \
	    chown $(HTTP_USER):$(HTTP_GROUP) $(PRIV_DIR); \
	    chmod 755 $(PRIV_DIR); \
	fi
	if [ -d $(SCRIPT_DIR) ]; then \
	    chown $(ROOT_USER):$(HTTP_GROUP) $(SCRIPT_DIR); \
	    chmod 755 $(SCRIPT_DIR); \
	fi
	if grep $(APP_NAME) $(CAMPSITE_REGISTER); then \
	    cp -f $(CAMPSITE_REGISTER) $(CAMPSITE_REGISTER).bak; \
	    grep -v -w $(APP_NAME) $(CAMPSITE_REGISTER).bak > $(CAMPSITE_REGISTER); \
	    rm -f $(CAMPSITE_REGISTER).bak; \
	fi
	echo "$(APP_NAME) : $(CAMPSITE_VERSION)" >> $(CAMPSITE_REGISTER)

clean:
	$(MAKE) -C implementation clean
	$(MAKE) -C supplemental clean
	rm -f install_log uninstall_log
	rm -fr .package .old_version

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

dummy:
