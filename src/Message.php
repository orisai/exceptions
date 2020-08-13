<?php declare(strict_types = 1);

namespace Orisai\Exceptions;

use Orisai\Exceptions\Logic\InvalidState;
use function count;
use function explode;
use function mb_strlen;
use function str_repeat;
use function strpos;
use function wordwrap;

final class Message
{

	private const LINE_LENGTH = 80;

	public ?string $context = null;
	public ?string $problem = null;
	public ?string $solution = null;

	public static function create(): self
	{
		return new self();
	}

	public function withContext(string $context): self
	{
		$this->context = $context;

		return $this;
	}

	public function withProblem(string $problem): self
	{
		$this->problem = $problem;

		return $this;
	}

	public function withSolution(string $solution): self
	{
		$this->solution = $solution;

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
			$message .= "\n";
			$message .= $this->formatPart($title, $content);
		}

		return $message;
	}

	private function formatPart(string $title, string $content): string
	{
		$titleLength = mb_strlen($title);

		if (strpos($content, "\n") === false) {
			$content = wordwrap($content, self::LINE_LENGTH - $titleLength);
		}

		$formatted = '';
		$i = 0;
		$lines = explode("\n", $content);
		foreach ($lines as $line) {
			$formatted .= $i === 0
				? $title
				: str_repeat(' ', $titleLength);
			$formatted .= $line;

			$i++;

			if (count($lines) !== $i) {
				$formatted .= "\n";
			}
		}

		return $formatted;
	}

	public function __toString(): string
	{
		$message = $this->addPart('Context: ', $this->context, null);
		$message = $this->addPart('Problem: ', $this->problem, $message);
		$message = $this->addPart('Solution: ', $this->solution, $message);

		if ($message !== null) {
			return $message;
		}

		throw InvalidState::create()
			->withMessage('Error message must specify at least one context, problem or solution.');
	}

}
