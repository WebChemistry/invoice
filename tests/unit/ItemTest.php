<?php

class ItemTest extends \Codeception\Test\Unit
{

	/** @var \Contributte\Invoice\Data\Item */
	private $item;

	/** @var \Contributte\Invoice\Calculators\ICalculator */
	private $calculator;

	protected function _before()
	{
		$this->item = new \Contributte\Invoice\Data\Item('Foo', 15, 2, 0.10);
		$this->calculator = new \Contributte\Invoice\Calculators\BcCalculator(2);
	}

	// tests
	public function testTotalPriceWithoutTax()
	{
		// php bug < 7.3.0, bcmul ignores scale, https://bugs.php.net/bug.php?id=66364
		$expected = PHP_VERSION_ID < 70300 ? '30' : '30.00';

		$this->assertSame($expected, $this->item->getTotalPrice($this->calculator, false));
	}

	public function testTotalPriceSetFixed()
	{
		$this->item->setTotalPrice(40);
		$this->assertSame('40.00', $this->item->getTotalPrice($this->calculator, false));
	}

	public function testTotalPriceWithTax()
	{
		$this->assertSame('33.00', $this->item->getTotalPrice($this->calculator, true));
	}

	public function testTotalPriceSetFixedWithUseTax()
	{
		$this->item->setTotalPrice(40.99);

		$this->assertSame('40.99', $this->item->getTotalPrice($this->calculator, true));
	}

}
