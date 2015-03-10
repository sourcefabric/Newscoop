<?php
exec(__DIR__.'/../../../../../../application/console oauth:create-client newscoop '.$_SERVER['HTTP_HOST'].' '.$_SERVER['HTTP_HOST'].' --default', $output, $code);
