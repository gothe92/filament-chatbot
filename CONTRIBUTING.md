# Contributing to Filament Chatbot

We love your input! We want to make contributing to Filament Chatbot as easy and transparent as possible, whether it's:

- Reporting a bug
- Discussing the current state of the code
- Submitting a fix
- Proposing new features
- Becoming a maintainer

## Development Process

We use GitHub to host code, to track issues and feature requests, as well as accept pull requests.

## Pull Requests

Pull requests are the best way to propose changes to the codebase. We actively welcome your pull requests:

1. Fork the repo and create your branch from `main`.
2. If you've added code that should be tested, add tests.
3. If you've changed APIs, update the documentation.
4. Ensure the test suite passes.
5. Make sure your code lints.
6. Issue that pull request!

## Any Contributions You Make Will Be Under the MIT Software License

In short, when you submit code changes, your submissions are understood to be under the same [MIT License](LICENSE) that covers the project. Feel free to contact the maintainers if that's a concern.

## Report Bugs Using GitHub's Issues

We use GitHub issues to track public bugs. Report a bug by opening a new issue; it's that easy!

**Great Bug Reports** tend to have:

- A quick summary and/or background
- Steps to reproduce
  - Be specific!
  - Give sample code if you can
- What you expected would happen
- What actually happens
- Notes (possibly including why you think this might be happening, or stuff you tried that didn't work)

## Development Setup

1. Fork and clone the repository
2. Install dependencies: `composer install`
3. Run tests to make sure everything is working: `composer test`

## Testing

We use Pest for testing. Please write tests for any new functionality:

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run specific test
vendor/bin/pest tests/Feature/ChatbotTest.php
```

## Code Style

We use Laravel Pint for code styling. The style will be automatically fixed by GitHub Actions, but you can also run it locally:

```bash
# Fix code style
composer format

# Check code style
vendor/bin/pint --test
```

## Commit Messages

Please use clear and meaningful commit messages. We prefer the following format:

```
type: short description

Optional longer description
```

Types:
- `feat`: A new feature
- `fix`: A bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

Examples:
- `feat: add support for custom message templates`
- `fix: resolve memory leak in document processing`
- `docs: update installation instructions`

## Feature Requests

We welcome feature requests! Please open an issue with:

- A clear and descriptive title
- A detailed description of the proposed feature
- Why you think this feature would be useful
- Any example code or mockups if applicable

## Code of Conduct

This project follows the [Contributor Covenant Code of Conduct](https://www.contributor-covenant.org/version/2/1/code_of_conduct/). By participating, you are expected to uphold this code.

## Questions?

Feel free to open an issue with the "question" label, or contact the maintainer directly at andras@szentivanyi.dev.

## License

By contributing, you agree that your contributions will be licensed under the MIT License.