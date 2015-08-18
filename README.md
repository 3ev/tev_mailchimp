#TYPO3 Mailchimp Extension

[![Latest Stable Version](https://poser.pugx.org/3ev/tev_mailchimp/version)](https://packagist.org/packages/3ev/tev_mailchimp) [![License](https://poser.pugx.org/3ev/tev_mailchimp/license)](https://packagist.org/packages/3ev/tev_mailchimp)

> Integrate Mailchimp newsletter subscriptions with your TYPO3 site.

##Contents

* [Overview](#overview)
* [Installation](#installation)
* [Setup and Configuration](#setup-and-configuration)
    * [Webhooks](#webhooks)
    * [Changing the Email Field](#changing-the-email-field)
    * [Changing the Logfile Path](#changing-the-logfile-path)
* [Usage](#usage)
    * [Downloading Lists](#downloading-lists)
    * [Logging](#logging)
* [License](#license)

##Overview

This extension allows you to subscribe FE Users to Mailchimp Lists. Information
is kept in sync using the [Mailchimp REST API](http://kb.mailchimp.com/api/) and
[webhooks](https://apidocs.mailchimp.com/webhooks/).

The following functionality is included:

* A CLI command to download all Mailchimp lists from your account to the local
database
* BE save hooks to ensure that whenever a user is created or updated in the backend
their List subscription preferences are downloaded from Mailchimp
* Extbase slots to ensure that whever a user is created or updated on the frontend
their List subscription preferences are downloaded from Mailchimp
* Webhook listeners to ensure that whenever a user's List subscription preferences
are updated in Mailchimp they are updated in the local database
* Services to easily trigger Mailchimp subscribe or unsubscribe requests anywhere
in your code

##Installation

```
$ composer require "3ev/tev_mailchimp"
```

##Setup and Configuration

Install the extension via the Extension Manager as normal. After the extension
is installed, you should access its settings and set your [Mailchimp API key](http://kb.mailchimp.com/accounts/management/about-api-keys).

Using the Constants Editor, you should then set the 'TEV_MAILCHIMP -> Storage Folder'
constant, most likely on your root page template. This is the folder that all
Mailchimp lists and subscriptions are stored in.

Finally, clear your TYPO3 caches and your extension will be ready to use.

###Webhooks

If you want your site to listen to [incoming webhooks from Mailchimp](https://apidocs.mailchimp.com/webhooks/)
(which will help to keep your local list data in sync), you can setup a page
and add its URL to your Mailchimp config.

To setup a page that can listen to webhooks:

* Create a page somewhere in your page tree. This page should not be linked to
anywhere on your site
* Add an Extension Template to it, with the following Typoscript in its setup field:

    ```
    page < tev_mailchimp_webhook_json
    ```
* Add the new page's URL to the webhook config for each Mailchimp list you want
to listen to

Then just clear your TYPO3 caches, and you'll be ready to go.

###Changing the Email Field

By default, the extension uses the `email` field on FE Users to sync email
preferences. You can change this by setting the 'FE User email field' in the
extension configuration. This should be the lower cased, underscored field name
as it is in the database. You may want to do this if, for example, your users
use an email address as their `username`, and you don't use the `email field.

###Changing the Logfile Path

By default, extension logs will be written to `typo3temp/logs/mailchimp.log`. If
you'd like to change this, simply set the 'Logfile path' in the extension
configuration. This path should either be absolute or relative to the directory
that contains your `typo3/` source folder. You can set it to be outside of that
directory if you use a `../` prefix.

You can of course completely change the logging functionality using the
[core TYPO3 API](https://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/Logging/Configuration/Index.html)
and setting your own logging config in an `ext_localconf.php` under the
`Tev\TevMailchimp` namespace.

##Usage

###Downloading lists

Firstly, you should download all Lists from your Mailchimp account to TYPO3. You
can do this by running the following Extbase CLI command in your TYPO3 installation
directory (you'll need to create the `cli_lowlevel` BE User if you haven't already):

```
$ ./cli_dispatch.phpsh extbase mailchimp:lists
```

Once this has been run, you'll be able to see all of the downloaded lists using
the List View on the Storage Folder you configured earlier. You can optionally
set a custom description on each List, and you'll also be able to see the full
list of FE Users subscribed to the list under the Subscribers tab.

**Note:** If you're changing lists regularly in your Mailchimp account, you should
put this command on a cronjob or TYPO3 Scheduler task.

###Using the MailchimpService

You can easily trigger subscriptions or unsubscriptions for users in your Extbase
code by injecting the `Tev\TevMailchimp\Services\MailchimpService` into your own
classes.

The service provides the following methods for managing subscriptions:

**subscribeUserToList(FrontendUser $user, Mlist $list)**

Subscribe the given FE User to the given Mailchimp list.

**unsubscribeUserFromList(FrontendUser $user, Mlist $list)**

Unsubscribe the given FE User from the given Mailchimp list.

**subscribeToList($email, $list)**

Subscribe the given email address to the given Mailchimp list.

**unsubscribeFromList($email, $list)**

Unsubscribe the given email address from the given Mailchimp list.

Each of these methods will trigger an exception if there is a Mailchimp API error,
so you should ensure you handle them appropriately.

**Note:** None of these methods will update the local database. You'll need to
configure [webhooks](#webhooks) to ensure the local database is updated once the user confirms
their subscription change.

###Using the MlistRepository

The `Tev\TevMailchimp\Domain\Repository\MlistRepository` class allows you to fetch
list data from the local database. It provides the standard Extbase repository
API. Some of the most common methods you'll likely want to use are:

**findAll()**

Get all lists from the local database.

TODO (more methods)

###Logging

The following events are logged in the system:

* Successful and failed webhook handlers
* Successful and failed list downloads via the CLI
* Any Mailchimp API errors

See [above](#changing-the-logfile-path) for information on changing the logging
setup.

##License

MIT © 3ev
