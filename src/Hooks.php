<?php

namespace TH\MediaWiki\OAuth2Auth;

class Hooks
{
    public static function onUserLoginForm($tpl) {
        global $wgMediaWikiOAuth2Auth;

        $url = htmlspecialchars(\Skin::makeSpecialUrl(Extension::name(), "returnto={$_GET["returnto"]}"));

        if (array_key_exists("service-name", $wgMediaWikiOAuth2Auth)) {
            $text = wfMessage("mediawiki-oauth2-auth_login", $wgMediaWikiOAuth2Auth["service-name"]);
        } else {
            $text = wfMessage("mediawiki-oauth2-auth_login-anonymous-service");
        }
        $header = <<<HTML
    {$tpl->get("header")}
    <a class="mw-ui-button media-wiki-o-auth2-button" href="$url">{$text->escaped()}</a>
HTML;
        $tpl->set("header", $header);
    }

    public static function onLoadExtensionSchemaUpdates(\DatabaseUpdater $updater) {
        $updater->addExtensionTable("mediawiki_oauth2_users", __DIR__."/sql/users.sql");
        $updater->doUpdates();
        return true;
    }
}
