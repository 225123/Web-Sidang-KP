<?php

// Pastikan Laravel tahu folder cache harus di /tmp karena Vercel read-only
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('VIEW_COMPILED_PATH=/tmp');

require __DIR__ . '/../public/index.php';
