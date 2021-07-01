<?php declare(strict_types = 1);

namespace Contributte\Invoice\Templates;

use Contributte\Invoice\Data\IOrder;
use Contributte\Invoice\Templates\Template\TemplateObject;
use Contributte\Invoice\Templates\Template\TuroisoTemplateObject;
use Contributte\Invoice\Templates\Translator\ITranslator;
use Contributte\Invoice\Templates\Translator\TuroisoTemplateTranslator;
use WebChemistry\SvgPdf\Pdf\Color;
use WebChemistry\SvgPdf\PdfSvg;

class TuroisoTemplate extends Template
{

	/** @var Color[] */
	public array $colors = [];

	private ITranslator $translator;

	public function __construct(?ITranslator $translator = null)
	{
		$renderer = new PdfSvg();
		$renderer->addFont('Montserrat', __DIR__ . '/assets/font/Montserrat-Regular.php')
			->addFont('Montserrat', __DIR__ . '/assets/font/Montserrat-Bold.php', 'bold');

		$this->translator = $translator ?? new TuroisoTemplateTranslator();

		parent::__construct($renderer);
	}

	protected function getTemplate(): string
	{
		return __DIR__ . '/templates/turoiso.phtml';
	}

	protected function createTemplateObject(IOrder $order): TemplateObject
	{
		return new TuroisoTemplateObject($order, $this->translator);
	}

}
