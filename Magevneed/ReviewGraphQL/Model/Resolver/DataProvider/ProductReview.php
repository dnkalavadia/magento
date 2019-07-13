<?php
/**
 * @category   Magevneed
 * @package    Magevneed_ReviewGraphQL
 * @author     magevneed@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Magevneed\ReviewGraphQL\Model\Resolver\DataProvider;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

/**
 * Product Review data provider
 */
class ProductReview
{
    /**
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
     */
    private $reviewCollectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Review\Model\Rating
     */
    private $ratingFactory;

    /**
     * ProductReview constructor.
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Review\Model\Rating $ratingFactory
     */
    public function __construct(
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory,
        ProductRepositoryInterface $productRepository,
        \Magento\Review\Model\Rating $ratingFactory
    ) {
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->productRepository = $productRepository;
        $this->ratingFactory = $ratingFactory;
    }

    /**
     * @param string $productSku
     * @return array
     * @throws NoSuchEntityException
     */
    public function getData($productSku, $storeId)
    {
        try {
            $product = $this->productRepository->get($productSku);
            $collection = $this->reviewCollectionFactory->create()
                ->addStatusFilter(
                    \Magento\Review\Model\Review::STATUS_APPROVED
                )->addEntityFilter(
                    'product',
                    $product->getId()
                )->addStoreFilter($storeId)
                ->setDateOrder();
            if ($collection->getSize()) {
                $ratingSummary = $this->ratingFactory->getEntitySummary($product->getId());
                $product_rating = $ratingSummary->getSum() / $ratingSummary->getCount();
                $reviewcount = $collection->getSize();
                $reviews = $collection->getItems();
                $reviewData = [
                    'product_name'=> $product->getName(),
                    'sku' => $productSku,
                    'rating'=> $product_rating,
                    'review_count' => $reviewcount,
                    'reviews' => $reviews
                ];
            } else {
                $message = __(
                    'No review found for %1 product.',
                    $productSku
                );
                throw new GraphQlNoSuchEntityException($message);
            }
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $reviewData;
    }
}
