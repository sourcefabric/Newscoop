## Autotrac--A Trac plugin that enables Trac to accept reports from BugReporter.
#
#  This module accepts automatic error reports sent from BugReporter.
#
#  Additionally, it creates an Inbox tab, which allows an
#  administrator to view the error reports and determine which ones to
#  accept as tickets, and which ones to dismiss.  

from trac import util
from trac.attachment import attachment_to_hdf, Attachment
from trac.core import *
from trac.web.chrome import add_stylesheet, INavigationContributor
from trac.web.main import IRequestHandler
from trac.util import escape, Markup
from trac.wiki import wiki_to_html, IWikiSyntaxProvider
from trac.ticket.model import Ticket, TicketSystem
import re, urllib, time

dynvars_re = re.compile('\$([A-Z]+)')
dynvars_disallowed_var_chars_re = re.compile('[^A-Z0-9_]')
dynvars_disallowed_value_chars_re = re.compile(r'[^a-zA-Z0-9-_@.,\\]')

class AutoTrac(Component):
    implements(INavigationContributor, IRequestHandler)

    ## Constructor
    #
    def __init__ (self):
        # --- Define the permission that is required for users to act
        # on inbox-tickets (eg accept) ---
        self.actionPermission = 'TICKET_CREATE'

    # --- INavigationContributor methods ---

    def get_active_navigation_item(self, req):
        return 'autotrac'
                
    ## Create Ticket Inbox tab
    #
    # @param Request req The HTTP Request data
    def get_navigation_items(self, req):
        yield 'mainnav', 'autotrac', util.Markup('<a href="%s">Ticket Inbox</a>', 
                                            self.env.href.autotrac())

    # IRequestHandler methods

    ## Return true if req-URL matches an Autotrac method. 
    #
    # @param Request req The HTTP request data
    def match_request(self, req):

        if re.compile (r'^/autotrac([/](.*))?$').match(req.path_info, 0):
            return True
        else:
            return False 
    
    ## Send request to the correct method.
    #
    # @param Request req The HTTP request data
    def process_request(self, req):
        # This method is based on _render_view() in ReportModule

        # --- BugReporter commands ---
        if re.compile ("^/autotrac/ping/?$").match (req.path_info, 0):
            self.reply_to_ping(req)

        elif re.compile ("^/autotrac/newreport/?$").match(req.path_info,0)\
        and req.method == "POST":
            self.bug_reporters_post(req)

        # --- User commands ---
        elif re.compile (r'^/autotrac/?$').match (req.path_info):
            db = self.env.get_db_cnx()
            id = 1
        
            resp = self.display_report_page (req, db, id)

            add_stylesheet (req, 'common/css/report.css')
            return 'autotracreport.cs', None

        elif re.compile (r'/autotrac/ticket(/[1-9][0-9]*)').match (req.path_info):
            ticketId =  re.sub (".*ticket/", "", req.path_info)
            return self.display_ticket_page (req, int(ticketId))

        # -- Redirect an unspecified ticket to view report --
        elif re.match (r"/autotrac/ticket/?$", req.path_info):
            req.redirect(self.env.href.autotrac())

        else:
            raise util.TracError('No handler matched request to %s' % req.path_info)

    ## uses a user specified sql query to extract some information
    #  from the database and presents it as a html table.
    #
    # @param Request           req The HTTP request data
    # @param ConnectionWrapper db  The database connection
    # @param int                id The ticket ID
    def display_report_page(self, req, db, id):

        actions = {'create': 'REPORT_CREATE', 'delete': 'REPORT_DELETE',
                   'modify': 'REPORT_MODIFY'}
        for action in [k for k,v in actions.items()
                       if req.perm.has_permission(v)]:
            req.hdf['report.can_' + action] = True
        req.hdf['report.href'] = self.env.href.report(id)

        try:
            args = self.get_var_args(req)
        except ValueError,e:
            raise TracError, 'Report failed: %s' % e

        title = "Ticket Inbox"
        description = "Automatically generated user reports"
        id = 0

        # --- SQL based on Report #3 ---
        sql = """
        SELECT p.value AS __color__,
           status AS __group__,
           id AS ticket, summary, component, version, t.type AS type, 
           time AS created,
           changetime AS _changetime, description AS _description,
           reporter AS _reporter
          FROM ticket t, enum p
          WHERE status IN ('inbox', 'postponed') 
        AND p.name = t.priority AND p.type = 'priority'
          ORDER BY status, id
        """
        if req.args.get('format') == 'sql':
            self._render_sql(req, id, title, description, sql)
            return

        req.hdf['report.mode'] = 'list'
        if id > 0:
            title = '{%i} %s' % (id, title)
        req.hdf['title'] = title
        req.hdf['report.title'] = title
        req.hdf['report.id'] = id
        req.hdf['report.description'] = wiki_to_html(description, self.env, req)

        try:
            cols, rows = self.execute_sql_report(req, db, id, sql, args)
        except Exception, e:
            req.hdf['report.message'] = 'Report execution failed: %s' % e
            return 'autotracreport.cs', None

        # Convert the header info to HDF-format
        idx = 0
        for col in cols:
            title=col[0].capitalize()
            prefix = 'report.headers.%d' % idx
            req.hdf['%s.real' % prefix] = col[0]
            if title.startswith('__') and title.endswith('__'):
                continue
            elif title[0] == '_' and title[-1] == '_':
                title = title[1:-1].capitalize()
                req.hdf[prefix + '.fullrow'] = 1
            elif title[0] == '_':
                continue
            elif title[-1] == '_':
                title = title[:-1]
                req.hdf[prefix + '.breakrow'] = 1
            req.hdf[prefix] = title
            idx = idx + 1

        if req.args.has_key('sort'):
            sortCol = req.args.get('sort')
            colIndex = None
            hiddenCols = 0
            for x in range(len(cols)):
                colName = cols[x][0]
                if colName == sortCol:
                    colIndex = x
                if colName.startswith('__') and colName.endswith('__'):
                    hiddenCols += 1
            if colIndex != None:
                k = 'report.headers.%d.asc' % (colIndex - hiddenCols)
                asc = req.args.get('asc', None)
                if asc:
                    sorter = ColumnSorter(colIndex, int(asc))
                    req.hdf[k] = asc
                else:
                    sorter = ColumnSorter(colIndex)
                    req.hdf[k] = 1
                rows.sort(sorter.sort)

        # Convert the rows and cells to HDF-format
        row_idx = 0
        for row in rows:
            col_idx = 0
            numrows = len(row)
            for cell in row:
                cell = str(cell)
                column = cols[col_idx][0]
                value = {}
                # Special columns begin and end with '__'
                if column.startswith('__') and column.endswith('__'):
                    value['hidden'] = 1
                elif (column[0] == '_' and column[-1] == '_'):
                    value['fullrow'] = 1
                    column = column[1:-1]
                    req.hdf[prefix + '.breakrow'] = 1
                elif column[-1] == '_':
                    value['breakrow'] = 1
                    value['breakafter'] = 1
                    column = column[:-1]
                elif column[0] == '_':
                    value['hidehtml'] = 1
                    column = column[1:]
                if column in ['id', 'ticket', '#', 'summary']:
                    id_cols = [idx for idx, col in util.enum(cols)
                               if col[0] in ('ticket', 'id')]
                    if id_cols:
                        id_val = row[id_cols[0]]
                        value['ticket_href'] = self.env.href.autotrac("ticket/" + str(id_val))
                elif column == 'description':
                    value['parsed'] = wiki_to_html(cell, self.env, req, db)
                elif column == 'reporter' and cell.find('@') != -1:
                    value['rss'] = cell
                elif column == 'report':
                    value['report_href'] = self.env.href.report(cell)
                elif column in ['time', 'date','changetime', 'created', 'modified']:
                    value['date'] = util.format_date(cell)
                    value['time'] = util.format_time(cell)
                    value['datetime'] = util.format_datetime(cell)
                    value['gmt'] = util.http_date(cell)
                prefix = 'report.items.%d.%s' % (row_idx, str(column))
                req.hdf[prefix] = str(cell)
                for key in value.keys():
                    req.hdf[prefix + '.' + key] = value[key]

                col_idx += 1
            row_idx += 1
        req.hdf['report.numrows'] = row_idx

        format = req.args.get('format')
        if format == 'rss':
            self._render_rss(req)
            return 'report_rss.cs', 'application/rss+xml'
        elif format == 'csv':
            self._render_csv(req, cols, rows)
            return None
        elif format == 'tab':
            self._render_csv(req, cols, rows, '\t')
            return None

        return 'report.cs', None


    ## Execute report query
    #
    # This method was adapted from ReportModule.execute_report()
    #
    # @param Request            req  The HTTP request object
    # @param ConnectionWrapper  db   The database connection
    # @param int                id   The ticket ID
    # @param str                sql  The SQL query
    # @param Dict               args ??
    def execute_sql_report(self, req, db, id, sql, args):

        sql = self.sql_sub_vars(req, sql, args)
        if not sql:
            raise util.TracError('Report %s has no SQL query.' % id)
        if sql.find('__group__') == -1:
            req.hdf['report.sorting.enabled'] = 1

        cursor = db.cursor()
        cursor.execute(sql)

        info = cursor.fetchall() or []
        cols = cursor.description or []

        db.rollback()

        return cols, info

    ## Get report-related arguments from the HTTP request
    #
    # @param Request req The HTTP request object.
    # @return The report related arguments.
    def get_var_args(self, req):
        report_args = {}
        for arg in req.args.keys():
            if not arg == arg.upper():
                continue
            m = re.search(dynvars_disallowed_var_chars_re, arg)
            if m:
                raise ValueError("The character '%s' is not allowed "
                                 " in variable names." % m.group())
            val = req.args.get(arg)
            m = re.search(dynvars_disallowed_value_chars_re, val)
            if m:
                raise ValueError("The character '%s' is not allowed "
                                 " in variable data." % m.group())
            report_args[arg] = val

        # Set some default dynamic variables
        if not report_args.has_key('USER'):
            report_args['USER'] = req.authname

        return report_args

    ## ??

    def sql_sub_vars(self, req, sql, args):
        def repl(match):
            aname = match.group()[1:]
            try:
                arg = args[aname]
            except KeyError:
                raise util.TracError("Dynamic variable '$%s' not defined." % aname)
            req.hdf['report.var.' + aname] = arg
            return arg

        return dynvars_re.sub(repl, sql)


    ## Send an HTTP header
    #  This method is used for dialogue with BugReporter
    #
    # @param Request req The HTTP request data

    def print_http_header(self, req):
        req.send_response(200)
        req.send_header('Content-Type', 'text/plain')
        req.end_headers()

    ## Send a reply to a BugReporter ping
    #
    # @param Request req The HTTP request data
    def reply_to_ping (self, req):
        self.print_http_header(req)
        req.write ("pong")

    ## Process info and reply to BugReporter
    #
    # @param Request req The HTTP request data
    def bug_reporters_post (self, req):

        self.print_http_header (req)

        errorId = req.args.get('f_id')
        if (errorId == None) or (not self.is_valid_error_id(errorId)):
            req.write ('error: Bad or missing error ID\n')
            return
        else:
            errorId = urllib.unquote_plus (errorId)

        time = req.args.get('f_time')
        if time == None:
            time = "";
        else:
            time = urllib.unquote_plus(time)

        summary = req.args.get('f_description')
        if summary == None or summary == "":
            summary = "Error " + errorId
        else:
            summary = urllib.unquote_plus (summary)

            # --- Remove unwanted backslashes before quotes ---
            summary = re.sub (r'''\\(\'|\")''', r'\1', summary)

        description = req.args.get('f_backtrace')
        if description == None:
            description = "";
        else:
            description = urllib.unquote_plus (description)
            description = "{{{\n" + description + "\n}}}"
            description = summary + "\n" + description

        reporter = req.args.get('f_email')
        if reporter == None or reporter == "":
            reporter = "anonymous"
        else:
            reporter = urllib.unquote_plus (reporter)

        software = req.args.get('f_software')
        if software == None:
            software = ""
        else:
            software = urllib.unquote_plus(software)

        version =  req.args.get('f_version')
        if version == None:
            version = ""
        else:
            version = urllib.unquote_plus (version)

        summary = self.summarize_text (summary)

        self.add_user_feedback (req, description, time, summary, reporter,
                            software, version, errorId)


        req.write ('accepted\n')
        req.write ('version: ' + version + '\n')
        req.write ('software: ' + software + '\n')

    ## Add BugReporter's feedback to the database
    #
    # @param Request req         The HTTP request data
    # @param str    description  User description & stack trace
    # @param str    time         The time the error occurred
    # @param str    summary      A summary of the user's description
    # @param str    reporter     The email of the user
    # @param str    software     The name of the software that crashed
    # @param str    version      The version number of the software that crashed
    # @param str    errorId      The error identification string
    def add_user_feedback (self, req, description, time, summary, reporter,
                     software, version, errorId):

        ticketId = self.get_ticket_id (errorId)

        if ticketId == None:

            self.insert_new_report_to_db (description, time, summary, reporter,
                            software, version, errorId)

        else:
            self.update_req (req, description, time, summary, reporter,
                            software, version, errorId)

            self.update_ticket_on_db (ticketId, reporter, description, req)


    ## Get ticket ID with error-ID 'errorId'
    #
    # @param str               errorId The error identification string
    # @param ConnectionWrapper db      The database connection
    def get_ticket_id (self, errorId, db=None):

        # --- BE CAUTIOUS & return if errorId's value seems
        # suspicious: potentially the ENTIRE TICKET TABLE could be
        # DELETED by this function.  ---
        if not (is_valid_error_id (errorId)):
            return None

        if not db:
            db = self.env.get_db_cnx()

        cursor = db.cursor()

        cursor.execute ("""
        select ticket,name,value from ticket_custom
        where name="error_id" and
        value="%s" order by
        ticket;""" % errorId)

        rows = cursor.rowcount 
        if rows == 0:
            return None
        elif rows == 1:
            row = cursor.fetchone()
            ticketId = row[0]
            return ticketId
        else:
            row = cursor.fetchone()
            ticketId = row[0]

            # --- This is a dangerous line, and if used incautiously
            #     the entire ticket table could be deleted. ---
            cursor.execute ("""
                DELETE FROM ticket_custom WHERE NAME =
                "error_id" AND VALUE = "%s" AND
                TICKET > %i;""" % (errorId, ticketId))

            return ticketId
        
    ## Insert a BugReporter report with a new Error ID into the database
    #
    # todo: this method should return False, if reports with this
    # errorId already exist
    #
    # @param str   description  User's description of the error plus a strack trace
    # @param str   time         Time at which the error occurred
    # @param str   summary      Summary of user's description
    # @param str   reporter     Email of the user reporting the trouble
    # @param str   software     The name of the software that crashed
    # @param str   version      Version of the software that crashed
    # @param str   errorId      The error identification string
    # @return True
    def insert_new_report_to_db(self, description, time, summary, reporter,
    software, version, errorId):
        
        ticket = Ticket(self.env)
        ticket['description'] = description
        ticket['version'] = version
        ticket['reporter'] = reporter
        ticket['summary'] = summary
        ticket['error_id'] = errorId 
        ticket['reporter'] = reporter

        ticket['milestone'] = ""
        ticket['status'] = "inbox"
        ticket['priority'] = "major"
        ticket['component'] = ""
        
        ticketId = ticket.insert()
        self.insert_error_id_to_db (ticketId, errorId)
        return True

    ## Insert an existing BugReporter error report
    #
    # todo: This method should return False, if there are no tickets
    # with this error ID.
    #
    # @param str     ticketId   The ticket ID
    # @param str     author     The reporter's email
    # @param str     comment    The user's comment
    # @param Request req        The HTTP request data
    # @return True
    def update_ticket_on_db(self, ticketId, author, comment, req):
        db = self.env.get_db_cnx()
        ticket = Ticket(self.env, ticketId, db=db)

        ticket.populate(req.args)

        now = int(time.time())
        ticket.save_changes(author, comment, when=now, db=db)
        db.commit()

        return True

    ## This method artificially updates 'req' (the HTTP-request object)
    #  as if it had been created with a POST form.  It does this so
    #  that certain methods (ie save_ticket_form_data()), can be handed a 'req'
    #  object.
    #
    # @param Request req         The HTTP request data
    # @param str     description The user's description
    # @param str     time        The time the error occurred
    # @param str     summary     A summary of the error description
    # @param str     reporter    The reporter's name
    # @param str     software    The software the error occurred on
    # @param str     version     The software version the error occurred on
    # @param str     errorId     The error ID string
    def update_req (self, req, description, time, summary, reporter,
                   software, version, errorId):
        req.args["description"] =  description
        req.args["time"] =  time
        req.args["reporter"] =  reporter
        req.args["software"] =  software
        req.args["version"] =  version

#        --- We won't update summary, as it's disconcerting for it to
#        keep changing ---
#        req.args["summary"] =  summary

    ## Display (and process forms from) the ticket webpage
    #
    # @param Request req      The HTTP request data
    # @param int     ticketId The ticket ID
    def display_ticket_page(self, req, ticketId):
        # This method is based on process_request() in TicketModule.

        # todo: security check should go here
        # --- For security, only display ticket if it's 
        req.perm.assert_permission('TICKET_VIEW')

        action = req.args.get('action', 'view')

        db = self.env.get_db_cnx()

        ticket = Ticket(self.env, ticketId, db=db)
        reporter_id = req.args.get('author')

#         req.hdf['ticket.debug'] = ", ".join (req.perm.perms.keys())

        if req.method == 'POST':
            if not req.args.has_key('preview'):
                self.save_ticket_form_data(req, db, ticket)

            else:
                # Use user supplied values
                ticket.populate(req.args)
                req.hdf['ticket.action'] = action
                req.hdf['ticket.ts'] = req.args.get('ts')
                req.hdf['ticket.reassign_owner'] = req.args.get('reassign_owner') \
                                                   or req.authname
                req.hdf['ticket.resolve_resolution'] = req.args.get('resolve_resolution')

                reporter_id = req.args.get('author')
                comment = req.args.get('comment')
                if comment:
                    req.hdf['ticket.comment'] = comment
                    # Wiki format a preview of comment
                    req.hdf['ticket.comment_preview'] = wiki_to_html(comment,
                                                                     self.env,
                                                                     req, db)
                                    
        else:
            req.hdf['ticket.reassign_owner'] = req.authname
            # Store a timestamp in order to detect "mid air collisions"
            req.hdf['ticket.ts'] = ticket.time_changed

        self.insert_ticket_data_to_hdf(req, db, ticket)

        add_stylesheet(req, 'common/css/ticket.css')
        return 'autotracticket.cs', None

    ## Insert ticket data into the HDF
    #
    # @param Request            req    The HTTP request data
    # @param ConnectionWrapper  db     The database connection
    # @param dict               ticket A dictionary with ticket values
    #                           to be displayed on the website
    def insert_ticket_data_to_hdf(self, req, db, ticket):
        # This method is based on _insert_ticket_data in TicketModule

        self.insert_autotrac_ticket_data_to_hdf (ticket, db)

        req.hdf['ticket'] = ticket.values
        req.hdf['ticket.id'] = ticket.id
        req.hdf['ticket.href'] = self.env.href.autotrac("ticket/" + repr(ticket.id))

        for field in TicketSystem(self.env).get_ticket_fields():
            if field['type'] in ('radio', 'select'):
                value = ticket.values.get(field['name'])
                options = field['options']
                if value and not value in options:
                    # Current ticket value must be visible even if its not in the
                    # possible values
                    options.append(value)
                field['options'] = options
            name = field['name']
            del field['name']
            if name in ('summary', 'reporter', 'description', 'type', 'status',
                        'resolution', 'owner'):
                field['skip'] = True
            req.hdf['ticket.fields.' + name] = field

        req.hdf['title'] = '#%d (%s)' % (ticket.id, ticket['summary'])
        req.hdf['ticket.description.formatted'] = wiki_to_html(ticket['description'],
                                                               self.env, req, db)

        req.hdf['ticket.opened'] = util.format_datetime(ticket.time_created)
        req.hdf['ticket.opened_delta'] = util.pretty_timedelta(ticket.time_created)
        if ticket.time_changed != ticket.time_created:
            req.hdf['ticket.lastmod'] = util.format_datetime(ticket.time_changed)
            req.hdf['ticket.lastmod_delta'] = util.pretty_timedelta(ticket.time_changed)

        changelog = ticket.get_changelog(db=db)
        curr_author = None
        curr_date   = 0
        changes = []
        for date, author, field, old, new in changelog:
            if date != curr_date or author != curr_author:
                changes.append({
                    'date': util.format_datetime(date),
                    'author': author,
                    'fields': {}
                })
                curr_date = date
                curr_author = author
            if field == 'comment':
                changes[-1]['comment'] = wiki_to_html(new, self.env, req, db)
            elif field == 'description':
                changes[-1]['fields'][field] = ''
            else:
                changes[-1]['fields'][field] = {'old': old,
                                                'new': new}
        req.hdf['ticket.changes'] = changes

        # List attached files
        for idx, attachment in util.enum(Attachment.select(self.env, 'ticket',
                                                           ticket.id)):
            hdf = attachment_to_hdf(self.env, db, req, attachment)
            req.hdf['ticket.attachments.%s' % idx] = hdf
        if req.perm.has_permission('TICKET_APPEND'):
            req.hdf['ticket.attach_href'] = self.env.href.attachment('ticket',
                                                                     ticket.id)

        # Add the possible actions to hdf
        actions = self.get_available_ticket_actions(ticket, req.perm)
        for action in actions:
            req.hdf['ticket.actions.' + action] = '1'

    def insert_autotrac_ticket_data_to_hdf (self, ticket, db=None):
        pass
        if not db:
            db = self.env.get_db_cnx()

        tkt_id = ticket.id
        # --- Add autotrac's custom fields ---
        cursor = db.cursor()
        cursor.execute("SELECT name,value FROM ticket_custom WHERE ticket=%s",
                       (tkt_id,))
        for name, value in cursor:
            if name in ["error_id"]:
                ticket.values[name] = value
        
        cursor.execute ("""
            SELECT DISTINCT time FROM ticket_change WHERE ticket = %i;"""
            % (tkt_id))
        row = cursor.fetchall()
        occurrences = len(row) + 1
        ticket.values["occurrences"] = int(occurrences)
        

    ## Returns the actions that can be performed on the ticket.
    #
    # todo: add user permissions
    #
    # @param  Ticket ticket The ticket in questioen
    # @param  str    perm_ The permission of the user
    # @return list   The available actions
    def get_available_ticket_actions(self, ticket, perm_):
        # (This method was adapted from TicketSystem.get_available_actions())

        # --- Assert user as appropriate permission ---
        if perm_.has_permission (self.actionPermission):

            # -- If viewing an inbox ticket, return these actions --
            if ticket['status'] == "inbox":
                return ['postpone', 'accept', 'close']

            # -- else if viewing a postponed ticket, return  these actions --
            elif ticket['status'] == 'postponed':
                return ['postpone', 'accept', 'close']

        # --- If lacking appropriate permission or ticket-type, return
        #     an empty list ---
        else:
            return []

    ## Save the data from the ticket form
    #
    # @param Request            req The HTTP request data
    # @param ConnectionWrapper  db The database connection
    # @param Ticket             ticket The ticket
    def save_ticket_form_data(self, req, db, ticket):
        # This method is based on _do_save() in TicketModule

        # TICKET_CHGPROP gives permission to edit the ticket
        if req.perm.has_permission('TICKET_CHGPROP'):
            if not req.args.get('summary'):
                raise TracError('Tickets must contain summary.')

            if req.args.has_key('description') or req.args.has_key('reporter'):
                req.perm.assert_permission('TICKET_ADMIN')

            ticket.populate(req.args)
        else:
            req.perm.assert_permission('TICKET_APPEND')

        # Mid air collision?
        if int(req.args.get('ts')) != ticket.time_changed:
            raise TracError("Sorry, can not save your changes. "
                            "This ticket has been modified by someone else "
                            "since you started", 'Mid Air Collision')

        # Do any action on the ticket?
        action = req.args.get('action')
        actions = self.get_available_ticket_actions(ticket, req.perm)
#         actions = ['postpone', 'close']
        if action not in actions:
            raise TracError('Invalid action: %s in %s' % (action, ', '.join(actions)))

        if action == 'accept':
            ticket['status'] =  'new'
#             ticket['owner'] = req.authname
        elif action == 'close':
            ticket['status'] = 'closed'
#             ticket['resolution'] = req.args.get('resolve_resolution')
        elif action == 'postpone':
            ticket['status'] = 'postponed'

        now = int(time.time())
        ticket.save_changes(req.args.get('author', req.authname),
                            req.args.get('comment'), when=now, db=db)
        db.commit()

        try:
            tn = TicketNotifyEmail(self.env)
            tn.notify(ticket, newticket=False, modtime=now)
        except Exception, e:
            self.log.exception("Failure sending notification on change to "
                               "ticket #%s: %s" % (ticket.id, e))

        req.redirect(self.env.href.autotrac())

    ## Create a short summary of text
    #
    # @param  str text The text to summarize
    # @return str The summarized text
    def summarize_text (self, text):
        maxLength = 55

        # --- If text is longer than 'maxLength', Eliminate any
        #     sentences ending after 'maxLength' characters ---
        if len (text) > maxLength:
            text = re.sub (r"(.+?[.?!]).*", r"\1", text)

            # -- If text is still too long, cut it off after 'maxLength' chars --
            if len(text) > maxLength:
                text = text[:maxLength]

                # - Eliminate final word (which is probably a partial word),
                #   and end with ellipsis -
                text = re.sub (r"(.*)\ .*$", r"\1", text) + "..."

                # - Eliminate a comma if it's directly preceding the
                #   ellipsis -
                text = re.sub (r"\,\.\.\.$", "...", text)

        return text

    ## Confirm that the 'errorId' is valid
    #
    #  Note: it's VERY IMPORTANT that this function reports False on
    #  blank errorId's.  If it were to do otherwise, the ENTIRE TICKET
    #  TABLE could be deleted.
    #
    # @param str errorId  The error ID string
    # @return True if valid, otherwise False 
    def is_valid_error_id (self, errorId):
        if re.match (r"[0-9]+:[^:]+:[^:]+:[^:]+:[0-9]+", errorId):
            return True
        else:
            return False

    ## Insert error-ID to database
    #
    # todo: autotrac should probably use it's own database, rather
    # than the geneic 'ticket_custom', which doesn't confirm that
    # errorId's are unique.
    #
    # @param int                ticketId        The ticket ID number
    # @param str                errorId         The error ID string
    # @param ConnectionWrapper db               The database connection
    def insert_error_id_to_db(self, ticketId, errorId, db=None):
        if not db:
            db = self.env.get_db_cnx()
            handle_ta = True
        else:
            handle_ta = False

        cursor = db.cursor()
        cursor.execute ("""
            INSERT INTO ticket_custom (ticket, name, value)
            VALUES (%i, "error_id", "%s")"""
            % (int(ticketId), errorId))

        if handle_ta:
            db.commit()
