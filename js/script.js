document.addEventListener('DOMContentLoaded', () => {
    const apiUrl = 'http://localhost/ProyectoFinal/api/api.php';
    const agregarForm = document.getElementById('agregarProductoForm');
    const actualizarForm = document.getElementById('actualizarProductoForm');
    const tableBody = document.getElementById('productosTable').querySelector('tbody');
    let isUpdating = false;

    const fetchProductos = async () => {
        try {
            const response = await fetch(apiUrl);
            const data = await response.json();
            tableBody.innerHTML = '';
            data.productos.forEach(producto => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${producto.id}</td>
                    <td>${producto.nombre}</td>
                    <td>${producto.cantidad}</td>
                    <td>${producto.precio}</td>
                    <td>
                        <button id="action1" onclick="editProducto(${producto.id}, '${producto.nombre}', ${producto.cantidad}, ${producto.precio})">Editar</button>
                        <button id="action2" onclick="deleteProducto(${producto.id})">Eliminar</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } catch (error) {
            console.error(JSON.stringify(error));
        }
    };

    const addProducto = async (producto) => {
        await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(producto)
        });
        fetchProductos();
    };

    const updateProducto = async (id, producto) => {
        await fetch(`${apiUrl}?id=${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(producto)
        });
        fetchProductos();
    };

    const deleteProducto = async (id) => {
        await fetch(`${apiUrl}?id=${id}`, {
            method: 'DELETE'
        });
        fetchProductos();
    };

    agregarForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const nombre = document.getElementById('nombre').value;
        const cantidad = document.getElementById('cantidad').value;
        const precio = document.getElementById('precio').value;
        const producto = { nombre, cantidad, precio };

        addProducto(producto);
        agregarForm.reset();
    });

    actualizarForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const id = document.getElementById('idActualizar').value;
        const nombre = document.getElementById('nombreActualizar').value;
        const cantidad = document.getElementById('cantidadActualizar').value;
        const precio = document.getElementById('precioActualizar').value;
        const producto = { nombre, cantidad, precio };

        if (isUpdating) {
            updateProducto(id, producto);
            isUpdating = false;
            toggleForm('actualizarProductoForm');
        }

        actualizarForm.reset();
        document.getElementById('idActualizar').value = '';
    });

    window.editProducto = (id, nombre, cantidad, precio) => {
        document.getElementById('idActualizar').value = id;
        document.getElementById('nombreActualizar').value = nombre;
        document.getElementById('cantidadActualizar').value = cantidad;
        document.getElementById('precioActualizar').value = precio;
        isUpdating = true;
        toggleForm('actualizarProductoForm');
    };

    window.deleteProducto = (id) => {
        if (confirm('¿Estás seguro de eliminar este producto?')) {
            deleteProducto(id);
        }
    };

    fetchProductos();
});

function toggleForm(formId) {
    const form = document.getElementById(formId);
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}