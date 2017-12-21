<?php

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Session\Adapter\Files;

/**
 * Class ControllerBase
 */
class ControllerBase extends Controller
{

    /** @var  Files */
    protected $session;

    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        /** @var Files $session */
        $this->session = $this->getDI()->get('session');
    }
}
