<?php declare(strict_types = 1);

namespace Contributte\Invoice\Templates\Translator;

final class ParaisoTemplateTranslator extends Translator
{

	public function __construct(string $lang = 'en')
	{
		$this->addLanguage('en', [
			'Invoice' => 'INVOICE',
			'Discount' => 'Discount',
			'Payment Info' => 'Payment Info',
			'Total price' => 'Total price',
			'Invoice from' => 'Invoice from',
			'Invoice to' => 'Invoice to',
			'ID' => 'ID',
			'Total' => 'Total',
			'Item description' => 'Item description',
			'Unit price' => 'Unit price',
			'Quantity' => 'Quantity',
			'VAT Number' => 'VAT Number',
			'Invoice No.' => 'Invoice No.',
			'Invoice date' => 'Invoice date',
			'Invoice due to' => 'Invoice due to',
			'Account number' => 'Account number',
			'IBAN' => 'IBAN',
			'Variable symbol' => 'Variable symbol',
			'Constant symbol' => 'Constant symbol',
			'Specific symbol' => 'Specific symbol',
		]);

		parent::__construct($lang);
	}

}
