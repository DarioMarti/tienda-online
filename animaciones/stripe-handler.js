const stripe = Stripe(STRIPE_PUBLISHABLE_KEY);
let elements;

initialize();

document
    .getElementById("checkout-form")
    .addEventListener("submit", handleSubmit);

async function initialize() {
    // 1. Obtener el clientSecret del backend
    const response = await fetch("../modelos/carrito/crear-intent.php", {
        method: "POST",
    });
    const { clientSecret, error: backendError } = await response.json();

    if (backendError) {
        showMessage(backendError);
        return;
    }

    // 2. Inicializar Stripe Elements con el secret
    elements = stripe.elements({
        clientSecret,
        appearance: {
            theme: 'stripe',
            variables: {
                colorPrimary: '#000000',
                fontFamily: 'Inter, sans-serif',
            }
        }
    });

    const paymentElement = elements.create("payment");
    paymentElement.mount("#payment-element");
}

async function handleSubmit(e) {
    e.preventDefault();
    setLoading(true);

    // 1. Validar el formulario de envío primero
    const form = e.target;
    if (!form.checkValidity()) {
        form.reportValidity();
        setLoading(false);
        return;
    }

    // 2. Guardar datos de envío en sesión antes de proceder con Stripe
    const formData = new FormData(form);
    const shippingData = {};
    formData.forEach((value, key) => {
        shippingData[key] = value;
    });

    try {
        const response = await fetch("../modelos/carrito/guardar-datos-envio.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(shippingData),
        });
        const result = await response.json();

        if (!result.success) {
            showMessage("Error al guardar información de envío: " + result.message);
            setLoading(false);
            return;
        }
    } catch (error) {
        console.error("Error al guardar datos de envío:", error);
        showMessage("Ocurrió un error al procesar tus datos de envío.");
        setLoading(false);
        return;
    }

    // 3. Confirmar el pago con Stripe
    const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
            // Redirigir a la página de confirmación de nuestro sitio
            return_url: window.location.origin + "/Tienda-Online/modelos/carrito/procesar-pago.php",
        },
    });

    // Este punto solo se alcanza si hay un error inmediato (ej. tarjeta rechazada)
    if (error.type === "card_error" || error.type === "validation_error") {
        showMessage(error.message);
    } else {
        showMessage("Ocurrió un error inesperado.");
    }

    setLoading(false);
}

// Helpers
function showMessage(messageText) {
    const messageContainer = document.querySelector("#payment-message");
    messageContainer.classList.remove("hidden");
    messageContainer.textContent = messageText;

    setTimeout(function () {
        messageContainer.classList.add("hidden");
        messageText.textContent = "";
    }, 4000);
}

function setLoading(isLoading) {
    if (isLoading) {
        document.querySelector("#pay-submit-btn").disabled = true;
        document.querySelector("#spinner").classList.remove("hidden");
        document.querySelector("#button-text").classList.add("hidden");
    } else {
        document.querySelector("#pay-submit-btn").disabled = false;
        document.querySelector("#spinner").classList.add("hidden");
        document.querySelector("#button-text").classList.remove("hidden");
    }
}
