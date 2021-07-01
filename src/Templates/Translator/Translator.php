<?php declare(strict_types = 1);

namespace Contributte\Invoice\Templates\Translator;

use InvalidArgumentException;

abstract class Translator implements ITranslator
{

	/** @var mixed[] */
	protected array $translations = [];

	public function __construct(
		private string $lang = 'en',
	)
	{
		if (!isset($this->translations[$this->lang])) {
			throw new InvalidArgumentException(sprintf('Translation language %s not exists', $this->lang));
		}
	}

	/**
	 * @param mixed[] $translations
	 */
	public function addLanguage(string $lang, array $translations): void
	{
		$this->translations[$lang] = $translations;
	}

	public function translate(string $message): string
	{
		if (!isset($this->translations[$this->lang][$message])) {
			throw new \Exception($message);
		}
		return $this->translations[$this->lang][$message] ?? $message;
	}

}
