MY_TARGETS=enter.php password.php sub_pub.php dictionary.php robots.php jump.php

all: $(MY_TARGETS)

%.php: %.m4 ../macros.m4
	$(M4) ../macros.m4 $< >$@

%.txt: %.m4 ../macros.m4
	$(M4) ../macros.m4 $< >$@

install: $(ALL)
	cp -f *.php $(HTML_COMMON_DIR)/

clean:
	rm -f *~ db_connect.php config.php $(MY_TARGETS)

dummy:
