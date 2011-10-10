Der Kommentar von <?php echo $this->username; ?> lautet:<br />
<?php echo $this->comment->getMessage(); ?><br />
<br />
<a href="<?php echo $this->article->url; ?>">Zum Artikel</a><br />
<a href="<?php echo $this->article->url; ?>#comment-<?php echo $this->comment->getId(); ?>">Zum Kommentar</a><br />
