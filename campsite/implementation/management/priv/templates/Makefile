include ../../../../make.env

export PRIV_DIR2=$(PRIV_COMMON_DIR)/templates

all: ;

install: all
	mkdir -p $(PRIV_DIR2)
	cp -f *.php $(PRIV_DIR2)/

clean:
	rm -f *~ 

uninstall: dummy

dummy:
