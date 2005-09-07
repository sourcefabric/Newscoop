export PRIV_DIR2=$(PRIV_COMMON_DIR)/localizer

MY_TARGETS=dummy

all: $(MY_TARGETS)

install: $(ALL)
	mkdir -p "$(PRIV_DIR2)"
	cp -f *.php "$(PRIV_DIR2)/"
	cp -f *.xml "$(PRIV_DIR2)/"
	cp -f *.txt "$(PRIV_DIR2)/"

clean:
	rm -f *~ $(MY_TARGETS)

dummy:
