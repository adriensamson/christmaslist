<?php

namespace Kyklydse\ChristmasListBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\EmbeddedDocument
 */
class Item
{
    /**
     * @MongoDB\Id
     */
    protected $id;
    
    
    /**
     * @MongoDB\String
     */
    protected $title;
    
    /**
     * @MongoDB\String
     */
    protected $description;
    
    /**
     * @MongoDB\String
     */
    protected $url;
    
    /**
     * @MongoDB\ReferenceOne(targetDocument="User")
     */
    protected $proposer;
    
    /**
     * @MongoDB\EmbedMany(targetDocument="Comment")
     */
    protected $comments;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set proposer
     *
     * @param Kyklydse\ChristmasListBundle\Document\User $proposer
     */
    public function setProposer(\Kyklydse\ChristmasListBundle\Document\User $proposer)
    {
        $this->proposer = $proposer;
    }

    /**
     * Get proposer
     *
     * @return Kyklydse\ChristmasListBundle\Document\User $proposer
     */
    public function getProposer()
    {
        return $this->proposer;
    }
    public function __construct()
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add comment
     *
     * @param Kyklydse\ChristmasListBundle\Document\Comment $comment
     */
    public function addComment(\Kyklydse\ChristmasListBundle\Document\Comment $comment)
    {
        $this->comments[] = $comment;
    }

    /**
     * Get comments
     *
     * @return Doctrine\Common\Collections\Collection $comments
     */
    public function getComments()
    {
        return $this->comments;
    }
}
