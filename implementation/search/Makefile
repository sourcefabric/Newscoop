include ../../make.env

PACKAGE_DIR=$(BIN_PACKAGE_DIR)/implementation

INCLUDE_PATH=-I$(MYSQL_H_PATH)

CFLAGS=-Wall -O3 -fomit-frame-pointer -falign-jumps=4 -falign-loops=4 -falign-functions=4 $(INCLUDE_PATH)
CXXFLAGS=$(CFLAGS)
LDFLAGS=-L$(LIBMYSQLCLIENT_PATH)
LDLIBS=-lmysqlclient -lm -lz $(LIBCRYPT_LINK)

LINKS=globals.h readconf.h readconf.cpp cms_types.h threadkey.h

all: links gather

links: dummy
	for l in $(LINKS); do \
	    if [ ! -L $$l ]; then ln -s ../parser/parser/$$l; fi; \
	done

gather: gather.o kwd.o readconf.o
	g++ -o gather gather.o kwd.o readconf.o $(LDFLAGS) $(LDLIBS)

broker: broker.o kwd.o readconf.o
	g++ -o broker broker.o kwd.o readconf.o $(LDFLAGS) $(LDLIBS)

gather.o: gather.cpp kwd.h configure.h readconf.h globals.h

kwd.o: kwd.cpp kwd.h

broker.o: broker.cpp kwd.h broker.h configure.h readconf.h globals.h

readconf.o: readconf.cpp readconf.h globals.h

clean: dummy
	rm -f *.o *~ configure.h $(LINKS) .env_vars gather broker

install: $(ALL)
	./install_search

uninstall: dummy
	./uninstall_search

dummy:
