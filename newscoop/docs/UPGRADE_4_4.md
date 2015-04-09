## Upgrading Newscoop version 4.3.x/4.4.x to 4.4.x

**Important!** Remember to backup all your data before performing upgrade.

1. Remove `newscoop/vendor` directory and its content from your current Newscoop instance (`sudo rm -rf newscoop/vendor`).
2. Copy Newscoop 4.4.x files over the 4.3.x/4.4.x files (e.g. `sudo cp -r /home/user/Newscoop44/newscoop/ /var/www/newscoop/`).
3. Run `upgrade.php` script. (Go to `http://www.example.com/upgrade.php`)
4. Check if there are any instructions to follow in the output of upgrade script. If so, then follow the steps.
5. When it is done, clear the cache folder: `sudo rm -rf cache/*`.
6. Run `php composer.phar dump-autoload --optimize --no-dev` command in `../newscoop/` directory - this will autoload new classes.
7. Run `php application/console assets:install public/` command in `../newscoop/` directory - it will install assets.
8. Run `php scripts/fixer.php` script in `../newscoop/` directory - it will fix files permissions. (optional, run it when you don't know how to manage files permissions)
9. Clear the cache folder for the last time: `sudo rm -rf cache/*`.
10. You are done!

Above steps are required to upgrade Newscoop 4.3.x/4.4.x to 4.4.x.

We also recommend to update all the legacy plugins: `debate`, `poll`, `soundcloud`, `recaptcha`. [How-to][1].

[1]: https://github.com/sourcefabric/Newscoop/blob/master/newscoop/docs/UPGRADE_4_3.md#updating-newscoop-legacy-plugins
