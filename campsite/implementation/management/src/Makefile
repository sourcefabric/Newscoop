INCLUDE_PATH=-I$(MYSQL_H_PATH)
CFLAGS=-Wall $(INCLUDE_PATH)
CXXFLAGS=$(CFLAGS)
LDFLAGS=-L$(LIBMYSQLCLIENT_PATH)
LIBS=-lmysqlclient -lm -lz $(LIBCRYPT_LINK)

OBJ=get_img.o parse_file.o process_i.o cleanb.o readconf.o
TARGET=get_img process_i cleanb

LINKS=globals.h readconf.h readconf.cpp

all: links $(TARGET)

links: dummy
	for l in $(LINKS); do \
	    if [ ! -L $$l ]; then ln -s ../../parser/parser/$$l; fi; \
	done

get_img: get_img.o readconf.o
	g++ -o $@ get_img.o readconf.o $(LDFLAGS) $(LIBS)
	strip get_img

process_i: process_i.o parse_file.o readconf.o
	g++ -o $@ process_i.o parse_file.o readconf.o $(LDFLAGS) $(LIBS)
	strip process_i

cleanb:	cleanb.o parse_file.o
	g++ -o $@ cleanb.o parse_file.o
	strip cleanb

get_img.o: get_img.cpp readconf.h globals.h

readconf.o: readconf.cpp readconf.h globals.h

parse_file.o: parse_file.cpp parse_file.h

process_i.o: process_i.cpp parse_file.h dir_conf.h readconf.h globals.h

cleanb.o: cleanb.cpp parse_file.h dir_conf.h

clean:
	rm -f *~ core $(OBJ) dir_conf.h $(LINKS) configure.h
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(TARGET); fi

install: $(ALL)
	cp -f get_img $(CGI_DIR)/
	chown $(ROOT_USER):$(HTTP_GROUP) $(CGI_DIR)/get_img
	chmod 750 $(CGI_DIR)/get_img
	cp -f process_i $(SCRIPT_DIR)/
	chown $(ROOT_USER):$(HTTP_GROUP) $(SCRIPT_DIR)/process_i
	chmod 750 $(SCRIPT_DIR)/process_i
	cp -f cleanb $(CGI_DIR)/
	chown $(ROOT_USER):$(HTTP_GROUP) $(CGI_DIR)/cleanb
	chmod 755 $(CGI_DIR)/cleanb
	chmod u+s $(CGI_DIR)/cleanb

uninstall:
	rm -f $(CGI_DIR)/get_img $(SCRIPT_DIR)/process_i $(CGI_DIR)/cleanb

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/src
	cp -f Makefile $(PACKAGE_DIR)/src
	cp -f configure $(PACKAGE_DIR)/src
	cp -f get_img $(PACKAGE_DIR)/src
	cp -f process_i $(PACKAGE_DIR)/src
	cp -f cleanb $(PACKAGE_DIR)/src

dummy:
