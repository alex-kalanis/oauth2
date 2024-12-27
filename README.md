OAuth2 Provider
===============

![Build Status](https://github.com/alex-kalanis/oauth2/actions/workflows/code_checks.yml/badge.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-kalanis/oauth2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-kalanis/oauth2/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/alex-kalanis/oauth2/v/stable.svg?v=1)](https://packagist.org/packages/alex-kalanis/oauth2)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg)](https://php.net/)
[![Downloads](https://img.shields.io/packagist/dt/alex-kalanis/oauth2.svg?v1)](https://packagist.org/packages/alex-kalanis/oauth2)
[![License](https://poser.pugx.org/alex-kalanis/oauth2/license.svg?v=1)](https://packagist.org/packages/alex-kalanis/oauth2)
[![Code Coverage](https://scrutinizer-ci.com/g/alex-kalanis/oauth2/badges/coverage.png?b=master&v=1)](https://scrutinizer-ci.com/g/alex-kalanis/oauth2/?branch=master)

This is repository for adding OAuth into Nette. Fork of older Drahak repository with
refactor to run on php 8.1+.

The main difference is in directory structure, namespaces, tests, static analysis and
dependency check.

This package also supports multiple storages. With a few simple steps (implementing
own version and extending configuration) you can add another storage. Currently available
are Nette DB and Dibi.

Requirements
------------
kalanis/OAuth2 requires PHP version 8.1.0 or higher. The only production dependency
is [Nette framework 3.2.x](http://www.nette.org).

But for php8.1 you probably need to add [`SensitiveParameter`](https://www.php.net/manual/en/class.sensitiveparameter.php)
attribute class into your bootstrap. The example is in Tests.

Installation & setup
--------------------
The easiest way is to use [Composer](http://doc.nette.org/en/composer)

```bash
$ composer require alex-kalanis/oauth2
```

Then add following code to your app bootstrap file before creating container:

```php
kalanis\OAuth2\DI\Extension::install($configurator);
```

Neon configuration
------------------
```yaml
oauth2:
	accessTokenLifetime: 3600 # 1 hour
	refreshTokenLifetime: 36000 # 10 hours
	authorizationCodeLifetime: 360 # 6 minutes
	storage: 'ndb' # currently allowed values: 'ndb', 'dibi'
	accessTokenStorage: 'kalanis\OAuth2\Storage\NDB\AccessTokenStorage'
	authorizationCodeStorage: 'kalanis\OAuth2\Storage\NDB\AuthorizationCodeStorage'
	clientStorage: 'kalanis\OAuth2\Storage\NDB\ClientStorage'
	refreshTokenStorage: 'kalanis\OAuth2\Storage\NDB\RefreshTokenStorage'
```

- `accessTokenLifetime` - access token lifetime in seconds
- `refreshTokenLifetime` - refresh token lifetime in seconds
- `authorizationCodeLifetime` - authorization code lifetime in seconds
- `storage` - storage will switch between default NDB and dibi storage. You can use
    your storage for each storage part.

OAuth2
------

#### [Abstract protocol flow](http://tools.ietf.org/html/rfc6749#section-1.2)
```
     +--------+                               +---------------+
     |        |------ Authorization Request ->|   Resource    |
     |        |                               |     Owner     |
     |        |<------ Authorization Grant ---|               |
     |        |                               +---------------+
     |        |
     |        |                               +---------------+
     |        |------- Authorization Grant -->| Authorization |
     | Client |                               |     Server    |
     |        |<--------- Access Token -------|               |
     |        |                               +---------------+
     |        |
     |        |                               +---------------+
     |        |---------- Access Token ------>|    Resource   |
     |        |                               |     Server    |
     |        |<------- Protected Resource ---|               |
     +--------+                               +---------------+
```

OAuth Roles
-----------

#### Client - the third-party application
This application wants to get user's data from Resource server, so it needs to get
an access token.

#### Resource server - API
There is data which client wants. API server uses access token to access user's
information.

#### Resource owner
Gives access to some portion of their account.

See also [OAuth 2 Simplified](http://aaronparecki.com/articles/2012/07/29/1/oauth2-simplified) and [original specification](http://tools.ietf.org/html/rfc6749)

OAuth presenter
---------------
Presenter (`IOAuthPresenter`) that gives an access. In base it has 2 main methods,
`issueAccessToken` and `issueAuthorizationCode`. Simple OAuth (Resource owner)
presenter could looks like this:

```php

namespace MyApp\OAuth;

use kalanis\OAuth2\Grant\IGrant;
use kalanis\OAuth2\Application;
use kalanis\OAuth2\Exceptions\OAuthException;

class AuthorizationPresenter extends Application\OAuthPresenter
{

    /**
     * Authorization
     * @param string $response_type
     * @param string $redirect_uri
     * @param string|null $scope
     */
    public function actionAuthorize(string $response_type, string $redirect_uri, ?string $scope = NULL): void
    {
        if (!$this->user->isLoggedIn()) {
            $this->redirect('AnyUser:login', array('backlink' => $this->storeRequest()));
        }

        if ($response_type == 'code') {
            $this->issueAuthorizationCode($response_type, $redirect_uri, $scope);
        } else if ($response_type == 'token') {
            $this->issueAccessToken(IGrant::IMPLICIT, $redirect_uri);
        }
    }

    /**
     * Access token provider
     */
    public function actionToken(): void
    {
        try {
            $this->issueAccessToken();
        } catch (OAuthException $e) {
            $this->oauthError($e);
        }
    }

}
```
Method `issueAccessToken` determines correct grant type from `grant_type` parameter.
In case of error throws some `OAuthException` which can be handled by `oauthError`
method in default implementation.

Action `authorize` is more complex. This is used for generating Authorization code
(see below - [Authorization code](#authorization-code)) but for Implicit grant type it's necessary
to generate access token here. In case if user is not logged in, redirect user to
some login page and then restore authorization request using backlink.

Grant types
-----------
Are determined by `grant_type` parameter. There is support of base grant types as
defined in OAuth2 specification: Authorization Code, Implicit, Password, Client
Credentials and Refresh token.

1. Authorization code
-----------
This grant type is great for third-party applications which can secure client secret
code.

To generate access token, you'll need to get authorization code first. You can obtain
it from `IOAuthPresenter` by calling `issueAuthorizationCode`.

##### Request for authorization code:
```
GET //oauth.presenter.url/authorize?response_type=code&client_id=CLIENT_ID&redirect_uri=REDIRECT_URI&scope=email
```
- [REQUIRED] **response_type** - you want to generate authorization `code`
- [REQUIRED] **client_id** - client ID (e.g. application) that requests for access token
- [REQUIRED] **redirect_uri** - URL address whereto redirect in case of success or error
- [OPTIONAL] **scope** -  specify the scope of access request


##### Authorization code response:
In any case (error or success) Resource owner redirects back to the client using
`redirect_uri` with authorization code as a query parameter:
```
//redirect_uri/?code=AnlSCIWYbchsCc5sdc5ac4caca8a2
```
Or
```
//redirect_uri/?error=unauthorized_client&error_description=Client+is+not+found
```

Since you have authorization code you can make access token request (data provided
as `application/x-www-form-urlencoded`).

##### Request for access token:

```
POST //oauth.presenter.url/token
	grant_type=authorization_code
	&code=AUTHORIZATION_CODE
	&client_id=CLIENT_ID
	&client_secret=CLIENT_SECRET
```

- [REQUIRED] **grant_type** - this parameter says OAuth to use Authorization code
- [REQUIRED] **code** - authorization code which you got from Resource owner
- [REQUIRED] **client_id** - client ID (e.g. application) that requests for access
    token
- [REQUIRED] **client_secret** - client (e.g. application) secret key that requests
    for access token

##### Access token response
```
{
	"access_token": "AnlSCIWYbchsCc5sdc5ac4caca8a2",
	"token_type": "bearer",
	"expires_in": 3600,
	"refresh_token": "DS6SA512ADCVa51adc54VDS51VD5"
}
```

In case or error, provides JSON response:
```
{
	"error": "invalid_request",
	"error_description": "Invalid authorization code"
}
```

2. Implicit
--------
Is used for browser-based (web) or mobile applications, where you can't secure
client secret so yopu can't use it to obtain access token.

##### Request for access token:

```
GET //oauth.presenter.url/authorization?response_type=token&client_id=CLIENT_ID&redirect_uri=REDIRECT_URI&scope=email
```

- [REQUIRED] **response_type** - since you request access token from Resource owner,
    you must tell you want an access token (not authorization code)
- [REQUIRED] **client_id** - client ID (e.g. application) that requests for access token
- [REQUIRED] **redirect_uri** - URL where to redirect in case of success or error
- [OPTIONAL] **scope** - specify the scope of access request

##### Access token response
Redirect to `redirect_uri`
```
//redirect_uri/#access_token=AnlSCIWYbchsCc5sdc5ac4caca8a2&expires_in=3600&token_type=bearer
```

In case or error, redirects to:
```
//redirect_uri/#error=unauthorized_client&error_description=Client+is+not+found
```

3. Password
-----------
Is used for trusted (usually first-party) applications, where you completely trust
client because you generate access token from real user credentials (username,
password)

##### Request for access token:

```
POST //oauth.presenter.url/token
	grant_type=password
	&username=USERNAME
	&password=PASSWORD
	&client_id=CLIENT_ID
```

- [REQUIRED] **grant_type** - Password grant type uses identifier (so unexpectedly)
    `password`
- [REQUIRED] **client_id** - client ID (e.g. application) that requests for access
    token
- [REQUIRED] **username** - real user's username
- [OPTIONAL] **password** - real user's password

##### Access token response
```
{
	"access_token": "AnlSCIWYbchsCc5sdc5ac4caca8a2",
	"token_type": "bearer",
	"expires_in": 3600,
	"refresh_token": "DS6SA512ADCVa51adc54VDS51VD5"
}
```

In case or error:
```
{
	"error": "invalid_request",
	"error_description": "Invalid authorization code"
}
```

4. Client credentials
---------------------
If application needs to get access token for their own account outside the context
of any specific user this is probably the best way.

##### Request for access token:

```
POST //oauth.presenter.url/token
	grant_type=client_credentials
	&client_id=CLIENT_ID
	&client_SECRET=CLIENT_SECRET
```

- [REQUIRED] **grant_type** - Password grant type uses identifier (so unexpectedly)
    `password`
- [REQUIRED] **client_id** - client ID (e.g. application) that requests for access
    token
- [REQUIRED] **client_secret** - client (e.g. application) secret key that requests
    for access token

##### Access token response
```
{
	"access_token": "AnlSCIWYbchsCc5sdc5ac4caca8a2",
	"token_type": "bearer",
	"expires_in": 3600,
	"refresh_token": "DS6SA512ADCVa51adc54VDS51VD5"
}
```

In case or error:
```
{
	"error": "invalid_request",
	"error_description": "Invalid authorization code"
}
```

5. Refresh token
---------------
Is used to restore (actually re-generate) access token without authentication
process. Refresh token is provided with almost every grant type (excluding Implicit).

##### Request for refresh token:

```
POST //oauth.presenter.url/token
	grant_type=refresh_token
	&refresh_token=DS6SA512ADCVa51adc54VDS51VD5
	&client_id=CLIENT_ID
```

- [REQUIRED] **grant_type** - Refresh token identifier
- [REQUIRED] **refresh_token** - refresh token itself, that you got from almost
    any access token
- [REQUIRED] **client_id** - client ID (e.g. application) that requests for access
    token

##### Access token response
```
{
	"access_token": "AnlSCIWYbchsCc5sdc5ac4caca8a2",
	"token_type": "bearer",
	"expires_in": 3600,
	"refresh_token": "DS6SA512ADCVa51adc54VDS51VD5"
}
```

In case or error:
```
{
	"error": "invalid_request",
	"error_description": "Invalid refresh token"
}
```

### Subnotes:

I run this locally on my own Docker instances which was based on phpdocker.io.
So you do not see the whole project. I also have my private tasks for it which
aren't part of the repository.
