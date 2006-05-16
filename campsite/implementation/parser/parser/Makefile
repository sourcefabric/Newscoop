include ../../../make.env

OBJ_FILES=main.o lex.o atoms.o parser.o actions.o util.o error.o\
 context.o cparser.o csocket.o threadpool.o mutex.o readconf.o\
 thread.o process_req.o attributes.o data_types.o operators.o\
 cmessage.o cmessagefactory.o cpublication.o cpublicationsregister.o curl.o\
 curlshortnames.o curltype.o curltyperegister.o cxmlreader.o curltemplatepath.o\
 articlecomment.o


CLEAN_XML_INCLUDE=$(shell echo -n "$(XML_INCLUDE)")
CLEAN_XML_LINK=$(shell echo -n "$(XML_LINK)")
LDFLAGS=-L$(LIBMYSQLCLIENT_PATH) -L$(LIBPTHREAD_PATH)
LIBS=-lmysqlclient $(NSL_LINK) -lm -lz $(LIBCRYPT_LINK) $(PTHREAD_LINK) $(CLEAN_XML_LINK)

DEPENDENCES= *.h

INCLUDE_PATH=-I$(MYSQL_H_PATH)

CSOCKET_FLAGS=-DHAVE_GETHOSTBYADDR_R=$(HAVE_GETHOSTBYADDR_R) -DHAVE_GETHOSTBYNAME_R=$(HAVE_GETHOSTBYNAME_R) -DHAVE_GETSERVBYNAME_R=$(HAVE_GETSERVBYNAME_R)
CFLAGS=-D_REENTRANT -Wall $(INCLUDE_PATH) $(CSOCKET_FLAGS) $(CLEAN_XML_INCLUDE)
# -D_DEBUG_SOURCE -g
# -D_DEBUG

export CAMPSITE_SERVER=campsite_server

all: links $(CAMPSITE_SERVER)

links: dummy
	@if [ ! -L "configure.h" ]; then ln -s "$(INSTALL_CONF)/configure.h"; fi

$(CAMPSITE_SERVER): $(OBJ_FILES)
	g++ -o $(CAMPSITE_SERVER) $(OBJ_FILES) $(LDFLAGS) $(LIBS)
	@if ! echo "$(CFLAGS)" | grep "_DEBUG" > /dev/null; then echo "stripping $(CAMPSITE_SERVER)"; strip $(CAMPSITE_SERVER); fi

clean: dummy
	rm -f *.o *~ configure.h $(CAMPSITECTL) $(CAMPSITEINIT) $(CAMPSITE_SERVER)

%.o: %.cpp $(DEPENDENCES)
	g++ -c -o $@ $(CFLAGS) $<

install: all
	mkdir -p $(BIN_DIR)
	@if [ -f $(BIN_DIR)/$(CAMPSITE_SERVER) ]; then $(KILLALL) -q campsite_server; rm -f $(BIN_DIR)/$(CAMPSITE_SERVER); fi
	install -o $(APACHE_USER) -g $(APACHE_GROUP) -m 750 $(CAMPSITE_SERVER) "$(BIN_DIR)"
	./configure_parser

uninstall: dummy
	rm -f $(BIN_DIR)/$(CAMPSITE_SERVER)

dummy:
