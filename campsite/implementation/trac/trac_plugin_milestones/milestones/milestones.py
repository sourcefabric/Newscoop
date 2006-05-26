# Milestones plugin

from trac.core import *
from trac.web.chrome import INavigationContributor
from trac.web.main import IRequestHandler
from trac.util import escape, Markup

class UserbaseModule(Component):
    implements(INavigationContributor, IRequestHandler)

    # INavigationContributor methods
    def get_active_navigation_item(self, req):
        return 'milestones'
                
    def get_navigation_items(self, req):
        yield 'mainnav', 'milestones', Markup('<a href="%s/3">Tickets by Milestone</a>', self.env.href.report())

    # IRequestHandler methods
    def match_request(self, req):
        return

