<?php

/* @var $em Doctrine\ORM\EntityManager */

require '../bootstrap.php';

$config = array(
    'templates.path' => '../templates/basic'
);

// Prepare app
$app = new \Slim\Slim($config);
$app->setName('TIKom');

// Create monolog logger and store logger in container as singleton 
// (Singleton resources retrieve the same log resource definition each time)
$app->container->singleton('log', function () {
            $log = new \Monolog\Logger('slim-skeleton');
            $log->pushHandler(new \Monolog\Handler\StreamHandler('../logs/app.log', \Psr\Log\LogLevel::DEBUG));
            return $log;
        });

// Prepare view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());

$app->hook('slim.before.router', function() use ($app) {
            $template = 'template.twig';
            if ($app->request()->headers('X-Pjax') === 'true')
                $template = 'template_pjax.twig';
            $app->view()->set('template', $template);
        });

$app->get('/', function() use ($app, $em) {
            $app->log->info("Slim-Skeleton '/' route");

            $articleRepo = $em->getRepository('Article');
            $articles = $articleRepo->findAll();
            $app->render('index.twig', array(
                'articles' => $articles
            ));
        })->name('homepage');


$app->get('/:slug', function($slug) use($app, $em) {
            $repo = $em->getRepository('Article');
            $article = $repo->findOneBy(array('slug' => $slug));
            if (null === $article)
                $app->notFound();

            $app->render('article.twig', array(
                'article' => $article
            ));
        })->name('article');




// Run app
$app->run();

class MyApplication extends Slim
{

    /**
     *
     * @var Slim\Slim
     */
    protected $app;

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(Slim $app, \Doctrine\ORM\EntityManager $em)
    {
        $this->app = $app;
        $this->em = $em;
    }

    public function homepage()
    {
        
    }

}