<?php
/**
 * @category   Magevneed
 * @package    Magevneed_ReviewGraphQL
 * @author     dlvaishnani@gmail.coom
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Magevneed\ReviewGraphQL\Model\Resolver;

use Magevneed\ReviewGraphQL\Model\Resolver\DataProvider\CustomerReview as customerReviewDataProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magevneed\ReviewGraphQL\Model\Customer\CheckCustomerAccount;

/**
 *  CustomerReviewResolver, used for GraphQL request processing
 */
class CustomerReviewResolver implements ResolverInterface
{
    /**
     * @var CheckCustomerAccount
     */
    private $checkCustomerAccount;

    /**
     * @var CustomerReviewResolver
     */
    private $customerReviewDataProvider;

    /**
     * CustomerReviewResolver constructor.
     * @param customerReviewDataProvider $customerReviewDataProvider
     * @param CheckCustomerAccount $checkCustomerAccount
     */
    public function __construct(
        customerReviewDataProvider $customerReviewDataProvider,
        CheckCustomerAccount $checkCustomerAccount
    ) {
        $this->customerReviewDataProvider = $customerReviewDataProvider;
        $this->checkCustomerAccount = $checkCustomerAccount;
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
        $currentUserId = $context->getUserId();
        $currentUserType = $context->getUserType();

        $this->checkCustomerAccount->execute($currentUserId, $currentUserType);

        $currentUserId = (int)$currentUserId;

        $customerReviewData = $this->getCustomerReviewData($currentUserId);

        return $customerReviewData;
    }

    /**
     * @param int $customerId, int $storeId
     * @return array
     * @throws GraphQlNoSuchEntityException
     */
    private function getCustomerReviewData($customerId)
    {
        try {
            $customerReviewData = $this->customerReviewDataProvider->getData($customerId);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $customerReviewData;
    }
}
