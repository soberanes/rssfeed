<?php
header("Content-Type: application/xml");

$xml = new DOMDocument("1.0", "UTF-8");
//rss element
$xml_rss = $xml->createElement('rss');
$xml_rss->setAttribute('version','2.0');
$xml_rss = $xml->appendChild($xml_rss);

//channel element
$xml_channel = $xml->createElement('channel');
$xml_channel = $xml_rss->appendChild($xml_channel);
//title elemente
$xml_title = $xml->createElement('title', 'fsdkfsgd fsd');
$xml_title = $xml_channel->appendChild($xml_title);
//link elemente
$xml_link = $xml->createElement('link', 'fsdkfsgd fsd');
$xml_link = $xml_channel->appendChild($xml_link);
//description elemente
$xml_description = $xml->createElement('description', 'NEUBOX ofrece soluciones de web hosting y hospedaje web así como, registro de dominios, planes para domainers, revendedor de hosting, resellers y VPS');
$xml_description = $xml_channel->appendChild($xml_description);
//language elemente
$xml_language = $xml->createElement('language', 'es');
$xml_language = $xml_channel->appendChild($xml_language);

//image elemente
$xml_image = $xml->createElement('image');
$xml_image = $xml_channel->appendChild($xml_image);

$xml_image->appendChild($xml->createElement('title', 'fds fds ds'));
$xml_image->appendChild($xml->createElement('link', 'fds'));
$xml_image->appendChild($xml->createElement('url', 'fds'));

$item = $xml->createElement('item');
$item = $xml_channel->appendChild($item);
$item->appendChild($xml->createElement('title', 'fdjkdf sajgkf'));
$item->appendChild($xml->createElement('link', 'fdjkdf sajgkf'));
$item->appendChild($xml->createElement('description', 'fdjkdf sajgkf'));
$item->appendChild($xml->createElement('pubDate', 'fdjkdf sajgkf'));
$item->appendChild($xml->createElement('category', 'fdjkdf sajgkf'));
$item->appendChild($xml->createElement('source', 'fdjkdf sajgkf'));


print $xml->saveXML();
die;
