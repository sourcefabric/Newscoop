export PRIV_DIR3=$(PRIV_COMMON_DIR)/users/subscriptions

subsystem: all
	$(MAKE) -C sections

all: ;

install: $(ALL)
	mkdir -p $(PRIV_DIR3)
	cp -f *.php $(PRIV_DIR3)/
	$(MAKE) -C sections install

clean:
	rm -f *~ 
	$(MAKE) -C sections clean

dummy:
