include ../make.env

all:
	$(MAKE) -C mysql_connect

install: $(ALL)
	$(MAKE) -C mysql_connect install

clean:
	$(MAKE) -C mysql_connect clean

uninstall: dummy
	$(MAKE) -C mysql_connect uninstall

dummy:
