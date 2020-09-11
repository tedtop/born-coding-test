<?php

class Terminal
{
	public $total; // making this public since spec pseudo-code accesses this directly, prefer Terminal::getTotal()
	protected $pricing;
	private $products;
	
	public function __construct(array $pricing = null)
	{
		$this->pricing = $pricing;
	}

	public function clearCart()
	{
		$this->products = null;
		$this->total = 0;
	}

    public function getTotal()
    {
        return number_format($this->total, 2);
    }
    
    public function scan($productCodes)
    {
		if (strlen($productCodes) > 1) {
			$this->scanMultiple($productCodes);
		} else {
			$this->scanSingle($productCodes);
		}
		return $this;
	}
	
	public function scanSingle($productCode)
	{
		if (!isset($this->pricing[$productCode])) {
			throw new Exception("Product code doesn't exist in pricing catalog");
		}
		$this->products[$productCode]++; // increment qty
		$this->updateTotal();
		return $this;
	}

	public function scanMultiple($productCodes)
	{
		foreach (str_split($productCodes) as $productCode) {
			$this->scanSingle($productCode);
		}
	}
    
    public function setPricing(array $pricing)
    {
		$this->pricing = $pricing;
	}
	
	private function updateTotal()
	{
		$cartTotal = 0;

		foreach ($this->products as $productCode => $qty) {
			
			$itemTotal = 0;
			
			if (is_array($this->pricing[$productCode])) { // then there's bulk pricing for this item
				
				// calculate bulk pricing
				$bundleSize = max(array_keys($this->pricing[$productCode]));
				$bundles = floor($qty / $bundleSize);
				$itemTotal += $this->pricing[$productCode][$bundleSize] * $bundles;
				
				// add cost for remaining qty at unit pricing
				$qty = $qty % $bundleSize;
				$itemTotal += $this->pricing[$productCode][1] * $qty;
			} else {
				$itemTotal = $this->pricing[$productCode] * $qty;
			}
			$cartTotal += $itemTotal;
			$this->total = $cartTotal;
		}
	}
}

$pricing = [
	'A' => [
		1 => 2,
		4 => 7, // 4 for $7.00
	],
	'B' => 12,
	'C' => [
		1 => 1.25,
		6 => 6, // $6 for a six pack
	],
	'D' => 0.15,
];

$terminal1 = new Terminal();
$terminal1->setPricing($pricing);
$terminal1->scan("A");
$terminal1->scan("B");
$terminal1->scan("C");
$terminal1->scan("D");
$terminal1->scan("A");
$terminal1->scan("B");
$terminal1->scan("A");
$terminal1->scan("A");
echo "Terminal 1 Total: " . $terminal1->getTotal() . "\n";

$terminal2 = new Terminal();
$terminal2->setPricing($pricing);
$terminal2->scan("C");
$terminal2->scan("C");
$terminal2->scan("C");
$terminal2->scan("C");
$terminal2->scan("C");
$terminal2->scan("C");
$terminal2->scan("C");
echo "Terminal 2 Total: " . $terminal2->getTotal() . "\n";

$terminal3 = new Terminal();
$terminal3->setPricing($pricing);
$terminal3->scan("A");
$terminal3->scan("B");
$terminal3->scan("C");
$terminal3->scan("D");
echo "Terminal 3 Total: " . $terminal3->getTotal() . "\n";

$terminal = new Terminal($pricing);
$terminal->scan('A')->scan('A')->scan('A')->scan('A')->scan('A');
	echo "Terminal Total: " . $terminal->getTotal() . "\n";
$terminal->clearCart();
$terminal->scan('AAAAA');
	echo "Terminal Total: " . $terminal->getTotal() . "\n";
$terminal->clearCart();
$terminal->scan('BBB');
	echo "Terminal Total: " . $terminal->getTotal() . "\n";
$terminal->clearCart();
$terminal->scan('G');
	echo "Terminal Total: " . $terminal->getTotal() . "\n";
