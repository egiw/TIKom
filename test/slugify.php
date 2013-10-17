<?php

/* @var $em Doctrine\ORM\EntityManager */
require_once '../bootstrap.php';

$articlesRepo = $em->getRepository('Article');

/* @var $articles Article[] */
$articles = $articlesRepo->findAll();

foreach ($articles as $index => $article) {
    $article->setSlug(slugify($article->getTitle()));
    
    $em->persist($article);
}

$em->flush();