include ../../make.env

export DROP_DB=Y

all: dummy

install:
	mkdir -p "$(CAMPSITE_DIR)/instance/database"
	cp -f campsite-db.sql "$(CAMPSITE_DIR)/instance/database"
	cp -fr upgrade "$(CAMPSITE_DIR)/instance/database"
	chown "$(APACHE_USER):$(APACHE_GROUP)" "$(CAMPSITE_DIR)/instance" -R

drop-db: uninstall

clean: dummy

uninstall: dummy
	./uninstall-db

dummy:
