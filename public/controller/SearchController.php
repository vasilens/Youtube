<?php

namespace controller;

use components\router\http\Request;
use exceptions\InvalidArgumentException;
use model\PlaylistDAO;
use model\UserDAO;
use model\VideoDAO;
use services\SearchService;

class SearchController extends AbstractController
{
    /**
     * @var SearchService
     */
    private $searchService;

    /**
     * SearchController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->searchService = new SearchService();
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function search()
    {
        $postParams = $this->request->getPostParams();
        if (isset($postParams['search'])) {
            if (empty(trim($postParams['search_query']))) {

                include_once "view/main.php";

                echo "<h3>Search field is empty.</h3>";

                return;
            }
            $this->searchService->search($postParams);
        } else {
            throw new InvalidArgumentException("Invalid arguments.");
        }
    }
}