<?php
class Report
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
            'event_date'  => 'Дата проведения',
            'created_at'  => 'Дата добавления',
            'event_name'  => 'Альманах',
            'customer_id' => 'Место проведения',
            'childrens'   => 'Детей',
            'teenager'    => 'Подростков',
        ];

        return $ordering;
    }
}
?>
