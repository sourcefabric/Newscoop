include ../../make.env

all: dummy

install:
	mkdir -p "$(CAMPSITE_DIR)/instance/database"
	cp -f campsite-db.sql "$(CAMPSITE_DIR)/instance/database"
	cp -fr upgrade "$(CAMPSITE_DIR)/instance/database"
	rm -f $(CAMPSITE_DIR)/instance/database/upgrade/*/install_conf.php
	rm -f $(CAMPSITE_DIR)/instance/database/upgrade/*/database_conf.php
	chown -R "$(APACHE_USER):$(APACHE_GROUP)" "$(CAMPSITE_DIR)/instance"

drop-db: uninstall

clean: dummy

uninstall: dummy
	if [ "$(CAMPSITE_DIR)" = "" ] || [ "$(CAMPSITE_DIR)" = "/" ]; then \
	    echo "Invalid campsite directory"; \
	    exit 1; \
	fi
	rm -fr "$(CAMPSITE_DIR)/instance/database"

dummy:
