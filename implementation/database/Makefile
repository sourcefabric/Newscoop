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

dummy:
