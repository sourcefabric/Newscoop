include ../../make.env

all: dummy

clean: dummy
	$(MAKE) -C smtp_wrapper clean

install: all
	mkdir -p "$(ETC_DIR)"
	$(MAKE) -C smtp_wrapper install
#	./install_mailnotify

uninstall: dummy
	$(MAKE) -C smtp_wrapper uninstall
#	./uninstall_mailnotify

dummy:
