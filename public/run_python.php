<?php
$output = shell_exec('python ../fix_filters.py 2>&1');
echo "<pre>$output</pre>";
echo "Done";
