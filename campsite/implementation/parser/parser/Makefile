ROOT_DIR= .
MY_INCLUDE_DIR=

OBJ_FILES=main.o tol_lex.o tol_atoms.o tol_parser.o tol_actions.o tol_util.o tol_error.o\
 tol_context.o tol_cparser.o cgi.o csocket.o threadpool.o mutex.o thread.o process_req.o

LIBS= -lnsl -lm -lz -lcrypt -lpthread -L/usr/lib/mysql -lmysqlclient

DEPENDANCES= *.h

CFLAGS= -D_REENTRANT
# -Wall -D_DEBUG -g

export CAMPSITE_SERVER=campsite_server
export CAMPSITECTL=campsitectl
export CAMPSITEINIT=campsite
export RC_INIT=/etc/rc.d/init.d/

all: $(CAMPSITE_SERVER)

$(CAMPSITE_SERVER): $(OBJ_FILES)
	g++ -o $(CAMPSITE_SERVER) $(OBJ_FILES) $(LIBS)
	strip $(CAMPSITE_SERVER)

clean:
	rm -f *.o *~ $(CAMPSITE_SERVER)

%.o: %.cpp $(DEPENDANCES)
	g++ -c -o $@ $(CFLAGS) $< -I$(ROOT_DIR)/$(MY_INCLUDE_DIR)

install: all
	./install

uninstall: dummy
	./uninstall

package: all
	mkdir $(PACKAGE_DIR)/parser
	cp -f Makefile.inst $(PACKAGE_DIR)/parser/Makefile
	cp -f install $(PACKAGE_DIR)/parser
	cp -f uninstall $(PACKAGE_DIR)/parser
	cp -f $(CAMPSITE_SERVER) $(PACKAGE_DIR)/parser
	cp -f $(CAMPSITECTL) $(PACKAGE_DIR)/parser
	cp -f $(CAMPSITEINIT) $(PACKAGE_DIR)/parser

dummy:
