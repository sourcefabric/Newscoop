include ../../../make.env

GET_IMG_OBJ_FILES=get_img.o readconf.o cgi.o cgi_common.o

CLEAN_XML_INCLUDE=$(shell echo -n "$(XML_INCLUDE)")
CLEAN_XML_LINK=$(shell echo -n "$(XML_LINK)")
CLEAN_CURL_FLAGS=$(shell echo -n "$(CURL_FLAGS)")
CLEAN_CURL_LINK=$(shell echo -n "$(CURL_LINK)")

GET_IMG_LDFLAGS=-L$(LIBMYSQLCLIENT_PATH)
GET_IMG_LDLIBS=-lmysqlclient $(PTHREAD_LINK) $(CLEAN_CURL_LINK)

DEPENDENCIES=globals.h readconf.h cms_types.h cgib.h cgi.h cgi_common.h threadkey.h

CFLAGS=-Wall $(CLEAN_XML_INCLUDE) $(CLEAN_CURL_CFLAGS)
# -D_DEBUG -g

TPL_CGI=tpl_cgi
GET_IMG=get_img

LINKS=readconf.h readconf.cpp cms_types.h threadkey.h

all: links $(TPL_CGI) $(GET_IMG)

links: dummy
	@for l in $(LINKS); do \
	    if [ ! -L $$l ]; then ln -s ../../parser/parser/$$l; fi; \
	done

$(TPL_CGI): dummy
	./create_tpl_cgi

$(GET_IMG): $(GET_IMG_OBJ_FILES)
	g++ -o $(GET_IMG) $(GET_IMG_OBJ_FILES) $(GET_IMG_LDFLAGS) $(GET_IMG_LDLIBS)
	strip $(GET_IMG)

clean: dummy
	rm -f *.o *~ $(TPL_CGI) $(GET_IMG) $(LINKS)

get_img.o: get_img.cpp $(DEPENDENCIES)
	g++ -c -o get_img.o $(CFLAGS) get_img.cpp

readconf.o: readconf.cpp $(DEPENDENCIES)
	g++ -c -o readconf.o $(CFLAGS) readconf.cpp

cgi.o: cgi.cpp $(DEPENDENCIES)
	g++ -c -o cgi.o $(CFLAGS) cgi.cpp

cgi_common.o: cgi_common.cpp $(DEPENDENCIES)
	g++ -c -o cgi_common.o $(CFLAGS) cgi_common.cpp

install: all
	mkdir -p $(CGI_COMMON_DIR)
	install -m 750 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(TPL_CGI)" "$(CGI_COMMON_DIR)"
	install -m 750 -o $(ROOT_USER) -g $(APACHE_GROUP) "$(GET_IMG)" "$(CGI_COMMON_DIR)"

uninstall: dummy
	rm -f $(CGI_COMMON_DIR)/$(TPL_CGI)
	rm -f $(CGI_COMMON_DIR)/$(GET_IMG)

dummy:
