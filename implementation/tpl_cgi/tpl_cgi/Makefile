OBJ_FILES=main.o csocket.o readconf.o cgi.o mutex.o

LDFLAGS=
LDLIBS=$(PTHREAD_LINK) $(XML_LINK)

DEPENDENCES=globals.h exceptions.h csocket.h srvdef.h readconf.h tpl_cgi.h cxmltree.h \
cms_types.h threadkey.h cgib.h cgi.h mutex.h

CFLAGS=-Wall $(XML_INCLUDE)
# -D_DEBUG -g

TPL_CGI=tpl_cgi

LINKS=csocket.h csocket.cpp exceptions.h srvdef.h readconf.h readconf.cpp \
cxmltree.h cxmltree.cpp cms_types.h threadkey.h mutex.cpp mutex.h

all: links $(TPL_CGI)

links: dummy
	for l in $(LINKS); do \
	    if [ ! -L $$l ]; then ln -s ../../parser/parser/$$l; fi; \
	done

$(TPL_CGI): $(OBJ_FILES)
	g++ -o $(TPL_CGI) $(OBJ_FILES) $(LDFLAGS) $(LDLIBS)
	strip $(TPL_CGI)

clean: dummy
	rm -f *.o *~ tpl_cgi.h $(TPL_CGI) $(LINKS)

main.o: main.cpp $(DEPENDENCES)
	g++ -c -o main.o $(CFLAGS) main.cpp

csocket.o: csocket.cpp $(DEPENDENCES)
	g++ -c -o csocket.o $(CFLAGS) csocket.cpp

readconf.o: readconf.cpp $(DEPENDENCES)
	g++ -c -o readconf.o $(CFLAGS) readconf.cpp

cgi.o: cgi.cpp $(DEPENDENCES)
	g++ -c -o cgi.o $(CFLAGS) cgi.cpp

mutex.o: mutex.cpp $(DEPENDENCES)
	g++ -c -o mutex.o $(CFLAGS) mutex.cpp

install: all
	mkdir -p $(CGI_COMMON_DIR)
	cp -f $(TPL_CGI) $(CGI_COMMON_DIR)
	chown $(ROOT_USER):$(APACHE_GROUP) $(CGI_COMMON_DIR)/$(TPL_CGI)
	chmod 750 $(CGI_COMMON_DIR)/$(TPL_CGI)
	mkdir -p $(ETC_DIR)
	echo -e "PARSER_IP $(ADMIN_INTERFACE_PARSER_IP)\nPARSER_PORT $(ADMIN_INTERFACE_PARSER_PORT)\n" > $(ETC_DIR)/tpl_cgi.conf
	chown $(ROOT_USER):$(APACHE_GROUP) $(ETC_DIR)/tpl_cgi.conf
	chmod 640 $(ETC_DIR)/tpl_cgi.conf

uninstall: dummy
	rm -f $(CGI_COMMON_DIR)/$(TPL_CGI) $(ETC_DIR)/tpl_cgi.conf

dummy:
