# Colossal-HTTP
An implementation of the PSR-7 and PSR-17 PHP HTTP standardized interfaces.

## Development Tips

### Running PHPUnit Test Suites

Run the PHPUnit test suites with the following command:

```bash
>> .\vendor\bin\phpunit
```

To additionally print the test coverage results to stdout run the following command:

```bash
>> .\vendor\bin\phpunit --coverage-text
```

### Running PHPStan Code Quality Analysis

Run the PHPStan code quality analysis with the following command:

```bash
>> .\vendor\bin\phpstan --configuration=phpstan.neon --xdebug
```

### Running PHP Code Sniffer Code Style Analysis

Run the PHP Code Sniffer code style analysis with the following commands:

```bash
>> .\vendor\bin\phpcs --standard=PSR12 src
>> .\vendor\bin\phpcs --standard=PSR12 test
```

To fix automatically resolve issues found by PHP Code Sniffer run the following commands:

```bash
>> .\vendor\bin\phpcbf --standard=PSR12 src
>> .\vendor\bin\phpcbf --standard=PSR12 test
```