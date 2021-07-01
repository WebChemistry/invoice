<?php declare(strict_types = 1);

namespace Contributte\Invoice\Templates\Translator;

final class TuroisoTemplateTranslator extends Translator
{

	public function __construct(string $lang = 'en')
	{
		$this->addLanguage('en', [
			'Invoice' => 'Invoice',
			'Payment' => 'Payment',
			'Total price' => 'Total price',
			'Customer' => 'Customer',
			'Item' => 'Item',
			'ID' => 'ID',
			'Price per item' => 'Price per item',
			'Price' => 'Price',
			'Quantity' => 'Quantity',
			'VAT Number' => 'VAT Number',
			'Invoice number' => 'Invoice number',
			'Date' => 'Date',
			'Due to' => 'Due to',
			'Account number' => 'Account number',
			'IBAN' => 'IBAN',
			'Variable symbol' => 'Variable symbol',
			'Constant symbol' => 'Constant symbol',
			'Specific symbol' => 'Specific symbol',
		]);

		parent::__construct($lang);
	}

}
