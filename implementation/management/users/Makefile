include ../../../make.env

subsystem: all

all: dummy
	$(MAKE) -C subscriptions

install: all
	mkdir -p $(PRIV_COMMON_DIR)/users
	cp -f *.php $(PRIV_COMMON_DIR)/users/
	$(MAKE) -C subscriptions install

clean:
	rm -f *~ $(MY_TARGETS)
	$(MAKE) -C subscriptions clean

dummy:
