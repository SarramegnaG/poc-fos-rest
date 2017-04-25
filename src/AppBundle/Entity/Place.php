<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Place
 *
 * @ORM\Table(name="places", uniqueConstraints={@ORM\UniqueConstraint(name="places_name_unique", columns={"name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaceRepository")
 * @UniqueEntity("name")
 */
class Place
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"place", "price"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     *
     * @Groups({"place", "price"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     *
     * @Groups({"place", "price"})
     */
    private $address;

    /**
     * @var Price[]
     *
     * @ORM\OneToMany(targetEntity="Price", mappedBy="place")
     *
     * @Groups({"place"})
     */
    protected $prices;

    public function __construct()
    {
        $this->prices = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Place
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
     * Set address
     *
     * @param string $address
     *
     * @return Place
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Add price
     *
     * @param \AppBundle\Entity\Price $price
     *
     * @return Place
     */
    public function addPrice(\AppBundle\Entity\Price $price)
    {
        $this->prices[] = $price;

        return $this;
    }

    /**
     * Remove price
     *
     * @param \AppBundle\Entity\Price $price
     */
    public function removePrice(\AppBundle\Entity\Price $price)
    {
        $this->prices->removeElement($price);
    }

    /**
     * Get prices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrices()
    {
        return $this->prices;
    }
}
