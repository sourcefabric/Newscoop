OBJ_FILES=main.o lex.o atoms.o parser.o actions.o util.o error.o\
 context.o cparser.o cgi.o csocket.o threadpool.o mutex.o readconf.o\
 thread.o process_req.o attributes.o data_types.o operators.o

LDFLAGS=-L$(LIBMYSQLCLIENT_PATH) -L$(LIBPTHREAD_PATH)
LIBS=-lmysqlclient -lnsl -lm -lz -lcrypt -lpthread

DEPENDENCES= *.h

CFLAGS= -D_REENTRANT -Wall
# -D_DEBUG -g

export CAMPSITE_SERVER=$(APP_NAME)_server
export CAMPSITECTL=$(APP_NAME)ctl
export CAMPSITEINIT=$(APP_NAME)
export RC_INIT=/etc/rc.d/init.d/

all: $(CAMPSITE_SERVER)

$(CAMPSITE_SERVER): $(OBJ_FILES)
	g++ -o $(CAMPSITE_SERVER) $(OBJ_FILES) $(LDFLAGS) $(LIBS)
	strip $(CAMPSITE_SERVER)

clean: dummy
	rm -f *.o *~ srvdef.h $(CAMPSITECTL) $(CAMPSITEINIT) .env_vars
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(CAMPSITE_SERVER); fi

%.o: %.cpp $(DEPENDENCES)
	g++ -c -o $@ $(CFLAGS) $<

install: $(ALL)
	./install_parser

uninstall: dummy
	./uninstall_parser

package: $(ALL)
	mkdir $(PACKAGE_DIR)/parser
	cp -f configure $(PACKAGE_DIR)/parser
	cp -f Makefile $(PACKAGE_DIR)/parser
	cp -f install_parser $(PACKAGE_DIR)/parser
	cp -f uninstall_parser $(PACKAGE_DIR)/parser
	cp -f $(CAMPSITE_SERVER) $(PACKAGE_DIR)/parser
	cp -f $(CAMPSITECTL) $(PACKAGE_DIR)/parser
	cp -f $(CAMPSITEINIT) $(PACKAGE_DIR)/parser

dummy:
