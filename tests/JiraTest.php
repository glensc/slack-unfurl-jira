<?php

namespace JiraSlackUnfurl\Test;

use chobie\Jira;
use JiraSlackUnfurl\Event\Subscriber\JiraUnfurler;
use Psr\Log\NullLogger;
use SlackUnfurl\Event\UnfurlEvent;

class JiraTest extends TestCase
{
    public function test1()
    {
        $api = new Jira\Api(
            getenv('JIRA_URL'),
            new Jira\Api\Authentication\Basic(getenv('JIRA_USERNAME'), getenv('JIRA_PASSWORD'))
        );
        $unfurler = new JiraUnfurler($api, 'jira.example.net', new NullLogger());
        $data = [
            'type' => 'link_shared',
            'user' => 'Uxxxxxxxx',
            'channel' => 'Dxxxxxxxx',
            'message_ts' => '1518125979.000251',
            'links' => [
                [
                    'url' => 'https://jira.example.net/browse/UNFURL-42',
                    'domain' => 'jira.example.net',
                ],
            ],
        ];
        $event = new UnfurlEvent($data);
        $unfurler->unfurl($event);
        dump($event->getUnfurls());
    }
}
