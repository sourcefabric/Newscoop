include ../../make.env

INCLUDE_PATH=-I$(MYSQL_H_PATH)

CFLAGS=-Wall -O3 $(INCLUDE_PATH)
CXXFLAGS=$(CFLAGS)
LDFLAGS=-L$(LIBMYSQLCLIENT_PATH)
LDLIBS=-lmysqlclient -lm -lz $(LIBCRYPT_LINK) $(PTHREAD_LINK)

PARSER_LINKS=globals.h readconf.h readconf.cpp cms_types.h threadkey.h mutex.h mutex.cpp
MAILNOTIFY_LINKS=ccampsiteinstance.h ccampsiteinstance.cpp

all: links gather

links: dummy
	@for l in $(PARSER_LINKS); do \
	    if [ ! -L $$l ]; then ln -s ../parser/parser/$$l; fi; \
	done
	@for l in $(MAILNOTIFY_LINKS); do \
	    if [ ! -L $$l ]; then ln -s ../mailnotify/$$l; fi; \
	done
	@if [ ! -L "configure.h" ]; then ln -s "$(INSTALL_CONF)/configure.h"; fi

gather: gather.o kwd.o readconf.o ccampsiteinstance.o mutex.o
	g++ -o gather gather.o kwd.o readconf.o ccampsiteinstance.o mutex.o $(LDFLAGS) $(LDLIBS)

gather.o: gather.cpp kwd.h configure.h readconf.h globals.h ccampsiteinstance.h mutex.h

kwd.o: kwd.cpp kwd.h

ccampsiteinstance.o: ccampsiteinstance.cpp ccampsiteinstance.h globals.h

readconf.o: readconf.cpp readconf.h globals.h

mutex.o: mutex.cpp mutex.h globals.h

clean: dummy
	rm -f *.o *~ configure.h $(PARSER_LINKS) $(MAILNOTIFY_LINKS) gather

install: all
	./install_search

uninstall: dummy
	./uninstall_search

dummy:
