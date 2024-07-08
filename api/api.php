<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include 'db.php';
include 'Producto.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($conn);
        break;
    case 'POST':
        handlePost($conn);
        break;
    case 'PUT':
        handlePut($conn);
        break;
    case 'DELETE':        
        handleDelete($conn);
        break;
    default:
        echo json_encode(['message' => 'Método no permitido']);
        break;
}

// Este método devuelve un producto o todos los productos
function handleGet($conn) 
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id > 0) 
    {
        $stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) 
        {
            $productoObj = Producto::fromArray($producto);
            echo json_encode($productoObj->toArray());
        } 
        else 
        {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron datos']);
        }
    } 
    else 
    {
        $stmt = $conn->query("SELECT * FROM productos");
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $productoObjs = array_map(fn($producto) => Producto::fromArray($producto)->toArray(), $productos);
        echo json_encode(['productos' => $productoObjs]);
    }
}

// Este método es para ingresar productos
function handlePost($conn) 
{
    if ($conn === null) 
    {
        echo json_encode(['message' => 'Error en la conexión a la base de datos']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    $requiredFields = ['nombre', 'cantidad', 'precio'];
    foreach ($requiredFields as $field) 
    {
        if (!isset($data[$field])) 
        {
            echo json_encode(['message' => 'Datos del producto incompletos']);
            return;
        }
    }

    $producto = Producto::fromArray($data);

    try 
    {
        $stmt = $conn->prepare("INSERT INTO productos (nombre, cantidad, precio) VALUES (?, ?, ?)");
        $stmt->execute([
            $producto->nombre,
            $producto->cantidad,
            $producto->precio
        ]);

        $producto->id = $conn->lastInsertId();
        echo json_encode(['message' => 'Producto ingresado correctamente', 'producto' => $producto->toArray()]);
    } 
    catch (PDOException $e) 
    {
        echo json_encode(['message' => 'Error al ingresar el producto', 'error' => $e->getMessage()]);
    }
}

function handlePut($conn) 
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id > 0) 
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $producto = Producto::fromArray($data);
        $producto->id = $id;

        $fields = [];
        $params = [];

        if ($producto->nombre !== null) {
            $fields[] = 'nombre = ?';
            $params[] = $producto->nombre;
        }
        if ($producto->cantidad !== null) {
            $fields[] = 'cantidad = ?';
            $params[] = $producto->cantidad;
        }
        if ($producto->precio !== null) {
            $fields[] = 'precio = ?';
            $params[] = $producto->precio;
        }

        if (!empty($fields)) 
        {
            $params[] = $id;
            $stmt = $conn->prepare("UPDATE productos SET " . implode(', ', $fields) . " WHERE id = ?");
            $stmt->execute($params);
            echo json_encode(['message' => 'Producto actualizado con éxito']);
        } 
        else 
        {
            echo json_encode(['message' => 'No hay campos para actualizar']);
        }
    } 
    else 
    {
        echo json_encode(['message' => 'ID no proporcionado']);
    }
}

// Método para borrar registros
function handleDelete($conn) 
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id > 0) 
    {
        $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['message' => 'Producto eliminado con éxito']);
    } 
    else 
    {
        echo json_encode(['message' => 'ID no proporcionado']);
    }
}
?>
