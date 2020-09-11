<?php

namespace Born\ShoppingCartExport\Test\Unit\Controller\Cart;

class IndexTest extends \PHPUnit\Framework\TestCase
{
    protected $controller;
    protected $contextMock;
    protected $checkoutSessionMock;
    protected $fileFactoryMock;
    protected $directoryListMock;
    protected $csvProcessorMock;
    protected $loggerMock;
    protected $item;
    protected $response;

    protected function setUp()
    {
        $this->contextMock = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->checkoutSessionMock = $this->getMockBuilder(\Magento\Checkout\Model\Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->fileFactoryMock = $this->getMockBuilder(\Magento\Framework\App\Response\Http\FileFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->directoryListMock = $this->getMockBuilder(\Magento\Framework\App\Filesystem\DirectoryList::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->csvProcessorMock = $this->getMockBuilder(\Magento\Framework\File\Csv::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->loggerMock = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $quote = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)->disableOriginalConstructor()->getMock();

        $this->directoryListMock->method('getPath')->willReturn("file_path");
        $this->csvProcessorMock->method('setDelimiter')->withAnyParameters()->willReturnSelf();
        $this->csvProcessorMock->method('setEnclosure')->withAnyParameters()->willReturnSelf();
        //$this->csvProcessorMock->method('saveData')->withAnyParameters()->willReturnSelf();
        $this->csvProcessorMock->method('appendData')->withAnyParameters()->willReturnSelf();
        $this->response = $this->createMock(\Magento\Framework\App\ResponseInterface::class);

        $this->fileFactoryMock->method('create')->withAnyParameters()->willReturn($this->response);

        $this->item = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $this->item->method('getId')->willReturn(1);
        $this->item->method('getName')->willReturn('Item Name');
        $this->item->method('getSku')->willReturn('Product Sku');
        $this->item->method('getQty')->willReturn(2);
        $this->item->method('getPrice')->willReturn(10.5);

        $quote->method('getItems')->willReturn([$this->item, $this->item]);
        $this->checkoutSessionMock->method('getQuote')->willReturn($quote);

        $this->controller = new \Born\ShoppingCartExport\Controller\Index\Index(
            $this->contextMock,
            $this->checkoutSessionMock,
            $this->fileFactoryMock,
            $this->directoryListMock,
            $this->csvProcessorMock,
            $this->loggerMock
        );
    }

    public function testExecute()
    {
        $result = $this->controller->execute();

        $this->assertInstanceOf(\Magento\Framework\App\ResponseInterface::class, $result);
    }
}
