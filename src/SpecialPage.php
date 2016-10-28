<?php

namespace TH\MediaWiki\OAuth2Auth;

use SpecialPage AS MediaWikiSpecialPage;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class SpecialPage extends MediaWikiSpecialPage
{
    public function __construct()
    {
        if(!Extension::enabled()) {
            return;
        }

        parent::__construct(Extension::name(), "", false);
    }

    public function execute($subPage) {
        global $wgRequest, $wgOut;
        $this->setHeaders();

        /** @var \League\OAuth2\Client\Provider\GenericProvider */
        $provider = self::provider();

        $session = $wgRequest->getSession();
        $stateSessionKeyPrefix = Extension::name().".state";

        if (!isset($_GET["code"])) {

            // Fetch the authorization URL from the provider; this returns the
            // urlAuthorize option and generates and applies any necessary parameters
            // (e.g. state).
            $authorizationUrl = $provider->getAuthorizationUrl();

            $session["$stateSessionKeyPrefix.id"] = $provider->getState();
            $session["$stateSessionKeyPrefix.returnto"] = $wgRequest->getVal("returnto");
            $session->save();

            $wgOut->redirect($authorizationUrl);
            return;
        }

        // Check given state against previously stored one to mitigate CSRF attack
        if (empty($_GET["state"]) || ($_GET["state"] !== $session["$stateSessionKeyPrefix.id"])) {

            unset($session["$stateSessionKeyPrefix.id"]);
            throw new \RuntimeException("Invalid OAuth2 state");

        }

        unset($session["$stateSessionKeyPrefix.id"]);

        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken("authorization_code", ["code" => $_GET["code"], "debug" => "true"]);

        $userInfo = $this->userInfo($provider->getResourceOwner($accessToken));

        $user = $this->userHandling($userInfo);
        $user->setCookies();

        $title = null;
        if(isset($session["$stateSessionKeyPrefix.returnto"])) {
            $title = \Title::newFromText($session["$stateSessionKeyPrefix.returnto"]);
            unset($session["$stateSessionKeyPrefix.returnto"]);
        }
        if ($title === null) {
            $title = \Title::newMainPage();
        }

        $wgOut->redirect($title->getFullUrl());
    }

    final private function userInfo(ResourceOwnerInterface $owner)
    {
        if (!isset($wgMediaWikiOAuth2Auth["provider.user-info-converter"])) {
            $wgMediaWikiOAuth2Auth["provider.user-info-converter"] = function (ResourceOwnerInterface $owner) {
                return new GenericUserInfo($owner);
            };
        }
        return $wgMediaWikiOAuth2Auth["provider.user-info-converter"]($owner);
    }

    final private function provider()
    {
        global $wgMediaWikiOAuth2Auth;

        if (isset($wgMediaWikiOAuth2Auth["provider"])) {
            return $wgMediaWikiOAuth2Auth["provider"];
        }

        if (!isset($wgMediaWikiOAuth2Auth["provider.config"])) {
            $wgMediaWikiOAuth2Auth["provider.config"] = [];
        }

        if (!isset($wgMediaWikiOAuth2Auth["provider.class"])) {
            $wgMediaWikiOAuth2Auth["provider.class"] = \League\OAuth2\Client\Provider\GenericProvider::class;
        }

        $wgMediaWikiOAuth2Auth["provider.config"]["redirectUri"] = Extension::oauth2RedirectUri();

        $wgMediaWikiOAuth2Auth["provider"] = new $wgMediaWikiOAuth2Auth["provider.class"]($wgMediaWikiOAuth2Auth["provider.config"]);
        return $wgMediaWikiOAuth2Auth["provider"];
    }

    private function userHandling(UserInfo $owner) {
        $externalId = $owner->getId();
        $row = wfGetDB(DB_SLAVE)->selectRow("mediawiki_oauth2_users", "internal_id", ["external_id" => $externalId]);

        if ($row !== false) {
            return \User::newFromId($row->internal_id);
        }

        $user = \User::newFromName($owner->getUsername(), "creatable");

        if($user === false || $user->getId() !== 0) {
            throw new \MWException("Unable to create user.");
        }
        $user->setRealName($owner->getFullName());
        if($owner->hasEmail()) {
            $user->setEmail($owner->getMainEmail());
            $user->setEmailAuthenticationTimestamp(time());
        }
        $user->addToDatabase();
        $dbw = wfGetDB(DB_MASTER);
        $dbw->replace(
            "mediawiki_oauth2_users",
            ["internal_id", "external_id"],
            ["internal_id" => $user->getId(), "external_id" => $externalId]
        );
        return $user;
    }
}
