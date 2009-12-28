Autotrac Installation
=====================

Autotrac is the Trac server side of BugReporter. It lets users view, save, or
remove BugReporter's automatically generated error reports.


Packages You Will Need
----------------------

In addition to Trac 0.9 or newer, you will need the following packages:

  * python-dev 
  * python-setuptools, v. 0.6 or newer

On a Debian-package based system, you may be able to install these
files with the following command:

  # apt-get install python-dev python-setuptools

However, at the time of this writing, the stable version of
python-setuptools is earlier than 0.6, so you may need to manually
download it and then install it with dpkg.


Compiling the Plugin
--------------------

Now you will need to create the plugin. Execute the following command in
CAMPSITE_SOURCE_DIR/implementation/trac/trac_plugin_reporter:

  # python setup.py bdist_egg


Installing the Plugin
---------------------

In the dist directory (below trac_plugin_reporter), you will now have a file
called something like TracAutotrac-0.1-py2.3.egg. Copy this to your Trac
plugin directory. For example:

  # cp dist/TracAutotrac-0.1-py2.3.egg /usr/local/trac-projects/campware/plugin


Installing the Plugin Templates
-------------------------------

Copy cs files from
CAMPSITE_SOURCE_DIR/implementation/trac/trac_plugin_reporter/cs:

  # cp cs/autotracticket.cs /usr/share/trac/templates
  # cp cs/autotracreport.cs /usr/share/trac/templates


You're Done
-----------

Just restart your server, and the plugin should load.  
