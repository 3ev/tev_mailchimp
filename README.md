#TYPO3 Mailchimp Extension

[![Latest Stable Version](https://poser.pugx.org/3ev/tev_mailchimp/version)](https://packagist.org/packages/3ev/tev_mailchimp) [![License](https://poser.pugx.org/3ev/tev_mailchimp/license)](https://packagist.org/packages/3ev/tev_mailchimp)

> Integrate Mailchimp newsletter subscriptions with your TYPO3 site.

## Contents

* [Overview](#overview)
* [Installation](#installation)
* [Configuration](#configuration)
    * [Webhooks](#webhooks)
* [Usage](#usage)
* [License](#license)

##Overview

This extension allows you to subscribe FE Users to Mailchimp newsletters. Information
is kept in sync using the [Mailchimp REST API](http://kb.mailchimp.com/api/) and
[webhooks](https://apidocs.mailchimp.com/webhooks/).

The following functionality is included:

* A CLI command to download all Mailchimp lists from your account to the local
database
* BE save hooks to ensure that whenever a user is created or updated in the backend
their subscription preferences are downloaded from Mailchimp
* Extbase slots to ensure that whever a user is created or updated on the frontend
their subscription preferences are downloaded from Mailchimp
* Webhook listeners to ensure that whenever a user's subscription preferences are
updated in Mailchimp they are updated in the local database
* Services to easily trigger Mailchimp subscribe or unsubscribe requests anywhere
in your code

##Installation

```
$ composer require "3ev/tev_mailchimp"
```

##Configuration

TODO

###Webhooks

TODO

##Usage

TODO

##License

MIT © 3ev
