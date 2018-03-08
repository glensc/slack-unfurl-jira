<?php

namespace SlackUnfurl\Test;

use JiraSlackUnfurl\Event\Subscriber\JiraUnfurler;
use Psr\Log\NullLogger;
use SlackUnfurl\Event\UnfurlEvent;

class JiraTest extends TestCase
{
    public function test1()
    {
        $unfurler = new JiraUnfurler('jira.example.net', new NullLogger());
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
        $unfurl = $unfurler->unfurl($event);
        dump($unfurl);
    }
}
