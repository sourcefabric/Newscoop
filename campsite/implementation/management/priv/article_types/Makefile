export PRIV_DIR2=$(PRIV_COMMON_DIR)/article_types

subsystem: all
	$(MAKE) -C fields

all: ;

install: $(ALL)
	mkdir -p "$(PRIV_DIR2)"
	cp -f *.php "$(PRIV_DIR2)/"
	$(MAKE) -C fields install

clean:
	rm -f *~ 
	$(MAKE) -C fields clean
