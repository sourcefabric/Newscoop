include ../make.env

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

dummy:
