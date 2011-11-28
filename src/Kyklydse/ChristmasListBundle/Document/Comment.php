<?php

namespace Kyklydse\ChristmasListBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\EmbeddedDocument
 */
class Comment
{
    /**
     * @MongoDB\Id
     */
    protected $id;
    
    /**
     * @MongoDB\String
     */
    protected $content;
    
    /**
     * @MongoDB\ReferenceOne(targetDocument="User")
     */
    protected $author;

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
     * Set content
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return string $content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set author
     *
     * @param Kyklydse\ChristmasListBundle\Document\User $author
     */
    public function setAuthor(\Kyklydse\ChristmasListBundle\Document\User $author)
    {
        $this->author = $author;
    }

    /**
     * Get author
     *
     * @return Kyklydse\ChristmasListBundle\Document\User $author
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
