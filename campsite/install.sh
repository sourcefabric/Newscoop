#!/bin/bash

do_configure()
{
    rm -f install_log
    echo -e "\nSTEP 1"
    echo -n "Configuring Campsite (this may take a while, please wait)..."
    echo -e "###############################################\n\tConfiguring campsite\n" >> install_log
    ./configure $* >> install_log 2>&1
    err_code=$?
    if [ $err_code -ne 0 ]; then
	echo "ERROR."
	echo "Read install_log for more information on configuration error."
	echo "Please report bug to http://bugs.campware.org"
	exit 1
    fi
    echo "done"
}

compile_sources()
{
    echo -e "\nSTEP 2"
    echo -n "Compiling sources (this may take a while, please wait)..."
    echo -e "###############################################\n\tCompiling sources\n" >> install_log
    $MAKE all >> install_log 2>&1
    err_code=$?
    if [ $err_code -ne 0 ]; then
	echo "ERROR."
	echo "Read install_log for more information on compilation error."
	echo "Please report bug to http://bugs.campware.org"
	exit 1
    fi
    echo "done"
}

install_campsite()
{
    echo -e "\nSTEP 3"
    echo -e "Installing Campsite...\n"
    echo -e "\n\n###############################################\n\tInstalling campsite\n" >> install_log
    $MAKE install >> install_log 2>&1
    if [ $? -ne 0 ]; then
	echo "ERROR."
	echo "Read install_log for more information on installation error."
	echo "Please report bug to http://bugs.campware.org"
	exit 101
    fi
    def_inst_error=0
    do_install=
    while true; do
	install_default_instance $do_install
	if [ $? -ne 0 ]; then
	    echo "There were errors creating the default instance"
	    choice_yn "Do you want to try again?" "Y"
	    if [ $? -ne 0 ]; then
		def_inst_error=1
		break
	    else
		do_install=Y
	    fi
	else
	    break
	fi
    done
    if [ $def_inst_error -ne 0 ]; then
	echo -e "\nError creating the default Campsite instance. This usually happens when"
	echo "MySQL configuration is different from the default one or you did not"
	echo "install an email server. Run:"
	echo "$BIN_DIR/campsite-create-instance --help"
	echo "to find out how to set Campsite instance parameters."
	echo -e "\nIMPORTANT!!!"
    else
	echo -e "\nCampsite was installed successfully."
	echo "For a complete installation log read install_log file."
	echo -e "\nIMPORTANT!!!\n"
    fi
    echo "Please configure apache server before using Campsite application; make sure"
    echo "the crond daemon is running; for details read the INSTALL file and follow"
    echo -e "the instructions.\n"
    if [ "$DEF_INST_INSTALLED" = "Y" ]; then
	echo "After configuring the apache server for Campsite enter the Campsite"
	echo "administration site by starting a browser and typing in the following"
	echo "URL: http://[\$SERVER_NAME]/admin/."
	echo -e "Fill in \"admin\" and \"admn00\" user and password respectively to log in."
	echo "You should change the password as soon as possible."
    else
	echo "Run this script in order to complete the Campsite installation:"
	echo "    $BIN_DIR/campsite-create-instance"
	echo
	echo "For help with this command, run:"
	echo "    $BIN_DIR/campsite-create-instance --help"
	echo "or read the manual at:"
	echo "    http://code.campware.org/manuals/campsite/2.7/index.php?id=147"
	echo
    fi
}


# start execution
if [ ! -x ./configure ]; then
    echo -e "Install script must be started from campsite directory!\nAborting..."
    exit 1
fi
go_to_install=false
do_interactive="--interactive"
while [ "$1" != "" ]; do
    case $1 in
    --go_to_install) go_to_install=true ;;
    --not_interactive) do_interactive=""
    esac
    shift 1
done
my_id=`id -u`
if [ "`uname | grep BSD`" != "" ]; then
	export BSD=1
else
	export BSD=0
fi;
echo -n "Preparing install scripts..."
. ./configure --define_start_env
[ $? -ne 0 ] && exit 1
echo "done"
if [ "$go_to_install" = "true" ] && [ $my_id -eq 0 ]; then
    install_campsite
    exit 0
fi
. ${INSTALL_CONF}/install_functions
if [ $? -ne 0 ]; then
    echo "Error reading install files."
    echo "Please report bug to http://www.campware.org/bugs"
    echo "Aborting..."
    exit 1
fi
if [ "$1" = "--version" ]; then
    echo -e "CAMPSITE $CAMPSITE_VERSION $RELEASE_NAME, released on $CAMPSITE_RELEASE_DATE"
    exit 0
fi
echo -e "\nInstalling CAMPSITE $CAMPSITE_VERSION $RELEASE_NAME"
configure_campsite $do_interactive
do_configure --apache_conf_path "$apache_conf_path" --apache_bin_path "$apache_bin_path"
compile_sources
$MAKE test_install &> /dev/null
if [ $? -ne 0 ] && [ $my_id -ne 0 ] && ! $USER_INSTALL; then
    echo -e "\nThe root password is needed in order to install campsite."
    while true; do
	echo -n "Root password: "
	archive_path=`pwd`
	su - $ROOT_USER -c "echo \"\"; cd $archive_path; ./install.sh --go_to_install" 2> /dev/null
	err_code=$?
	if [ $err_code -eq 1 ]; then
	    echo -n -e "\nThe password was not correct. Try again (Y/N) ? [Y]: "
	    read yn
	    if [ "$yn" = "" ]; then
		yn="Y"
	    fi
	    if [ "${yn:0:1}" != "Y" ] && [ "${yn:0:1}" != "y" ]; then
		echo "Aborting..."
		exit 1
	    fi
	else
	    exit $err_code
	fi
    done
else
    install_campsite
fi
exit 0
