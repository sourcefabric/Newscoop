CC	= gcc
CFLAGS	= -Wall -g -pipe
LDFLAGS	= -L/usr/lib/mysql
LIBS	= -lmysqlclient -lm -lz -lcrypt

OBJ	= get_img.o ls_url.o url_util.o parse_file.o process_i.o process_t.o cleanb.o
TARGET	= get_img ls_url process_i process_t cleanb
HEADERS = sql.h

all: $(TARGET)

%.h: %.m4 ../macros.m4
	m4 ../macros.m4 $< >$@

get_img: get_img.o
	$(CC) $(LDFLAGS) -o $@ get_img.o $(LIBS)
	strip get_img

process_t: process_t.o parse_file.o url_util.o
	$(CC) -o $@ process_t.o parse_file.o url_util.o
	strip process_t

process_i: process_i.o parse_file.o
	$(CC) $(LDFLAGS) -o $@ process_i.o parse_file.o $(LIBS)
	strip process_i

ls_url: ls_url.o url_util.o
	$(CC) -o $@ ls_url.o url_util.o
	strip ls_url

cleanb:	cleanb.o parse_file.o
	$(CC) -o $@ cleanb.o parse_file.o
	strip cleanb

get_img.o: get_img.c sql.h

url_util.o: url_util.c url_util.h

parse_file.o: parse_file.c parse_file.h

process_i.o: process_i.c parse_file.h

process_t.o: process_t.c parse_file.h url_util.h

cleanb.o: cleanb.c

clean:
	rm -f *~ core $(OBJ) $(TARGET) $(HEADERS)

install: all
	cp ls_url $(SCRIPT_BIN)/
	chmod +x $(SCRIPT_BIN)/ls_url
	cp get_img $(CGI_BIN)/
	chmod 755 $(CGI_BIN)/get_img
	cp process_i $(SCRIPT_BIN)/
	chmod 755 $(SCRIPT_BIN)/process_i
	cp process_t $(SCRIPT_BIN)/
	chmod 755 $(SCRIPT_BIN)/process_t
	cp cleanb $(CGI_BIN)/
	chmod 755 $(CGI_BIN)/cleanb
	chmod u+s $(CGI_BIN)/cleanb

uninstall:
	rm -f $(SCRIPT_BIN)/ls_url $(CGI_BIN)/get_img $(SCRIPT_BIN)/process_i $(SCRIPT_BIN)/process_t $(CGI_BIN)/cleanb

package: all
	mkdir -p $(PACKAGE_DIR)/src
	cp -f Makefile.inst $(PACKAGE_DIR)/src/Makefile
	cp -f ls_url $(PACKAGE_DIR)/src
	cp -f get_img $(PACKAGE_DIR)/src
	cp -f process_i $(PACKAGE_DIR)/src
	cp -f process_t $(PACKAGE_DIR)/src
	cp -f cleanb $(PACKAGE_DIR)/src
