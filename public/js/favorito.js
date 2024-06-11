document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.add-favorito').forEach(span => {
        span.addEventListener('click', () => {
            const idUsuario = span.getAttribute('data-id-usuario');
            const idProducto = span.getAttribute('data-id-producto');
            const icon = span.querySelector('i');

            const isFavorito = icon.classList.contains('bi-heart-fill');

            fetch('/favorito', {
                method: isFavorito ? 'DELETE' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ idUsuario: idUsuario, idProducto: idProducto }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    if (isFavorito) {
                        icon.classList.remove('bi-heart-fill');
                        icon.classList.add('bi-heart');
                    } else {
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill');
                    }
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
