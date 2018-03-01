<?php

namespace JiraSlackUnfurl\Event\Subscriber;

use Psr\Log\LoggerInterface;
use SlackUnfurl\Event\Events;
use SlackUnfurl\Event\UnfurlEvent;
use SlackUnfurl\LoggerTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JiraUnfurler implements EventSubscriberInterface
{
    use LoggerTrait;

    /** @var string */
    private $domain;

    public function __construct(
        string $domain,
        LoggerInterface $logger
    ) {
        $this->domain = $domain;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::SLACK_UNFURL => ['unfurl', 10],
        ];
    }

    public function unfurl(UnfurlEvent $event)
    {
        foreach ($this->getMatchingLinks($event) as $link) {
            $url = $link['url'];
            $unfurl = [];
            $event->addUnfurl($url, $unfurl);
        }
    }

    private function getMatchingLinks(UnfurlEvent $event)
    {
        foreach ($event->getLinks() as $link) {
            $domain = $link['domain'] ?? null;
            if ($domain !== $this->domain) {
                continue;
            }

            yield $link;
        }
    }
}