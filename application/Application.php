<?php

use Slim\Slim;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LogLevel;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\Pagination\Paginator;
use \Twig_Extension_Debug;

/**
 * 
 * @property Twig $view Twig
 */
class Application extends Slim
{

    /**
     * Doctrine EntityManager
     * @var EntityManager;
     */
    protected $em;

    /**
     * Monolog Logger
     * @var Logger
     */
    protected $logger;

    public function __construct(array $userSettings = array())
    {
        parent::__construct(array(
            'templates.path' => '../templates/basic',
            'view' => 'Slim\Views\Twig'
        ));

        $this->setName('TikomLab');

        $this->view->parserOptions = array(
            'charset' => 'utf-8',
            'cache' => realpath('../templates/cache'),
            'auto_reload' => true,
            'strict_variables' => false,
            'auto_escape' => true,
            'debug' => true
        );

        $this->view->parserExtensions = array(
            new TwigExtension(),
            new Twig_Extension_Debug()
        );


        $isDevMode = true;
        $config = Setup::createAnnotationMetadataConfiguration(array(realpath('../src')), $isDevMode);

        $conn = array(
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'user' => 'root',
            'password' => '',
            'dbname' => 'Blog'
        );

        $this->em = EntityManager::create($conn, $config);

        $this->notFound(array($this, 'error404'));

        $this->hook('slim.before.router', array($this, 'beforeRouter'));
        $this->hook('slim.after.router', array($this, 'afterRouter'));

        $this->get('/(:page)', array($this, 'homepage'))
                ->name('homepage')
                ->conditions(array('page' => '\d+'));

        $this->get('/:slug', array($this, 'article'))->name('article');
        $this->get('/create', array($this, 'create'))->name('create');
        $this->post('/create', array($this, 'store'));
        $this->get('/search', array($this, 'search'))->name('search');
        $this->get('/manage(/page-:page)', array($this, 'manage'))
                ->name('manage')->conditions(array('page' => '\d+'));
    }

    /**
     * 
     * @param int $page
     */
    public function homepage($page = 1)
    {
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $dql = "SELECT a FROM Article a ORDER BY a.created DESC";
        $query = $this->em->createQuery($dql)
                ->setFirstResult($offset)
                ->setMaxResults($limit);

        $articles = new Paginator($query);
        $count = count($articles);
        $totalPage = ceil($count / $limit);

        $this->render('index.twig', array(
            'articles' => $articles,
            'totalPage' => $totalPage,
            'currentPage' => $page
        ));
    }

    public function article($slug)
    {
        $repo = $this->em->getRepository('Article');
        $article = $repo->findOneBy(array('slug' => $slug));
        if (null === $article)
            $this->pass();

        $this->render('article.twig', array(
            'article' => $article
        ));
    }

    public function create()
    {
        $this->render('create.twig');
    }

    public function store()
    {
        $data = $this->request()->post();
        $article = new Article();
        $article->setTitle($data['title']);
        $article->setContent($data['content']);

        $this->em->persist($article);
        $this->em->flush();

        $this->redirect('/');
    }

    public function manage($page = 1)
    {
        $limit = 5;
        $offset = (($page - 1) * $limit);

        $dql = "SELECT a FROM Article a";
        $query = $this->em->createQuery($dql)
                ->setFirstResult($offset)
                ->setMaxResults($limit);
        $articles = new Paginator($query);

        $count = count($articles);

        $pageCount = ceil($count / $limit);

        $this->render('manage.twig', array(
            'articles' => $articles,
            'pageCount' => $pageCount,
            'currentPage' => (int) $page
        ));
    }

    public function search()
    {
        $q = $this->request()->get('q');
        
        $this->render('search.twig', array('q' => $q));
    }

    public function beforeRouter()
    {
        $template = 'template.twig';
        if ($this->request()->headers('X-Pjax') === 'true')
            $template = 'template_pjax.twig';
        $this->view->set('template', $template);
    }

    public function afterRouter()
    {
        $this->response()->header('X-PJAX-URL', $_SERVER['REQUEST_URI']);
    }

    public function error404()
    {
        $this->render('error/404.twig');
    }

    /**
     * 
     * @return \Monolog\Logger
     */
    private function log()
    {
        $log = new Logger($this->getName());
        $log->pushHandler(new StreamHandler('../logs/app.log', LogLevel::DEBUG));
        return $log;
    }

}