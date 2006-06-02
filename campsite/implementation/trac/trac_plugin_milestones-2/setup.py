from setuptools import setup

PACKAGE = 'TracTicketsbymilestone'
VERSION = '0.2'

setup(name=PACKAGE,
      version=VERSION,
      packages=['Ticketsbymilestone'],
      entry_points={'trac.plugins': '%s = Ticketsbymilestone' % PACKAGE},
)
