export PRIV_DIR5=$(PRIV_DIR4)/articles

subsystem: all
	$(MAKE) -C images
	$(MAKE) -C topics

all: ;

install: $(ALL)
	mkdir -p $(PRIV_DIR5)
	cp -f *.php $(PRIV_DIR5)/
	$(MAKE) -C images install
	$(MAKE) -C topics install

clean:
	rm -f *~
	$(MAKE) -C images clean
	$(MAKE) -C topics clean

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/articles
	cp -f Makefile $(PACKAGE_DIR)/articles
	cp -f *.php $(PACKAGE_DIR)/articles
	$(MAKE) -C images package PACKAGE_DIR=$(PACKAGE_DIR)/articles
	$(MAKE) -C topics package PACKAGE_DIR=$(PACKAGE_DIR)/articles
