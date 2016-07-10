<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ShortestController
{
    /**
     * @Route("/shortest/{owner}/{repo}")
     */
    public function shortestAction()
    {
        return [];
    }
}
