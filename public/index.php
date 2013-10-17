<?php

use Slim\Slim;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LogLevel;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

require '../vendor/autoload.php';

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

    public function __construct(array $userSettings = array())
    {
        parent::__construct(array(
            'templates.path' => '../templates/basic',
            'view' => 'Slim\Views\Twig'
        ));

        $this->view->parserOptions = array(
            'charset' => 'utf-8',
            'cache' => realpath('../templates/cache'),
            'auto_reload' => true,
            'strict_variables' => false,
            'auto_escape' => true
        );

        $this->view->parserExtensions = array(new TwigExtension());

        $this->setName('TikomLab');

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

        $this->hook('slim.before.router', array($this, 'beforeRouter'));
        $this->hook('slim.after.router', array($this, 'afterRouter'));

        $this->get('/', array($this, 'homepage'))->name('homepage');
        $this->get('/:slug', array($this, 'article'))->name('article');
        $this->get('/create', array($this, 'create'))->name('create');
        $this->post('/create', array($this, 'store'));
    }

    public function homepage()
    {
        $this->log->info("Slim-Skeleton '/' route");

        $articleRepo = $this->em->getRepository('Article');
        $articles = $articleRepo->findAll();
        $this->render('index.twig', array(
            'articles' => $articles
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
        $article->setCreated(new DateTime('now'));
        $article->setSlug($this->slugify($data['title']));

        $this->em->persist($article);
        $this->em->flush();

        $this->redirect('/');
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
        $this->response()->header('X-PJAX-URL', $this->request()->getResourceUri());
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

    private function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

}
$app = new Application;
$app->run();