<?php

namespace GameBundle\Quests;

use GameBundle\Monsters\Skeleton;
use GameBundle\Monsters\SkeletonBoss;
use GameBundle\Monsters\SkeletonWarrior;
use GameBundle\Quests\Requirements\KillMonster;

class SkeletonKing extends AbstractQuest
{
    const QUEST_ID = 1;

    /**
     * SkeletonKing constructor.
     */
    public function __construct()
    {
        $this->objectName    = 'questLog';
        $this->title         = 'Skeleton King';
        $this->description   = 'Here it is not safe. This forest is settled by skeletons. We need to return peace in this place, to do that you must kill skeleton king in tombs!';
        $this->actualChapter = 1;

        $chapters[1] = (new Chapter('Kill five skeletons in forest.', $this))
            ->addRequrement(
                new KillMonster(
                    new Skeleton(),
                    5
                )
            );
        $chapters[2] = (new Chapter('Kill three skeleton warriors in forest.', $this))
            ->addRequrement(
                new KillMonster(
                    new SkeletonWarrior(),
                    3
                )
            );

        $chapters[3] = (new Chapter('Kill King of Skeleton in tomb. When you kill him, open golden chest using key which is protected by king.', $this))
            ->addRequrement(
                new KillMonster(
                    new SkeletonBoss(),
                    1
                )
            );

        $this->chapters = $chapters;
    }


}