export INSTALL_DIR2=$(INSTALL_DIR)/templates

all: index.xql idx.xql new_dir.xql do_new_dir.xql list.sh stempl.sh make_dir.sh upload_templ.xql upload_t.sh process_t.xql del.xql do_del.xql delete.sh

%.xql: %.m4 ../macros.m4
	m4 ../macros.m4 $< > $@

%.sh: %.m4 ../macros.m4
	m4 ../macros.m4 $< > $@

%.html: %.m4 ../macros.m4
	m4 ../macros.m4 $< > $@

install: all
	mkdir -p $(INSTALL_DIR2)
	cp *.xql $(INSTALL_DIR2)/
	cp list.sh $(SCRIPT_BIN)/list
	cp make_dir.sh $(SCRIPT_BIN)/make_dir
	cp stempl.sh $(SCRIPT_BIN)/stempl
	cp upload_t.sh $(CGI_BIN)/upload_t
	cp delete.sh $(SCRIPT_BIN)/delete
	chmod 755 $(SCRIPT_BIN)/list
	chmod 755 $(SCRIPT_BIN)/make_dir
	chmod 755 $(SCRIPT_BIN)/stempl
	chmod 755 $(CGI_BIN)/upload_t
	chmod 755 $(SCRIPT_BIN)/delete

clean:
	rm -f *.xql *.sh *~

uninstall: dummy
	rm -f $(SCRIPT_BIN)/list $(SCRIPT_BIN)/make_dir $(SCRIPT_BIN)/stempl $(CGI_BIN)/upload_t $(SCRIPT_BIN)/delete

package: all
	mkdir -p $(PACKAGE_DIR)/templates
	cp -f Makefile $(PACKAGE_DIR)/templates
	cp -f *.xql $(PACKAGE_DIR)/templates
	cp -f *.sh $(PACKAGE_DIR)/templates

dummy:
