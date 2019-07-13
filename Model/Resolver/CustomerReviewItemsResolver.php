<?php
/**
 * @category   Magevneed
 * @package    Magevneed_ReviewGraphQL
 * @author     magevneed@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Magevneed\ReviewGraphQL\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Fetches the Review Details data according to the GraphQL schema
 */
class CustomerReviewItemsResolver implements ResolverInterface
{

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
        if (!isset($value['reviews'])) {
            throw new LocalizedException(__('Missing key "reviews" in Review value data'));
        }
        $reviewItems = $value['reviews'];

        $data = [];
        foreach ($reviewItems as $reviewItem) {
            $data[] = [
                'created_at' => $reviewItem->getCreatedAt(),
                'title'      => $reviewItem->getTitle(),
                'detail'     => $reviewItem->getDetail(),
                'nickname'   => $reviewItem->getNickname(),
                'reviewid'   => $reviewItem->getId(),
                'product'    => $reviewItem->getEntityPkValue(),
            ];
        }
        return $data;
    }
}
