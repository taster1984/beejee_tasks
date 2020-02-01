<?php

namespace Tasks\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="tasks")
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @ORM\Column(type="string")
     */
    private $mail;
    /**
     * @ORM\Column(type="string")
     */
    private $text;

    /**
     * @ORM\Column(type="integer")
     */
    private $admined;

    /**
     * @ORM\Column(type="integer")
     */
    private $complited;

    /**
     * @return mixed
     */
    public function getComplited()
    {
        return $this->complited;
    }

    /**
     * @param mixed $complited
     */
    public function setComplited($complited)
    {
        $this->complited = $complited;
    }

    /**
     * @return mixed
     */
    public function getAdmined()
    {
        return $this->admined;
    }

    /**
     * @param mixed $admined
     */
    public function setAdmined($admined)
    {
        $this->admined = $admined;
    }
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


}