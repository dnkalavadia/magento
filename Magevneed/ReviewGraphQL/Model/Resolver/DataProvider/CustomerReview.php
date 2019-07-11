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
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

/**
 * Product Review data provider
 */
class CustomerReview
{
    /**
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
     */
    private $reviewCollectionFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * CustomerReview constructor.
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param string $customerId
     * @return array
     * @throws NoSuchEntityException
     */
    public function getData($customerId)
    {
        try {
            if ($customerId!=0) {
                $customer = $this->customerRepository->getById($customerId);
                $collection = $this->reviewCollectionFactory->create()
                    ->addStatusFilter(
                        \Magento\Review\Model\Review::STATUS_APPROVED
                    )->addCustomerFilter($customerId)
                    ->setDateOrder();
                $name = $customer->getFirstname().' '.$customer->getLastname();
                $reviewData = [
                    'name'=> $name,
                    'email'=> $customer->getEmail(),
                    'review_count' => $collection->getSize(),
                    'reviews' => $collection->getItems()
                ];
            } else {
                $collection = $this->reviewCollectionFactory->create()
                    ->addStatusFilter(
                        \Magento\Review\Model\Review::STATUS_APPROVED
                    )->setDateOrder();
                $reviewData = [
                    'review_count' => $collection->getSize(),
                    'reviews' => $collection->getItems()
                ];
            }
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $reviewData;
    }
}
