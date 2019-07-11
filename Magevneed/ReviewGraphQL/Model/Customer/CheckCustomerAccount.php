<?php
/**
 * @category   Magevneed
 * @package    Magevneed_ReviewGraphQL
 * @author     dlvaishnani@gmail.coom
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Magevneed\ReviewGraphQL\Model\Customer;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthenticationException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

/**
 * Check customer account
 */
class CheckCustomerAccount
{
    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @param AuthenticationInterface $authentication
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     */
    public function __construct(
        AuthenticationInterface $authentication,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement
    ) {
        $this->authentication = $authentication;
        $this->customerRepository = $customerRepository;
        $this->accountManagement = $accountManagement;
    }

    /**
     * Check customer account
     *
     * @param int|null $customerId
     * @param int|null $customerType
     * @return void
     * @throws GraphQlAuthorizationException
     * @throws GraphQlNoSuchEntityException
     * @throws GraphQlAuthenticationException
     */
    public function execute(?int $customerId, ?int $customerType): void
    {
        if (true === $this->isCustomerGuest($customerId, $customerType)) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        try {
            $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(
                __('Customer with id "%customer_id" does not exist.', ['customer_id' => $customerId]),
                $e
            );
        }

        if (true === $this->authentication->isLocked($customerId)) {
            throw new GraphQlAuthenticationException(__('The account is locked.'));
        }

        $confirmationStatus = $this->accountManagement->getConfirmationStatus($customerId);
        if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
            throw new GraphQlAuthenticationException(__("This account isn't confirmed. Verify and try again."));
        }
    }

    /**
     * Checking if current customer is guest
     *
     * @param int|null $customerId
     * @param int|null $customerType
     * @return bool
     */
    private function isCustomerGuest(?int $customerId, ?int $customerType): bool
    {
        if (null === $customerId || null === $customerType) {
            return true;
        }
        return 0 === (int)$customerId || (int)$customerType === UserContextInterface::USER_TYPE_GUEST;
    }
}
