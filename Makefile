include ./make.env

all:
	$(MAKE) -C implementation all

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
	cp -f "$(INSTALL_CONF)/create_instance" "$(BIN_DIR)"
	chown $(ROOT_USER):$(APACHE_GROUP) "$(BIN_DIR)/create_instance"
	chmod 755 "$(BIN_DIR)/create_instance"
	cp -f "$(INSTALL_CONF)/create_instance.php" "$(BIN_DIR)"
	chown $(ROOT_USER):$(APACHE_GROUP) "$(BIN_DIR)/create_instance.php"
	chmod 644 "$(BIN_DIR)/create_instance.php"
	cp -f "$(INSTALL_CONF)/campsite_config" "$(BIN_DIR)"
	chown $(ROOT_USER):$(APACHE_GROUP) "$(BIN_DIR)/campsite_config"
	chmod 755 "$(BIN_DIR)/campsite_config"
	cp -f "$(INSTALL_CONF)/install_conf.php" "$(ETC_DIR)"
	cp -f "$(INSTALL_CONF)/vhost-template.conf" "$(ETC_DIR)"
	cp -f "$(INSTALL_CONF)/parser_conf.php" "$(ETC_DIR)"
	chown $(ROOT_USER):$(APACHE_GROUP) $(ETC_DIR)/*
	chmod 644 $(ETC_DIR)/*
	$(MAKE) -C implementation install

test_install:
	mkdir -p "$(CAMPSITE_DIR)/test"
	rmdir "$(CAMPSITE_DIR)/test"

default_instance:
	"$(BIN_DIR)/create_instance"

clean:
	$(MAKE) -C implementation clean
	rm -f install_log uninstall_log "$(INSTALL_CONF)/campsite_config"

distclean: clean
	rm -f make.env

uninstall: dummy
	$(MAKE) -C implementation uninstall
	rm -f "$(BIN_DIR)/create_instance" "$(BIN_DIR)/create_instance.php"
	rm -f "$(ETC_DIR)/install_conf.php" "$(ETC_DIR)/parser_conf.php"
	rmdir --ignore-fail-on-non-empty "$(BIN_DIR)"
	rmdir --ignore-fail-on-non-empty "$(ETC_DIR)"
	rmdir --ignore-fail-on-non-empty "$(WWW_DIR)"
	rmdir --ignore-fail-on-non-empty "$(WWW_COMMON_DIR)"
	rmdir --ignore-fail-on-non-empty "$(CAMPSITE_DIR)/backup"
	rmdir --ignore-fail-on-non-empty "$(CAMPSITE_DIR)/instance"
	rmdir --ignore-fail-on-non-empty "$(CAMPSITE_DIR)"

dummy:
