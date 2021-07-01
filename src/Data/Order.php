<?php declare(strict_types = 1);

namespace Contributte\Invoice\Data;

class Order implements IOrder
{

	/** @var IItem[] */
	private array $items = [];

	public function __construct(
		private string $number,
		private string $totalPrice,
		private ICompany $company,
		private ICustomer $customer,
		private IPaymentInformation $payment,
		private ITimestamps $timestamps,
	)
	{
	}

	public function addItem(IItem $item): IItem
	{
		return $this->items[] = $item;
	}

	public function addItemInline(string $name, string $unitPrice, int $quantity, string $totalPrice): Item
	{
		return $this->items[] = new Item($name, $unitPrice, $quantity, $totalPrice);
	}

	public function getTotalPrice(): string
	{
		return $this->totalPrice;
	}

	public function getNumber(): string
	{
		return $this->number;
	}

	public function getTimestamps(): ITimestamps
	{
		return $this->timestamps;
	}

	public function getCompany(): ICompany
	{
		return $this->company;
	}

	public function getCustomer(): ICustomer
	{
		return $this->customer;
	}

	public function getPayment(): IPaymentInformation
	{
		return $this->payment;
	}

	/**
	 * @return IItem[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}

}
