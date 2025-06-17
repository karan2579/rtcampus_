<?php
require_once 'functions.php';

// Fetch HTML content of a random XKCD comic
$comicHtml = fetchAndFormatXKCDData();

// Send it to all subscribers
sendXKCDUpdatesToSubscribers();
