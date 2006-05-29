# Milestones plugin

from trac.core import *
from trac.web.chrome import INavigationContributor
from trac.web.main import IRequestHandler
from trac.util import escape, Markup

class UserbaseModule(Component):
    implements(INavigationContributor, IRequestHandler)

    # INavigationContributor methods
    def get_active_navigation_item(self, req):
        return 'ticketsbymilestone'
                
    def get_navigation_items(self, req):
        if not req.perm.has_permission('REPORT_VIEW'):
            return
        yield ('mainnav', 'ticketsbymilestone',
               Markup('<a href="%s">Tickets by Milestone</a>',
                      self.env.href.ticketsbymilestone()))

    # IRequestHandler methods
    def match_request(self, req):
        return req.path_info == '/ticketsbymilestone'

    def process_request (self, req):
        req.redirect (self.env.href.report() + "/3")

        
