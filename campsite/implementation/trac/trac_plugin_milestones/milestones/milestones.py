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
        yield 'mainnav', 'milestones', Markup('<a href="/trac/report/3">Tickets by Milestone</a>')

    # IRequestHandler methods
    def match_request(self, req):
        return req.path_info == '/milestones'
    
    def process_request(self, req):
        req.send_response(200)
        req.send_header('Content-Type', 'text/plain')
        req.end_headers()
        req.write('Milestones')
        
