<?php
/**
 * @category   Magevneed
 * @package    Magevneed_ReviewGraphQL
 * @author     magevneed@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Magevneed\ReviewGraphQL\Model\Resolver;

use Magevneed\ReviewGraphQL\Model\Resolver\DataProvider\AllReview as allReviewDataProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 *  AllReviewResolver, used for GraphQL request processing
 */
class AllReviewResolver implements ResolverInterface
{
    /**
     * @var allReviewDataProvider
     */
    private $allReviewDataProvider;

    /**
     * AllReviewResolver constructor.
     * @param allReviewDataProvider $allReviewDataProvider
     */
    public function __construct(
        allReviewDataProvider $allReviewDataProvider
    ) {
        $this->allReviewDataProvider = $allReviewDataProvider;
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
        $allrReviewData = $this->getAllReviewData();
        return $allrReviewData;
    }

    /**
     * @return array
     * @throws GraphQlNoSuchEntityException
     */
    private function getAllReviewData()
    {
        try {
            $allReviewData = $this->allReviewDataProvider->getData();
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $allReviewData;
    }
}
