<?php

if (count(get_included_files()) == 1) die("bye");

function monPrint_r($tab) {
return '<pre>' . print_r($tab, true) . '</pre>';
}