<?php
header("Content-Type: application/xml");
// header("Content-Type: text/plain");
require_once 'classes/rss.class.php';

//set more namespaces if you need them
$xmlns = 'xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"';

/**
 * Database connection settings
 */
$a_db = array(
    "db_server" => "localhost",
    "db_name"   => "feed",
    "db_user"   => "root",
    "db_passwd" => "root",
);

/**
 * RSS channel properties
 */
$a_channel = array(
    "title" => "neubox.com",
    "link" => "https://neubox.com",
    "description" => "NEUBOX ofrece soluciones de web hosting y hospedaje web así como, registro de dominios, planes para domainers, revendedor de hosting, resellers y VPS",
    "language" => "en",
    "image_title" => "neubox.com",
    "image_link" => "https://neubox.com/",
    "image_url" => "https://neubox.com/feed/rss.php",
);

$site_url = 'https://neubox.com';
$site_name = "NEUBOX, Hospedaje Web, Web Hosting, Registro de Dominios y VPS";

$rss = new rss($a_db, $xmlns, $a_channel, $site_url, $site_name);
print $rss->create_feed();
