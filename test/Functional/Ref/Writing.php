<?php

namespace BultonFr\Annotation\Test\Functional\Ref;

/**
 * @Table(name="writing_list")
 * @AddEntity(
 *  ns="\BultonFr\Annotation\Test\Functional\Ref\Account",
 *  alias="Account"
 * )
 * @AddEntity(
 *  ns="\BultonFr\Annotation\Test\Functional\Ref\Category",
 *  alias="Category"
 * )
 */
class Writing
{
    /**
     * @Column(type="int", primary=true)
     */
    protected $id;
    
    /**
     * @Column(type="entity", entity="Account")
     */
    protected $account;
    
    /**
     * @Column(type="entity", entity="Category")
     */
    protected $category;
    
    /**
     * @Column(type="enum", values="A,B")
     */
    protected $type;
    
    /**
     * @Column(type="datetime")
     */
    protected $date;
    
    /**
     * @Column(type="datetime")
     */
    protected $realDate;
    
    /**
     * @Column(type="string")
     */
    protected $label;
    
    /**
     * @Column(type="float")
     */
    protected $amount;
}
