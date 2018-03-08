# Slack unfurl JIRA Provider

JIRA links unfurler for [slack-unfurl].

[slack-unfurl]: https://github.com/glensc/slack-unfurl

## Installation

1. Install [slack-unfurl]
2. Require this package: `composer require glen/slack-unfurl-jira`
3. Merge `env.example` from this project to `.env`
4. Register provider: in `src/Application.php` add `$this->register(new \JiraSlackUnfurl\ServiceProvider\JiraUnfurlServiceProvider());`

[slack-unfurl]: https://github.com/glensc/slack-unfurl
