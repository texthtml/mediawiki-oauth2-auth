<?php

namespace TH\MediaWiki\OAuth2Auth;

class Extension
{
    public static function load() {
        global $wgVersion;
        assert(
            version_compare($wgVersion, '1.27', '>='),
            "This version of the MediaWikiOAuth2 extension requires MediaWiki 1.27+"
        );

        wfLoadExtension(self::name());
    }

    public static function enabled()
    {
        return true;
    }

    public static function name()
    {
        return "MediawikiOauth2Auth";
    }

    public static function oauth2RedirectUri()
    {
        return \SpecialPage::getSafeTitleFor(self::name())->getFullURL();
    }
}
