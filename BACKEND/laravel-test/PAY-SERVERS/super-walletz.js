// Simulador de servidor para imitar respuestas de los sistemas de pago ficticios
const express = require('express');
const axios = require('axios'); // Asegúrate de tener Axios instalado
const app = express();
app.use(express.json());

// Endpoint para iniciar Super Walletz
app.post('/pay', async (req, res) => {
    const { amount, currency, callback_url } = req.body;

    // Datos a enviar a la API de Laravel
    const paymentData = {
        amount: amount,
        currency: currency,
        callback_url: callback_url,
    };

    try {
        // Llamada a la API de Laravel
        const response = await axios.post('http://localhost:8000/api/payments/superwalletz', paymentData);
        
        // Simula una respuesta inicial exitosa
        res.status(200).send({ transaction_id: response.data.transaction_id });

        // Simula el envío del webhook tras un tiempo de 5 segundos
        setTimeout(() => {
            const webhookResponse = {
                transaction_id: response.data.transaction_id,
                status: 'success'
            };

            // Realiza 3 llamadas POST consecutivas al callback_url con la respuesta del webhook
            let send = () => {
                axios.post(callback_url, webhookResponse)
                    .then(() => console.log('Webhook enviado: ', webhookResponse))
                    .catch((error) => console.error('Error enviando webhook: ', error.message));
            }

            for (let i = 0; i < 3; i++) {
                setTimeout(send, 1000);
            }

        }, 5000); // 5 segundos de retraso para simular el tiempo de procesamiento

    } catch (error) {
        console.error('Error al comunicarse con la API de Laravel:', error.message);
        res.status(500).send({ error: 'Error al procesar el pago' });
    }
});

// Iniciar el servidor en el puerto 3003
const PORT = 3003;
app.listen(PORT, () => {
    console.log(`Servidor de pago SuperWalletz ejecutándose en el puerto ${PORT}`);
});