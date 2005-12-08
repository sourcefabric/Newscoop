include ../../make.env

ALL=all

all: dummy
	$(MAKE) -C bin
	$(MAKE) -C cgi
	$(MAKE) -C priv

install: $(ALL)
	mkdir -p $(BIN_DIR)
	mkdir -p $(CGI_COMMON_DIR)
	mkdir -p $(HTML_COMMON_DIR)
	mkdir -p $(HTML_COMMON_DIR)/look
	mkdir -p $(HTML_COMMON_DIR)/css
	cp -fr css $(HTML_COMMON_DIR)
	mkdir -p $(HTML_COMMON_DIR)/classes
	cp -f classes/*.php $(HTML_COMMON_DIR)/classes
	mkdir -p $(HTML_COMMON_DIR)/images/thumbnails
	mkdir -p $(HTML_COMMON_DIR)/attachments
	cp -rf javascript $(HTML_COMMON_DIR)
	cp -f javascript/xinha/htmlarea-compressed.js $(HTML_COMMON_DIR)/javascript/xinha/htmlarea.js
	cp -rf include $(HTML_COMMON_DIR)
	cp -rf priv $(HTML_COMMON_DIR)
	cp -f *.php $(HTML_COMMON_DIR)/
	$(MAKE) -C bin install
	$(MAKE) -C cgi install
	chown -R $(APACHE_USER):$(APACHE_GROUP) $(HTML_COMMON_DIR)/*
	chmod -R u+rw $(HTML_COMMON_DIR)/*
	chmod -R g+r $(HTML_COMMON_DIR)/*
	$(PEAR) install --soft Archive_Tar Console_Getopt XML_RPC PEAR Net_Socket
	$(PEAR) upgrade Archive_Tar Console_Getopt XML_RPC Net_Socket
	$(PEAR) install --soft Net_URL HTTP_Request HTTP_Client File File_Find XML_Util XML_Parser
	$(PEAR) upgrade Net_URL HTTP_Request HTTP_Client File File_Find XML_Util XML_Parser

clean: dummy
	$(MAKE) -C bin clean
	$(MAKE) -C cgi clean
	$(MAKE) -C priv clean

uninstall: dummy
# check the validity of the directory name
	./check_dir_name "$(WWW_COMMON_DIR)"
	rm -fr $(WWW_COMMON_DIR)/*
	$(MAKE) -C cgi uninstall
	$(MAKE) -C bin uninstall

dummy:
