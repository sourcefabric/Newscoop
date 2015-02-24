<?php
exec(__DIR__.'/../../../../../../application/console oauth:create-client newscoop newscoop.dev newscoop.dev --default', $output, $code);
