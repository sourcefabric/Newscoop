include ../../make.env

MY_TARGETS=ad.php login.php logout.php 

all: $(MY_TARGETS)
	$(MAKE) -C root
	$(MAKE) -C src
	$(MAKE) -C pub
	$(MAKE) -C templates
	$(MAKE) -C topics
	$(MAKE) -C users
	$(MAKE) -C languages
	$(MAKE) -C u_types
	$(MAKE) -C a_types
	$(MAKE) -C glossary
	$(MAKE) -C infotype
	$(MAKE) -C logs
	$(MAKE) -C country
	$(MAKE) -C popup
	$(MAKE) -C localizer

%.php: %.m4 macros.m4
	$(M4) macros.m4 $< >$@

%.html: %.m4 macros.m4
	$(M4) macros.m4 $< >$@

install: $(ALL)
	mkdir -p $(CGI_COMMON_DIR)
	mkdir -p $(HTML_COMMON_DIR)
	mkdir -p $(PRIV_COMMON_DIR)/img
	mkdir -p $(HTML_COMMON_DIR)/images/thumbnails
	cp -rf article_import $(PRIV_COMMON_DIR)
	cp -rf javascript $(HTML_COMMON_DIR)
	cp -rf classes $(HTML_COMMON_DIR)
	cp -rf css $(HTML_COMMON_DIR)
	cp -rf imagearchive $(PRIV_COMMON_DIR)
	cp -rf include $(HTML_COMMON_DIR)
	mkdir -p $(SCRIPT_COMMON_DIR)
	cp -fr img/* $(PRIV_COMMON_DIR)/img
	cp -f *.php $(PRIV_COMMON_DIR)/
	$(MAKE) -C root install
	$(MAKE) -C src install
	$(MAKE) -C pub install
	$(MAKE) -C templates install
	$(MAKE) -C topics install	
	$(MAKE) -C users install
	$(MAKE) -C languages install
	$(MAKE) -C u_types install
	$(MAKE) -C a_types install
	$(MAKE) -C glossary install
	$(MAKE) -C infotype install
	$(MAKE) -C logs install
	$(MAKE) -C country install
	$(MAKE) -C popup install
	$(MAKE) -C localizer install
	chown $(APACHE_USER):$(APACHE_GROUP) $(HTML_COMMON_DIR)/*.php
	chown -R $(APACHE_USER):$(APACHE_GROUP) $(HTML_COMMON_DIR)/*
	chown -R $(APACHE_USER):$(APACHE_GROUP) $(PRIV_COMMON_DIR)
	chown -R $(APACHE_USER):$(APACHE_GROUP) $(SCRIPT_COMMON_DIR)
	chmod u+rw $(HTML_COMMON_DIR)/*.php
	chmod g+r $(HTML_COMMON_DIR)/*.php
	chmod o-rwx $(HTML_COMMON_DIR)/*.php
	chmod -R u+rw $(HTML_COMMON_DIR)/javascript
	chmod -R g+r $(HTML_COMMON_DIR)/javascript
	chmod -R o-rwx $(HTML_COMMON_DIR)/javascript
	chmod -R u+rw $(HTML_COMMON_DIR)/classes
	chmod -R g+r $(HTML_COMMON_DIR)/classes
	chmod -R o-rwx $(HTML_COMMON_DIR)/classes
	chmod -R u+rw $(HTML_COMMON_DIR)/css
	chmod -R g+r $(HTML_COMMON_DIR)/css
	chmod -R o-rwx $(HTML_COMMON_DIR)/css
	chmod -R u+rw $(PRIV_COMMON_DIR)/imagearchive
	chmod -R g+r $(PRIV_COMMON_DIR)/imagearchive
	chmod -R o-rwx $(PRIV_COMMON_DIR)/imagearchive
	chmod -R u+rw $(PRIV_COMMON_DIR)
	chmod -R g+rw $(PRIV_COMMON_DIR)
	chmod -R o-rwx $(PRIV_COMMON_DIR)
	chmod -R u+rw $(SCRIPT_COMMON_DIR)
	chmod -R g+r $(SCRIPT_COMMON_DIR)
	chmod -R o-rwx $(SCRIPT_COMMON_DIR)
	chmod -R u+rw $(HTML_COMMON_DIR)/images
	chmod -R g+r $(HTML_COMMON_DIR)/images

clean: dummy
	rm -f *~ macros.m4 $(MY_TARGETS)
	$(MAKE) -C root clean
	$(MAKE) -C src clean
	$(MAKE) -C pub clean
	$(MAKE) -C templates clean
	$(MAKE) -C topics clean	
	$(MAKE) -C users clean
	$(MAKE) -C languages clean
	$(MAKE) -C u_types clean
	$(MAKE) -C a_types clean
	$(MAKE) -C glossary clean
	$(MAKE) -C infotype clean
	$(MAKE) -C logs clean
	$(MAKE) -C country clean
	$(MAKE) -C popup clean
	$(MAKE) -C localizer clean

uninstall: dummy
	rm -fr $(WWW_COMMON_DIR)
	$(MAKE) -C src uninstall
	$(MAKE) -C templates uninstall

dummy:
