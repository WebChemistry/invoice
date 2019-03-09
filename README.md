[![Build Status](https://travis-ci.org/contributte/invoice.svg?branch=master)](https://travis-ci.org/contributte/invoice)

# PHP Invoice

Average output ~20ms

## Installation

php 7.1

```
composer require contributte/invoice
```

## Usage

### Company

```php
$company = new Contributte\Invoice\Data\Company('John Doe', 'Los Angeles', 'Cavetown', '720 55', 'USA', '0123456789', 'CZ0123456789');
```

### Customer

```php
$customer = new Contributte\Invoice\Data\Customer('John Doe', 'Los Angeles', 'Cavetown', '720 55', 'USA');
```

### Account

```php
$account = new Contributte\Invoice\Data\Account('1111', 'CZ4808000000002353462015', 'GIGACZPX');
```

### Payment info

```php
$payment = new Contributte\Invoice\Data\PaymentInformation('Kč', '0123456789', '1234', 0.21);
```

### Order

```php
$order = new Contributte\Invoice\Data\Order('20160001', new \DateTime('+ 14 days'), $account, $payment);
```

Adding items

```php
$order->addItem('Logitech G700s Rechargeable Gaming Mouse', 4, 1790);
```

### Customizing

```php
class CustomFormatter implements IFormatter {
	
}
```

## Generating invoices

```php
$invoice = new Contributte\Invoice\Invoice($company);

header('Content-Type: application/pdf; charset=utf-8');
echo $invoice->create($customer, $order);
```

shorter
```php
$invoice = new Contributte\Invoice\Invoice($company);

$invoice->send($customer, $order);
```

nette framework way
```php
$invoice = new Contributte\Invoice\Invoice($company);

$this->sendResponse($invoice->createResponse($customer, $order));
```

## Generating preview

```php
$invoice->send(Contributte\Invoice\Preview\PreviewFactory::createCustomer(), Contributte\Invoice\Preview\PreviewFactory::createOrder());
```

```php

```

## Nette DI

```yaml
extensions:
	invoice: Contributte\Invoice\DI\InvoiceExtension

invoice:
	lang: en
	company:
		name:
		town:
		address:
		zip:
		country:
		## Optional
		tin:
		vaTin:
		isTax:
```

```php

class Component {

	public function __construct(Contributte\Invoice\Invoice $invoice) {
		// ...
	}
}

```

## Previews

First page:
![first page](http://i.imgbox.com/pwFByZ1L.jpg)

Second page:
![second page](http://i.imgbox.com/ebrwXldf.jpg)
