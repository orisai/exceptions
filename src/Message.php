<?php declare(strict_types = 1);

namespace Orisai\Exceptions;

use Orisai\Exceptions\Logic\InvalidState;
use Stringable;
use function count;
use function explode;
use function mb_strlen;
use function str_repeat;
use function str_replace;
use function strpos;
use function wordwrap;
use const PHP_EOL;

final class Message implements Stringable
{

	/** @phstan-var positive-int */
	public static int $lineLength = 80;

	/** @var array<string, string> */
	private array $fields = [];

	public static function create(): self
	{
		return new self();
	}

	public function withContext(string $context): self
	{
		return $this->with('Context', $context);
	}

	public function withProblem(string $problem): self
	{
		return $this->with('Problem', $problem);
	}

	public function withSolution(string $solution): self
	{
		return $this->with('Solution', $solution);
	}

	public function with(string $title, string $content): self
	{
		$this->fields[$title] = $content;

		return $this;
	}

	private function addPart(string $title, ?string $content, ?string $message): ?string
	{
		if ($message === null && $content === null) {
			return null;
		}

		if ($content === null) {
			return $message;
		}

		if ($message === null) {
			$message = $this->formatPart($title, $content);
		} else {
			$message .= PHP_EOL;
			$message .= $this->formatPart($title, $content);
		}

		return $message;
	}

	private function formatPart(string $title, string $content): string
	{
		$titleLength = mb_strlen($title);

		if (strpos($content, PHP_EOL) === false) {
			$content = wordwrap($content, self::$lineLength - $titleLength);
			if (PHP_EOL !== "\n") {
				$content = str_replace("\n", PHP_EOL, $content);
			}
		}

		$formatted = '';
		$i = 0;
		$lines = explode(PHP_EOL, $content);
		foreach ($lines as $line) {
			$formatted .= $i === 0
				? $title
				: str_repeat(' ', $titleLength);
			$formatted .= $line;

			$i++;

			if (count($lines) !== $i) {
				$formatted .= PHP_EOL;
			}
		}

		return $formatted;
	}

	public function toString(): string
	{
		return (string) $this;
	}

	/**
	 * @internal
	 */
	public function __toString(): string
	{
		$message = null;
		$fields = $this->fields;

		foreach (['Context', 'Problem', 'Solution'] as $title) {
			if (isset($fields[$title])) {
				$message = $this->addPart("$title: ", $fields[$title], $message);
				unset($fields[$title]);
			}
		}

		foreach ($fields as $title => $content) {
			$message = $this->addPart("$title: ", $content, $message);
		}

		if ($message !== null) {
			return $message;
		}

		throw InvalidState::create()
			->withMessage('Error message must specify at least one of context, problem or solution.');
	}

}
