<?php

namespace AppBundle\ServerEvents\SceneInit;


use AppBundle\Manager\PlayerManager;
use AppBundle\Server\ConnectionEstablishedEvent;
use AppBundle\ServerEvents\AbstractEvent;
use AppBundle\Storage\SocketSessionData;
use GameBundle\Scenes\Factory;
use GameBundle\Scenes\SelectCharacter;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\EventDispatcher\Event;


/**
 * @DI\Service
 */
class OnChangeScenePre extends AbstractEvent
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
            'changeScenePre',
            function () use ($self, $event, $socket) {
                $socketSessionData = $event->getSocketSessionData();
                $playerSessionData = $self->serializer->normalize($socketSessionData, 'array');
                $scene             = $socketSessionData->getActiveRoom()->getActiveScene();

                if ($scene::TYPE == SelectCharacter::TYPE) {
                    $user              = $socketSessionData->getUser();
                    $players           = $self->playerManager->getRepo()->findByUser($user);
                    $playersNormalized = $self->serializer->normalize($players, 'array');

                    $socket->emit('showPlayersToSelect', $playersNormalized);
                } else {
                    $room = $socketSessionData->getActiveRoom();
                    $room->setMonsters($scene->monsters);

                    $socket->emit('showPlayer', $playerSessionData);
                    /** @var SocketSessionData $playerSession */
                    foreach($room->getPlayers() as $playerSession) {
                        $remotePlayerSessionData = $self->serializer->normalize($playerSession, 'array');
                        $socket->emit('showRoomPlayer', $remotePlayerSessionData);
                        $socket
                            ->in($socketSessionData->getActiveRoom()->getId())
                            ->emit('showRoomPlayer', $remotePlayerSessionData);
                    };

                    $socket
                        ->to($self->socketIOServer->monsterServerId)
                        ->emit(
                            'createEnemies',
                            [
                                'enemies' => $self->serializer->normalize($room->getMonsters(), 'array'),
                                'roomId'  => $room->getId()
                            ]
                        );
                }

            }
        );

        return $this;
    }

}
