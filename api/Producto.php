<?php
class Producto 
{
    public $id;
    public $nombre;
    public $cantidad;
    public $precio;

    public function __construct($nombre, $cantidad, $precio, $id = null) 
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->cantidad = $cantidad;
        $this->precio = $precio;
    }

    public static function fromArray($data) 
    {
        return new self(
            $data['nombre'] ?? null,
            $data['cantidad'] ?? null,
            $data['precio'] ?? null,
            $data['id'] ?? null
        );
    }

    public function toArray() 
    {
        return get_object_vars($this);
    }
}
?>