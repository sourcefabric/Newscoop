MAILTO=__CRON_EMAIL__

* * * * * __WWW_USER__   [ -x /var/lib/newscoop/bin/console newscoop:autopublish ] && /var/lib/newscoop/bin/console newscoop:autopublish
0 */4 * * * __WWW_USER__ [ -x /var/lib/newscoop/bin/newscoop-indexer ] && /var/lib/newscoop/bin/newscoop-indexer --silent
0 */8 * * * __WWW_USER__ [ -x /var/lib/newscoop/bin/subscription-notifier ] && /var/lib/newscoop/bin/subscription-notifier
*/2 * * * * __WWW_USER__ [ -x /var/lib/newscoop/bin/events-notifier ] && /var/lib/newscoop/bin/events-notifier
0 */4 * * * __WWW_USER__ [ -x /var/lib/newscoop/bin/console newscoop:statistics:clean-old ] && /var/lib/newscoop/bin/console newscoop:statistics:clean-old
