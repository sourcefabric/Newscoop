#!/bin/sh

echo "Clears all webcodes first, then generates webcodes for all articles with autorestart on errors."
echo "Starting in 5 seconds. Press ^C to cancel."

sleep 5

php application/console webcode:generate --only-clear

until php application/console webcode:generate; do
    echo "Webcode generation stopped on error with code $?. Automatically restarting..." >&2
    sleep 2
done

echo "Webcode generation done."

exit 0
