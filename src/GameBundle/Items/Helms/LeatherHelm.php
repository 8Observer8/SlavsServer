<?php

namespace GameBundle\Items\Helms;

use AppBundle\Entity\PlayerItem;
use GameBundle\Statistics\Statistics;

class LeatherHelm extends Helm
{
    const ITEM_ID = 4;

    public function __construct(PlayerItem $entity)
    {
        parent::__construct($entity);

        $this
            ->setName('leatherHelm')
            ->setMeshName('leatherHelm')
            ->setImage('Helm')
            ->setStatistics(new Statistics(0, 0, 0, 0, 5, 0, 0, 0));

    }

}


