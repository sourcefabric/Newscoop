include ../../make.env

JAVA_DIR=$(PRIV_DIR)/pub/issues/sections/articles/java
PACKAGE_DIR=$(BIN_PACKAGE_DIR)/implementation/java

all: dummy

install: dummy
	mkdir -p $(JAVA_DIR)
	if [ -f *.class ]; then cp -f *.class $(JAVA_DIR); fi
	if [ -f *.jar ]; then cp -f *.jar $(JAVA_DIR); fi
	if [ -f *.crt ]; then cp -f *.crt $(JAVA_DIR); fi
	mkdir -p $(JAVA_DIR)/resources
	cp -f resources/*.* $(JAVA_DIR)/resources
	chown $(ROOT_USER).$(HTTP_GROUP) $(JAVA_DIR) -R
	chmod u+rw $(JAVA_DIR)/* $(JAVA_DIR)/resources/*
	chmod g+r $(JAVA_DIR)/* $(JAVA_DIR)/resources/*
	chmod +x $(JAVA_DIR)/resources

clean: dummy

uninstall: dummy
	rm -fr $(JAVA_DIR)

package: dummy
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)
	cp -f Makefile $(PACKAGE_DIR)
	cp -f *.jar $(PACKAGE_DIR)
	cp -f *.crt $(PACKAGE_DIR)
	cp -fr resources $(PACKAGE_DIR)

dummy:
