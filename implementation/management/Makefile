include ../../make.env

ALL=all

all: dummy
	$(MAKE) -C root
	$(MAKE) -C bin
	$(MAKE) -C cgi
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
	$(MAKE) -C localizer

install: $(ALL)
	mkdir -p $(BIN_DIR)
	mkdir -p $(CGI_COMMON_DIR)
	mkdir -p $(HTML_COMMON_DIR)
	mkdir -p $(HTML_COMMON_DIR)/look
	mkdir -p $(PRIV_COMMON_DIR)/img
	cp -fr img/* $(PRIV_COMMON_DIR)/img
	mkdir -p $(PRIV_COMMON_DIR)/article_import
	cp -fr article_import/* $(PRIV_COMMON_DIR)/article_import
	mkdir -p $(HTML_COMMON_DIR)/css
	cp -f css/*.css $(HTML_COMMON_DIR)/css
	mkdir -p $(HTML_COMMON_DIR)/classes
	cp -f classes/*.php $(HTML_COMMON_DIR)/classes
	mkdir -p $(HTML_COMMON_DIR)/images/thumbnails
	cp -rf javascript $(HTML_COMMON_DIR)
	cp -rf imagearchive $(PRIV_COMMON_DIR)
	cp -rf include $(HTML_COMMON_DIR)
	cp -f *.php $(PRIV_COMMON_DIR)/
	$(MAKE) -C root install
	$(MAKE) -C bin install
	$(MAKE) -C cgi install
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
	$(MAKE) -C localizer install
	chown -R $(APACHE_USER):$(APACHE_GROUP) $(HTML_COMMON_DIR)/*
	chmod -R u+rw $(HTML_COMMON_DIR)/*
	chmod -R g+r $(HTML_COMMON_DIR)/*
	$(PEAR) install --soft Archive_Tar Console_Getopt XML_RPC PEAR Net_Socket
	$(PEAR) upgrade Archive_Tar Console_Getopt XML_RPC PEAR Net_Socket
	$(PEAR) install --soft Net_URL HTTP_Request HTTP_Client File File_Find XML_Util XML_Parser
	$(PEAR) upgrade Net_URL HTTP_Request HTTP_Client File File_Find XML_Util XML_Parser

clean: dummy
	$(MAKE) -C root clean
	$(MAKE) -C bin clean
	$(MAKE) -C cgi clean
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
	$(MAKE) -C localizer clean

uninstall: dummy
# check the validity of the directory name
	./check_dir_name "$(WWW_COMMON_DIR)"
	rm -fr $(WWW_COMMON_DIR)/*
	$(MAKE) -C cgi uninstall
	$(MAKE) -C bin uninstall

dummy:
