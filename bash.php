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

// Create a Notifier (or null if no notifier supported)
$notifier = NotifierFactory::create();

$stop_server = FALSE;
while (!$stop_server) {
 $res = $client->get('http://bash.im/random');
 $html = $res->getBody()->getContents();

 $crawler = new Crawler($html);

 if ($notifier) {
  // Create your notification
  $notification =
   (new Notification())
    ->setTitle('bash.im')
    ->setBody($crawler->filter('div.text')->first()->html())
   ;

  // Send it
  $notifier->send($notification);
  sleep(500);
 }
 else {
  break;
 }
}
