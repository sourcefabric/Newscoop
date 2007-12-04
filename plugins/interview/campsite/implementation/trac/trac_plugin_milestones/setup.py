from setuptools import setup

PACKAGE = 'TracMilestones'
VERSION = '0.1'

setup(name=PACKAGE,
      version=VERSION,
      packages=['ticketsbymilestone'],
      entry_points={'trac.plugins': '%s = ticketsbymilestone' % PACKAGE},
)

