<?php

/**
 * @Entity
 * @Table(name="articles")
 */
class Article {

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string", unique=true)
     * @var string
     */
    protected $title;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $slug;

    /**
     * @Column(type="text")
     * @var string
     */
    protected $content;

    /**
     * @Column(type="datetime")
     * @var DateTime
     */
    protected $created;

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function getCreated() {
        return $this->created;
    }

    public function setCreated(DateTime $created) {
        $this->created = $created;
    }

    public function getSlug() {
        return $this->slug;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

}