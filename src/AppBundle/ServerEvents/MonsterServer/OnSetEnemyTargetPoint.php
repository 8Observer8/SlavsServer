<?php

namespace AppBundle\ServerEvents\MonsterServer;


use AppBundle\Entity\Player;
use AppBundle\Server\MonsterServerConnectionEstablishedEvent;
use AppBundle\ServerEvents\AbstractEvent;
use AppBundle\Storage\SocketSessionData;
use GameBundle\Monsters\AbstractMonster;
use GameBundle\Rooms\Room;
use GameBundle\Skills\Block;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\EventDispatcher\Event;


/**
 * @DI\Service
 */
class OnSetEnemyTargetPoint extends AbstractEvent
{

    /**
     * @DI\Observe("monster.server.connection.established.event")
     * @param Event|MonsterServerConnectionEstablishedEvent $event
     *
     * @return AbstractEvent
     */
    public function registerEvent(Event $event): AbstractEvent
    {
        $socket = $event->getSocket();
        $self   = $this;
        $socket->on(
            'setEnemyTarget',
            function ($data) use ($self, $event, $socket) {
                /** @var Room $room */
                $roomId = $data['roomId'];
                $room   = $self->socketIOServer->rooms->getRoom($roomId);

                if(!$room) {
                    return;
                }

                /** @var AbstractMonster $enemy */
                $enemy = $room->getMonsters()[$data['enemyKey']];
                if(!$enemy) {
                    return;
                }
                $attackIsDone = false;

                $enemy
                    ->setPosition($data['position'])
                    ->setTarget($data['target'])
                    ->setAttack($data['attack'])
                    ->setAvailableAttacksFromCharacters($data['availableCharactersToAttack']);

                if ($enemy->isAttack()) {
                    $attackIsDone = true;
                    $players = $room->getPlayers();
                    $attackPlayerId = $enemy->getAvailableAttacksFromCharacters()[$enemy->getTarget()];

                    /** @var SocketSessionData $playerSession */
                    $playerSession = $players[$attackPlayerId];
                    /** @var Player $player */
                    $player = $playerSession->getActivePlayer();

                    $randomDamage = random_int($enemy->getStatistics()->getDamageMin(), $enemy->getStatistics()->getDamageMax());
                    $damage = $randomDamage-$player->getAllStatistics()->getArmor();

                    if($playerSession->getActiveSkill() instanceof Block) {
                        $playerSession->getActiveSkill()->useSkill($damage, $enemy, $player);
                    }

                    if($damage < 1) {
                        $damage = 1;
                    }

                    $player->getStatistics()->setHp($player->getStatistics()->getHp() - $damage);
                    $socket
                        ->to($roomId)
                        ->emit('updatePlayer', $self->serializer->normalize($playerSession, 'array'));
                }

                //emit for update enemy
                $socket
                    ->to($roomId)
                    ->emit(
                        'updateEnemy',
                        [
                            'enemy'          => $self->serializer->normalize($enemy, 'array'),
                            'collisionEvent' => $data['collisionEvent'],
                            'enemyKey'       => $data['enemyKey'],
                            'attackIsDone'  => $attackIsDone,

                ]
                    );

                $enemy->setAttack(false);
            }
        );

        return $this;
    }

}
