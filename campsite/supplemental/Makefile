include ../make.env

ALL=$(shell if [ "$(PACKAGE_TYPE)" = "source" ]; then echo all; fi)

PACKAGE_DIR=$(BIN_PACKAGE_DIR)/supplemental

WWWSQL_DIR=www-sql-0.5.7-patch

all:
	@if [ ! -f ./$(WWWSQL_DIR)/Makefile ]; then \
	    (cd ./www-sql-0.5.7-patch; rm -f config.cache; ./configure --enable-apache-action-check --enable-unsafe; cd ..); \
	fi
	$(MAKE) -C $(WWWSQL_DIR) all
	$(MAKE) -C mysql_connect

install: $(ALL)
	cp -f $(WWWSQL_DIR)/www-sql $(CGI_DIR)
	chown $(ROOT_USER).$(HTTP_GROUP) $(CGI_DIR)/www-sql
	chmod 755 $(CGI_DIR)/www-sql
	$(MAKE) -C mysql_connect install
	if [ -f swingall.jar ]; then \
	    cp swingall.jar $(CLASS_DIR); \
	    chown $(ROOT_USER).$(HTTP_GROUP) $(CLASS_DIR)/swingall.jar; \
	    chmod 644 $(CLASS_DIR)/swingall.jar; \
	fi

clean:
	@if [ -f ./$(WWWSQL_DIR)/Makefile ]; then \
	    $(MAKE) -C $(WWWSQL_DIR) distclean; \
	fi
	$(MAKE) -C mysql_connect clean

uninstall: dummy
	rm -f $(CGI_DIR)/www-sql
	$(MAKE) -C mysql_connect uninstall

package: $(ALL)
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)/$(WWWSQL_DIR)
	cp -f Makefile $(PACKAGE_DIR)
	cp -f $(WWWSQL_DIR)/www-sql $(PACKAGE_DIR)/$(WWWSQL_DIR)
	$(MAKE) -C mysql_connect package PACKAGE_DIR=$(PACKAGE_DIR)

dummy:
