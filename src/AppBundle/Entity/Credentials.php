<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Credentials
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    protected $login;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    protected $password;

    /**
     * Set login
     *
     * @param $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}