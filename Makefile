include ./make.env

all:
	$(MAKE) -C implementation all
	$(MAKE) -C supplemental all

install: dummy
	mkdir -p "$(BIN_DIR)"
	chown $(ROOT_USER):$(APACHE_GROUP) "$(BIN_DIR)"
	chmod 755 "$(BIN_DIR)"
	mkdir -p "$(ETC_DIR)"
	chown $(ROOT_USER):$(APACHE_GROUP) "$(ETC_DIR)"
	chmod 755 "$(ETC_DIR)"
	mkdir -p "$(WWW_DIR)"
	chown $(ROOT_USER):$(APACHE_GROUP) "$(WWW_DIR)"
	chmod 755 "$(WWW_DIR)"
	mkdir -p "$(WWW_COMMON_DIR)"
	chown $(ROOT_USER):$(APACHE_GROUP) "$(WWW_COMMON_DIR)"
	chmod 755 "$(WWW_COMMON_DIR)"
	mkdir -p "$(CAMPSITE_DIR)/backup"
	chown $(ROOT_USER):$(APACHE_GROUP) "$(CAMPSITE_DIR)/backup"
	chmod 755 "$(CAMPSITE_DIR)/backup"
	mkdir -p "$(CAMPSITE_DIR)/instance"
	chown $(ROOT_USER):$(APACHE_GROUP) "$(CAMPSITE_DIR)/instance"
	chmod 755 "$(CAMPSITE_DIR)/instance"
	$(MAKE) -C implementation install
	$(MAKE) -C supplemental install
	echo "$(CAMPSITE_VERSION)" >> "$(CAMPSITE_DIR)/$(CAMPSITE_REGISTER)"

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
	rm -f "$(CAMPSITE_DIR)/database.conf"

dummy:
