<?php

namespace TH\MediaWiki\OAuth2Auth;

class Hooks
{
    public static function onUserLoginForm($tpl) {
        $url = htmlspecialchars(\Skin::makeSpecialUrl(Extension::name(), "returnto={$_GET["returnto"]}"));
        $text = wfMessage('mediawiki-oauth2-auth_login')->parse(); //"Se connecter avec FactorLead";
        $header = <<<HTML
    {$tpl->get("header")}
    <a class="mw-ui-button media-wiki-o-auth2-button" href="$url">$text</a>
HTML;
        $tpl->set("header", $header);
    }

    public static function onLoadExtensionSchemaUpdates(\DatabaseUpdater $updater) {
        $updater->addExtensionTable("mediawiki_oauth2_users", __DIR__."/sql/users.sql");
        $updater->doUpdates();
        return true;
    }
}
