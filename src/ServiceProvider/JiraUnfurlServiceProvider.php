<?php

namespace JiraSlackUnfurl\ServiceProvider;

use JiraSlackUnfurl\Event\Subscriber\JiraUnfurler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class JiraUnfurlServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['jira.url'] = getenv('JIRA_URL');

        $app[JiraUnfurler::class] = function ($app) {
            $domain = parse_url($app['jira.url'], PHP_URL_HOST);

            return new JiraUnfurler(
                $domain,
                $app['logger']
            );
        };
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app[JiraUnfurler::class]);
    }
}