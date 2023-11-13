<?php
class Product
{
    /**
     *
     */
    public function __construct()
    {
    }

    /**
     *
     */
    public function __destruct()
    {
    }
    
    /**
     * Set friendly columns\' names to order tables\' entries
     */
    public function setOrderingValues()
    {
        $ordering = [
            'id'          => 'ID',
            'name'        => 'Название',
            'image'       => 'Фото',
            'description' => 'Описание',
            'price'       => 'Цена',
            'discount'    => 'Скидка'
        ];

        return $ordering;
    }
}
?>
