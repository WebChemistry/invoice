# Contributte / Invoice

## Content

- [Benchmark](#benchmark)
- [Setup](#setup)
- [Preview with minimal setup](#preview-with-minimal-setup)
- [Entities](#entities)
- [Generating invoices](#generating-invoices)
- [Neon configuration](#neon-configuration)
- [Templates](#templates)

## Benchmark

Average output is ~5ms

## Setup

```php
$invoice = new Contributte\Invoice\Invoice();

$dataProvider = new Contributte\Invoice\Provider\DataProvider(
    new Company('Contributte', 'Prague', 'U haldy', '110 00', 'Czech Republic', 'CZ08304431', '08304431'),
    [new Account('CZ4808000000002353462013')],
);
```

## Preview with minimal setup

```php
use Contributte\Invoice\Preview\PreviewFactory;

$invoice->send(PreviewFactory::createOrder());
```

## Entities

We have following entities: Company (seller), Customer, Account (bank account), Payment Info, Currency, Timestamps, Order and Item.

### Company - seller

```php
use Contributte\Invoice\Data\Company;

$company = new Company('Contributte', 'Prague', 'U haldy', '110 00', 'Czech Republic', 'CZ08304431', '08304431');
```

### Customer

```php
use Contributte\Invoice\Data\Customer;

$customer = new Customer('John Doe', 'Los Angeles', 'Cavetown', '720 55', 'USA', 'CZ08304431', '08304431');
```

### Account - bank account

```php
use Contributte\Invoice\Data\Account;

$account = new Account('CZ4808000000002353462013');
```

### Payment info

```php
use Contributte\Invoice\Data\Account;
use Contributte\Invoice\Data\PaymentInformation;

$payment = new PaymentInformation(
    [new Account('CZ4808000000002353462013')],
);
```

### Order

```php
use Contributte\Invoice\Data\Account;
use Contributte\Invoice\Data\Company;
use Contributte\Invoice\Data\Customer;
use Contributte\Invoice\Data\Order;
use Contributte\Invoice\Data\PaymentInformation;
use Contributte\Invoice\Data\Timestamps;

$order = new Order(
    date('Y') . '0001',
    '$ 15.000,00',
    new Company('Contributte', 'Prague', 'U haldy', '110 00', 'Czech Republic', 'CZ08304431', '08304431'),
    new Customer('John Doe', 'Los Angeles', 'Cavetown', '720 55', 'USA', 'CZ08304431', '08304431'),
    new PaymentInformation(
        [new Account('CZ4808000000002353462013')],
    ),
    new Timestamps(
        (new DateTime())->format('Y-m-d'),
        (new DateTime('+ 1 week'))->format('Y-m-d'),
    ),
);
```

### Item

```php
use Contributte\Invoice\Data\Item;

$order->addInlineItem('Logitech G700s Rechargeable Gaming Mouse', '$ 1.790,00', 4, '$ 7.160,00');

// or

$order->addItem(new Item('Logitech G700s Rechargeable Gaming Mouse', '$ 1.790,00', 4, '$ 7.160,00'));
```

## Generating invoices

```php
header('Content-Type: application/pdf; charset=utf-8');
echo $invoice->create($order);
```

method `Invoice::send()` automatically sets content-type header

```php
$invoice->send($order);
```

if you use nette, recommended way is

```php
class CustomPresenter {

	public function actionPreview() {
		$this->sendResponse($this->invoice->createResponse($order));
	}

}
```

## Neon configuration

```neon
extensions:
	invoice: Contributte\Invoice\DI\InvoiceExtension

invoice:
	company:
		name: string
		town: string
		address: string
		zip: string
		country: string
		## Optional below
		vatNumber: string
		id: string
	accounts:
		-
			iban: string
```

## Templates

## Paraiso
Single page:
![single page](/.docs/img/paraiso.png?raw=true)

Multiple pages:
![multiple pages](/.docs/img/paraiso-paginator.png?raw=true)

Greyscale:
![greyscale](/.docs/img/paraiso-greyscale.png?raw=true)

## Turoiso
Single page:
![single page](/.docs/img/turoiso.png?raw=true)

Greyscale:
![greyscale](/.docs/img/turoiso-greyscale.png?raw=true)
