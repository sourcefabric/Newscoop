include ../../make.env

all: dummy

install:
	mkdir -p "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "create_instance" "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "backup_instance" "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "restore_instance" "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "remove_instance" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "create_instance.php" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "backup_instance.php" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "restore_instance.php" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "remove_instance.php" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "cli_script_lib.php" "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "update_instances" "$(BIN_DIR)"

clean: dummy

uninstall: dummy
	rm -f "$(BIN_DIR)/create_instance" "$(BIN_DIR)/create_instance.php"
	rm -f "$(BIN_DIR)/backup_instance" "$(BIN_DIR)/backup_instance.php"
	rm -f "$(BIN_DIR)/restore_instance" "$(BIN_DIR)/restore_instance.php"
	rm -f "$(BIN_DIR)/remove_instance" "$(BIN_DIR)/remove_instance.php"
	rm -f "$(BIN_DIR)/cli_script_lib.php" "$(BIN_DIR)/update_instances"

dummy:
