INCLUDE_PATH=-I$(MYSQL_H_PATH)
CFLAGS=-Wall $(INCLUDE_PATH)
CXXFLAGS=$(CFLAGS)
LDFLAGS=-L$(LIBMYSQLCLIENT_PATH)
LIBS=-lmysqlclient -lm -lz $(LIBCRYPT_LINK)

OBJ=get_img.o url_util.o parse_file.o process_t.o readconf.o
TARGET=get_img process_t

LINKS=globals.h readconf.h readconf.cpp cms_types.h threadkey.h

all: links $(TARGET)

links: dummy
	for l in $(LINKS); do \
	    if [ ! -L $$l ]; then ln -s ../../parser/parser/$$l; fi; \
	done

get_img: get_img.o readconf.o
	g++ -o $@ get_img.o readconf.o $(LDFLAGS) $(LIBS)
	strip get_img

process_t: process_t.o parse_file.o url_util.o
	g++ -o $@ process_t.o parse_file.o url_util.o
	strip process_t

get_img.o: get_img.cpp readconf.h globals.h

readconf.o: readconf.cpp readconf.h globals.h

url_util.o: url_util.cpp url_util.h

parse_file.o: parse_file.cpp parse_file.h

process_t.o: process_t.cpp parse_file.h url_util.h dir_conf.h

clean:
	rm -f *~ core $(OBJ) dir_conf.h $(LINKS) configure.h $(TARGET)

install: $(ALL)
	cp -f get_img $(CGI_COMMON_DIR)/
	chown $(ROOT_USER):$(APACHE_GROUP) $(CGI_COMMON_DIR)/get_img
	chmod 750 $(CGI_COMMON_DIR)/get_img
	cp -f process_t $(SCRIPT_COMMON_DIR)/
	chown $(ROOT_USER):$(APACHE_GROUP) $(SCRIPT_COMMON_DIR)/process_t
	chmod 755 $(SCRIPT_COMMON_DIR)/process_t

uninstall:
	rm -f $(CGI_COMMON_DIR)/get_img $(SCRIPT_COMMON_DIR)/process_t

dummy:
