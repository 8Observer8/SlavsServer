<?php

namespace GameBundle\Scenes;

use GameBundle\Gateways\EntraceForestHouseTomb;
use GameBundle\Gateways\EntraceHouse;
use GameBundle\Monsters\Skeleton;
use GameBundle\Monsters\SkeletonBoss;
use GameBundle\Monsters\SkeletonWarrior;
use GameBundle\SpecialItems\Gold;
use Symfony\Component\Config\Definition\Exception\Exception;

class Factory
{
    /**
     * @param int $type
     *
     * @return AbstractScene
     */
    static public function createSceneByType(int $type): AbstractScene
    {
        $scene = null;

        switch ($type) {
            case ForestHouse::TYPE:
                $scene = new ForestHouse();
                break;
            case ForestHouseStart::TYPE:
                $scene = new ForestHouseStart();
                break;
            case ForestHouseTomb::TYPE:
                $scene = new ForestHouseTomb();
                break;
        }

        if (!$scene) {
            throw new Exception('Can not find scene');
        }

        return $scene;

    }

}