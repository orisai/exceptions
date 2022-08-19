<h1 align="center">
	<img src="https://github.com/orisai/.github/blob/main/images/repo_title.png?raw=true" alt="Orisai"/>
	<br/>
	Exceptions
</h1>

<p align="center">
    Exceptions designed for static analysis and easy usage
</p>

<p align="center">
	📄 Check out our <a href="docs/README.md">documentation</a>.
</p>

<p align="center">
	💸 If you like Orisai, please <a href="https://orisai.dev/sponsor">make a donation</a>. Thank you!
</p>

<p align="center">
	<a href="https://github.com/orisai/exceptions/actions?query=workflow%3Aci">
		<img src="https://github.com/orisai/exceptions/workflows/ci/badge.svg">
	</a>
	<a href="https://coveralls.io/r/orisai/exceptions">
		<img src="https://badgen.net/coveralls/c/github/orisai/exceptions/v1.x?cache=300">
	</a>
	<a href="https://dashboard.stryker-mutator.io/reports/github.com/orisai/exceptions/v1.x">
		<img src="https://badge.stryker-mutator.io/github.com/orisai/exceptions/v1.x">
	</a>
	<a href="https://packagist.org/packages/orisai/exceptions">
		<img src="https://badgen.net/packagist/dt/orisai/exceptions?cache=3600">
	</a>
	<a href="https://packagist.org/packages/orisai/exceptions">
		<img src="https://badgen.net/packagist/v/orisai/exceptions?cache=3600">
	</a>
	<a href="https://choosealicense.com/licenses/mpl-2.0/">
		<img src="https://badgen.net/badge/license/MPL-2.0/blue?cache=3600">
	</a>
<p>

##

```php
use Orisai\Exceptions\Logic\InvalidState;
use Orisai\Exceptions\Message;

$message = Message::create()
    ->withContext('Trying to commit an import.')
    ->withProblem('There is nothing to commit.')
    ->withSolution('Check that the import files are not empty, and that filters are not too restrictive.');

throw InvalidState::create()
    ->withMessage($message);
```

... and [more](docs/README.md).
