all:

install: all
	@./install-db

drop-db:
	@echo -n "Dropping old database..."
	@echo "y" | mysqladmin drop campsite -u root 1> /dev/null
	@echo "done."

clean:

uninstall: dummy
	./uninstall

package: all
	mkdir -p $(PACKAGE_DIR)/database
	cp -f Makefile $(PACKAGE_DIR)/database
	cp -f install-db $(PACKAGE_DIR)/database
	cp -f uninstall $(PACKAGE_DIR)/database
	cp -f campsite-db.sql $(PACKAGE_DIR)/database

dummy:
