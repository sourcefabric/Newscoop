export PRIV_DIR2=$(PRIV_DIR)/localizer

MY_TARGETS=dummy

all: $(MY_TARGETS)

install: $(ALL)
	mkdir -p $(PRIV_DIR2)
	cp -f *.php $(PRIV_DIR2)/

clean:
	rm -f *~
	if [ "$(PACKAGE_TYPE)" = "source" ]; then rm -f $(MY_TARGETS); fi

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/localizer
	cp -f Makefile $(PACKAGE_DIR)/localizer
	cp -f *.php $(PACKAGE_DIR)/localizer

dummy:
