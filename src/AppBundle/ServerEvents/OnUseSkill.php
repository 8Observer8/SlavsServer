<?php

namespace AppBundle\ServerEvents;


use AppBundle\Manager\PlayerManager;
use AppBundle\Manager\SpecialItemManager;
use AppBundle\Server\ConnectionEstablishedEvent;
use AppBundle\Server\SocketIO;
use GameBundle\Items\AbstractItem;
use GameBundle\Monsters\AbstractMonster;
use GameBundle\Quests\Chapter;
use GameBundle\Quests\Requirements\AbstractRequirement;
use GameBundle\Quests\Requirements\KillMonster;
use GameBundle\Skills\Factory;
use GameBundle\SpecialItems\AbstractSpecialItem;
use GameBundle\SpecialItems\Gold;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\EventDispatcher\Event;


/**
 * @DI\Service
 */
class OnUseSkill extends AbstractEvent
{

    /**
     * @DI\Inject("manager.player")
     *
     * @var PlayerManager
     */
    public $playerManager;

    /**
     * @DI\Inject("manager.special_item")
     *
     * @var SpecialItemManager
     */
    public $specialItemManager;

    /**
     * @DI\Inject("app.server.socket")
     *
     * @var SocketIO
     **/
    public $socketIOServer;

    /**
     * @DI\Observe("connection.established.event")
     * @param Event|ConnectionEstablishedEvent $event
     *
     * @return AbstractEvent
     */
    public function registerEvent(Event $event): AbstractEvent
    {
        $socket = $event->getSocket();
        $self   = $this;
        $socket->on(
            'useSkill',
            function ($skillType) use ($self, $event, $socket) {
                $socketSessionData = $event->getSocketSessionData();
                $playerEnergy      = $socketSessionData->getActivePlayer()->getStatistics()->getEnergy();
                $skill             = ($skillType) ? Factory::getSkillByType($skillType) : null;
                if ($playerEnergy - $skill->energy >= 0) {
                    $socketSessionData
                        ->setActiveSkill($skill)
                        ->setAttack(null)
                        ->getActivePlayer()->getStatistics()->setEnergy($playerEnergy - $skill->energy);

                    $socket
//                    ->to($roomId)
                        ->emit('updatePlayerSkill', $self->serializer->normalize($socketSessionData, 'array'));
                    $socket
                        ->to($self->socketIOServer->monsterServerId)
                        ->emit('updatePlayerSkill', $self->serializer->normalize($socketSessionData, 'array'));
                }
            }
        );

        return $this;
    }

}
