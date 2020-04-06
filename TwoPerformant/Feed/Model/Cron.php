<?php
namespace TwoPerformant\Feed\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Store\Model\StoreManagerInterface;


class Cron
{
    protected $productRepository;
    protected $searchCriteriaBuilder;
    protected $filterBuilder;
    protected $storeManager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->storeManager = $storeManager;
    }

    public function export()
    {
        $date = $this->getLastWeekDate();
        $items = $this->getProducts($date);
        $this->writeToFile($items);
    }

    protected function getLastWeekDate()
    {
        $now = new \DateTime();
        $interval = new \DateInterval('P1W');
        $lastWeek = $now->sub($interval);
        return $lastWeek;
    }

    public function getProducts($date)
    {
        $filters = [];

        $filters[] = $this->filterBuilder
            ->setField('created_at')
            ->setConditionType('gt')
            ->setValue($date->format('Y-m-d H:i:s'))
            ->create();

        $this->searchCriteriaBuilder->addFilters($filters);

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->productRepository->getList($searchCriteria);
        return $searchResults->getItems();
    }

    protected function writeToFile($items)
    {
        if (count($items) > 0) {
            $feed = fopen("pub/feeds/2p_feed.csv", "w") or die("Unable to open file!");
            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

            foreach ($items as $item) {
                $category = $item->getCategory() ? $item->getCategory() : 'default';

                if($item->getPrice()) {
                    fputcsv($feed, [
                        $item->getName(),
                        strip_tags($item->getDescription()),
                        "", //Short message: another short additional description or any other information about the product (it’s optional – if you don’t want to add a short message fill the row with “”);
                        $item->getPrice(),
                        $category,
                        "", //Subcategory
                        $item->getProductUrl(),
                        $mediaUrl . 'catalog/product' . $item->getImage(),
                        $item->getId(),
                        0, // this function is disabled, thus it should always be filled with 0;
                        "", // brand
                        $item->isInStock(),
                        "" // Other data in JSON or YAML format
                    ]);
                }
            }
            fclose($feed);
        }
    }
}
