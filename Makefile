WWW_DIR=/var/www/
REL_CGI_BIN=cgi-bin
REL_HTML=html

export HTTP_USER=apache
export HTTP_GROUP=apache

export CGI_BIN=$(WWW_DIR)/$(REL_CGI_BIN)
export SCRIPT_BIN=$(WWW_DIR)/script
export INSTALL_DIR=$(WWW_DIR)/$(REL_HTML)/priv
export ROOT_DIR=$(WWW_DIR)/$(REL_HTML)
export PUBLIC_DIR=$(WWW_DIR)/$(REL_HTML)/pub

export UTIL_BIN=/usr/local/campsite

export PACKAGE_DIR=$(shell echo `pwd`/.package/campsite)

export CLASS_DIR=/usr/lib/netscape/java/classes
export CDIRS=$(shell find . -maxdepth 1 -type d -print)

export CAMPSITE_VERSION=$(shell echo `cat ./README | grep -w ^version | grep ":" | cut -f 2 -d ":"`)
export PROCESSOR_TYPE=`uname -m`


all:
	$(MAKE) -C implementation all
	$(MAKE) -C supplemental all

install: all
	$(MAKE) -C implementation install
	$(MAKE) -C supplemental install

clean:
	$(MAKE) -C implementation clean
	$(MAKE) -C supplemental clean
	rm -f install_log uninstall_log

uninstall: dummy
	$(MAKE) -C implementation uninstall
	$(MAKE) -C supplemental uninstall
	if [ -d $(UTIL_BIN) ]; then rmdir $(UTIL_BIN); fi
	if [ -d $(WWW_DIR)/script ]; then rmdir $(WWW_DIR)/script; fi

package: dummy
	rm -fr $(PACKAGE_DIR)
	mkdir -p $(PACKAGE_DIR)
	cp -f Makefile $(PACKAGE_DIR)
	cp -f install $(PACKAGE_DIR)
	cp -f .inst_components-package $(PACKAGE_DIR)/.inst_components
	cp -f uninstall $(PACKAGE_DIR)
	cp -f AUTHORS $(PACKAGE_DIR)
	cp -f COPYING $(PACKAGE_DIR)
	cp -f INSTALL $(PACKAGE_DIR)
	cp -f README $(PACKAGE_DIR)
	cp -f ChangeLog $(PACKAGE_DIR)
	cp -f thisisacampsite.gif $(PACKAGE_DIR)
	cp -fr ./documentation $(PACKAGE_DIR)
	$(MAKE) -C implementation package
	$(MAKE) -C supplemental package
	echo "version: [$(CAMPSITE_VERSION)]"
	cd .package; tar czf campsite-$(CAMPSITE_VERSION).$(PROCESSOR_TYPE).tar.gz campsite; mv campsite-*.tar.gz ..; cd ..; rm -fr .package
	echo "Packge file built: campsite-$(CAMPSITE_VERSION).$(PROCESSOR_TYPE).tar.gz."

dummy:
