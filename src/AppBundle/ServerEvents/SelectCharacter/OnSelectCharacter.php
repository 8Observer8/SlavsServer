<?php

namespace AppBundle\ServerEvents\SelectCharacter;


use AppBundle\Manager\PlayerManager;
use AppBundle\Server\ConnectionEstablishedEvent;
use AppBundle\ServerEvents\AbstractEvent;
use GameBundle\Rooms\Room;
use GameBundle\Scenes\Battleground;
use GameBundle\Scenes\CaveExit;
use GameBundle\Scenes\Factory;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\EventDispatcher\Event;


/**
 * @DI\Service
 */
class OnSelectCharacter extends AbstractEvent
{

    /**
     * @DI\Inject("manager.player")
     *
     * @var PlayerManager
     */
    public $playerManager;

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
            'selectCharacter',
            function ($playerId) use ($self, $event, $socket) {
                $socketSessionData = $event->getSocketSessionData();
                $activePlayer      = $self->playerManager->getRepo()->find($playerId);

                //Reset stats after login
                if($activePlayer->statistics) {
                    $activePlayer->statistics = null;
                }

                $startScene = new Battleground();
                $scene      = Factory::createSceneByType($startScene::TYPE);
                $newRoom    = (new Room())
                    ->setId($socket->id)
                    ->setName('RoomTest')
                    ->setPlayers([$activePlayer->getId() => $socketSessionData]);

                $socketSessionData
                    ->setActiveScene($scene)
                    ->setActiveRoom($newRoom)
                    ->setActivePlayer($activePlayer);

                $self->socketIOServer->rooms[$newRoom->getId()] = $newRoom;
                $socketSessionData->setPosition($startScene::START_POSITION);

                $socket
                    ->to($self->socketIOServer->monsterServerId)
                    ->emit('createRoom', $newRoom->getId());

                $socket->emit('changeScene', $scene->type);
            }
        );

        return $this;
    }

}