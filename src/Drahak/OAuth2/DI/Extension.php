<?php

namespace Drahak\OAuth2\DI;

use Nette\Bootstrap\Configurator;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\Definition;

/**
 * OAuth2 compiler extension
 * @package Drahak\OAuth2\DI
 * @author Drahomír Hanák
 */
class Extension extends CompilerExtension
{

    /**
     * Default DI settings
     * @var array
     */
    protected $defaults = [
        'accessTokenStorage' => \Drahak\OAuth2\Storage\NDB\AccessTokenStorage::class,
        'authorizationCodeStorage' => \Drahak\OAuth2\Storage\NDB\AuthorizationCodeStorage::class,
        'clientStorage' => \Drahak\OAuth2\Storage\NDB\ClientStorage::class,
        'refreshTokenStorage' => \Drahak\OAuth2\Storage\NDB\RefreshTokenStorage::class,
        'accessTokenLifetime' => 3600,
        // 1 hour
        'refreshTokenLifetime' => 36000,
        // 10 hours
        'authorizationCodeLifetime' => 360,
    ];

    /**
     * Register OAuth2 extension
     */
    public static function install(Configurator $configurator)
    {
        $configurator->onCompile[] = function ($configurator, $compiler): void {
            $compiler->addExtension('oauth2', new Extension);
        };
    }

    /**
     * Load DI configuration
     */
    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig();

        // Library common
        $container->addDefinition($this->prefix('keyGenerator'))
            ->setType(\Drahak\OAuth2\KeyGenerator::class);

        $container->addDefinition($this->prefix('input'))
            ->setType(\Drahak\OAuth2\Http\Input::class);

        // Grant types
        $container->addDefinition($this->prefix('authorizationCodeGrant'))
            ->setType(\Drahak\OAuth2\Grant\AuthorizationCode::class);
        $container->addDefinition($this->prefix('refreshTokenGrant'))
            ->setType(\Drahak\OAuth2\Grant\RefreshToken::class);
        $container->addDefinition($this->prefix('passwordGrant'))
            ->setType(\Drahak\OAuth2\Grant\Password::class);
        $container->addDefinition($this->prefix('implicitGrant'))
            ->setType(\Drahak\OAuth2\Grant\Implicit::class);
        $container->addDefinition($this->prefix('clientCredentialsGrant'))
            ->setType(\Drahak\OAuth2\Grant\ClientCredentials::class);

        $container->addDefinition($this->prefix('grantContext'))
            ->setType(\Drahak\OAuth2\Grant\GrantContext::class)
            ->addSetup('$service->addGrantType(?)', [$this->prefix('@authorizationCodeGrant')])
            ->addSetup('$service->addGrantType(?)', [$this->prefix('@refreshTokenGrant')])
            ->addSetup('$service->addGrantType(?)', [$this->prefix('@passwordGrant')])
            ->addSetup('$service->addGrantType(?)', [$this->prefix('@implicitGrant')])
            ->addSetup('$service->addGrantType(?)', [$this->prefix('@clientCredentialsGrant')]);

        // Tokens
        $container->addDefinition($this->prefix('accessToken'))
            ->setType(\Drahak\OAuth2\Storage\AccessTokens\AccessTokenFacade::class)
            ->setArguments([$config['accessTokenLifetime']]);
        $container->addDefinition($this->prefix('refreshToken'))
            ->setType(\Drahak\OAuth2\Storage\RefreshTokens\RefreshTokenFacade::class)
            ->setArguments([$config['refreshTokenLifetime']]);
        $container->addDefinition($this->prefix('authorizationCode'))
            ->setType(\Drahak\OAuth2\Storage\AuthorizationCodes\AuthorizationCodeFacade::class)
            ->setArguments([$config['authorizationCodeLifetime']]);

        $container->addDefinition('tokenContext')
            ->setType(\Drahak\OAuth2\Storage\TokenContext::class)
            ->addSetup('$service->addToken(?)', [$this->prefix('@accessToken')])
            ->addSetup('$service->addToken(?)', [$this->prefix('@refreshToken')])
            ->addSetup('$service->addToken(?)', [$this->prefix('@authorizationCode')]);

        // Nette database Storage
        if ($this->getByType($container, \Nette\Database\Explorer::class)) {
            $container->addDefinition($this->prefix('accessTokenStorage'))
                ->setType($config['accessTokenStorage']);
            $container->addDefinition($this->prefix('refreshTokenStorage'))
                ->setType($config['refreshTokenStorage']);
            $container->addDefinition($this->prefix('authorizationCodeStorage'))
                ->setType($config['authorizationCodeStorage']);
            $container->addDefinition($this->prefix('clientStorage'))
                ->setType($config['clientStorage']);
        }
    }

    /**
     * @param string $type
     * @return Definition|null
     */
    private function getByType(ContainerBuilder $container, string $type): ?Definition
    {
        $definitions = $container->getDefinitions();
        foreach ($definitions as $definition) {
            if ($definition->class === $type) {
                return $definition;
            }
        }
        return NULL;
    }

}