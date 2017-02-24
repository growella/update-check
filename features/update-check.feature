Feature: Test WP-CLI's update-check command.

	Scenario: Update Check generates a report with all headings
		Given a WP install

		When I run `wp update-check run`
		Then STDOUT should contain:
			"""
			Update check for
			"""
		And STDOUT should contain:
			"""
			WordPress Core:
			"""
		And STDOUT should contain:
			"""
			Plugin Updates:
			"""
		And STDOUT should contain:
			"""
			Theme Updates:
			"""

	Scenario: Update Check catches out-of-date plugins
		Given a WP install
		And plugin 'akismet' at version '2.2.5'

		When I run `wp update-check run`
		Then STDOUT should contain:
			"""
			- An update is available for akismet
			"""

	Scenario: Update Check catches out-of-date themes
		Given a WP install
		And theme 'twentyfifteen' at version '1.0'

		When I run `wp update-check run`
		Then STDOUT should contain:
			"""
			- An update is available for twentyfifteen
			"""

	Scenario: Update Check emails report with --email option
		Given a WP install

		When I run `wp update-check run --email=test@example.com`
		Then STDOUT should be:
			"""
			Success: Report has been sent to test@example.com
			"""

	Scenario: Update Check emails report with --email and --quiet options
		Given a WP install

		When I run `wp update-check run --email=test@example.com --quiet`
		Then STDOUT should be empty
