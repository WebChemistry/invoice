<?php declare(strict_types = 1);

namespace Contributte\Invoice\Templates\Template;

use Contributte\Invoice\Data\IOrder;
use Contributte\Invoice\Templates\Translator\ITranslator;

abstract class TemplateObject
{

	public function __construct(
		protected IOrder $order,
		protected ITranslator $translator,
	)
	{
	}

	public function getOrder(): IOrder
	{
		return $this->order;
	}

	public function getTranslator(): ITranslator
	{
		return $this->translator;
	}

	public function translate(string $message): string
	{
		return $this->translator->translate($message);
	}

	protected function prepend(string $prepend, ?string $str): ?string
	{
		return $str === null ? null : $prepend . $str;
	}

	/**
	 * @param mixed[] $items
	 */
	protected function implode(array $items, string $separator): ?string
	{
		$items = array_filter($items);
		if (!$items) {
			return null;
		}

		return implode($separator, $items);
	}

}
