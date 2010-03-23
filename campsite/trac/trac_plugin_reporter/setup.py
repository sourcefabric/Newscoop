from setuptools import setup

PACKAGE = 'TracAutotrac'
VERSION = '1.0'

setup(name=PACKAGE,
      version=VERSION,
      packages=['autotrac'],
      entry_points={'trac.plugins': '%s = autotrac' % PACKAGE},
)
