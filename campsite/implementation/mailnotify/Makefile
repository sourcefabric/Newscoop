include ../../make.env

ALL=$(shell if [ "$(PACKAGE_TYPE)" = "source" ]; then echo all; else echo dummy; fi)

PACKAGE_DIR=$(BIN_PACKAGE_DIR)/implementation/mailnotify

DEPENDENCES= sql_macros.h configure.h

CFLAGS= -Wall

LINKS=global.h readconf.h readconf.cpp

all: links notifyendsubs notifyevents
	$(MAKE) -C smtp_wrapper

links: dummy
	for l in $(LINKS); do \
	    if [ ! -L $$l ]; then ln -s ../parser/parser/$$l; fi; \
	done

notifyendsubs: notifyendsubs.o readconf.o
	g++ -o notifyendsubs notifyendsubs.o readconf.o -L$(LIBMYSQLCLIENT_PATH) -lmysqlclient -lnsl -lm -lz -lcrypt
	strip notifyendsubs

notifyevents: notifyevents.o readconf.o
	g++ -o notifyevents notifyevents.o readconf.o -L$(LIBMYSQLCLIENT_PATH) -lmysqlclient -lnsl -lm -lz -lcrypt
	strip notifyevents

clean: dummy
	rm -f $(LINKS) .env_vars
	$(MAKE) -C smtp_wrapper clean
	rm -f *.o configure.h *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then \
	    rm -f notifyendsubs notifyevents; \
	fi

readconf.o: readconf.cpp readconf.h
	g++ -c -o readconf.o readconf.cpp $(CFLAGS)

%.o: %.cpp $(DEPENDENCES) readconf.h global.h
	g++ -c -o $@ $(CFLAGS) $<

install: $(ALL)
	mkdir -p $(CONF_DIR)
	echo -e "SERVER $(EVENT_HANDLERS_SMTP_SERVER)\nWRAPPER $(BIN_DIR)/smtp_wrapper" > $(CONF_DIR)/smtp.conf
	$(MAKE) -C smtp_wrapper install
	mkdir -p $(BIN_DIR)
	cp -f notifyendsubs $(BIN_DIR)
	cp -f notifyevents $(BIN_DIR)
	chown $(ROOT_USER).$(ROOT_GROUP) $(BIN_DIR)/notifyendsubs $(BIN_DIR)/notifyevents
	chmod 750 $(BIN_DIR)/notifyendsubs
	chmod 750 $(BIN_DIR)/notifyevents
	cat /etc/crontab | grep "$(BIN_DIR)/notifyendsubs" > /dev/null || echo "0 0 * * * $(ROOT_USER) $(BIN_DIR)/notifyendsubs" >> /etc/crontab
	cat /etc/crontab | grep "$(BIN_DIR)/notifyevents" > /dev/null || echo "* * * * * $(ROOT_USER) $(BIN_DIR)/notifyevents" >> /etc/crontab
	killall -1 crond

uninstall: dummy
	$(MAKE) -C smtp_wrapper uninstall
	./uninstall_mailnotify

package: $(ALL)
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(PACKAGE_DIR)
	cp -f Makefile $(PACKAGE_DIR)
	cp -f configure $(PACKAGE_DIR)
	cp -f uninstall_mailnotify $(PACKAGE_DIR)
	cp -f notifyendsubs $(PACKAGE_DIR)
	cp -f notifyevents $(PACKAGE_DIR)
	$(MAKE) -C smtp_wrapper package PACKAGE_DIR=$(PACKAGE_DIR)

dummy:
