include ../../../make.env

MY_TARGETS=enter.php password.php sub_pub.php dictionary.php robots.php jump.php

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	$(M4) ../macros.m4 $< >$@

%.txt: %.m4 ../macros.m4
	$(M4) ../macros.m4 $< >$@

install: all
	cp -f *.php $(HTML_COMMON_DIR)/

clean:
	rm -f *~ $(MY_TARGETS)

dummy:
