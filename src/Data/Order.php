<?php declare(strict_types = 1);

namespace Contributte\Invoice\Data;

use Contributte\Invoice\Calculators\ICalculator;
use DateTime;
use Nette\SmartObject;

class Order
{

	use SmartObject;

	/** @var string */
	private $number;

	/** @var DateTime|null */
	private $dueDate;

	/** @var Account|null */
	private $account;

	/** @var PaymentInformation */
	private $payment;

	/** @var DateTime */
	private $created;

	/** @var Item[] */
	private $items = [];

	/** @var string|float|int|null */
	private $totalPrice;

	public function __construct(string $number, ?DateTime $dueDate, ?Account $account, PaymentInformation $payment, ?DateTime $created = null)
	{
		$this->number = $number;
		$this->dueDate = $dueDate;
		$this->account = $account;
		$this->payment = $payment;
		$this->created = $created ?: new DateTime();
	}

	/**
	 * @param int|float $price
	 * @param int|float $count
	 */
	public function addItem(string $name, $price, $count = 1, ?float $tax = null): Item
	{
		return $this->items[] = new Item($name, $price, $count, $tax ?: $this->getPayment()->getTax());
	}

	/**
	 * @param float|int|string|null $totalPrice
	 * @return static
	 */
	public function setTotalPrice($totalPrice)
	{
		$this->totalPrice = $totalPrice;

		return $this;
	}

	public function getNumber(): string
	{
		return $this->number;
	}

	public function getDueDate(): ?DateTime
	{
		return $this->dueDate;
	}

	public function getAccount(): ?Account
	{
		return $this->account;
	}

	public function getPayment(): PaymentInformation
	{
		return $this->payment;
	}

	public function getCreated(): DateTime
	{
		return $this->created;
	}

	/**
	 * @return Item[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}

	/**
	 * @return float|int|string
	 */
	public function getTotalPrice(ICalculator $calculator, bool $useTax = false)
	{
		if ($this->totalPrice !== null) {
			return $this->totalPrice;
		}

		$total = 0;
		foreach ($this->getItems() as $item) {
			$total = $calculator->add($total, $item->getTotalPrice($calculator, $useTax));
		}

		return $total;
	}

}
