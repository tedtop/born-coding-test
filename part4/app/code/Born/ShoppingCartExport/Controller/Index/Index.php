<?php

namespace Born\ShoppingCartExport\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $checkoutSession;
    protected $fileFactory;
    protected $directoryList;
    protected $csvProcessor;
    protected $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\File\Csv $csvProcessor,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->fileFactory = $fileFactory;
        $this->directoryList = $directoryList;
        $this->csvProcessor = $csvProcessor;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Get items from checkout quote and return an array for csv export
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCartItems(): array // of \Magento\Quote\Api\Data\CartItemInterface
    {
        $items = $this->checkoutSession->getQuote()->getItems();

        $result[] = [
            'id',
            'name',
            'sku',
            'qty',
            'price'
        ];

        foreach ($items as $item) {
            if ($item instanceof \Magento\Quote\Api\Data\CartItemInterface) {
                $result[] = [
                    $item->getItemId(),
                    $item->getName(),
                    $item->getSku(),
                    $item->getQty(),
                    $item->getPrice()
                ];
            }
        }
        return $result;
    }

    /**
     * Generate csv export file of shopping cart items
     *
     * @param array $cartItems
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    protected function generateCsvOutput(array $cartItems)
    {
        $fileName = 'cart_items.csv';
        $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR) . "/" . $fileName;

        $this->csvProcessor
            ->setDelimiter(',')
            ->setEnclosure('"')
            ->appendData(
                $filePath,
                $cartItems
            );

        return $this->fileFactory->create(
            $fileName,
            [
                'type' => "filename",
                'value' => $fileName,
                'rm' => true,
            ],
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
            'application/octet-stream'
        );
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try {
            $cartItems = $this->getCartItems();
            $result = $this->generateCsvOutput($cartItems);
        } catch (\Exception $e) {
            echo $e;
            $this->logger->critical($e);
            //$this->getResponse()->setHttpResponseCode(404);
        }
        return $result;
    }
}
