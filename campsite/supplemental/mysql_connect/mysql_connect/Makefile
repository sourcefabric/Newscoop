LDLIBS=-lmysqlclient -lm -lz
LDFLAGS=-L/usr/lib/mysql

CFLAGS=-Wall

all: mysql_connect

mysql_connect: mysql_connect.o
	g++ -o mysql_connect mysql_connect.o $(LDFLAGS) $(LDLIBS)

clean:
	rm -f *.o *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f mysql_connect; fi

mysql_connect.o: mysql_connect.cpp
	g++ -c -o mysql_connect.o $(CFLAGS) mysql_connect.cpp

install: $(ALL)
	mkdir -p $(BIN_DIR)
	cp -f mysql_connect $(BIN_DIR)
	chown $(ROOT_USER).$(HTTP_GROUP) $(BIN_DIR)/mysql_connect
	chmod 755 $(BIN_DIR)/mysql_connect

uninstall: dummy
	rm -f $(BIN_DIR)/mysql_connect

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/mysql_connect
	cp -f Makefile $(PACKAGE_DIR)/mysql_connect
	cp -f mysql_connect $(PACKAGE_DIR)/mysql_connect

dummy:

