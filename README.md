# WP-CLI: Update Check

WP-CLI command to automatically check WordPress core and all installed themes and plugins for available updates.

[![Build Status](https://travis-ci.org/growella/update-check.svg?branch=develop)](https://travis-ci.org/growella/update-check)

Quick links: [Using](#using) | [Installing](#installing) | [Contributing](#contributing)

## Using

Update Check will check WordPress core, plugins, and themes for available updates, then generate a report. This can be displayed within the CLI or, with the optional `--email` argument, sent via email to the site administrator.


### Default usage

If you'd like to be able to see what needs updating on your WordPress site at a glance, you can run the following on the command line:

```bash
$ wp update-check run
```


### Emailed reports

If you'd prefer to generate an email to be sent, use the `--email` option:

```bash
# Send the report to johndoe@example.com.
$ wp update-check run --email=johndoe@example.com

# Send the report to the admin_email for the WP site.
$ wp update-check run --email
```

Note that a report will *not* be emailed if everything's up-to-date unless the `--report-current` flag is also passed.

```bash
# Email John Doe, even if everything is up-to-date.
$ wp update-check run --email=johndoe@example.com --report-current
```


#### Daily email reports

WP-CLI: Update Check is designed to work well with system cron jobs:

```bash
# Check for available updates and send them to the engineering team.
0 8 * * * wp update-check run --email=engineering@example.com --path=/path/to/my/site --quiet
```


## Installing

Installing this package requires WP-CLI v1.1.0 or greater. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install this package with `wp package install growella/update-check`.


## Contributing

We appreciate you taking the initiative to contribute to this project.

Contributing isn’t limited to just code. We encourage you to contribute in the way that best fits your abilities, by writing tutorials, giving a demo at your local meetup, helping other users with their support questions, or revising our documentation.


### Reporting a bug

Think you’ve found a bug? We’d love for you to help us get it fixed.

Before you create a new issue, you should [search existing issues](https://github.com/growella/update-check/issues?q=label%3Abug%20) to see if there’s an existing resolution to it, or if it’s already been fixed in a newer version.

Once you’ve done a bit of searching and discovered there isn’t an open or fixed issue for your bug, please [create a new issue](https://github.com/growella/update-check/issues/new) with the following:

1. What you were doing (e.g. "When I run `wp post list`").
2. What you saw (e.g. "I see a fatal about a class being undefined.").
3. What you expected to see (e.g. "I expected to see the list of posts.")

Include as much detail as you can, and clear steps to reproduce if possible.


### Creating a pull request

Want to contribute a new feature? Please first [open a new issue](https://github.com/growella/update-check/issues/new) to discuss whether the feature is a good fit for the project.

Once you've decided to commit the time to seeing your pull request through, please follow our guidelines for creating a pull request to make sure it's a pleasant experience:

1. Create a feature branch for each contribution.
2. Submit your pull request early for feedback.
3. Include functional tests with your changes. [Read the WP-CLI documentation](https://wp-cli.org/docs/pull-requests/#functional-tests) for an introduction.
4. Follow the [WordPress Coding Standards](http://make.wordpress.org/core/handbook/coding-standards/).
