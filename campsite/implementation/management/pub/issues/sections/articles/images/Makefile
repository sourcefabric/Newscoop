export PRIV_DIR6=$(PRIV_DIR5)/images

all: 

install: $(ALL)
	mkdir -p $(PRIV_DIR6)
	cp -f *.php $(PRIV_DIR6)

clean:
	rm -f *~

package: $(ALL)
	mkdir -p $(PACKAGE_DIR)/images
	cp -f Makefile $(PACKAGE_DIR)/images
	cp -f *.php $(PACKAGE_DIR)/images
