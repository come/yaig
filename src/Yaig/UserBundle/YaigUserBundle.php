<?php

namespace Yaig\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class YaigUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
