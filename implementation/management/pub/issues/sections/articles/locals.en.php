<?php 

// -- autopublish.php
regGS("Article automatic publishing schedule", "Article automatic publishing schedule:en");

// -- index.php
regGS("Delete article $1","Delete article $1:en");
regGS("$1 articles found","$1 articles found:en");

// -- add.m4
regGS("Show article on front page","Show article on front page:en");
regGS("Show article on section page","Show article on section page:en");
regGS("Enter keywords, comma separated","Enter keywords, comma separated:en");

// -- do_add.m4
regGS("You must select an article type.","You must select an article type.:en");

// log
regGS("Article $1 added to $2. $3 from $4. $5 of $6","Article $1 added to $2. $3 from $4. $5 of $6:en");

// -- del.m4
regGS("You do not have the right to delete articles.","You do not have the right to delete articles.:en");
regGS("Are you sure you want to delete the article $1 ($2)?","Are you sure you want to delete the article $1 ($2)?:en");

// log
regGS("Article $1 ($2) deleted from $3. $4 from $5. $6 ($7) of $8","Article $1 ($2) deleted from $3. $4 from $5. $6 ($7) of $8:en");

// -- status.m4
regGS("Change article status","Change article status:en");
regGS("Change the status of article $1 ($2) from $3 to","Change the status of article $1 ($2) from $3 to:en");

// -- do_status.m4
regGS("You do not have the right to change this article status. Once submitted an article can only changed by authorized users.","You do not have the right to change this article status. Once submitted an article can only changed by authorized users.:en");
// log
regGS("Article $1 status from $2. $3 from $4. $5 ($6) of $7 changed","Article $1 status from $2. $3 from $4. $5 ($6) of $7 changed:en");

// -- translate.m4
regGS("Translate article","Translate article:en");
regGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users.","You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users.:en");

// -- edit.m4
regGS("Edit article details","Edit Article:en");
regGS("Allow users without subscriptions to view the article","Allow users without subscriptions to view the article:en");
regGS("The article has been locked by $1 ($2) $3 hour(s) and $4 minute(s) ago.", "The article has been locked by $1 ($2) $3 hour(s) and $4 minute(s) ago.:en");
regGS("The article has been locked by $1 ($2) $3 minute(s) ago.", "The article has been locked by $1 ($2) $3 minute(s) ago.:en");
regGS("Could not save the article.  It has been locked by $1 $2 hours and $3 minutes ago.", "Could not save the article.  It has been locked by $1 $2 hours and $3 minutes ago.");
regGS("Are you sure you want to unlock it?","Are you sure you want to unlock it?:en");
regGS("Article is locked", "Article is locked:en");

// -- do_edit.m4
regGS("The article has been updated.","The article has been updated.:en");
regGS("The article cannot be updated or no changes have been made.","No changes have been made, the article has not been updated.:en");

// -- duplicate.m4
regGS("Duplicate article", "Duplicate article");

// -- dupform.m4
regGS("The destination section is the same as the source section.", "The destination section is the same as the source section.:en");

regGS("The article is new; it is not possible to schedule it for automatic publishing.", "The article is new; it is not possible to schedule it for automatic publishing.:en");

?>