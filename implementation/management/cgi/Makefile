TARGETS=get_img get_img.php tpl_cgi tpl_cgi.php

all: dummy

clean:
	rm -f *~

install: $(ALL)
	cp -f $(TARGETS) $(CGI_COMMON_DIR)
	chown $(ROOT_USER):$(APACHE_GROUP) $(CGI_COMMON_DIR)/*
	chmod 750 $(CGI_COMMON_DIR)/*

uninstall:
	for t in $(TARGETS); do rm -f "$(CGI_COMMON_DIR)/$t"; done

dummy:
