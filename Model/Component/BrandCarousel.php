<?php

namespace MageSuite\ContentConstructorFrontend\Model\Component;

class BrandCarousel extends \Magento\Framework\DataObject implements ViewModel
{
    /**
     * @var \MageSuite\BrandManagement\Model\ResourceModel\Brands\CollectionFactory
     */
    protected $brandCollection;

    /**
     * @var \MageSuite\BrandManagement\Model\BrandsRepository
     */
    protected $brandsRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \MageSuite\BrandManagement\Model\ResourceModel\Brands\CollectionFactory $brandCollection,
        \MageSuite\BrandManagement\Model\BrandsRepository $brandsRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        parent::__construct($data);

        $this->brandCollection = $brandCollection;
        $this->brandsRepository = $brandsRepository;
        $this->storeManager = $storeManager;
    }

    public function getBrands() {
        $brands = $this->brandCollection->create();

        $data = [];

        if(empty($brands)) {
            return $data;
        }

        foreach($brands as $brand) {
            $storeId = $this->storeManager->getStore()->getId();
            $brand = $this->brandsRepository->getById($brand->getEntityId(), $storeId);

            if(!$this->isBrandVisible($brand)) {
                continue;
            }

            $data[] = [
                'href' => $brand->getBrandUrl(),
                'image' => [
                    'src' => $brand->getBrandIconUrl(),
                    'alt' => $brand->getBrandName()
                ]
            ];
        }

        return $data;
    }

    protected function isBrandVisible($brand) {
        return $brand->getEnabled() and
            $brand->getShowInBrandCarousel() and
            $brand->getBrandIconUrl() and
            $brand->getBrandUrl();
    }
}
