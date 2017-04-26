<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Preference
 *
 * @ORM\Table(name="preferences", uniqueConstraints={@ORM\UniqueConstraint(name="preferences_name_user_unique", columns={"name", "user_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PreferenceRepository")
 * @UniqueEntity({"name", "user"})
 * @ExclusionPolicy("none")
 */
class Preference
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"user", "preference"})
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
     * @Groups({"user", "preference"})
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
     * @Groups({"user", "preference"})
     */
    private $value;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="preferences")
     *
     * @Groups({"preference"})
     */
    protected $user;

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
     * @return Preference
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
     * @return Preference
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Preference
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Check if theme matches with preference
     *
     * @param Theme $theme
     *
     * @return bool
     */
    public function match(Theme $theme)
    {
        return $this->name === $theme->getName();
    }
}
