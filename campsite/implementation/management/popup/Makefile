export PRIV_DIR2=$(PRIV_COMMON_DIR)/popup

MY_TARGETS=index.php empty.php lang.php i1.php pub.php i2.php iss.php i3.php sect.php i4.php art.php img.php

all: $(MY_TARGETS)

popup.m4: popup.in
	cat popup.in >popup.m4

%.php: %.m4 ../macros.m4 popup.m4
	$(M4) ../macros.m4 popup.m4 $< >$@

install: $(ALL)
	mkdir -p "$(PRIV_DIR2)"
	cp -f *.php "$(PRIV_DIR2)/"

clean:
	rm -f *~ $(MY_TARGETS)
