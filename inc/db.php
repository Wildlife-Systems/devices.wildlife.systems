<?php

if (!file_exists("../config/db.php")) {
    print(".ws-db.php does not exist.\nSee https://github.com/Wildlife-Systems/wildlife.systems-tools/wiki/.ws-db.php-does-not-exist\n\n");
    exit(1);
}
include("../config/db.php");
