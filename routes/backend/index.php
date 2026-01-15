<?php

// Load all backend subfolders automatically
foreach (glob(__DIR__ . '/*/*.php') as $routeFile) {
    require $routeFile;
}
