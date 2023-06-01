## Demo WP Plugin

A demo implementation of a plugin that adds a Gutenberg block which can generate content using OpenAI.

### Configuration

The plugin requires an option ( `sdn-demo-openai-key` ) that contains a valid OpenAI API key.



### Testing

The tests are based on the `wp scaffold plugin-tests` scaffolding, so we need to setup the test environment before
running the tests.

The `composer setup-test-env` command is available to setup the test environment, make sure to first update the database
credentials before running the command.
