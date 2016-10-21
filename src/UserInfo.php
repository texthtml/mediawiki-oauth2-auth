<?php

namespace TH\MediaWiki\OAuth2Auth;

interface UserInfo
{
    /**
     * Returns the identifier of the authorized resource owner.
     *
     * @return string
     */
    public function getId();

    /**
     * Returns the username of the authorized resource owner.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Returns the full name of the authorized resource owner.
     *
     * @return string
     */
    public function getFullName();

    /**
     * Returns true if the authorized resource owner has an email.
     *
     * @return string
     */
    public function hasEmail();

    /**
     * Returns the main email of the authorized resource owner.
     *
     * @return string
     */
    public function getMainEmail();
}
