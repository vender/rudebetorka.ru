<?php
class Requests
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
            'city'       => 'Регион',
            'company'    => 'Название учреждения',
            'fio'        => 'Ответственное лицо',
            'contacts'   => 'Контакты',
            'created_at' => 'Дата',
        ];

        return $ordering;
    }
}
?>
