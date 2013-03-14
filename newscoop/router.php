<?php

$static = array(
    'css',
    'js',
    'jpg',
    'png',
    'gif',
    'bmp',
    'txt',
);

$ext = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_EXTENSION);
if (in_array($ext, $static) && file_exists($_SERVER['SCRIPT_FILENAME'])) {
    return false;
}

if (strlen($_SERVER['SCRIPT_NAME']) >= 6 && substr($_SERVER['SCRIPT_NAME'], 0, 6) === '/admin') {
    // rewrite
    $_SERVER['SCRIPT_NAME'] = '/admin.php';
} elseif (strlen($_SERVER['SCRIPT_NAME']) >= 8 && substr($_SERVER['SCRIPT_NAME'], 0, 8) === '/install') {
    $_SERVER['SCRIPT_NAME'] = '/install/index.php';
} else {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
}

if (empty($_SERVER['REQUEST_TIME_FLOAT'])) {
    $_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);
}

// logs requests calling exit
register_shutdown_function(function () {
    file_put_contents(
        'php://stdout',
        sprintf(
            "[%s] %s:%s [%d]: %s (generated in %.3fs)\n",
            date('D M d H:i:s Y', $_SERVER['REQUEST_TIME']),
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['REMOTE_PORT'],
            200,
            $_SERVER['REQUEST_URI'],
            microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']
        )
    );
});

require __DIR__ . $_SERVER['SCRIPT_NAME'];
