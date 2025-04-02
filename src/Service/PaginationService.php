<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService
{
    public function __construct(PaginatorInterface $paginator, RequestStack $requestStack)
    {
        $this->paginator = $paginator;
        $this->requestStack = $requestStack;
    }
    //option limit of element
    public function paginate(QueryBuilder $queryBuilder, int $limit = 10): PaginationInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        $page = max(1, $request->query->getInt('page', 1)); //page num, min 1

        return $this->paginator->paginate($queryBuilder, $page, $limit, [
            'distinct' => true,
            'fetchJoinCollection' => true
        ]);
    }
}
