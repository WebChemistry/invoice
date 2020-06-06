<?php declare(strict_types = 1);

namespace Contributte\Invoice\DI;

use Contributte\Invoice\Data\Company;
use Contributte\Invoice\Formatter;
use Contributte\Invoice\IFormatter;
use Contributte\Invoice\Invoice;
use Contributte\Invoice\ITranslator;
use Contributte\Invoice\Renderers\IRenderer;
use Contributte\Invoice\Renderers\PDFRenderer;
use Contributte\Invoice\Templates\DefaultTemplate;
use Contributte\Invoice\Templates\ITemplate;
use Contributte\Invoice\Translator;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class InvoiceExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'company' => Expect::structure([
				'name' => Expect::string()->required(),
				'town' => Expect::string()->required(),
				'address' => Expect::string()->required(),
				'zip' => Expect::string()->required(),
				'country' => Expect::string()->required(),
				'tin' => Expect::string(),
				'vaTin' => Expect::string(),
				'hasTax' => Expect::bool(false),
			]),
			'lang' => Expect::string('en'),
			'template' => Expect::string(DefaultTemplate::class),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = (array) $this->getConfig();

		$builder->addDefinition($this->prefix('company'))
			->setFactory(Company::class, array_values((array) $config['company']));

		$builder->addDefinition($this->prefix('template'))
			->setFactory($config['template'])
			->setType(ITemplate::class);

		$builder->addDefinition($this->prefix('renderer'))
			->setFactory(PDFRenderer::class)
			->setType(IRenderer::class);

		$builder->addDefinition($this->prefix('translation'))
			->setType(ITranslator::class)
			->setFactory(Translator::class, [$config['lang']]);

		$builder->addDefinition($this->prefix('formatter'))
			->setType(IFormatter::class)
			->setFactory(Formatter::class, [$config['lang']]);

		$builder->addDefinition($this->prefix('invoice'))
			->setFactory(Invoice::class);
	}

}
