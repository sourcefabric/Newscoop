include ../../../make.env

all: dummy

install:
	mkdir -p $(BIN_DIR)
	cp -f autopublish.php $(BIN_DIR)
	cp -f autopublish $(BIN_DIR)
	chown $(ROOT_USER):$(APACHE_GROUP) $(BIN_DIR)/autopublish
	chown $(ROOT_USER):$(APACHE_GROUP) $(BIN_DIR)/autopublish.php
	./install_autopublish

clean: dummy

uninstall:
	rm -f $(BIN_DIR)/autopublish.php
	rm -f $(BIN_DIR)/autopublish
	./uninstall_autopublish

dummy:
