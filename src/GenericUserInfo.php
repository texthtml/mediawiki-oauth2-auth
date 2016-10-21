<?php

namespace TH\MediaWiki\OAuth2Auth;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class GenericUserInfo implements UserInfo
{
    /** @var ResourceOwnerInterface */
    private $owner;

    public function __construct(ResourceOwnerInterface $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return (string)$this->owner->getId();
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        $data = $this->data();
        return $data["nickname"] ?: $data["name"];
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->data()["name"];
    }

    /**
     * Returns true if the authorized resource owner has an email.
     *
     * @return string
     */
    public function hasEmail()
    {
        return !empty($this->getMainEmail());
    }

    /**
     * Returns the main email of the authorized resource owner.
     *
     * @return string
     */
    public function getMainEmail()
    {
        return $this->data()["email"];
    }

    final private function data()
    {
        return $this->owner->toArray();
    }
}
