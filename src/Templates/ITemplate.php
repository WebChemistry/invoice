<?php declare(strict_types = 1);

namespace Contributte\Invoice\Templates;

use Contributte\Invoice\Data\IOrder;

interface ITemplate
{

	public function renderToPdf(IOrder $order): string;

	public function renderToSvg(IOrder $order): string;

}
