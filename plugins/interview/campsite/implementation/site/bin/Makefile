include ../../../make.env

all: dummy

install:
	mkdir -p $(SBIN_DIR)
	install -o $(ROOT_USER) -g $(APACHE_GROUP) -m 750 campsite_autopublish "$(SBIN_DIR)"
	install -o $(ROOT_USER) -g $(APACHE_GROUP) -m 750 campsite_autopublish.php "$(SBIN_DIR)"
	./install_autopublish

clean: dummy

uninstall:
	rm -f $(SBIN_DIR)/campsite_autopublish.php
	rm -f $(SBIN_DIR)/campsite_autopublish
	./uninstall_autopublish

dummy:
