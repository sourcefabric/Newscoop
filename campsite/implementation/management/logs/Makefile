export PRIV_DIR2=$(PRIV_COMMON_DIR)/logs

MY_TARGETS=index.php

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	$(M4) ../macros.m4 $< >$@

install: $(ALL)
	mkdir -p "$(PRIV_DIR2)"
	cp -f *.php "$(PRIV_DIR2)/"

clean:
	rm -f *~ $(MY_TARGETS)
