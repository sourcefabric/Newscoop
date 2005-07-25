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
	mkdir -p "$(HTML_COMMON_DIR)"
	chown $(ROOT_USER):$(APACHE_GROUP) "$(HTML_COMMON_DIR)"
	chmod 755 "$(HTML_COMMON_DIR)"
	mkdir -p "$(CAMPSITE_DIR)/backup"
	chown $(ROOT_USER):$(APACHE_GROUP) "$(CAMPSITE_DIR)/backup"
	chmod 755 "$(CAMPSITE_DIR)/backup"
	mkdir -p "$(CAMPSITE_DIR)/instance"
	chown $(ROOT_USER):$(APACHE_GROUP) "$(CAMPSITE_DIR)/instance"
	chmod 755 "$(CAMPSITE_DIR)/instance"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/campsite_config" "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/create_instance" "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/backup_instance" "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/restore_instance" "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/remove_instance" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/create_instance.php" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/backup_instance.php" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/restore_instance.php" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/remove_instance.php" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/cli_script_lib.php" "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/update_instances" "$(BIN_DIR)"
	install -m 640 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/install_conf.php" "$(ETC_DIR)"
	install -m 640 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/vhost-template.conf" "$(ETC_DIR)"
	install -m 640 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/parser_conf.php" "$(ETC_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(INSTALL_CONF)/campsite_version.php" "$(HTML_COMMON_DIR)"
	$(MAKE) -C implementation install
	$(BIN_DIR)/update_instances

test_install:
	mkdir -p "$(CAMPSITE_DIR)/test"
	chown $(ROOT_USER):$(APACHE_GROUP) "$(CAMPSITE_DIR)/test"
	chmod 755 "$(CAMPSITE_DIR)/test"
	rmdir "$(CAMPSITE_DIR)/test"

default_instance:
	@"$(BIN_DIR)/create_instance"

clean:
	$(MAKE) -C implementation clean
	rm -f install_log uninstall_log

distclean: clean
	rm -f make.env "$(INSTALL_CONF)/campsite_config" "$(INSTALL_CONF)/configure.h" "$(INSTALL_CONF)/campsite_version.php"

uninstall: dummy
	"$(INSTALL_CONF)/remove_all_instances" -f
	$(MAKE) -C implementation uninstall
	rm -f "$(BIN_DIR)/campsite_config" "$(BIN_DIR)/cli_script_lib.php"
	rm -f "$(BIN_DIR)/create_instance" "$(BIN_DIR)/create_instance.php"
	rm -f "$(BIN_DIR)/backup_instance" "$(BIN_DIR)/backup_instance.php"
	rm -f "$(BIN_DIR)/restore_instance" "$(BIN_DIR)/restore_instance.php"
	rm -f "$(BIN_DIR)/remove_instance" "$(BIN_DIR)/remove_instance.php" "$(BIN_DIR)/update_instances"
	rm -f "$(ETC_DIR)/install_conf.php" "$(ETC_DIR)/parser_conf.php" "$(ETC_DIR)/vhost-template.conf"
	rmdir --ignore-fail-on-non-empty "$(BIN_DIR)"
	rmdir --ignore-fail-on-non-empty "$(ETC_DIR)"
	rmdir --ignore-fail-on-non-empty "$(WWW_DIR)"
	rmdir --ignore-fail-on-non-empty "$(WWW_COMMON_DIR)"
	rmdir --ignore-fail-on-non-empty "$(CAMPSITE_DIR)/backup"
	rmdir --ignore-fail-on-non-empty "$(CAMPSITE_DIR)/instance"
	rmdir --ignore-fail-on-non-empty "$(CAMPSITE_DIR)"

dummy:
