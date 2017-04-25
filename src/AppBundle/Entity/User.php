<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="users_email_unique", columns={"email"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User
{
    const MATCH_VALUE_THRESHOLD = 25;

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
     * @Assert\NotBlank()
     *
     * @Groups({"user", "preference"})
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     *
     * @Groups({"user", "preference"})
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     *
     * @Assert\Email()
     * @Assert\NotBlank()
     *
     * @Groups({"user", "preference"})
     */
    private $email;

    /**
     * @var Preference[]
     *
     * @ORM\OneToMany(targetEntity="Preference", mappedBy="user")
     *
     * @Groups({"user"})
     */
    protected $preferences;

    public function __construct()
    {
        $this->preferences = new ArrayCollection();
    }

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
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Add preference
     *
     * @param \AppBundle\Entity\Preference $preference
     *
     * @return User
     */
    public function addPreference(\AppBundle\Entity\Preference $preference)
    {
        $this->preferences[] = $preference;

        return $this;
    }

    /**
     * Remove preference
     *
     * @param \AppBundle\Entity\Preference $preference
     */
    public function removePreference(\AppBundle\Entity\Preference $preference)
    {
        $this->preferences->removeElement($preference);
    }

    /**
     * Get preferences
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPreferences()
    {
        return $this->preferences;
    }

    /**
     * Check if preferences matches
     *
     * @param $themes
     *
     * @return bool
     */
    public function preferencesMatch($themes)
    {
        $matchValue = 0;
        foreach ($this->preferences as $preference) {
            /** @var Preference $preference */
            foreach ($themes as $theme) {
                /** @var Theme $theme */
                if ($preference->match($theme)) {
                    $matchValue += $preference->getValue() * $theme->getValue();
                }
            }
        }

        return $matchValue >= self::MATCH_VALUE_THRESHOLD;
    }
}
