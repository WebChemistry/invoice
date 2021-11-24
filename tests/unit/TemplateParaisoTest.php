<?php declare(strict_types = 1);

use Codeception\PHPUnit\TestCase;
use Contributte\Invoice\Preview\PreviewFactory;
use Contributte\Invoice\Templates\ParaisoTemplate;

final class TemplateParaisoTest extends TestCase
{

	public function testRenders(): void
	{
		(new ParaisoTemplate())->renderToPdf(PreviewFactory::createOrder());
		(new ParaisoTemplate())->renderToSvg(PreviewFactory::createOrder());
	}

}
