<?php
echo "http://".getenv('HTTP_HOST').substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/') + 1);
