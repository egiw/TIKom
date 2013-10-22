<?php

/**
 * Custom paginator class
 */
class Paginator extends Doctrine\ORM\Tools\Pagination\Paginator
{

    /**
     * The current page
     * @var int
     */
    protected $currentPage = null;

    /**
     *
     * @var type 
     */
    public $rowCountPerPage = 5;

    public function __construct($query, $page, $fetchJoinCollection = true)
    {
        parent::__construct($query, $fetchJoinCollection);

        $this->currentPage = (int) $page;
    }

    /**
     * The current page
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Get number of pages
     * @return int
     */
    public function getPageCount()
    {
        return (int) ceil($this->count() / $this->rowCountPerPage);
    }

    /**
     * Return the next page if available
     * otherwise false
     * @return mixed
     */
    public function getNextPage()
    {
        $nextPage = $this->currentPage + 1;

        // whether the next page available
        $hasNextPage = $nextPage <= $this->getPageCount();

        return $hasNextPage ? $nextPage : false;
    }

    /**
     * Return the previous page if available
     * otherwise false
     * @return mixed
     */
    public function getPreviousPage()
    {
        $prevPage = $this->currentPage - 1;

        $hasPrevPage = $prevPage > 0;

        return $hasPrevPage ? $prevPage : false;
    }

    /**
     * Get part page numbers based on offset and limit
     * @param int $offset
     * @param int $limit
     * @return Array The page numbers
     */
    public function getPageNumbers($offset = 3, $limit = 8)
    {
        // array of full page numbers
        $fullPageNumbers = range(1, $this->getPageCount());

        // default start number index is 0
        $start = 0;

        if ($this->currentPage + ($limit - $offset) >= $this->getPageCount())
            $start = $this->getPageCount() - $limit;
        else if ($this->currentPage > $offset)
            $start = $this->currentPage - $offset;

        // array of display page numbers
        $partPageNumbers = array_slice($fullPageNumbers, $start, $limit);

        return $partPageNumbers;
    }

    /**
     * Check whether the given page is currentPage
     * @param int $page
     */
    public function isActive($page)
    {
        return (int) $page == $this->currentPage;
    }

}