CC=gcc
CFLAGS=-Wall -O3 -fomit-frame-pointer -pipe -mpentiumpro -malign-jumps=4 -malign-loops=4 -malign-functions=4
LDLIBS=-L/usr/lib/mysql -lmysqlclient -lm -lz -lcrypt

all: gather

gather: gather.o kwd.o

broker: broker.o kwd.o

gather.o: gather.c kwd.h

kwd.o: kwd.c kwd.h

broker.o: broker.c kwd.h broker.h

clean:
	-rm -f *.o gather broker *~

install: all
	mkdir -p $(UTIL_BIN)
	cp -f gather $(UTIL_BIN)
	chown root.$(HTTP_GROUP) $(UTIL_BIN) -R
	chmod 750 $(UTIL_BIN) -R
	cat /etc/crontab | grep -w gather > /dev/null || echo "0 0 * * * root $(UTIL_BIN)/gather" >> /etc/crontab
	killall -1 crond

uninstall: dummy
	./uninstall

package: all
	mkdir -p $(PACKAGE_DIR)/search
	cp -f Makefile.inst $(PACKAGE_DIR)/search/Makefile
	cp -f gather $(PACKAGE_DIR)/search
	cp -f uninstall $(PACKAGE_DIR)/search

dummy:
