<?php declare(strict_types = 1);

namespace Contributte\Invoice\Data;

use Contributte\Invoice\Calculators\ICalculator;
use Nette\SmartObject;

class Item
{

	use SmartObject;

	/** @var string */
	protected $name;

	/** @var int|float */
	protected $count;

	/** @var int|float */
	protected $price;

	/** @var float|null */
	private $tax;

	/** @var int|float|string|null */
	private $totalPrice;

	/**
	 * @param int|float $price
	 * @param int|float $count
	 */
	public function __construct(string $name, $price, $count, ?float $tax = null)
	{
		$this->name = $name;
		$this->count = $count;
		$this->price = $price;
		$this->tax = $tax;
	}

	/////////////////////////////////////////////////////////////////

	/**
	 * @param float|int|string $totalPrice
	 * @return static
	 */
	public function setTotalPrice($totalPrice)
	{
		$this->totalPrice = $totalPrice;

		return $this;
	}

	public function getTax(): ?float
	{
		return $this->tax;
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return int|float
	 */
	public function getCount()
	{
		return $this->count;
	}

	/**
	 * @return int|float
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * @return float|int|string
	 */
	public function getTotalPrice(ICalculator $calculator, bool $useTax = false)
	{
		if ($this->totalPrice !== null) {
			return $calculator->add($this->totalPrice, 0);
		}

		if (!$useTax) {
			return $calculator->mul($this->price, $this->count);
		} else {
			$total = $calculator->mul($this->price, $this->count);

			assert($this->tax !== null);
			$tax = $calculator->add($this->tax, 1.0);

			return $calculator->mul($total, $tax);
		}
	}

}
