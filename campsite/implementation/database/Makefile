include ../../make.env

PACKAGE_DIR=$(BIN_PACKAGE_DIR)/implementation/database
export DROP_DB=Y

all: dummy

install: dummy
	@./install-db

drop-db: uninstall

clean: dummy

uninstall: dummy
	./uninstall-db

package: dummy
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)
	cp -f Makefile $(PACKAGE_DIR)
	cp -f install-db $(PACKAGE_DIR)
	cp -f uninstall-db $(PACKAGE_DIR)
	cp -f campsite-db.sql $(PACKAGE_DIR)

dummy:
