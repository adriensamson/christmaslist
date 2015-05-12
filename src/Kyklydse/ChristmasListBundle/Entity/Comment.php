<?php

namespace Kyklydse\ChristmasListBundle\Entity;

class Comment
{
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $content;
    
    /**
     * @var User
     */
    protected $author;

    /**
     * Get id
     *
     * @return int $id
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
     * @param User $author
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
    }

    /**
     * Get author
     *
     * @return User $author
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
