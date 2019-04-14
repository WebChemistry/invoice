<?php declare(strict_types = 1);

namespace Contributte\Invoice;

use DateTime;
use Nette\SmartObject;

class Formatter implements IFormatter
{

	use SmartObject;

	public const ENGLISH = 'en';

	public const CZECH = 'cs';

	public const SLOVENIAN = 'si';

	/** @var mixed[] */
	private static $options = [
		'cs' => [
			'number' => [
				'dec' => ',',
				'sep' => ' ',
			],
			'money' => '%money %currency',
			'date' => 'd.m.Y',
		],
		'en' => [
			'number' => [
				'dec' => null,
				'sep' => null,
			],
			'money' => '%currency %money',
			'date' => 'd/m/Y',
		],
        'si' => [
            'number' => [
                'dec' => ',',
                'sep' => ' ',
            ],
            'money' => '%money %currency',
            'date' => 'd.m.Y',
        ],
	];

	/** @var string */
	private $lang;

	public function __construct(string $lang = self::ENGLISH)
	{
		$this->lang = $lang;
	}

	/**
	 * @param mixed $number
	 */
	public function formatNumber($number): string
	{
		return number_format((float) $number, 2, self::$options[$this->lang]['number']['dec'], self::$options[$this->lang]['number']['sep']);
	}

	/**
	 * @param mixed $number
	 */
	public function formatMoney($number, string $currency): string
	{
		return strtr(self::$options[$this->lang]['money'], ['%money' => $this->formatNumber($number), '%currency' => $currency]);
	}

	public function formatDate(DateTime $date): string
	{
		return $date->format(self::$options[$this->lang]['date']);
	}

}
