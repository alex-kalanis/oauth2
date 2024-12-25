<?php

namespace kalanis\OAuth2\DI;


use kalanis\OAuth2\Storage\Clients\IClientStorage;
use kalanis\OAuth2\Storage\ITokenStorage;
use Nette\Bootstrap\Configurator;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\Definition;


/**
 * OAuth2 compiler extension
 * @package kalanis\OAuth2\DI
 */
class Extension extends CompilerExtension
{

    /**
     * @var array<string, array<string, class-string<ITokenStorage|IClientStorage>>>
     */
    protected array $storages = [
        'ndb' => [
            'accessTokenStorage' => \kalanis\OAuth2\Storage\NDB\AccessTokenStorage::class,
            'authorizationCodeStorage' => \kalanis\OAuth2\Storage\NDB\AuthorizationCodeStorage::class,
            'clientStorage' => \kalanis\OAuth2\Storage\NDB\ClientStorage::class,
            'refreshTokenStorage' => \kalanis\OAuth2\Storage\NDB\RefreshTokenStorage::class,
        ],
        'dibi' => [
            'accessTokenStorage' => \kalanis\OAuth2\Storage\Dibi\AccessTokenStorage::class,
            'authorizationCodeStorage' => \kalanis\OAuth2\Storage\Dibi\AuthorizationCodeStorage::class,
            'clientStorage' => \kalanis\OAuth2\Storage\Dibi\ClientStorage::class,
            'refreshTokenStorage' => \kalanis\OAuth2\Storage\Dibi\RefreshTokenStorage::class,
        ],
    ];

    /**
     * Default DI settings
     * @var array<string, string|int|null>
     */
    protected array $defaults = [
        'accessTokenStorage' => null,
        'authorizationCodeStorage' => null,
        'clientStorage' => null,
        'refreshTokenStorage' => null,
        'accessTokenLifetime' => 3600, // 1 hour
        'refreshTokenLifetime' => 36000, // 10 hours
        'authorizationCodeLifetime' => 360, // 6 minutes
        'storage' => null,
    ];

    /**
     * Register OAuth2 extension
     */
    public static function install(Configurator $configurator): void
    {
        $configurator->onCompile[] = function ($configurator, $compiler): void {
            $compiler->addExtension('oauth2', new Extension);
        };
    }

    /**
     * Load DI configuration
     */
    public function loadConfiguration(): void
    {
        $container = $this->getContainerBuilder();
        $passedConf = $this->getConfig();
        $passedConf = is_object($passedConf) ? $this->propertiesFromConfig($passedConf) : (array) $passedConf;
        $config = array_merge($this->defaults, $passedConf);

        // Library common
        $container->addDefinition($this->prefix('keyGenerator'))
            ->setType(\kalanis\OAuth2\KeyGenerator::class);

        $container->addDefinition($this->prefix('input'))
            ->setType(\kalanis\OAuth2\Http\Input::class);

        // Grant types
        $container->addDefinition($this->prefix('authorizationCodeGrant'))
            ->setType(\kalanis\OAuth2\Grant\AuthorizationCode::class);
        $container->addDefinition($this->prefix('refreshTokenGrant'))
            ->setType(\kalanis\OAuth2\Grant\RefreshToken::class);
        $container->addDefinition($this->prefix('passwordGrant'))
            ->setType(\kalanis\OAuth2\Grant\Password::class);
        $container->addDefinition($this->prefix('implicitGrant'))
            ->setType(\kalanis\OAuth2\Grant\Implicit::class);
        $container->addDefinition($this->prefix('clientCredentialsGrant'))
            ->setType(\kalanis\OAuth2\Grant\ClientCredentials::class);

        $container->addDefinition($this->prefix('grantContext'))
            ->setType(\kalanis\OAuth2\Grant\GrantContext::class)
            ->addSetup('$service->addGrantType(?)', [$this->prefix('@authorizationCodeGrant')])
            ->addSetup('$service->addGrantType(?)', [$this->prefix('@refreshTokenGrant')])
            ->addSetup('$service->addGrantType(?)', [$this->prefix('@passwordGrant')])
            ->addSetup('$service->addGrantType(?)', [$this->prefix('@implicitGrant')])
            ->addSetup('$service->addGrantType(?)', [$this->prefix('@clientCredentialsGrant')]);

        // Tokens
        $container->addDefinition($this->prefix('accessToken'))
            ->setType(\kalanis\OAuth2\Storage\AccessTokens\AccessTokenFacade::class)
            ->setArguments([$config['accessTokenLifetime']]);
        $container->addDefinition($this->prefix('refreshToken'))
            ->setType(\kalanis\OAuth2\Storage\RefreshTokens\RefreshTokenFacade::class)
            ->setArguments([$config['refreshTokenLifetime']]);
        $container->addDefinition($this->prefix('authorizationCode'))
            ->setType(\kalanis\OAuth2\Storage\AuthorizationCodes\AuthorizationCodeFacade::class)
            ->setArguments([$config['authorizationCodeLifetime']]);

        $container->addDefinition('tokenContext')
            ->setType(\kalanis\OAuth2\Storage\TokenContext::class)
            ->addSetup('$service->addToken(?)', [$this->prefix('@accessToken')])
            ->addSetup('$service->addToken(?)', [$this->prefix('@refreshToken')])
            ->addSetup('$service->addToken(?)', [$this->prefix('@authorizationCode')]);

        // Default fallback value
        $storageIndex = 'ndb';

        // Nette database Storage
        if ('DIBI' == strtoupper(strval($config['storage'])) || (is_null($config['storage']) && $this->getByType($container, 'DibiConnection'))) {
            $storageIndex = 'dibi';
        }

        // Nette database Storage
        $container->addDefinition($this->prefix('accessTokenStorage'))
            ->setType($config['accessTokenStorage'] ?: $this->storages[$storageIndex]['accessTokenStorage']);
        $container->addDefinition($this->prefix('refreshTokenStorage'))
            ->setType($config['refreshTokenStorage'] ?: $this->storages[$storageIndex]['refreshTokenStorage']);
        $container->addDefinition($this->prefix('authorizationCodeStorage'))
            ->setType($config['authorizationCodeStorage'] ?: $this->storages[$storageIndex]['authorizationCodeStorage']);
        $container->addDefinition($this->prefix('clientStorage'))
            ->setType($config['clientStorage'] ?: $this->storages[$storageIndex]['clientStorage']);
    }

    /**
     * @param object $config
     * @return array<string, mixed>
     */
    private function propertiesFromConfig(object $config): array
    {
        $result = [];
        $obj = new \ReflectionObject($config);
        foreach ($obj->getProperties() as $property) {
            if ($property->isPublic()) {
                $result[$property->getName()] = $property->getValue();
            }
        }
        return $result;
    }

    /**
     * @param string $type
     * @return Definition|null
     */
    private function getByType(ContainerBuilder $container, string $type): ?Definition
    {
        $definitions = $container->getDefinitions();
        foreach ($definitions as $definition) {
            if (isset($definition->class) && $definition->class === $type) {
                return $definition;
            }
        }
        return null;
    }
}
