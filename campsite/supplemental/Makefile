include ../make.env

PACKAGE_DIR=$(BIN_PACKAGE_DIR)/supplemental

all:
	$(MAKE) -C mysql_connect

install: $(ALL)
	$(MAKE) -C mysql_connect install
	if [ -f swingall.jar ]; then \
	    cp swingall.jar $(CLASS_DIR); \
	    chown $(ROOT_USER):$(HTTP_GROUP) $(CLASS_DIR)/swingall.jar; \
	    chmod 644 $(CLASS_DIR)/swingall.jar; \
	fi

clean:
	$(MAKE) -C mysql_connect clean

uninstall: dummy
	$(MAKE) -C mysql_connect uninstall

dummy:
