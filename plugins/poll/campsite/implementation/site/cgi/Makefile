include ../../../make.env

TPL_CGI=tpl_cgi
GET_IMG=get_img

all: dummy

clean: dummy
	rm -f *~

install: all
	mkdir -p $(CGI_COMMON_DIR)
	install -m 750 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(TPL_CGI)" "$(CGI_COMMON_DIR)"
	install -m 750 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(GET_IMG)" "$(CGI_COMMON_DIR)"

uninstall: dummy
	rm -f $(CGI_COMMON_DIR)/$(TPL_CGI)
	rm -f $(CGI_COMMON_DIR)/$(GET_IMG)

dummy:
