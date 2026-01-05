document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkout-form');
    if (!checkoutForm) return;

    checkoutForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('pay-submit-btn');
        const spinner = document.getElementById('spinner');
        const btnText = document.getElementById('button-text');
        
        // UI Loading State
        if (submitBtn) submitBtn.disabled = true;
        if (spinner) spinner.classList.remove('hidden');
        if (btnText) btnText.classList.add('hidden');

        try {
            // Stripe initialization
            // Asegurarse de que STRIPE_PUBLIC_KEY está definido en la página
            if (typeof STRIPE_PUBLIC_KEY === 'undefined') {
                throw new Error("Clave pública de Stripe no encontrada");
            }
            
            const stripe = Stripe(STRIPE_PUBLIC_KEY);

            // Crear sesi�n de pago en backend
            const response = await fetch('../modelos/crear-sesion-pago.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const session = await response.json();

            if (session.error) {
                throw new Error(session.error);
            }

            // Redirigir a Stripe Checkout
            const result = await stripe.redirectToCheckout({
                sessionId: session.id
            });

            if (result.error) {
                throw new Error(result.error.message);
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Error: ' + error.message);
            
            // Reset UI
            if (submitBtn) submitBtn.disabled = false;
            if (spinner) spinner.classList.add('hidden');
            if (btnText) btnText.classList.remove('hidden');
        }
    });
});
