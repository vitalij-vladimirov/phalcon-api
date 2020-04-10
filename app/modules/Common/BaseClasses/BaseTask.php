<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Phalcon\Config;
use Dice\Dice;

abstract class BaseTask
{
    protected Config $config;
    protected Dice $inject;

    public function __construct()
    {
        $this->config = $GLOBALS['app']->di->getShared('config');
        $this->inject = new Dice();
    }

    abstract public function mainAction(): void;
}
