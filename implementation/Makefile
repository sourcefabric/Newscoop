include ../make.env

ALL=$(shell if [ "$(PACKAGE_TYPE)" = "source" ]; then echo all; else echo dummy; fi)

all:
	for part in $(IMPLEMENTATION_PARTS); do \
	    $(MAKE) -C $$part all; \
	    if [ $$? -ne 0 ]; then exit 1; fi; \
	done

install:
	for part in $(IMPLEMENTATION_PARTS); do \
	    $(MAKE) -C $$part install; \
	    if [ $$? -ne 0 ]; then exit 1; fi; \
	done

clean:
	for part in $(IMPLEMENTATION_PARTS); do \
	    $(MAKE) -C $$part clean; \
	    if [ $$? -ne 0 ]; then exit 1; fi; \
	done

uninstall: dummy
	for part in $(IMPLEMENTATION_PARTS); do \
	    $(MAKE) -C $$part uninstall; \
	    if [ $$? -ne 0 ]; then exit 1; fi; \
	done

package: dummy
	@if [ "$(PACKAGE_TYPE)" = "binary" ]; then echo "This is a binary package"; exit 1; fi
	mkdir -p $(BIN_PACKAGE_DIR)/implementation
	cp -f Makefile $(BIN_PACKAGE_DIR)/implementation
	cp -f configure $(BIN_PACKAGE_DIR)/implementation
	for part in $(IMPLEMENTATION_PARTS); do \
	    $(MAKE) -C $$part package; \
	    if [ $$? -ne 0 ]; then exit 1; fi; \
	done

dummy:
