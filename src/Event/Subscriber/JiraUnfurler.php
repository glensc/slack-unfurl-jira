<?php

namespace JiraSlackUnfurl\Event\Subscriber;

use chobie\Jira;
use Psr\Log\LoggerInterface;
use SlackUnfurl\Event\Events;
use SlackUnfurl\Event\UnfurlEvent;
use SlackUnfurl\Traits\LoggerTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JiraUnfurler implements EventSubscriberInterface
{
    use LoggerTrait;

    /** @var Jira\Api */
    private $api;
    /** @var string */
    private $domain;

    public function __construct(
        Jira\Api $api,
        string $domain,
        LoggerInterface $logger
    ) {
        $this->api = $api;
        $this->domain = $domain;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::SLACK_UNFURL => ['unfurl', 10],
        ];
    }

    public function unfurl(UnfurlEvent $event): void
    {
        foreach ($event->getMatchingLinks($this->domain) as $link) {
            $unfurl = $this->getIssueUnfurl($link['url']);
            if ($unfurl) {
                $event->addUnfurl($link['url'], $unfurl);
            }
        }
    }

    private function getIssueUnfurl(string $url): ?array
    {
        $issue = $this->getIssueDetails($url);
        $this->debug('jira', ['issue' => $issue]);

        if (!$issue) {
            return null;
        }

        return [
            'title' => "<$url|{$issue['key']}>: {$issue['fields']['summary']}",
        ];
    }

    private function getIssueDetails(string $url): ?array
    {
        if (!preg_match("#^https?://\Q{$this->domain}\E/browse/(?P<projectKey>[\d\w]+)-(?P<issueId>\d+)#", $url, $m)) {
            return null;
        }

        $issueKey = "{$m['projectKey']}-{$m['issueId']}";

        return $this->api->getIssue($issueKey)->getResult();
    }
}