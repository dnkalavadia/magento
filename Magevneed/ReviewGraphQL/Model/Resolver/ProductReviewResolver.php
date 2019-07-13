<?php
/**
 * @category   Magevneed
 * @package    Magevneed_ReviewGraphQL
 * @author     magevneed@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Magevneed\ReviewGraphQL\Model\Resolver;

use Magevneed\ReviewGraphQL\Model\Resolver\DataProvider\ProductReview as ProductReviewDataProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 *  ProductReviewResolver, used for GraphQL request processing
 */
class ProductReviewResolver implements ResolverInterface
{
    /**
     * @var ProductReviewDataProvider
     */
    private $productReviewDataProvider;

    /**
     * Review constructor.
     * @param ProductReviewDataProvider $productReviewDataProvider
     */
    public function __construct(
        productReviewDataProvider $productReviewDataProvider
    ) {
        $this->productReviewDataProvider = $productReviewDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $productSku = $this->getProductSku($args);
        $storeId = $this->getStoreId($args);
        $productReviewData = $this->getProductReviewData($productSku, $storeId);

        return $productReviewData;
    }

    /**
     * @param array $args
     * @return int
     * @throws GraphQlInputException
     */
    private function getProductSku(array $args)
    {
        if (!isset($args['product_sku'])) {
            throw new GraphQlInputException(__('"Product sku should be specified'));
        }

        return (string)$args['product_sku'];
    }

    /**
     * @param array $args
     * @return int
     * @throws GraphQlInputException
     */
    private function getStoreId(array $args)
    {
        if (!isset($args['store_id'])) {
            return 0;
        }

        return (int)$args['store_id'];
    }

    /**
     * @param string $productSku, int $storeId
     * @return array
     * @throws GraphQlNoSuchEntityException
     */
    private function getProductReviewData($productSku, $storeId)
    {
        try {
            $productReviewData = $this->productReviewDataProvider->getData($productSku, $storeId);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $productReviewData;
    }
}
