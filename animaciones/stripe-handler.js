

const stripe = Stripe('pk_test_51SjaCeQ2gnFM99eyyW9LtaYHxFUMg3PPqUaj3xetyr31veB0Bvs6oVNiFarjrJEcDJWKrSWNZRnIvClPYGga8ncp00uPfDIvD3');

document.getElementById('realizar-pago-btn').addEventListener('click', () => {
    fetch('../modelos/confirmar-pago.php', { method: 'POST' }) // Ruta al archivo PHP
        .then(response => response.json())
        .then(session => {
            return stripe.redirectToCheckout({ sessionId: session.id });
        })
        .catch(error => {
            console.error('Error:', error);
        });
});