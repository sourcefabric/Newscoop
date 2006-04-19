from setuptools import setup

PACKAGE = 'TracAutotrac'
VERSION = '0.1'

setup(name=PACKAGE,
      version=VERSION,
      packages=['autotrac'],
      entry_points={'trac.plugins': '%s = autotrac' % PACKAGE},
)
