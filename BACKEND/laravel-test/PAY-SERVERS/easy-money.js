const express = require('express');
const axios = require('axios'); // Importar Axios
const app = express();
app.use(express.json());

// Endpoint para Pago Directo (Pago EasyMoney)
app.post('/process', async (req, res) => {
    const { amount, currency } = req.body;

    // Validar que amount es un número entero
    if (typeof amount !== 'number' || !Number.isInteger(amount)) {
        console.log('Error: El monto debe ser un número entero.');
        return res.status(400).send({ message: 'Error en el pago' }); // Devuelve error
    }

    try {
        // Realizar la llamada a la API de EasyMoney
        const response = await axios.post('http://127.0.0.1:8000/api/payments/easymoney', {
            amount: amount,
            currency: currency
        });

        // Si la llamada es exitosa, devolver la respuesta
        return res.status(response.status).send(response.data);
    } catch (error) {
        // Manejar errores de la llamada a la API
        console.error('Error en la llamada a la API:', error.message);
        return res.status(500).send({ message: 'Error en la conexión' });
    }
});

// Iniciar el servidor en el puerto 3000
const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Servidor de pago EasyMoney ejecutándose en el puerto ${PORT}`);
});