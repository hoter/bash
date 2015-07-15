<?php

require "vendor/autoload.php";
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;
use Symfony\Component\DomCrawler\Crawler;

$pid = pcntl_fork();

if ($pid == -1) {
  echo "Something is wrong!";
} 
elseif ($pid) {
  exit;
}

posix_setsid();

$client = new GuzzleHttp\Client();
$res = $client->get('http://bash.im/random');

$html = $res->getBody()->getContents();

$crawler = new Crawler($html);

// Create a Notifier (or null if no notifier supported)
$notifier = NotifierFactory::create();

if ($notifier) {
 // Create your notification
 $notification =
  (new Notification())
   ->setTitle('bash.im')
   ->setBody($crawler->filter('div.text')->first()->html())
  ;

 // Send it
 $notifier->send($notification);


 $stop_server = FALSE;
 while (!$stop_server) {
  sleep(300);
  $notifier->send($notification);
 }
}
