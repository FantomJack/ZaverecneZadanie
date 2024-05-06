<?php

session_start();
setcookie('loggedin', false, time() - 3600, '/');
session_unset();
session_destroy();

