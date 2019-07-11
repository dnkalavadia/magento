<?php
/**
 * @category   Magevneed
 * @package    Magevneed_ReviewGraphQL
 * @author     dlvaishnani@gmail.coom
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Magevneed\ReviewGraphQL\Model\Review;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class CreateReview
{
    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    private $reviewFactory;

    /**
     * @var \Magento\Review\Model\RatingFactory
     */
    private $ratingFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * CreateReview constructor.
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->ratingFactory = $ratingFactory;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
    }

    public function execute(array $args, $customerId)
    {
        $productSku = $args['input']['sku'];
        $product = $this->productRepository->get($productSku);
        $productId = $product->getId();//product id you set accordingly
        $reviewFinalData['ratings'][1] = 5;
        $reviewFinalData['ratings'][2] = 5;
        $reviewFinalData['ratings'][3] = 5;
        $reviewFinalData['ratings'][4] = 20;
        $reviewFinalData['ratings'][5] = 5;
        $reviewFinalData['ratings'][6] = 5;

        $reviewFinalData['title'] = $args['input']['title'];
        $reviewFinalData['detail'] = $args['input']['detail'];
        $reviewFinalData['nickname'] = $args['input']['nickname'];
        $reviewFinalData['customer_id'] = $customerId;
        $review = $this->reviewFactory->create()->setData($reviewFinalData);
        $review->unsetData('review_id');
        $review->setEntityId($review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE))
            ->setEntityPkValue($productId)
            ->setStatusId(\Magento\Review\Model\Review::STATUS_APPROVED)//By default set approved
            ->setStoreId($this->storeManager->getStore()->getId())
            ->setStores([$this->storeManager->getStore()->getId()])
            ->save();

        foreach ($reviewFinalData['ratings'] as $ratingId => $optionId) {
            $this->ratingFactory->create()
                ->setRatingId($ratingId)
                ->setReviewId($review->getId())
                ->addOptionVote($optionId, $productId);
        }
        $review->aggregate();
        $data = [];
        $data['nickname'] = $args['input']['nickname'];
        $data['title'] = $args['input']['title'];
        $data['detail'] = $args['input']['detail'];
        $data['customer_id'] = $customerId;
        $data['review_id'] = $review->getId();
        $data['created_at'] = $review->getCreatedAt();
        $data['sku'] = $productSku;
        $data['product_name'] = $product->getName();
        return $data;
    }
}
