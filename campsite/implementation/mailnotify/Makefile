ROOT_DIR= .
MY_INCLUDE_DIR=

MYSQL_LIBS= -L/usr/lib/mysql -lmysqlclient -lnsl -lm -lz -lcrypt

DEPENDANCES= *.h

CFLAGS= -Wall

all: links notifyendsubs notifyevents

links:
	@if [ ! -L sql_connect.h ]; then /bin/ln -s ../parser/parser/sql_connect.h; fi

notifyendsubs: notifyendsubs.o
	g++ -o notifyendsubs notifyendsubs.o $(MYSQL_LIBS)
	strip notifyendsubs

notifyevents: notifyevents.o
	g++ -o notifyevents notifyevents.o $(MYSQL_LIBS)
	strip notifyevents

clean:
	rm -f *.o *~ notifyendsubs notifyevents sql_connect.h

%.o: %.C $(DEPENDANCES)
	g++ -c -o $@ $(CFLAGS) $< -I$(ROOT_DIR)$(MY_INCLUDE_DIR)

install: all
	mkdir -p $(UTIL_BIN)
	cp -f notifyendsubs $(UTIL_BIN)
	cp -f notifyevents $(UTIL_BIN)
	chown root.$(HTTP_GROUP) $(UTIL_BIN) -R
	chmod 750 $(UTIL_BIN) -R
	cat /etc/crontab | grep "$(UTIL_BIN)/notifyendsubs" > /dev/null || echo "0 0 * * * root $(UTIL_BIN)/notifyendsubs" >> /etc/crontab
	cat /etc/crontab | grep "$(UTIL_BIN)/notifyevents" > /dev/null || echo "* * * * * root $(UTIL_BIN)/notifyevents" >> /etc/crontab
	killall -1 crond

uninstall: dummy
	./uninstall

package: all
	mkdir -p $(PACKAGE_DIR)/mailnotify
	cp -f Makefile.inst $(PACKAGE_DIR)/mailnotify/Makefile
	cp -f uninstall $(PACKAGE_DIR)/mailnotify
	cp -f notifyendsubs $(PACKAGE_DIR)/mailnotify
	cp -f notifyevents $(PACKAGE_DIR)/mailnotify

dummy:
