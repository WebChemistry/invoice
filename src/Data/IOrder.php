<?php declare(strict_types = 1);

namespace Contributte\Invoice\Data;

interface IOrder
{

	public function getNumber(): string;

	public function getTotalPrice(): string;

	public function getTimestamps(): ITimestamps;

	public function getCompany(): ICompany;

	public function getCustomer(): ICustomer;

	public function getPayment(): IPaymentInformation;

	/**
	 * @return IItem[]
	 */
	public function getItems(): array;

	public function addItem(IItem $item): IItem;

}
