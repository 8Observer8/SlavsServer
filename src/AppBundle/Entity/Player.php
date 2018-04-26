<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use GameBundle\Items\ItemFactory;
use GameBundle\Statistics\Statistics;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerRepository")
 */
class Player
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\PlayerAttributes")
     *
     * @var PlayerAttributes
     */
    protected $attributes;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlayerSpecialItems", mappedBy="player", fetch="EAGER")
     *
     * @var Collection|PlayerSpecialItems
     */
    protected $specialItems;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PlayerItem", mappedBy="player", fetch="EAGER")
     *
     * @var Collection|PlayerItem
     */
    protected $items;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $type;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $lvl;


    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $experience;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $gold;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $freeSkillPoints;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $freeAttributesPoints;

    /**
     * @var Statistics
     */
    protected $statistics;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Player
     */
    public function setId(int $id): Player
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Player
     */
    public function setUser(User $user): Player
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return PlayerAttributes
     */
    public function getAttributes(): PlayerAttributes
    {
        return $this->attributes;
    }

    /**
     * @param PlayerAttributes $attributes
     *
     * @return Player
     */
    public function setAttributes(PlayerAttributes $attributes): Player
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Player
     */
    public function setName(string $name): Player
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return Player
     */
    public function setType(int $type): Player
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getLvl(): int
    {
        return $this->lvl;
    }

    /**
     * @param int $lvl
     *
     * @return Player
     */
    public function setLvl(int $lvl): Player
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * @return int
     */
    public function getExperience(): int
    {
        return $this->experience;
    }

    /**
     * @param int $experience
     *
     * @return Player
     */
    public function setExperience(int $experience): Player
    {
        $this->experience = $experience;

        return $this;
    }

    /**
     * @return int
     */
    public function getGold(): int
    {
        return $this->gold;
    }

    /**
     * @param int $gold
     *
     * @return Player
     */
    public function setGold(int $gold): Player
    {
        $this->gold = $gold;

        return $this;
    }

    /**
     * @return int
     */
    public function getFreeSkillPoints(): int
    {
        return $this->freeSkillPoints;
    }

    /**
     * @param int $freeSkillPoints
     *
     * @return Player
     */
    public function setFreeSkillPoints(int $freeSkillPoints): Player
    {
        $this->freeSkillPoints = $freeSkillPoints;

        return $this;
    }

    /**
     * @return int
     */
    public function getFreeAttributesPoints(): int
    {
        return $this->freeAttributesPoints;
    }

    /**
     * @param int $freeAttributesPoints
     *
     * @return Player
     */
    public function setFreeAttributesPoints(int $freeAttributesPoints): Player
    {
        $this->freeAttributesPoints = $freeAttributesPoints;

        return $this;
    }

    /**
     * @return Collection|PlayerSpecialItems
     */
    public function getSpecialItems(): Collection
    {
        return $this->specialItems;
    }

    /**
     * @return Statistics
     */
    public function getStatistics(): Statistics
    {
        if(!$this->statistics) {
            $this->statistics = new Statistics(
                100 + $this->getAttributes()->getHealth() * 5,
                100 + $this->getAttributes()->getHealth() * 5,
                100 + $this->getAttributes()->getAttackSpeed(),
                1 + $this->getAttributes()->getDamage() * 5,
                10 + $this->getAttributes()->getDefence() * 5,
                2.9,
                50 + $this->getAttributes()->getBlockChance(),
                100
            );
        }

        return $this->statistics;
    }

    /**
     * @return Collection
     */
    public function getItems(): Collection
    {
        return $this->items->map(
            function (PlayerItem $playerItem) use (&$return) {
                return ItemFactory::create($playerItem);
            }
        );
    }

}
