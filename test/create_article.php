<?php

require_once '../bootstrap.php';

if ($argc !== 3) {
    echo "The correct syntax is create_article.php [title] [content].\n";
    exit(1);
}   

$title = $argv[1];
$content = $argv[2];

$article = new Article();
$article->setTitle($title);
$article->setContent($content);
$article->setCreated(new DateTime('now'));
$article->setSlug(slugify($article->getTitle()));

/* @var $em Doctrine\ORM\EntityManager */
$em->persist($article);
$em->flush();

echo "An article with id {$article->getId()} has been craeted.\n";