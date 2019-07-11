<?php
/**
 * @category   Magevneed
 * @package    Magevneed_ReviewGraphQL
 * @author     dlvaishnani@gmail.coom
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Magevneed\ReviewGraphQL\Model\Resolver\DataProvider;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

/**
 * Product Review data provider
 */
class AllReview
{
    /**
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
     */
    private $reviewCollectionFactory;

    /**
     * AllReview constructor.
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory
     */
    public function __construct(
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory
    ) {
        $this->reviewCollectionFactory = $reviewCollectionFactory;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getData()
    {
        try {
                $collection = $this->reviewCollectionFactory->create()
                    ->addStatusFilter(
                        \Magento\Review\Model\Review::STATUS_APPROVED
                    )->setDateOrder();
                $reviewData = [
                    'review_count' => $collection->getSize(),
                    'reviews' => $collection->getItems()
                ];
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $reviewData;
    }
}
