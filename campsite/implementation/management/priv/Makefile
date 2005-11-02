include ../../../make.env

ALL=all

all: dummy

install: $(ALL)
	mkdir -p $(PRIV_COMMON_DIR)/article_import
	cp -fr article_import/* $(PRIV_COMMON_DIR)/article_import
	cp -rf imagearchive $(PRIV_COMMON_DIR)
	cp -f *.php $(PRIV_COMMON_DIR)/
	mkdir -p $(PRIV_COMMON_DIR)/img
	cp -fr img/* $(PRIV_COMMON_DIR)/img
	$(MAKE) -C pub install
	$(MAKE) -C issues install
	$(MAKE) -C sections install
	$(MAKE) -C articles install
	$(MAKE) -C templates install
	$(MAKE) -C topics install	
	$(MAKE) -C users install
	$(MAKE) -C languages install
	$(MAKE) -C user_types install
	$(MAKE) -C article_types install
	$(MAKE) -C logs install
	$(MAKE) -C country install
	$(MAKE) -C localizer install

clean: dummy
	$(MAKE) -C pub clean
	$(MAKE) -C issues clean
	$(MAKE) -C sections clean
	$(MAKE) -C articles clean
	$(MAKE) -C templates clean
	$(MAKE) -C topics clean	
	$(MAKE) -C users clean
	$(MAKE) -C languages clean
	$(MAKE) -C user_types clean
	$(MAKE) -C article_types clean
	$(MAKE) -C logs clean
	$(MAKE) -C country clean
	$(MAKE) -C localizer clean

uninstall: dummy

dummy:
