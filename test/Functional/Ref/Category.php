<?php

namespace BultonFr\Annotation\Test\Functional\Ref;

/**
 * @AddNS(ns="\BultonFr\Annotation\Test\Functional\Annotations\HTTPMethod")
 * @AddEntity(
 *  ns="\BultonFr\Annotation\Test\Functional\Ref\Category",
 *  alias="Ref\Category"
 * )
 * @AddNS(ns="\BultonFr\Annotation\Test\Functional\Annotations\Security")
 */
class Category
{
    /**
     * @Column(type="int", primary=true)
     */
    protected $id;
    
    /**
     * @Column(type="entity", entity="\BultonFr\Annotation\Test\Functional\Ref\Account")
     */
    protected $account;
    
    /**
     * @Column(type="string")
     */
    protected $name;
    
    /**
     * @Column(
     *      name="parent_id",
     *      type="entity",
     *      entity="Ref\Category"
     * )
     */
    protected $parent;

    /**
     * @param int $paramId
     *
     * @Route(name="category", path="/category")
     * @Security(fct="mySecurity", role="ADMIN", test="not-to-be-here")
     * @HTTPMethod(methods="GET")
     */
    public function indexAction($paramId)
    {
        echo 'Hello test world';
    }
}
