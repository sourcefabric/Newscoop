include ../../make.env

DEPENDENCES= sql_macros.h configure.h

INCLUDE_PATH=-I$(MYSQL_H_PATH)

CFLAGS= -Wall $(INCLUDE_PATH)
CXXFLAGS=$(CFLAGS)

LINKS=globals.h readconf.h readconf.cpp cms_types.h threadkey.h

all: links notifyendsubs notifyevents
	$(MAKE) -C smtp_wrapper

links: dummy
	for l in $(LINKS); do \
	    if [ ! -L $$l ]; then ln -s ../parser/parser/$$l; fi; \
	done

notifyendsubs: notifyendsubs.o readconf.o
	g++ -o notifyendsubs notifyendsubs.o readconf.o -L$(LIBMYSQLCLIENT_PATH) -lmysqlclient $(NSL_LINK) -lm -lz $(LIBCRYPT_LINK)
	strip notifyendsubs

notifyevents: notifyevents.o readconf.o
	g++ -o notifyevents notifyevents.o readconf.o -L$(LIBMYSQLCLIENT_PATH) -lmysqlclient $(NSL_LINK) -lm -lz $(LIBCRYPT_LINK)
	strip notifyevents

clean: dummy
	rm -f $(LINKS) .env_vars *.o configure.h *~ notifyendsubs notifyevents
	$(MAKE) -C smtp_wrapper clean

readconf.o: readconf.cpp readconf.h
	g++ -c -o readconf.o readconf.cpp $(CFLAGS)

%.o: %.cpp $(DEPENDENCES) readconf.h globals.h
	g++ -c -o $@ $(CFLAGS) $<

install: $(ALL)
	mkdir -p $(ETC_DIR)
	echo -e "SERVER $(EVENT_HANDLERS_SMTP_SERVER)\nWRAPPER $(BIN_DIR)/smtp_wrapper" > $(ETC_DIR)/smtp.conf
	$(MAKE) -C smtp_wrapper install
	./install_mailnotify

uninstall: dummy
	$(MAKE) -C smtp_wrapper uninstall
	./uninstall_mailnotify

dummy:
