<?php

namespace JiraSlackUnfurl\ServiceProvider;

use chobie\Jira;
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
        $app['jira.username'] = getenv('JIRA_USERNAME');
        $app['jira.password'] = getenv('JIRA_PASSWORD');

        $app[JiraUnfurler::class] = function ($app) {
            $domain = parse_url($app['jira.url'], PHP_URL_HOST);

            return new JiraUnfurler(
                $app[Jira\Api::class],
                $domain,
                $app['logger']
            );
        };

        $app[Jira\Api::class] = function ($app) {
            return new Jira\Api(
                $app['jira.url'],
                new Jira\Api\Authentication\Basic($app['jira.username'], $app['jira.password'])
            );
        };
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app[JiraUnfurler::class]);
    }
}