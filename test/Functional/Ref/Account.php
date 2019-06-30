<?php

namespace BultonFr\Annotation\Test\Functional\Ref;

class Account
{
    /**
     * @Column(type="int", primary=true)
     */
    protected $id;
    
    /**
     * @Column(type="string")
     */
    protected $name;
    
    /**
     * @Column(name="current_value", type="float")
     */
    protected $currentValue;

    /**
     * @param int $paramId
     *
     * @Route(name="my-path", path="/my-path/")
     * @Route(name="my-path_index", path="/my-path/index")
     *
     * @return void
     * @throws Exception
     */
    public function indexAction($paramId)
    {

    }
}
