include ../../make.env

PACKAGE_DIR=$(BIN_PACKAGE_DIR)/implementation

INCLUDE_PATH=-I$(MYSQL_H_PATH)

CFLAGS=-Wall -O3 $(INCLUDE_PATH)
CXXFLAGS=$(CFLAGS)
LDFLAGS=-L$(LIBMYSQLCLIENT_PATH)
LDLIBS=-lmysqlclient -lm -lz $(LIBCRYPT_LINK)

LINKS=globals.h readconf.h readconf.cpp

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
	rm -f *.o *~ configure.h $(LINKS)
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f gather broker; fi

install: $(ALL)
	./install_search

uninstall: dummy
	./uninstall_search

package: $(ALL)
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)/search
	cp -f Makefile $(PACKAGE_DIR)/search
	cp -f configure $(PACKAGE_DIR)/search
	cp -f gather $(PACKAGE_DIR)/search
	cp -f uninstall_search $(PACKAGE_DIR)/search

dummy:
