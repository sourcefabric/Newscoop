MAILTO=__CRON_EMAIL__

* * * * * __WWW_USER__   [ -x /var/lib/newscoop/application/console newscoop:autopublish ] && /var/lib/newscoop/application/console newscoop:autopublish
0 */4 * * * __WWW_USER__ [ -x /var/lib/newscoop/application/console newscoop:indexer:run ] && /var/lib/newscoop/application/console newscoop:indexer:run
0 */8 * * * __WWW_USER__ [ -x /var/lib/newscoop/application/console newscoop:notifier:subscriptions ] && /var/lib/newscoop/application/console newscoop:notifier:subscriptions
*/2 * * * * __WWW_USER__ [ -x /var/lib/newscoop/application/console newscoop:notifier:events ] && /var/lib/newscoop/application/console newscoop:notifier:events
0 */4 * * * __WWW_USER__ [ -x /var/lib/newscoop/application/console newscoop:statistics:clean-old ] && /var/lib/newscoop/application/console newscoop:statistics:clean-old
