
LDFLAGS=

CFLAGS=-Wall -g

LINKS=csocket.h csocket.cpp exceptions.h global.h

all: links smtp_wrapper

links:
	for l in $(LINKS); do if [ ! -L $$l ]; then ln -s ../../../parser/parser/$$l; fi; done

smtp_wrapper: smtp_wrapper.o csocket.o
	g++ -o smtp_wrapper smtp_wrapper.o csocket.o $(LDFLAGS)

clean: dummy
	rm -f *.o *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f smtp_wrapper; fi
	for l in $(LINKS); do rm -f $$l; done

smtp_wrapper.o: smtp_wrapper.cpp
	g++ -c -o smtp_wrapper.o $(CFLAGS) smtp_wrapper.cpp

csocket.o: csocket.cpp csocket.h exceptions.h global.h
	g++ -c -o csocket.o $(CFLAGS) csocket.cpp

install: $(ALL)
	mkdir -p $(BIN_DIR)
	cp -f smtp_wrapper $(BIN_DIR)
	chown $(ROOT_USER).$(ROOT_GROUP) $(BIN_DIR)/smtp_wrapper
	chmod 755 $(BIN_DIR)/smtp_wrapper

uninstall: dummy
	rm -f $(BIN_DIR)/smtp_wrapper

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/smtp_wrapper/smtp_wrapper
	cp -f Makefile $(PACKAGE_DIR)/smtp_wrapper/smtp_wrapper
	cp -f smtp_wrapper $(PACKAGE_DIR)/smtp_wrapper/smtp_wrapper

dummy:

