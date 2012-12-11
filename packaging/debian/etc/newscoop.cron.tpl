MAILTO=__CRON_EMAIL__

* * * * * __WWW_USER__   [ -x /var/lib/newscoop/bin/console newscoop:autopublish ] && /var/lib/newscoop/bin/console newscoop:autopublish
0 */4 * * * __WWW_USER__ [ -x /var/lib/newscoop/bin/console newscoop:indexer:run --verbose ] && /var/lib/newscoop/bin/console newscoop:indexer:run
0 */8 * * * __WWW_USER__ [ -x /var/lib/newscoop/bin/console newscoop:notifier:subscriptions ] && /var/lib/newscoop/bin/console newscoop:notifier:subscriptions
*/2 * * * * __WWW_USER__ [ -x /var/lib/newscoop/bin/console newscoop:notifier:events ] && /var/lib/newscoop/bin/console newscoop:notifier:events
0 */4 * * * __WWW_USER__ [ -x /var/lib/newscoop/bin/console newscoop:statistics:clean-old ] && /var/lib/newscoop/bin/console newscoop:statistics:clean-old
