include ../../make.env

DEPENDENCES= sql_macros.h configure.h

INCLUDE_PATH=-I$(MYSQL_H_PATH)

CFLAGS= -Wall $(INCLUDE_PATH)
CXXFLAGS=$(CFLAGS)
LDFLAGS=-L$(LIBMYSQLCLIENT_PATH)
LDLIBS=-lmysqlclient -lm -lz $(LIBCRYPT_LINK) $(PTHREAD_LINK) $(NSL_LINK)

LINKS=globals.h readconf.h readconf.cpp cms_types.h threadkey.h ccampsiteinstance.h \
 ccampsiteinstance.cpp mutex.h mutex.cpp

all: links notifyendsubs notifyevents
	$(MAKE) -C smtp_wrapper

links: dummy
	for l in $(LINKS); do \
	    if [ ! -L $$l ]; then ln -s ../parser/parser/$$l; fi; \
	done
	if [ ! -L "configure.h" ]; then ln -s "$(INSTALL_CONF)/configure.h"; fi

notifyendsubs: notifyendsubs.o ccampsiteinstance.o mutex.o readconf.o
	g++ -o notifyendsubs notifyendsubs.o readconf.o ccampsiteinstance.o mutex.o $(LDFLAGS) $(LDLIBS)
	strip notifyendsubs

notifyevents: notifyevents.o ccampsiteinstance.o mutex.o readconf.o
	g++ -o notifyevents notifyevents.o readconf.o ccampsiteinstance.o mutex.o $(LDFLAGS) $(LDLIBS)
	strip notifyevents

clean: dummy
	rm -f $(LINKS) *.o configure.h *~ notifyendsubs notifyevents
	$(MAKE) -C smtp_wrapper clean

ccampsiteinstance.o: ccampsiteinstance.cpp $(DEPENDENCES) ccampsiteinstance.h readconf.h globals.h configure.h
	g++ -c -o ccampsiteinstance.o $(CFLAGS) ccampsiteinstance.cpp

mutex.o: mutex.cpp $(DEPENDENCES) ccampsiteinstance.h readconf.h globals.h configure.h
	g++ -c -o mutex.o $(CFLAGS) mutex.cpp

readconf.o: readconf.cpp $(DEPENDENCES) ccampsiteinstance.h readconf.h globals.h configure.h
	g++ -c -o readconf.o $(CFLAGS) readconf.cpp

notifyevents.o: notifyevents.cpp $(DEPENDENCES) ccampsiteinstance.h readconf.h globals.h configure.h
	g++ -c -o notifyevents.o $(CFLAGS) notifyevents.cpp

notifyendsubs.o: notifyendsubs.cpp $(DEPENDENCES) ccampsiteinstance.h readconf.h globals.h configure.h
	g++ -c -o notifyendsubs.o $(CFLAGS) notifyendsubs.cpp

install: all
	mkdir -p "$(ETC_DIR)"
	$(MAKE) -C smtp_wrapper install
	./install_mailnotify

uninstall: dummy
	$(MAKE) -C smtp_wrapper uninstall
	./uninstall_mailnotify

dummy:
