<?php declare(strict_types = 1);

namespace Contributte\Invoice\Templates;

use Contributte\Invoice\Calculators\ICalculator;
use Contributte\Invoice\Data\Company;
use Contributte\Invoice\Data\Customer;
use Contributte\Invoice\Data\Order;
use Contributte\Invoice\Formatter;
use Contributte\Invoice\ITranslator;
use Contributte\Invoice\Renderers\IRenderer;

interface ITemplate
{

	public function __construct(ITranslator $translator, Formatter $formatter);

	public function build(ICalculator $calculator, IRenderer $renderer, Customer $customer, Order $order, Company $company): string;

}
