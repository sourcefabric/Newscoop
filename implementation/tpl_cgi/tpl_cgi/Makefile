ROOT_DIR= .
MY_INCLUDE_DIR=

OBJ_FILES=main.o csocket.o

LIB_FLAGS=
LIBS=

DEPENDANCES=global.h exceptions.h csocket.h tol_srvdef.h

CFLAGS=-Wall
# -D_DEBUG -g

TPL_CGI=tpl_cgi

LINKS=csocket.h csocket.cpp exceptions.h global.h tol_srvdef.h

all: links $(TPL_CGI)

links: $(LINKS)

csocket.h:
	if [ ! -L csocket.h ]; then ln -s ../../parser/parser/csocket.h; fi

csocket.cpp:
	if [ ! -L csocket.cpp ]; then ln -s ../../parser/parser/csocket.cpp; fi

exceptions.h:
	if [ ! -L exceptions.h ]; then ln -s ../../parser/parser/exceptions.h; fi

global.h:
	if [ ! -L global.h ]; then ln -s ../../parser/parser/global.h; fi

tol_srvdef.h:
	if [ ! -L tol_srvdef.h ]; then ln -s ../../parser/parser/tol_srvdef.h; fi

$(TPL_CGI): $(OBJ_FILES)
	g++ -o $(TPL_CGI) $(OBJ_FILES) $(LIB_FLAGS) $(LIBS)
	strip $(TPL_CGI)

clean:
	rm -f *.o *~ $(TPL_CGI)
	for l in $(LINKS); do rm -f $$l; done

main.o: main.cpp $(DEPENDANCES)
	g++ -c -o main.o $(CFLAGS) main.cpp -I$(ROOT_DIR)/$(MY_INCLUDE_DIR)

csocket.o: csocket.cpp $(DEPENDANCES)
	g++ -c -o csocket.o $(CFLAGS) csocket.cpp -I$(ROOT_DIR)/$(MY_INCLUDE_DIR)

install: all
	mkdir -p $(CGI_BIN)
	cp -f $(TPL_CGI) $(CGI_BIN)

uninstall: dummy
	rm -f $(CGI_BIN)/$(TPL_CGI)

package: all
	mkdir -p $(PACKAGE_DIR)/tpl_cgi
	cp -f Makefile.inst $(PACKAGE_DIR)/tpl_cgi/Makefile
	cp -f $(TPL_CGI) $(PACKAGE_DIR)/tpl_cgi

dummy:
