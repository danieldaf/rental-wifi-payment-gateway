<?php

include_once 'config/init.php';

Session::destroySession();

header('Location: login.php');