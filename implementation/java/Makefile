
all:

install:
	mkdir -p $(INSTALL_DIR)/pub/issues/sections/articles/java
	if [ -f *.class ]; then cp -f *.class $(INSTALL_DIR)/pub/issues/sections/articles/java; fi
	if [ -f *.jar ]; then cp -f *.jar $(INSTALL_DIR)/pub/issues/sections/articles/java; fi
	cp -fr resources $(INSTALL_DIR)/pub/issues/sections/articles/java
	chown $(HTTP_USER).$(HTTP_GROUP) $(INSTALL_DIR)/pub/issues/sections/articles/java -R

clean:

uninstall: dummy
	rm -fr $(INSTALL_DIR)/pub/issues/sections/articles/java/

package: dummy
	mkdir -p $(PACKAGE_DIR)/java
	cp -f Makefile $(PACKAGE_DIR)/java
	cp -f *.jar $(PACKAGE_DIR)/java
	cp -fr resources $(PACKAGE_DIR)/java

dummy:
