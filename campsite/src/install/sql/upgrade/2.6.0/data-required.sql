-- fix for bug 2277 (Ticket Inbox: Subscriptions are not added correctly to the database)
UPDATE `Subscriptions` SET `Type` = 'P' WHERE `Type` = ' ' OR `Type` = '';
