# Ticketsbymilestone plugin

from trac.core import *
from trac.web.chrome import INavigationContributor
from trac.web.main import IRequestHandler
from trac.util import escape, Markup

class UserbaseModule(Component):
    implements(INavigationContributor, IRequestHandler)

    # INavigationContributor methods
    def get_active_navigation_item(self, req):
        return 'Ticketsbymilestone'
                
    def get_navigation_items(self, req):
        yield 'mainnav', 'Ticketsbymilestone', Markup(
            '<a href="%s">Tickets by Milestone</a>', self.env.href.Ticketsbymilestone())

    # IRequestHandler methods
    def match_request(self, req):
        return req.path_info == '/Ticketsbymilestone'
    
    def process_request(self, req):
        req.redirect (str(self.env.href.report()) + "/3")
