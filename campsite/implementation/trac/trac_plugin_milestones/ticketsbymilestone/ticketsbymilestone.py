# Milestones plugin

from trac.core import *
from trac.web.chrome import INavigationContributor
from trac.web.main import IRequestHandler
from trac.util import escape, Markup
import time

class TicketsByMilestone (Component):
    implements(INavigationContributor, IRequestHandler)

    # INavigationContributor methods
    def get_active_navigation_item(self, req):
        return 'ticketsbymilestone'
                
    def get_navigation_items(self, req):
        if not req.perm.has_permission('REPORT_VIEW'):
            return
        time.sleep(.5)
        yield ('mainnav', 'ticketsbymilestone',
#                Markup('<a href="%s">Tickets by Milestone</a>', self.env.href.ticketsbymilestone()))
               Markup('<a href="%s">Tickets by Milestone</a>', str(self.env.href.report()) + "/3"))

    # IRequestHandler methods
    def match_request(self, req):
        return req.path_info == '/ticketsbymilestone'

    def process_request (self, req):
        time.sleep(.5)
        req.redirect (self.env.href.report() + "/3")

        
