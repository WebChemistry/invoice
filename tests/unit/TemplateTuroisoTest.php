<?php declare(strict_types = 1);

use Codeception\PHPUnit\TestCase;
use Contributte\Invoice\Preview\PreviewFactory;
use Contributte\Invoice\Templates\TuroisoTemplate;

final class TemplateTuroisoTest extends TestCase
{

	public function testRenders(): void
	{
		(new TuroisoTemplate())->renderToPdf(PreviewFactory::createOrder());
		(new TuroisoTemplate())->renderToSvg(PreviewFactory::createOrder());
	}

}
