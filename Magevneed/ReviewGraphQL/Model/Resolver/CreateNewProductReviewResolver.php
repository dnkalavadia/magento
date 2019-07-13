<?php
/**
 * @category   Magevneed
 * @package    Magevneed_ReviewGraphQL
 * @author     magevneed@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Magevneed\ReviewGraphQL\Model\Resolver;

use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Validator\Exception as ValidatorException;
use Magevneed\ReviewGraphQL\Model\Customer\CheckCustomerAccount;
use Magevneed\ReviewGraphQL\Model\Review\CreateReview;

class CreateNewProductReviewResolver implements ResolverInterface
{
    /**
     * @var CheckCustomerAccount
     */
    private $checkCustomerAccount;

    /**
     * @var CreateReview
     */
    private $createReview;

    /**
     * CreateNewProductReviewResolver constructor.
     * @param CheckCustomerAccount $checkCustomerAccount
     * @param CreateReview $createReview
     */
    public function __construct(
        CheckCustomerAccount $checkCustomerAccount,
        CreateReview $createReview
    ) {
        $this->checkCustomerAccount = $checkCustomerAccount;
        $this->createReview = $createReview;
    }
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($args['input']) || !is_array($args['input']) || empty($args['input'])) {
            throw new GraphQlInputException(__('"input" value should be specified'));
        }
        if (!isset($args['input']['sku'])) {
            throw new GraphQlInputException(__('"sku" value should be specified'));
        }

        if (!isset($args['input']['title'])) {
            throw new GraphQlInputException(__('"title" value should be specified'));
        }

        if (!isset($args['input']['detail'])) {
            throw new GraphQlInputException(__('"detail" value should be specified'));
        }
        if (!isset($args['input']['nickname'])) {
            throw new GraphQlInputException(__('"nickname" value should be specified'));
        }

        try {
            $currentUserId = $context->getUserId();
            if (!$currentUserId) {
                $currentUserId = null;
            }
            $review = $this->createReview->execute($args, $currentUserId);
        } catch (ValidatorException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        } catch (InputMismatchException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return $review;
    }
}
