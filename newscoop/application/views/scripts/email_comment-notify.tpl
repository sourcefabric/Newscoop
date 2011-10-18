Der Kommentar von <?php echo $this->username; ?> lautet: <?php echo $this->comment->getSubject(); ?><br />
<?php echo $this->comment->getMessage(); ?><br /><br />
<a href="<?php echo $this->publication, $this->articleLink; ?>">Zum Artikel</a><br />
<a href="<?php echo $this->publication, $this->articleLink; ?>#comment-<?php echo $this->comment->getId(); ?>">Zum Kommentar</a><br />
