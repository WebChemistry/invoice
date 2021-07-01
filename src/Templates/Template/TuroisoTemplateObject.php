<?php declare(strict_types = 1);

namespace Contributte\Invoice\Templates\Template;

use Contributte\Invoice\Data\Extension\IAccountNumber;
use Contributte\Invoice\Data\Extension\IConstantSymbol;
use Contributte\Invoice\Data\Extension\ISpecificSymbol;
use Contributte\Invoice\Data\Extension\IVariableSymbol;
use Contributte\Invoice\Data\IOrder;
use Contributte\Invoice\Templates\Translator\ITranslator;
use WebChemistry\SvgPdf\Pdf\Color;

class TuroisoTemplateObject extends TemplateObject
{

	/** @var Color[] */
	public array $colors;

	/** @var string[] */
	public array $leftHeaderLeft;

	/** @var string[] */
	public array $leftHeaderRight;

	/** @var string[] */
	public array $rightHeader;

	/** @var string[] */
	public array $leftSubheader;

	/** @var string[] */
	public array $rightSubheader;

	public function __construct(IOrder $order, ITranslator $translator)
	{
		parent::__construct($order, $translator);

		$company = $order->getCompany();
		$customer = $order->getCustomer();
		$payment = $order->getPayment();
		$account = $payment->getFirstAccount();

		$this->colors = [
			'primary' => new Color(6, 178, 194),
			'secondary' => new Color(52, 52, 53),
			'even' => new Color(241, 240, 240),
			'text' => Color::black(),
			'white' => Color::white(),
			'odd' => Color::white(),
		];

		$this->leftHeaderLeft = array_filter([
			$this->implode([$company->getZip(), $company->getTown()], ' '),
			$company->getAddress(),
			$company->getCountry(),
		]);

		$this->leftHeaderRight = array_filter([
			$this->prepend($translator->translate('ID') . ': ', $company->getId()),
			$this->prepend($translator->translate('VAT Number') . ': ', $company->getVatNumber()),
		]);

		$this->rightHeader = [
			$translator->translate('Date') . ': ' . $order->getTimestamps()->getCreated(),
			$translator->translate('Invoice number') . ': ' . $order->getNumber(),
		];

		$this->leftSubheader = array_filter([
			$customer->getName(),
			$this->implode([$company->getZip(), $company->getTown()], ', '),
			$customer->getAddress(),
			$customer->getCountry(),
			$this->prepend($translator->translate('ID') . ': ', $customer->getId()),
			$this->prepend($translator->translate('VAT Number') . ': ', $customer->getVatNumber()),
		]);

		$this->rightSubheader = array_filter([
			$this->prepend($translator->translate('Due to') . ': ', $order->getTimestamps()->getDueTo()),
			$account instanceof IAccountNumber ?
				$this->prepend($translator->translate('Account number') . ': ', $account->getAccountNumber()) : null,
			$this->prepend($translator->translate('IBAN') . ': ', $account?->getIban()),
			$payment instanceof IVariableSymbol ?
				$this->prepend($translator->translate('Variable symbol') . ': ', $payment->getVariableSymbol()) : null,
			$payment instanceof IConstantSymbol ?
				$this->prepend($translator->translate('Constant symbol') . ': ', $payment->getConstantSymbol()) : null,
			$payment instanceof ISpecificSymbol ?
				$this->prepend($translator->translate('Specific symbol') . ': ', $payment->getSpecificSymbol()) : null,
		]);
	}

}
