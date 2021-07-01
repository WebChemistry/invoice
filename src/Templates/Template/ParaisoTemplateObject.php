<?php declare(strict_types = 1);

namespace Contributte\Invoice\Templates\Template;

use Contributte\Invoice\Data\Extension\IAccountNumber;
use Contributte\Invoice\Data\Extension\IConstantSymbol;
use Contributte\Invoice\Data\Extension\ISpecificSymbol;
use Contributte\Invoice\Data\Extension\IVariableSymbol;
use Contributte\Invoice\Data\IOrder;
use Contributte\Invoice\Data\ISubject;
use Contributte\Invoice\Templates\Translator\ITranslator;

class ParaisoTemplateObject extends TemplateObject
{

	/** @var string[] */
	protected array $invoiceFrom;

	/** @var string[] */
	protected array $invoiceTo;

	/** @var string[] */
	protected array $invoiceInfo;

	/** @var string[] */
	protected array $paymentInfo;

	protected ?string $footer = null;

	public function __construct(IOrder $order, ITranslator $translator)
	{
		parent::__construct($order, $translator);

		$company = $order->getCompany();

		$this->invoiceFrom = array_filter([
			$translator->translate('Invoice from'),
			$company->getName(),
			$this->toFullAddress($company),
			$this->prepend($translator->translate('ID') . ': ', $company->getId()),
			$this->prepend($translator->translate('VAT Number') . ': ', $company->getVatNumber()),
		]);

		$customer = $order->getCustomer();
		$this->invoiceTo = array_filter([
			$translator->translate('Invoice to'),
			$customer->getName(),
			$this->toFullAddress($customer),
			$this->prepend($translator->translate('ID') . ': ', $customer->getId()),
			$this->prepend($translator->translate('VAT Number') . ': ', $customer->getVatNumber()),
		]);

		$timestamps = $order->getTimestamps();
		$this->invoiceInfo = array_filter([
			sprintf('%s %s', $translator->translate('Invoice No.'), $order->getNumber()),
			sprintf('%s: %s', $translator->translate('Invoice date'), $timestamps->getCreated()),
			$this->prepend($this->translate('Invoice due to') . ': ', $timestamps->getDueTo()),
		]);

		$payment = $order->getPayment();
		$account = $payment->getFirstAccount();
		$this->paymentInfo = array_filter([
			$this->prepend(
				$translator->translate('Account number') . ': ',
				$account instanceof IAccountNumber ? $account->getAccountNumber() : null
			),
			$this->prepend($translator->translate('IBAN') . ': ', $account?->getIban()),
			$this->prepend(
				$translator->translate('Variable symbol') . ': ',
				$payment instanceof IVariableSymbol ? $payment->getVariableSymbol() : null
			),
			$this->prepend(
				$translator->translate('Constant symbol') . ': ',
				$payment instanceof IConstantSymbol ? $payment->getConstantSymbol() : null,
			),
			$this->prepend(
				$translator->translate('Specific symbol') . ': ',
				$payment instanceof ISpecificSymbol ? $payment->getSpecificSymbol() : null,
			)
 		]);
	}

	/**
	 * @return string[]
	 */
	public function getInvoiceFrom(): array
	{
		return $this->invoiceFrom;
	}

	/**
	 * @return string[]
	 */
	public function getInvoiceTo(): array
	{
		return $this->invoiceTo;
	}

	/**
	 * @return string[]
	 */
	public function getInvoiceInfo(): array
	{
		return $this->invoiceInfo;
	}

	/**
	 * @return string[]
	 */
	public function getPaymentInfo(): array
	{
		return $this->paymentInfo;
	}

	public function getFooter(): ?string
	{
		return $this->footer;
	}

	private function toFullAddress(ISubject $subject): string
	{
		return implode(', ', array_filter([
			$subject->getAddress(),
			$subject->getTown(),
			$subject->getZip(),
			$subject->getCountry(),
		]));
	}

}
