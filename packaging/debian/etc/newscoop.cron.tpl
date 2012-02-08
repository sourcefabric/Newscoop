MAILTO=__CRON_EMAIL__

* * * * * __WWW_USER__   [ -x /usr/share/newscoop/bin/newscoop-autopublish ] && /usr/share/newscoop/bin/newscoop-autopublish
0 */4 * * * __WWW_USER__ [ -x /usr/share/newscoop/bin/newscoop-indexer ] && /usr/share/newscoop/bin/newscoop-indexer --silent
0 */8 * * * __WWW_USER__ [ -x /usr/share/newscoop/bin/subscription-notifier ] && /usr/share/newscoop/bin/subscription-notifier
*/2 * * * * __WWW_USER__ [ -x /usr/share/newscoop/bin/events-notifier ] && /usr/share/newscoop/bin/events-notifier
0 */4 * * * __WWW_USER__ [ -x /usr/share/newscoop/bin/newscoop-statistics ] && /usr/share/newscoop/bin/newscoop-statistics
