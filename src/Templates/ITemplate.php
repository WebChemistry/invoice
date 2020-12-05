<?php

declare(strict_types=1);

namespace WebChemistry\Invoice\Templates;

use WebChemistry\Invoice\Data\Company;
use WebChemistry\Invoice\Data\Customer;
use WebChemistry\Invoice\Data\Order;
use WebChemistry\Invoice\IFormatter;
use WebChemistry\Invoice\ITranslator;
use WebChemistry\Invoice\Renderers\IRenderer;

interface ITemplate {

	public function __construct(ITranslator $translator, IFormatter $formatter);

	public function build(IRenderer $renderer, Customer $customer, Order $order, Company $company): string;

}
