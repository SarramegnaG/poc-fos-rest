<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Theme
 *
 * @ORM\Table(name="themes", uniqueConstraints={@ORM\UniqueConstraint(name="themes_name_place_unique", columns={"name", "place_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ThemeRepository")
 * @UniqueEntity({"name", "place"})
 * @ExclusionPolicy("none")
 */
class Theme
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"place", "theme"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     *
     * @Assert\NotNull()
     * @Assert\Choice({"art", "architecture", "history", "science-fiction", "sport"})
     *
     * @Groups({"place", "theme"})
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\NotNull()
     * @Assert\Type("numeric")
     * @Assert\GreaterThan(0)
     * @Assert\LessThanOrEqual(10)
     *
     * @Groups({"place", "theme"})
     */
    private $value;

    /**
     * @var Place
     *
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="themes")
     *
     * @Groups({"theme"})
     */
    protected $place;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Theme
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param integer $value
     *
     * @return Theme
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set place
     *
     * @param \AppBundle\Entity\Place $place
     *
     * @return Theme
     */
    public function setPlace(\AppBundle\Entity\Place $place = null)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return \AppBundle\Entity\Place
     */
    public function getPlace()
    {
        return $this->place;
    }
}
