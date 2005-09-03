export PRIV_DIR5=$(PRIV_COMMON_DIR)/articles

subsystem: all
	$(MAKE) -C images
	$(MAKE) -C topics

all: ;

install: $(ALL)
	mkdir -p $(PRIV_DIR5)
	cp -f *.php *.css $(PRIV_DIR5)/
	$(MAKE) -C images install
	$(MAKE) -C topics install

clean:
	$(MAKE) -C images clean
	$(MAKE) -C topics clean
