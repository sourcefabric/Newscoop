OBJ_FILES=main.o csocket.o readconf.o

LDFLAGS=
LDLIBS=

DEPENDENCES=globals.h exceptions.h csocket.h srvdef.h readconf.h tpl_cgi.h

CFLAGS=-Wall
# -D_DEBUG -g

TPL_CGI=tpl_cgi

LINKS=csocket.h csocket.cpp exceptions.h globals.h srvdef.h readconf.h readconf.cpp

all: links $(TPL_CGI)

links: dummy
	for l in $(LINKS); do \
	    if [ ! -L $$l ]; then ln -s ../../parser/parser/$$l; fi; \
	done

$(TPL_CGI): $(OBJ_FILES)
	g++ -o $(TPL_CGI) $(OBJ_FILES) $(LDFLAGS) $(LDLIBS)
	strip $(TPL_CGI)

clean: dummy
	rm -f *.o *~ tpl_cgi.h .env_vars
	for l in $(LINKS); do rm -f $$l; done
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(TPL_CGI); fi

main.o: main.cpp $(DEPENDENCES)
	g++ -c -o main.o $(CFLAGS) main.cpp

csocket.o: csocket.cpp $(DEPENDENCES)
	g++ -c -o csocket.o $(CFLAGS) csocket.cpp

readconf.o: readconf.cpp $(DEPENDENCES)
	g++ -c -o readconf.o $(CFLAGS) readconf.cpp

install: $(ALL)
	mkdir -p $(CGI_DIR)
	cp -f $(TPL_CGI) $(CGI_DIR)
	chown $(ROOT_USER).$(HTTP_GROUP) $(CGI_DIR)/$(TPL_CGI)
	chmod 750 $(CGI_DIR)/$(TPL_CGI)
	mkdir -p $(CONF_DIR)
	echo -e "PARSER_IP $(ADMIN_INTERFACE_PARSER_IP)\nPARSER_PORT $(ADMIN_INTERFACE_PARSER_PORT)\n" > $(CONF_DIR)/tpl_cgi.conf
	chown $(ROOT_USER).$(HTTP_GROUP) $(CONF_DIR)/tpl_cgi.conf
	chmod 640 $(CONF_DIR)/tpl_cgi.conf

uninstall: dummy
	rm -f $(CGI_DIR)/$(TPL_CGI) $(CONF_DIR)/tpl_cgi.conf

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/tpl_cgi
	cp -f Makefile $(PACKAGE_DIR)/tpl_cgi
	cp -f configure $(PACKAGE_DIR)/tpl_cgi
	cp -f $(TPL_CGI) $(PACKAGE_DIR)/tpl_cgi

dummy:
