include ../../../make.env

ALL=all

all: dummy
	$(MAKE) -C pub
	$(MAKE) -C issues
	$(MAKE) -C sections
	$(MAKE) -C articles
	$(MAKE) -C templates
	$(MAKE) -C topics
	$(MAKE) -C users
	$(MAKE) -C languages
	$(MAKE) -C u_types
	$(MAKE) -C a_types
	$(MAKE) -C logs
	$(MAKE) -C country
	$(MAKE) -C localizer

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
	$(MAKE) -C u_types install
	$(MAKE) -C a_types install
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
	$(MAKE) -C u_types clean
	$(MAKE) -C a_types clean
	$(MAKE) -C logs clean
	$(MAKE) -C country clean
	$(MAKE) -C localizer clean

uninstall: dummy

dummy:
