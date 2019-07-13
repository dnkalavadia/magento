<?php
/**
 * @category   Magevneed
 * @package    Magevneed_ReviewGraphQL
 * @author     magevneed@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Magevneed\ReviewGraphQL\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Product data provider
 *
 */
class ProductDataProvider
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get product data by id
     *
     * @param int $productId
     * @return array
     */
    public function getProductDataById(int $productId): array
    {
        $product = $this->productRepository->getById($productId);
        $productData = $product->toArray();
        $productData['model'] = $product;
        return $productData;
    }
}
