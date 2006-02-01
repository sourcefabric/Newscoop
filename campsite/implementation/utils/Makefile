include ../../make.env

all: dummy

install:
	mkdir -p "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "campsite-create-instance" "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "campsite-backup-instance" "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "campsite-restore-instance" "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "campsite-remove-instance" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "campsite-create-instance.php" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "campsite-backup-instance.php" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "campsite-restore-instance.php" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "campsite-remove-instance.php" "$(BIN_DIR)"
	install -m 644 -o $(ROOT_USER) -g $(APACHE_GROUP) "cli_script_lib.php" "$(BIN_DIR)"
	install -m 755 -o $(ROOT_USER) -g $(APACHE_GROUP) "campsite-update-instances" "$(BIN_DIR)"

clean: dummy

uninstall: dummy
	rm -f "$(BIN_DIR)/campsite-create-instance" "$(BIN_DIR)/campsite-create-instance.php"
	rm -f "$(BIN_DIR)/campsite-backup-instance" "$(BIN_DIR)/campsite-backup-instance.php"
	rm -f "$(BIN_DIR)/campsite-restore-instance" "$(BIN_DIR)/campsite-restore-instance.php"
	rm -f "$(BIN_DIR)/campsite-remove-instance" "$(BIN_DIR)/campsite-remove-instance.php"
	rm -f "$(BIN_DIR)/cli_script_lib.php" "$(BIN_DIR)/campsite-update-instances"

dummy:
