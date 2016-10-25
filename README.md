# mediawiki-oauth2-auth

Authenticate Mediawiki users from external OAuth2 server

## installation

```sh
composer require texthtml/mediawiki-oauth2-auth
```

## 

```php

// load the extension
\TH\MediaWiki\OAuth2Auth\Extension::load();

// configure the OAuth2 provider
$wgMediaWikiOAuth2Auth['provider.config'] = [
    'clientId'                => 'demoapp',     // The client ID assigned to you by the provider
    'clientSecret'            => 'demopass', // The client password assigned to you by the provider
    'redirectUri'             => 'https://example.com/callback-url',
    'urlAuthorize'            => 'https://auth.dataporten.no/oauth/authorization',
    'urlAccessToken'          => 'https://auth.dataporten.no/oauth/token',
    'urlResourceOwnerDetails' => 'https://auth.dataporten.no/userinfo',
];

// optional: select another OAuth2 provider @see https://github.com/thephpleague/oauth2-client/blob/master/docs/providers/thirdparty.md
$wgMediaWikiOAuth2Auth['provider.class'] = \League\OAuth2\Client\Provider\GenericProvider::class;

// option: or build it manually (without setting $wgMediaWikiOAuth2Auth['provider.config'])
$wgMediaWikiOAuth2Auth['provider'] = new \League\OAuth2\Client\Provider\GenericProvider::class([
    'clientId'                => 'demoapp',     // The client ID assigned to you by the provider
    'clientSecret'            => 'demopass', // The client password assigned to you by the provider
    'redirectUri'             => 'https://example.com/callback-url',
    'urlAuthorize'            => 'https://auth.dataporten.no/oauth/authorization',
    'urlAccessToken'          => 'https://auth.dataporten.no/oauth/token',
    'urlResourceOwnerDetails' => 'https://auth.dataporten.no/userinfo',
]);

```
