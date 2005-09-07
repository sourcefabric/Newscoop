
LDFLAGS=$(PTHREAD_LINK)

CFLAGS=-Wall
CSOCKET_FLAGS=-DHAVE_GETHOSTBYADDR_R=$(HAVE_GETHOSTBYADDR_R) -DHAVE_GETHOSTBYNAME_R=$(HAVE_GETHOSTBYNAME_R) -DHAVE_GETSERVBYNAME_R=$(HAVE_GETSERVBYNAME_R)
LINKS=csocket.h csocket.cpp exceptions.h mutex.cpp mutex.h

all: links smtp_wrapper

links:
	for l in $(LINKS); do if [ ! -L $$l ]; then ln -s ../../../parser/parser/$$l; fi; done

smtp_wrapper: smtp_wrapper.o csocket.o mutex.o
	g++ -o smtp_wrapper smtp_wrapper.o csocket.o mutex.o $(LDFLAGS)
	strip smtp_wrapper

clean: dummy
	rm -f *.o *~ $(LINKS) smtp_wrapper

smtp_wrapper.o: smtp_wrapper.cpp
	g++ -c -o smtp_wrapper.o $(CFLAGS) smtp_wrapper.cpp

csocket.o: csocket.cpp csocket.h exceptions.h globals.h
	g++ -c -o csocket.o $(CFLAGS) $(CSOCKET_FLAGS) csocket.cpp

mutex.o: mutex.cpp mutex.h
	g++ -c -o mutex.o $(CFLAGS) mutex.cpp

install: all
	mkdir -p "$(BIN_DIR)"
	install -o $(ROOT_USER) -g $(APACHE_GROUP) -m 755 smtp_wrapper "$(BIN_DIR)"

uninstall: dummy
	rm -f "$(BIN_DIR)/smtp_wrapper"

dummy:

